<?php
require_once __DIR__ . '/auth_guard.php';
session_start();
require_once 'conn.php';

// Verificar se usuário está logado
if (!isset($_SESSION['login'])) {
    header('Location: login.php');
    exit;
}

// Buscar dados do usuário
$login = $_SESSION['login'];
$stmt_user = mysqli_prepare($conn, "SELECT * FROM login WHERE login = ?");
mysqli_stmt_bind_param($stmt_user, "s", $login);
mysqli_stmt_execute($stmt_user);
$query_busca_usuario = mysqli_stmt_get_result($stmt_user);
$total_busca_usuario = mysqli_num_rows($query_busca_usuario);

if ($total_busca_usuario == 0) {
    header('Location: login.php');
    exit;
}

while($rows_usuarios = $query_busca_usuario->fetch_array()) {
    $nome = $rows_usuarios['nome'];
    $img_perfil = $rows_usuarios['perfil_img'];
    $autorizado = $rows_usuarios['autorizado'];
    $tipo = $rows_usuarios['tipo'];
    $usuario_api = $rows_usuarios['usuario_api'];
}

// Verificar se é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: msg_massa2.php');
    exit;
}

try {
    // Receber dados do formulário
    $clientes = $_POST['clientes'] ?? [];
    $media_type = $_POST['media_type'] ?? 'text';
    $message_text = $_POST['message_text'] ?? '';
    $send_option = $_POST['send_option'] ?? 'now';
    $schedule_date = $_POST['schedule_date'] ?? '';
    $schedule_time = $_POST['schedule_time'] ?? '';
    $interval_value = (int)($_POST['interval_value'] ?? 5);
    $interval_unit = $_POST['interval_unit'] ?? 'minutes';
    $interval_seconds = (int)($_POST['interval_seconds'] ?? 300);
    $usar_ia = isset($_POST['usar_ia']) ? 1 : 0;
    $campaign_name = $_POST['campaign_name'] ?? '';
    $start_time = $_POST['start_time'] ?? '09:00';
    $end_time = $_POST['end_time'] ?? '18:00';
    $repeat_option = $_POST['repeat_option'] ?? 'once';
    $days = $_POST['days'] ?? [1,2,3,4,5]; // Segunda a sexta por padrão
    
    // Validações básicas
    if (empty($clientes)) {
        throw new Exception('Nenhum cliente selecionado.');
    }
    
    if ($media_type === 'text' && empty(trim($message_text))) {
        throw new Exception('Mensagem de texto é obrigatória.');
    }
    
    if ($media_type !== 'text' && !isset($_FILES['media_file'])) {
        throw new Exception('Arquivo de mídia é obrigatório.');
    }
    
    // Processar agendamento
    $schedule_datetime = null;
    $proximo_envio = null;
    
    if ($send_option === 'later') {
        if (empty($schedule_date) || empty($schedule_time)) {
            throw new Exception('Data e hora do agendamento são obrigatórias.');
        }
        
        $schedule_datetime = $schedule_date . ' ' . $schedule_time;
        $proximo_envio = $schedule_datetime;
        
        // Verificar se é futuro
        if (strtotime($schedule_datetime) <= time()) {
            throw new Exception('Data e hora devem ser futuras.');
        }
    } else {
        // Envio imediato
        $proximo_envio = date('Y-m-d H:i:s');
    }
    
    // Processar arquivo de mídia
    $media_file_path = null;
    $media_file_base64 = null;
    $media_file_url = null;
    
    if ($media_type !== 'text' && isset($_FILES['media_file'])) {
        $upload = $_FILES['media_file'];
        
        if ($upload['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Erro no upload do arquivo.');
        }
        
        // Verificar tamanho (16MB máximo)
        if ($upload['size'] > 16 * 1024 * 1024) {
            throw new Exception('Arquivo muito grande. Máximo 16MB.');
        }
        
        // Criar diretório se não existir
        $upload_dir = 'uploads/mensagens_massa/' . date('Y/m/');
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        // Gerar nome único
        $extension = pathinfo($upload['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $filepath = $upload_dir . $filename;
        
        // Mover arquivo
        if (!move_uploaded_file($upload['tmp_name'], $filepath)) {
            throw new Exception('Erro ao salvar arquivo.');
        }
        
        $media_file_path = $filepath;
        
        // Se arquivo for pequeno (< 2MB), converter para base64 também
        if ($upload['size'] < 2 * 1024 * 1024) {
            $media_file_base64 = base64_encode(file_get_contents($filepath));
        } else {
            // Para arquivos grandes, criar URL completa
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
            $media_file_url = $protocol . $_SERVER['HTTP_HOST'] . '/' . $filepath;
        }
    }
    
    // Preparar dados dos dias da semana
    $days_week = implode(',', $days);
    
    // Preparar nome da campanha
    if (empty($campaign_name)) {
        $campaign_name = 'Campanha ' . date('d/m/Y H:i');
    }
    
    // Preparar dados dos clientes em JSON
    $clientes_json = json_encode($clientes);
    $total_clientes = count($clientes);
    
    // Inserir na tabela mensagens_massa
    $stmt_insert = mysqli_prepare($conn, "INSERT INTO mensagens_massa (
        login, usuario_api, campaign_name, media_type, message_text,
        media_file_path, media_file_base64, media_file_url, clientes_ids,
        send_option, schedule_datetime, interval_seconds, start_time, end_time,
        repeat_option, days_week, usar_ia, status, total_clientes, proximo_envio, created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pendente', ?, ?, NOW())");
    mysqli_stmt_bind_param($stmt_insert, "sssssssssssissssiis",
        $login, $usuario_api, $campaign_name, $media_type, $message_text,
        $media_file_path, $media_file_base64, $media_file_url, $clientes_json,
        $send_option, $schedule_datetime, $interval_seconds, $start_time, $end_time,
        $repeat_option, $days_week, $usar_ia, $total_clientes, $proximo_envio
    );

    if (!mysqli_stmt_execute($stmt_insert)) {
        throw new Exception('Erro ao salvar campanha: ' . mysqli_error($conn));
    }

    $mensagem_massa_id = mysqli_insert_id($conn);
    
    // Inserir registros individuais para cada cliente na tabela mensagens_massa_envios
    $clientes_ids_int = array_map('intval', $clientes);
    $in_placeholders = implode(',', array_fill(0, count($clientes_ids_int), '?'));
    $sql_clientes = "SELECT id, nome, telefone FROM clientes WHERE id IN ($in_placeholders) AND usuario_api = ?";
    $stmt_clientes = mysqli_prepare($conn, $sql_clientes);
    $bind_types = str_repeat('i', count($clientes_ids_int)) . 's';
    $bind_values = array_merge($clientes_ids_int, [$usuario_api]);
    mysqli_stmt_bind_param($stmt_clientes, $bind_types, ...$bind_values);
    mysqli_stmt_execute($stmt_clientes);
    $result_clientes = mysqli_stmt_get_result($stmt_clientes);

    $stmt_envio = mysqli_prepare($conn,
        "INSERT INTO mensagens_massa_envios (mensagem_massa_id, cliente_id, cliente_nome, cliente_telefone, status, created_at)
         VALUES (?, ?, ?, ?, 'pendente', NOW())");

    if ($result_clientes && mysqli_num_rows($result_clientes) > 0) {
        while ($cliente = mysqli_fetch_assoc($result_clientes)) {
            $cid = $cliente['id'];
            $cnome = $cliente['nome'];
            $ctel = $cliente['telefone'];
            mysqli_stmt_bind_param($stmt_envio, "iiss", $mensagem_massa_id, $cid, $cnome, $ctel);
            mysqli_stmt_execute($stmt_envio);
        }
    }

    // Se for envio imediato, marcar como processando
    if ($send_option === 'now') {
        $stmt_update = mysqli_prepare($conn, "UPDATE mensagens_massa SET status = 'processando' WHERE id = ?");
        mysqli_stmt_bind_param($stmt_update, "i", $mensagem_massa_id);
        mysqli_stmt_execute($stmt_update);
        
        // Chamar o cron imediatamente para iniciar o processamento
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
        $cron_url = $protocol . $_SERVER['HTTP_HOST'] . '/cron_mensagens.php';
        $post_data = 'usuario_api=' . urlencode($usuario_api);
        
        // Chamada assíncrona
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'content' => $post_data,
                'timeout' => 1
            ]
        ]);
        
        @file_get_contents($cron_url, false, $context);
    }
    
    // Redirecionar com sucesso
    $success_message = "Campanha criada com sucesso! ID: $mensagem_massa_id";
    header('Location: msg_relatorio.php?success=' . urlencode($success_message));
    exit;
    
} catch (Exception $e) {
    // Em caso de erro, redirecionar de volta
    header('Location: msg_massa2.php?error=' . urlencode($e->getMessage()));
    exit;
}

// Log para debug (opcional)
function debug_log($message) {
    $log_file = 'logs/mensagens_debug_' . date('Y-m-d') . '.log';
    $timestamp = date('Y-m-d H:i:s');
    
    // Criar diretório se não existir
    if (!is_dir('logs')) {
        mkdir('logs', 0755, true);
    }
    
    file_put_contents($log_file, "[$timestamp] $message" . PHP_EOL, FILE_APPEND | LOCK_EX);
}
?>