<?php
include '../conn.php';
include '../funcoes.php';
#include 'api_funcao.php';
include '../config_dados.php';
include 'editacodigo.php';
include 'salvajson.php';

?><?php
// === Lê o JSON enviado via POST ===
$inputJSON = file_get_contents("php://input");
$data = json_decode($inputJSON, true);

// === SALVAR DADOS RECEBIDOS ===
$log_recebido = "=== DADOS RECEBIDOS EM " . date('Y-m-d H:i:s') . " ===\n";
$log_recebido .= "JSON BRUTO: " . $inputJSON . "\n";
$log_recebido .= "JSON DECODIFICADO: " . print_r($data, true) . "\n";
$log_recebido .= "================================================\n\n";
file_put_contents('log_recebidos.txt', $log_recebido, FILE_APPEND | LOCK_EX);

// === Verifica se houve erro na decodificação ===
if ($data === null) {
    $erro_log = "ERRO: JSON inválido em " . date('Y-m-d H:i:s') . "\n";
    file_put_contents('log_recebidos.txt', $erro_log, FILE_APPEND | LOCK_EX);
    http_response_code(400);
    echo "Erro: JSON inválido.";
    exit;
}

// === Extrai os dados necessários ===
$usuarios_array = isset($data['usuarios']) ? $data['usuarios'] : [];
$message = isset($data['message']) ? $data['message'] : "";
$token_recebido = isset($data['token']) ? $data['token'] : "";

// === Verifica se há usuários para processar ===
if (empty($usuarios_array)) {
    $erro_usuarios = "ERRO: Nenhum usuário encontrado para processar em " . date('Y-m-d H:i:s') . "\n";
    file_put_contents('log_recebidos.txt', $erro_usuarios, FILE_APPEND | LOCK_EX);
    echo "Nenhum usuário encontrado para processar.";
    exit;
}

// === INICIALIZAR LOG DE PROCESSAMENTO ===
$log_processamento = "=== INÍCIO DO PROCESSAMENTO EM " . date('Y-m-d H:i:s') . " ===\n";
$log_processamento .= "Total de usuários a processar: " . count($usuarios_array) . "\n";
$log_processamento .= "Usuários: " . implode(', ', $usuarios_array) . "\n";
$log_processamento .= "=========================================\n\n";

// === Ajustar o fuso horário para o Brasil ===
mysqli_query($conn, "SET time_zone = '-03:00'");

// === Definir data e hora ===
date_default_timezone_set('America/Sao_Paulo');
$data_atual = date('Y-m-d');
$hora_atual = date('H:i');
$data_hora_atual = date('Y-m-d H:i:s');
// === Funções auxiliares ===
function novo_texto($string, $nome, $horario,$data_formatada, $profissional,$servico,$valor_servico) {
    $substituicoes = [
        '{nome}'        => $nome,
        '{data_agendamento}' => $data_formatada,
        '{hora_agendamento}' => $horario,
        '{serviço}' => $servico,
        '{preço_serviço}' => '$'.$valor_servico,
        '{profissional}' => $profissional
    ];
    return str_replace(array_keys($substituicoes), array_values($substituicoes), $string);
}

function formatar_data_brasileira($data) {
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
        return date("d/m/Y", strtotime($data));
    } else {
        return $data;
    }
}

function formatarTexto($texto) {
    $texto = preg_replace('/\s+/', ' ', trim($texto));
    $texto .= "\nSim\nNão";
    return $texto;
}

