<?php
$auth_ajax_mode = true;
require_once __DIR__ . '/auth_guard.php';
// buscar_horario.php

// ===================================
// CONFIGURAÇÕES E SEGURANÇA
// ===================================
header('Content-Type: application/json; charset=utf-8');

include 'conn.php';
include 'funcoes.php'; // Incluindo para garantir consistência, caso use funções dele

// Verifica se o usuário está logado
if (!isset($_SESSION['login'])) {
    echo json_encode(['success' => false, 'message' => 'Acesso negado. Por favor, faça o login.']);
    exit();
}
$login = $_SESSION['login'];

// ===================================
// LÓGICA DO SCRIPT
// ===================================

// Verifica se a requisição é do tipo POST e se o ID do horário foi enviado
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['horario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Requisição inválida.']);
    exit();
}

$horario_id = (int)$_POST['horario_id'];
$response_data = [];

try {
    // 1. Buscar dados do horário e do profissional, garantindo que pertence ao usuário logado
    $sql_horario = "
        SELECT 
            hp.id, hp.profissional_id, hp.dia_semana, hp.hora_entrada, 
            hp.almoco_inicio, hp.almoco_fim, hp.hora_saida, hp.ativo,
            p.profissional_nome, p.profissional_cargo
        FROM horarios_profissional AS hp
        JOIN profissional AS p ON hp.profissional_id = p.id
        WHERE hp.id = ? AND p.login = ?";

    $stmt_horario = mysqli_prepare($conn, $sql_horario);
    mysqli_stmt_bind_param($stmt_horario, "is", $horario_id, $login);
    mysqli_stmt_execute($stmt_horario);
    $result_horario = mysqli_stmt_get_result($stmt_horario);

    if ($horario_data = mysqli_fetch_assoc($result_horario)) {
        // Mapear nome do dia da semana para exibição amigável
        $dias_map = [
            'segunda' => 'Segunda-feira', 'terca' => 'Terça-feira', 'quarta' => 'Quarta-feira',
            'quinta' => 'Quinta-feira', 'sexta' => 'Sexta-feira', 'sabado' => 'Sábado', 'domingo' => 'Domingo'
        ];
        $horario_data['dia_semana_nome'] = $dias_map[$horario_data['dia_semana']] ?? ucfirst($horario_data['dia_semana']);
        
        $response_data['horario'] = $horario_data;
        $response_data['profissional'] = [
            'nome' => $horario_data['profissional_nome'],
            'cargo' => $horario_data['profissional_cargo']
        ];
        $profissional_id = $horario_data['profissional_id'];

        // 2. Buscar intervalos adicionais associados ao horário
        $sql_intervalos = "SELECT * FROM intervalos_profissional WHERE horario_id = ? AND login = ?";
        $stmt_intervalos = mysqli_prepare($conn, $sql_intervalos);
        mysqli_stmt_bind_param($stmt_intervalos, "is", $horario_id, $login);
        mysqli_stmt_execute($stmt_intervalos);
        $result_intervalos = mysqli_stmt_get_result($stmt_intervalos);
        $response_data['intervalos'] = mysqli_fetch_all($result_intervalos, MYSQLI_ASSOC);

        // 3. Se for uma solicitação de "Ver Detalhes", buscar também os serviços associados ao profissional
        if (isset($_POST['detalhes']) && $_POST['detalhes'] == 1) {
            $sql_servicos = "
                SELECT s.nome, ps.tempo_execucao_minutos, ps.valor_profissional
                FROM profissional_servicos AS ps
                JOIN servicos AS s ON ps.servico_id = s.id
                WHERE ps.profissional_id = ? AND ps.login = ?";
            $stmt_servicos = mysqli_prepare($conn, $sql_servicos);
            mysqli_stmt_bind_param($stmt_servicos, "is", $profissional_id, $login);
            mysqli_stmt_execute($stmt_servicos);
            $result_servicos = mysqli_stmt_get_result($stmt_servicos);
            $response_data['servicos'] = mysqli_fetch_all($result_servicos, MYSQLI_ASSOC);
        }

        echo json_encode(['success' => true, 'data' => $response_data]);

    } else {
        // Se não encontrou o horário, retorna um erro
        echo json_encode(['success' => false, 'message' => 'Horário não encontrado ou você não tem permissão para acessá-lo.']);
    }

} catch (Exception $e) {
    // Captura qualquer erro inesperado e envia uma resposta JSON de erro
    echo json_encode(['success' => false, 'message' => 'Ocorreu um erro no servidor: ' . $e->getMessage()]);
} finally {
    // Fecha a conexão com o banco de dados
    mysqli_close($conn);
}