// === PROCESSA CADA USUÁRIO INDIVIDUALMENTE ===
foreach ($usuarios_array as $usuario_api) {
    
    echo "==== PROCESSANDO USUÁRIO: $usuario_api ====\n";
    $log_processamento .= "--- PROCESSANDO USUÁRIO: $usuario_api ---\n";
    
    // === Buscar configurações ===
    $sql_config = "SELECT * FROM config";
    $query_config = mysqli_query($conn, $sql_config);
    $total_config = mysqli_num_rows($query_config);

    while($rows_config = mysqli_fetch_array($query_config)) {
        $servidor  = $rows_config['ip_vps'];
        $porta  = $rows_config['porta'];
        $nova_porta  = $rows_config['nova_porta'];
        $token  = $rows_config['chave'];
        $chave_painel  = $rows_config['chave_painel'];
        $webhook  = $rows_config['webhook'];
        $google  = $rows_config['google'];
        $link_pagamento  = $rows_config['link_pagamento'];
    } 
    
    // === Buscar dados do login ===
    $stmt_lg = $conn->prepare("SELECT * FROM login WHERE usuario_api = ? AND tipo = '2'");
    $stmt_lg->bind_param("s", $usuario_api);
    $stmt_lg->execute();
    $query = $stmt_lg->get_result();
    $stmt_lg->close();

    $tempo = '';
    $despedida = '';
    $agenda_verfica = '';
    
    while ($lista_login = $query->fetch_array()) {
        $tempo           = $lista_login['tempo_final'];
        $despedida       = $lista_login['IA_despedida'];
        $agenda_verfica  = $lista_login['agenda_verfica'];
    }

    // === PROCESSAMENTO DE DESPEDIDAS ===
    if (!empty($tempo) && !empty($despedida)) {
        
        // === Buscar clientes para envio de mensagem de despedida ===
        $tempo_int = (int)$tempo;
        $stmt_cli = $conn->prepare("SELECT * FROM clientes WHERE usuario_api = ? AND (situacao = '1' OR situacao IS NULL) AND time_atendimento <= DATE_SUB(NOW(), INTERVAL ? MINUTE) LIMIT 1");
        $stmt_cli->bind_param("si", $usuario_api, $tempo_int);
        $stmt_cli->execute();
        $query = $stmt_cli->get_result();
        $total = $query->num_rows;
        $stmt_cli->close();

        if ($total > 0) {
            while ($lista_login = $query->fetch_array()) {
                $id       = $lista_login['id'];
                $telefone = $lista_login['telefone'];

                echo "Enviando despedida para cliente ID: $id, Telefone: $telefone\n";
                $log_processamento .= "DESPEDIDA: Cliente ID $id, Telefone: $telefone\n";

                // Atualizar situação do cliente
                $stmt_upd_cli = $conn->prepare("UPDATE clientes SET situacao = '2' WHERE id = ?");
                $stmt_upd_cli->bind_param("i", $id);
                $stmt_upd_cli->execute();
                $stmt_upd_cli->close();

                // Registrar no histórico de envio
                $stmt_ins_env = $conn->prepare("INSERT INTO envio (comando, telefone, msg, status, usuario_api) VALUES ('MsgTexto', ?, ?, '2', ?)");
                $stmt_ins_env->bind_param("sss", $telefone, $despedida, $usuario_api);
                $stmt_ins_env->execute();
                $stmt_ins_env->close();

                $id_msg = mysqli_insert_id($conn);

                // Enviar a mensagem
                $response = enviarMensagem($servidor, $porta, $usuario_api, $token, $telefone, $despedida, $id_msg);
                
                $log_processamento .= "RESPOSTA DESPEDIDA: " . print_r($response, true) . "\n";
                
                salvando($response);
                
                // Limpar histórico da IA
                $stmt_del_hist = $conn->prepare("DELETE FROM ia_historico WHERE usuario_api = ? AND telefone_usuario = ?");
                $stmt_del_hist->bind_param("ss", $usuario_api, $telefone);
                $stmt_del_hist->execute();
                $stmt_del_hist->close();
            }
        } else {
            $log_processamento .= "Nenhum cliente encontrado para despedida\n";
        }
    }

    // === PROCESSAMENTO DE AGENDAMENTOS ===
    
    // === Buscar tempo de verificação ===
    $stmt_tv = $conn->prepare("SELECT * FROM login WHERE usuario_api = ? AND tipo = '2'");
    $stmt_tv->bind_param("s", $usuario_api);
    $stmt_tv->execute();
    $query = $stmt_tv->get_result();
    $stmt_tv->close();

    $tempo_verificar = '';
    $agenda_verifica_msg = '';
    while ($lista_login = $query->fetch_array()) {
        $tempo_verificar = $lista_login['tempo_verifica'];
        $agenda_verifica_msg = $lista_login['agenda_verfica'];
    }
    
    $log_processamento .= "Tempo de verificação encontrado: '$tempo_verificar' minutos\n";

    if (!empty($tempo_verificar)) {
        
        $log_processamento .= "Buscando agendamentos para lembrete com $tempo_verificar minutos de antecedência\n";
        
        // === Buscar agendamento próximo para enviar enquete ===
        $tempo_verificar_int = (int)$tempo_verificar;
        $stmt_agend = $conn->prepare("SELECT *, TIMESTAMP(data, horario) AS agendamento_completo, TIMESTAMP(data, horario) - INTERVAL ? MINUTE AS lembrete_ajustado FROM agendamento WHERE usuario_api = ? AND TIMESTAMP(data, horario) - INTERVAL ? MINUTE <= ? AND lembrete = '0' ORDER BY agendamento_completo ASC LIMIT 1");
        $stmt_agend->bind_param("isis", $tempo_verificar_int, $usuario_api, $tempo_verificar_int, $data_hora_atual);
        $stmt_agend->execute();
        $query = $stmt_agend->get_result();
        $stmt_agend->close();
        
        if ($query) {
            $log_processamento .= "Consulta de agendamentos executada. Resultados encontrados: " . $query->num_rows . "\n";
        } else {
            $log_processamento .= "ERRO na consulta de agendamentos: " . $conn->error . "\n";
        }

        if ($query && $query->num_rows > 0) {
            while ($row = $query->fetch_assoc()) {
                $id               = $row['id'];
                $telefone         = $row['cliente_telefone'];
                $profissional_nome = $row['profissional_nome'];
                $data_agend       = $row['data'];
                $horario          = $row['horario'];
                $duracao_minutos = $row['duracao_minutos'];
                $valor_servico = $row['valor_servico'];
                $servico_id = $row['servico_id'];
                
                echo "Processando agendamento ID: $id para telefone: $telefone\n";
                $log_processamento .= "AGENDAMENTO: ID $id, Telefone: $telefone, Data: $data_agend, Horário: $horario\n";
            }

            // === Buscar nome do cliente ===
            $stmt_cli2 = $conn->prepare("SELECT * FROM clientes WHERE telefone = ? AND usuario_api = ?");
            $stmt_cli2->bind_param("ss", $telefone, $usuario_api);
            $stmt_cli2->execute();
            $query_busca_clientes = $stmt_cli2->get_result();
            $stmt_cli2->close();
            
            $nome = "Cliente";
            if ($row = $query_busca_clientes->fetch_array()) {
                $nome     = $row['nome'];
                $telefone = $row['telefone'];
            }
            
            $log_processamento .= "Nome do cliente encontrado: '$nome'\n";
                
            $stmt_serv = $conn->prepare("SELECT * FROM servicos WHERE id = ?");
            $stmt_serv->bind_param("i", $servico_id);
            $stmt_serv->execute();
            $query_serv = $stmt_serv->get_result();
            $stmt_serv->close();

            while($rows_usuarios = $query_serv->fetch_array()) {
                $servico = $rows_usuarios['nome'];
            }      
                
            // === Montar mensagem de enquete ===
            $data_formatada = formatar_data_brasileira($data_agend);
            $agendamento = "$horario $data_formatada";
            $profissional = $profissional_nome;
            
            $agenda_verfica2 = novo_texto($agenda_verifica_msg, $nome, $horario, $data_formatada, $profissional, $servico, $valor_servico);
            $agenda_verfica3 = formatarTexto($agenda_verfica2);
            
            $log_processamento .= "Mensagem da enquete montada: '$agenda_verfica3'\n";

            // === Inserir enquete na fila de envio ===
            $stmt_ins_enq = $conn->prepare("INSERT INTO envio (comando, telefone, msg, status, usuario_api) VALUES ('Enquete', ?, ?, '2', ?)");
            $stmt_ins_enq->bind_param("sss", $telefone, $agenda_verfica3, $usuario_api);
            $stmt_ins_enq->execute();
            $stmt_ins_enq->close();

            $id_msg = mysqli_insert_id($conn);
            $opcoes    = ['Sim', 'Não'];

            // === Enviar enquete ===
            $response = EscreverEnquete($servidor, $porta, $usuario_api, $token, $telefone, $agenda_verfica2, $opcoes, $id_msg);

            $log_processamento .= "RESPOSTA ENQUETE: " . print_r($response, true) . "\n";

            // === Atualizar lembrete como enviado ===
            $stmt_upd_lemb = $conn->prepare("UPDATE agendamento SET lembrete = '2' WHERE id = ?");
            $stmt_upd_lemb->bind_param("i", $id);
            $stmt_upd_lemb->execute();
            $stmt_upd_lemb->close();

            echo "Enquete enviada para agendamento ID: $id\n";
            $log_processamento .= "Enquete enviada com sucesso para agendamento ID: $id\n";

        } else {
            echo "Nenhum agendamento encontrado para $usuario_api\n";
            $log_processamento .= "Nenhum agendamento encontrado para este usuário\n";
        }
    } else {
        $log_processamento .= "Tempo de verificação não configurado ou vazio para este usuário\n";
    }
    
    echo "==== USUÁRIO $usuario_api PROCESSADO ====\n\n";
    $log_processamento .= "--- USUÁRIO $usuario_api PROCESSADO COM SUCESSO ---\n\n";
}

// === SALVAR LOG FINAL ===
$log_processamento .= "=== PROCESSAMENTO COMPLETO EM " . date('Y-m-d H:i:s') . " ===\n";
$log_processamento .= "Total de usuários processados: " . count($usuarios_array) . "\n";
$log_processamento .= "================================================\n\n";

file_put_contents('log_processamento.txt', $log_processamento, FILE_APPEND | LOCK_EX);

echo "==== PROCESSAMENTO COMPLETO ====\n";
echo "Total de usuários processados: " . count($usuarios_array) . "\n";

?>















<?php
include '../conn.php';

// Define o fuso horário para o de Brasília para garantir que as comparações de hora estejam corretas
date_default_timezone_set('America/Sao_Paulo');

// === SISTEMA DE LOG ===
$log_file = '../logs/mensagens_massa_' . date('Y-m-d') . '.txt';
$log_dir = dirname($log_file);

// Criar diretório de logs se não existir
if (!is_dir($log_dir)) {
    mkdir($log_dir, 0755, true);
}

function escreverLog($mensagem) {
    global $log_file;
    $timestamp = date('Y-m-d H:i:s');
    $linha_log = "[$timestamp] $mensagem\n";
    file_put_contents($log_file, $linha_log, FILE_APPEND | LOCK_EX);
    echo $linha_log; // Também exibe no console
}

escreverLog("=== INICIANDO PROCESSAMENTO DE MENSAGENS EM MASSA ===");

// === MODO DEBUG/TESTE - Mude para false em produção ===
$FORCAR_ENVIO_TESTE = false; 
escreverLog("MODO TESTE: " . ($FORCAR_ENVIO_TESTE ? "ATIVADO - Ignorando restrições de horário" : "DESATIVADO - Respeitando horários"));

// === Lê o JSON enviado via POST ===
$inputJSON = file_get_contents("php://input");
escreverLog("JSON recebido: " . $inputJSON);

$data = json_decode($inputJSON, true);

// === Validação do JSON ===
if (!isset($data['usuarios']) || !is_array($data['usuarios']) || empty($data['usuarios'])) {
    escreverLog("ERRO: JSON inválido ou sem usuários. Finalizando.");
    http_response_code(400);
    echo "Erro: JSON inválido ou sem usuários.";
    exit;
}

$usuarios_array = $data['usuarios'];
escreverLog("Usuários para processar: " . implode(', ', $usuarios_array));

// === Buscar configuração geral ===
escreverLog("Buscando configurações do sistema...");
$config_query = mysqli_query($conn, "SELECT * FROM config LIMIT 1");

if (!$config_query) {
    escreverLog("ERRO MySQL na busca de config: " . mysqli_error($conn));
    exit;
}

$config = mysqli_fetch_assoc($config_query);

if (!$config) {
    escreverLog("ERRO: Nenhuma configuração encontrada na tabela config");
    exit;
}








 $sql_config = "SELECT * FROM config";
    $query_config = mysqli_query($conn, $sql_config);
    $total_config = mysqli_num_rows($query_config);

    while($rows_config = mysqli_fetch_array($query_config)) {
        $servidor  = $rows_config['ip_vps'];
        $porta  = $rows_config['porta'];
        $nova_porta  = $rows_config['nova_porta'];
        $token  = $rows_config['chave'];
        $chave_painel  = $rows_config['chave_painel'];
        $webhook  = $rows_config['webhook'];
        $google  = $rows_config['google'];
        $link_pagamento  = $rows_config['link_pagamento'];
    } 


















$servidor = $config['ip_vps'];
$porta    = $config['porta'];
$token    = $config['chave'];
// Define o webhook a partir da config, com um fallback
#$webhook = isset($config['webhook_url']) ? $config['webhook_url'] : 'https://seudominio.com';

escreverLog("Configurações carregadas - Servidor: $servidor, Porta: $porta");

function barrass($texto) {
    return preg_replace('#/+#', '/', $texto);
}

// === Funções de verificação (mantidas como no seu original) ===
function dentroHorarioPermitido($start_time, $end_time, $id_campanha = '') {
    $agora = date('H:i:s');
    $permitido = ($agora >= $start_time && $agora <= $end_time);
    escreverLog("HORÁRIO CAMPANHA $id_campanha: Agora=$agora | Permitido=$start_time-$end_time | Resultado=" . ($permitido ? 'PERMITIDO' : 'BLOQUEADO'));
    return $permitido;
}

function diaPermitido($days_week, $id_campanha = '') {
    if (!$days_week || trim($days_week) == '') {
        escreverLog("DIAS: Sem restrição de dias para campanha $id_campanha - PERMITINDO");
        return true;
    }
    $hoje = date('w');
    $dias_permitidos = explode(',', $days_week);
    $permitido = in_array($hoje, array_map('trim', $dias_permitidos));
    escreverLog("DIAS CAMPANHA $id_campanha: Hoje=$hoje | Permitidos=$days_week | Resultado=" . ($permitido ? 'PERMITIDO' : 'BLOQUEADO'));
    return $permitido;
}

// Itera sobre cada usuário API recebido no JSON
foreach ($usuarios_array as $usuario_api) {
    escreverLog("=== PROCESSANDO USUÁRIO API: $usuario_api ===");

    // Parâmetros de data/hora atuais para a consulta
    $agora = date('Y-m-d H:i:s');
    $dia_da_semana = date('w');

    // === CONSULTA PRINCIPAL CORRIGIDA: Busca campanhas prontas para envio AGORA ===
    $sql_campanhas = "
        SELECT *
        FROM mensagens_massa
        WHERE
            usuario_api = ? AND
            (status = 'pendente' OR status = 'processando')  AND
            proximo_envio <= ? AND
            CAST(? AS TIME) BETWEEN start_time AND end_time AND
            FIND_IN_SET(?, days_week) > 0
        ORDER BY proximo_envio ASC";
    
    $stmt_campanhas = $conn->prepare($sql_campanhas);
    if (!$stmt_campanhas) {
        escreverLog("ERRO ao preparar query de campanhas: " . mysqli_error($conn));
        continue;
    }
    
    $stmt_campanhas->bind_param("sssi", $usuario_api, $agora, $agora, $dia_da_semana);
    $stmt_campanhas->execute();
    $result_campanhas = $stmt_campanhas->get_result();
    
    $total_campanhas = $result_campanhas->num_rows;
    escreverLog("Encontradas $total_campanhas campanhas ativas para processar neste momento.");

    if ($total_campanhas == 0) {
        continue;
    }

    while ($campanha = $result_campanhas->fetch_assoc()) {
        $id_campanha = $campanha['id'];
        $tipo = $campanha['media_type'];
        $mensagem = $campanha['message_text'];
        $repeat_option = $campanha['repeat_option'];
        $interval_seconds = (int)$campanha['interval_seconds'];
        
        escreverLog("--- Processando Campanha ID: $id_campanha ($tipo) ---");

        if ($FORCAR_ENVIO_TESTE) {
            escreverLog("MODO TESTE: Ignorando restrições de horário/dia para campanha $id_campanha");
        }

        // Buscar próximo contato pendente desta campanha
        $sql_contato = "SELECT * FROM mensagens_massa_envios 
                        WHERE mensagem_massa_id = ? AND status = 'pendente'
                        ORDER BY id ASC";
        
        $stmt_contato = mysqli_prepare($conn, $sql_contato);
        mysqli_stmt_bind_param($stmt_contato, 'i', $id_campanha);
        mysqli_stmt_execute($stmt_contato);
        $result_contato = mysqli_stmt_get_result($stmt_contato);

        if ($contato = mysqli_fetch_assoc($result_contato)) {
            $envio_id = $contato['id'];
            $nome = $contato['cliente_nome'];
            $telefone = $contato['cliente_telefone'];

            escreverLog("CONTATO ENCONTRADO: ID=$envio_id, Nome=$nome, Telefone=$telefone");
            
            // Personalizar mensagem
    $data_atual = date('d/m/Y'); // Pega a data atual no formato DD/MM/AAAA

// Array de placeholders e seus valores
$substituicoes = [
    '{nome}'     => $nome,
    '{telefone}' => $telefone,
    '{data}'     => $data_atual,
];

// Faz todas as trocas de uma vez
$mensagem_final = strtr($mensagem, $substituicoes);            
            
            $response = null;
            $sucesso = false;

            try {
                // ==================================================================
                // === AJUSTE: CHAMADA REAL DAS SUAS FUNÇÕES DE ENVIO ================
                // ==================================================================
                if ($tipo == 'text') {
                    escreverLog("ENVIANDO TEXTO para $telefone");
                    $response = enviarMensagem($servidor, $porta, $usuario_api, $token, $telefone, $mensagem_final, $id_campanha);
                } else {
                    escreverLog("ENVIANDO MÍDIA ($tipo) para $telefone");
                    $url = $campanha['media_file_path'];
                    $url_completa = $webhook . '/login/painel/' . $url;
                    $url_final = barrass($url_completa);
                    $legenda = $mensagem_final;
                    
                    escreverLog("URL da mídia: $url_final");
                    $response = enviarMidiaDeUrl($servidor, $porta, $usuario_api, $token, $telefone, $url_final, $tipo, $legenda);
                }
                // ==================================================================
                // === FIM DO AJUSTE ================================================
                // ==================================================================

                escreverLog("Resposta da API de envio: " . json_encode($response));

                if (isset($response['status']) && $response['status'] == 'error') {
                    throw new Exception(isset($response['message']) ? $response['message'] : 'Erro desconhecido da API');
                }
                $sucesso = true;

            } catch (Exception $e) {
                escreverLog("ERRO no processo de envio: " . $e->getMessage());
                $response = ['error' => $e->getMessage()];
            }

            // ATUALIZAÇÃO NO BANCO DE DADOS (Lógica original mantida)
            if ($sucesso) {
                // Atualiza o envio para 'enviado'
                $valor_enviado = 'enviado';
               # if($repeat_option != 'once'){
                 #   $valor_enviado = 'pendente';
              #  }
                
                
                $stmt_update_envio = $conn->prepare("UPDATE mensagens_massa_envios SET status = ?, enviado_em = NOW(), response_api = ? WHERE id = ?");
                $response_json = json_encode($response);
                $stmt_update_envio->bind_param('ssi', $valor_enviado, $response_json, $envio_id);
                $stmt_update_envio->execute();
                $stmt_update_envio->close();
                
                // Atualiza a campanha com o próximo envio agendado
                $proximo_envio = date('Y-m-d H:i:s', time() + $interval_seconds);
                $stmt_update_campanha = mysqli_prepare($conn, "UPDATE mensagens_massa SET enviados = enviados + 1, ultimo_envio = NOW(), proximo_envio = ? WHERE id = ?");
                mysqli_stmt_bind_param($stmt_update_campanha, 'si', $proximo_envio, $id_campanha);
                mysqli_stmt_execute($stmt_update_campanha);
                escreverLog("SUCESSO: Envio $envio_id marcado como 'enviado'. Próximo envio para campanha $id_campanha agendado para $proximo_envio.");
            } else {
                // Lógica de erro
                escreverLog("ERRO: Falha no envio para o registro $envio_id.");
                // ... (seu código de tratamento de erro e tentativas pode ser inserido aqui) ...
            }
        } else {
    // VERIFICAR se ainda há contatos pendentes da campanha
    $stmt_check = mysqli_prepare($conn, "SELECT COUNT(*) as pendentes FROM mensagens_massa_envios WHERE mensagem_massa_id = ? AND status = 'pendente'");
    mysqli_stmt_bind_param($stmt_check, 'i', $id_campanha);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);
    $pendentes = mysqli_fetch_assoc($result_check)['pendentes'] ?? 0;

    if ($pendentes > 0) {
        escreverLog("Ainda existem $pendentes contato(s) pendente(s) para a campanha $id_campanha. Aguardando ciclo atual.");
    } else {
        $repeat_type = $repeat_option;

        if (in_array($repeat_type, ['daily', 'weekly', 'monthly'])) {
            escreverLog("CAMPANHA RECORRENTE ($repeat_type): $id_campanha sem pendentes. Iniciando ciclo seguinte...");

            // RESETAR
            $stmt_reset_envios = mysqli_prepare($conn, "UPDATE mensagens_massa_envios SET status = 'pendente', enviado_em = NULL WHERE mensagem_massa_id = ?");
            mysqli_stmt_bind_param($stmt_reset_envios, 'i', $id_campanha);
            mysqli_stmt_execute($stmt_reset_envios);

            escreverLog("Contatos resetados.");
$hora_envio_real = date('H:i:s', strtotime($data_base));

// Se o horário do último envio estiver dentro do intervalo permitido, mantemos
if ($hora_envio_real >= $campanha['start_time'] && $hora_envio_real <= $campanha['end_time']) {
    $hora_base = $hora_envio_real;
} else {
    // Se estiver fora, usamos o horário de início da campanha
    $hora_base = $campanha['start_time'];
}





            // CALCULAR PRÓXIMA DATA
            $data_base = $campanha['ultimo_envio'] ?? date('Y-m-d H:i:s');
            $hora_base = $campanha['start_time'] ?? '09:00:00';
            $dias_permitidos = array_map('intval', explode(',', $campanha['days_week'] ?? '0,1,2,3,4,5,6'));

            for ($i = 1; $i <= 30; $i++) {
                if ($repeat_type === 'daily') {
                    $nova_data = strtotime("+$i day", strtotime($data_base));
                } elseif ($repeat_type === 'weekly') {
                    $nova_data = strtotime("+$i week", strtotime($data_base));
                } elseif ($repeat_type === 'monthly') {
                    $nova_data = strtotime("+$i month", strtotime($data_base));
                }

                $dia_semana = (int)date('w', $nova_data);
                if (in_array($dia_semana, $dias_permitidos)) {
                    $proximo_envio = date('Y-m-d', $nova_data) . ' ' . $hora_base;
                    break;
                }
            }

            if (!empty($proximo_envio)) {
                $stmt_update_proximo = mysqli_prepare($conn, "UPDATE mensagens_massa SET proximo_envio = ?, ultimo_envio = NOW() WHERE id = ?");
                mysqli_stmt_bind_param($stmt_update_proximo, 'si', $proximo_envio, $id_campanha);
                mysqli_stmt_execute($stmt_update_proximo);
                escreverLog("Novo ciclo agendado para: $proximo_envio");
            } else {
                escreverLog("ERRO: Não foi possível calcular o próximo envio.");
            }

        } else {
            escreverLog("Campanha $id_campanha não é recorrente. Marcando como concluída.");
            $stmt_finalizar = mysqli_prepare($conn, "UPDATE mensagens_massa SET status = 'concluida' WHERE id = ?");
            mysqli_stmt_bind_param($stmt_finalizar, 'i', $id_campanha);
            mysqli_stmt_execute($stmt_finalizar);
        }
    }
}
        escreverLog("--- Fim da Campanha ID: $id_campanha ---");
    }
    escreverLog("=== FINALIZADO USUÁRIO: $usuario_api ===");
}

escreverLog("=== PROCESSAMENTO COMPLETO ===");
#$conn->close();
?>

<?php
/*
include '../conn.php';
#include '../funcoes.php'; // onde está a função enviarMensagem()
#include 'editacodigo.php';

date_default_timezone_set('America/Sao_Paulo');

// === Lê o JSON enviado via POST ===
$inputJSON = file_get_contents("php://input");
$data = json_decode($inputJSON, true);

// === Verifica se o JSON está válido e contém usuários ===
if (!isset($data['usuarios']) || !is_array($data['usuarios']) || empty($data['usuarios'])) {
    http_response_code(400);
    echo "Erro: JSON inválido ou sem usuários.";
    exit;
}

$usuarios_array = $data['usuarios'];

// === Carregar configurações ===
$sql_config = "SELECT * FROM config LIMIT 1";
$query_config = mysqli_query($conn, $sql_config);
$config = mysqli_fetch_assoc($query_config);

$servidor = $config['ip_vps'];
$porta    = $config['porta'];
$token    = $config['chave'];

// === PROCESSA CADA USUÁRIO INDIVIDUALMENTE ===
foreach ($usuarios_array as $usuario_api) {
    echo "\n==== PROCESSANDO USUÁRIO: $usuario_api ====\n";

    // === Buscar a campanha mais recente do usuário ===
    $sql = "
        SELECT *
        FROM mensagens_massa
        WHERE usuario_api = ?
        ORDER BY id DESC
        LIMIT 1
    ";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 's', $usuario_api);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($campanha = mysqli_fetch_assoc($result)) {
        $id_campanha    = $campanha['id'];
        $mensagem_texto = $campanha['message_text'];
        $clientes_ids   = json_decode($campanha['clientes_ids'], true);
        $enviados       = (int)$campanha['enviados'];
        $total_clientes = (int)$campanha['total_clientes'];

        echo "🧪 Campanha encontrada: ID $id_campanha\n";

        // Verifica se ainda há cliente a ser atendido
        if ($enviados >= $total_clientes) {
            echo "✅ Campanha já finalizada para $usuario_api.\n";
            continue;
        }

        // Obter próximo cliente
        $proximo_cliente_id = $clientes_ids[$enviados];
        $sql_cliente = "SELECT nome, telefone FROM clientes WHERE id = ? AND usuario_api = ?";
        $stmt_cliente = mysqli_prepare($conn, $sql_cliente);
        mysqli_stmt_bind_param($stmt_cliente, 'is', $proximo_cliente_id, $usuario_api);
        mysqli_stmt_execute($stmt_cliente);
        $res_cliente = mysqli_stmt_get_result($stmt_cliente);
        $cliente = mysqli_fetch_assoc($res_cliente);

        if (!$cliente) {
            echo "❌ Cliente ID $proximo_cliente_id não encontrado para $usuario_api.\n";
            continue;
        }

        // Personalizar mensagem
        $mensagem_final = str_replace('{nome}', $cliente['nome'], $mensagem_texto);
        $telefone_cliente = $cliente['telefone'];

        // Enviar mensagem
        $response = enviarMensagem($servidor, $porta, $usuario_api, $token, $telefone_cliente, $mensagem_final, $id_campanha);

        if (isset($response['status']) && $response['status'] === 'error') {
            echo "❌ Falha no envio para $telefone_cliente: " . json_encode($response) . "\n";
            // Atualiza erros
            $sql_erro = "UPDATE mensagens_massa SET erros = erros + 1, log_erros = CONCAT(IFNULL(log_erros, ''), ?) WHERE id = ?";
            $log_erro_msg = "Erro ao enviar para $telefone_cliente: " . json_encode($response) . "\n";
            $stmt_erro = mysqli_prepare($conn, $sql_erro);
            mysqli_stmt_bind_param($stmt_erro, 'si', $log_erro_msg, $id_campanha);
            mysqli_stmt_execute($stmt_erro);
        } else {
            echo "✅ Mensagem enviada para {$cliente['nome']} ($telefone_cliente)\n";
            // Atualiza campanha
            $novo_enviados = $enviados + 1;
            $proximo_envio = date('Y-m-d H:i:s', time() + (int)$campanha['interval_seconds']);
            $sql_update = "UPDATE mensagens_massa SET enviados = ?, ultimo_envio = NOW(), proximo_envio = ? WHERE id = ?";
            $stmt_upd = mysqli_prepare($conn, $sql_update);
            mysqli_stmt_bind_param($stmt_upd, 'isi', $novo_enviados, $proximo_envio, $id_campanha);
            mysqli_stmt_execute($stmt_upd);
        }

    } else {
        echo "❌ Nenhuma campanha encontrada para $usuario_api.\n";
    }

    echo "==== FINALIZADO USUÁRIO: $usuario_api ====\n";
}

echo "\n==== PROCESSAMENTO COMPLETO ====\n";

*/
?>



