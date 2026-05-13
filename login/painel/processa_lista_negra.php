<?php
session_start();

include 'conn.php';
#include 'funcoes.php';
#salvar_dados_resquest();
// Verificar conexão
if (!$conn) {
    die("Erro na conexão: " . mysqli_connect_error());
}

// Configurar charset
mysqli_set_charset($conn, "utf8");

// Função para sanitizar dados
function sanitizar($data) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($data));
}

// Função para validar telefone
function validarTelefone($telefone) {
    $telefone_limpo = preg_replace('/[^0-9]/', '', $telefone);
    return strlen($telefone_limpo) >= 10 && strlen($telefone_limpo) <= 15;
}

// Função para obter IP do usuário
function obterIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

// Verificar se o método é POST
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: lista_negra.php?erro=metodo_invalido");
    exit;
}

// Obter ação
$acao = isset($_POST['acao']) ? sanitizar($_POST['acao']) : '';
$usuario_api = isset($_POST['usuario_api']) ? sanitizar($_POST['usuario_api']) : '';

// Validar usuário API
if (empty($usuario_api)) {
    header("Location: lista_negra.php?erro=usuario_invalido");
    exit;
}

// =============================================
// PROCESSAR AÇÕES
// =============================================

switch ($acao) {
    
    // ==========================================
    // ADICIONAR CONTATO À LISTA NEGRA
    // ==========================================
    case 'adicionar':
        try {
            // Obter dados do formulário
            $nome = sanitizar($_POST['nome']);
            $telefone = sanitizar($_POST['telefone']);
            $motivo_bloqueio = sanitizar($_POST['motivo_bloqueio']);
            $observacoes = isset($_POST['observacoes']) ? sanitizar($_POST['observacoes']) : '';
            $ip_origem = obterIP();
            
            // Validações
            if (empty($nome)) {
                throw new Exception("Nome é obrigatório");
            }
            
            if (empty($telefone)) {
                throw new Exception("Telefone é obrigatório");
            }
            
            if (!validarTelefone($telefone)) {
                throw new Exception("Telefone inválido");
            }
            
            if (empty($motivo_bloqueio)) {
                throw new Exception("Motivo do bloqueio é obrigatório");
            }
            
            // Limpar telefone (apenas números)
            $telefone_limpo = preg_replace('/[^0-9]/', '', $telefone);
            
            // Verificar se já existe bloqueio ativo para este telefone e usuário
            $sql_verificar = "SELECT id FROM lista_negra 
                             WHERE telefone = ? AND usuario_api = ? AND status = 'ativo'";
            $stmt_verificar = mysqli_prepare($conn, $sql_verificar);
            mysqli_stmt_bind_param($stmt_verificar, "ss", $telefone_limpo, $usuario_api);
            mysqli_stmt_execute($stmt_verificar);
            $resultado_verificar = mysqli_stmt_get_result($stmt_verificar);
            
            if (mysqli_num_rows($resultado_verificar) > 0) {
                throw new Exception("Este telefone já está bloqueado para este usuário");
            }
            
            // Inserir novo bloqueio
            $sql_inserir = "INSERT INTO lista_negra 
                           (nome, telefone, usuario_api, motivo_bloqueio, observacoes, ip_origem, status) 
                           VALUES (?, ?, ?, ?, ?, ?, 'ativo')";
            
            $stmt_inserir = mysqli_prepare($conn, $sql_inserir);
            mysqli_stmt_bind_param($stmt_inserir, "ssssss", 
                $nome, $telefone_limpo, $usuario_api, $motivo_bloqueio, $observacoes, $ip_origem);
            
            if (mysqli_stmt_execute($stmt_inserir)) {
                $id_inserido = mysqli_insert_id($conn);
                
                // Log da operação
                error_log("Lista Negra - Contato adicionado: ID $id_inserido, Usuario: $usuario_api, Telefone: $telefone_limpo");
                
                header("Location: lista_negra.php?sucesso=contato_bloqueado&nome=" . urlencode($nome));
            } else {
                throw new Exception("Erro ao inserir contato na lista negra: " . mysqli_error($conn));
            }
            
        } catch (Exception $e) {
            header("Location: lista_negra.php?erro=" . urlencode($e->getMessage()));
        }
        break;
    
    // ==========================================
    // EDITAR CONTATO DA LISTA NEGRA
    // ==========================================
    case 'editar':
        try {
            // Obter dados do formulário
            $id_contato = (int)$_POST['id_contato'];
            $nome = sanitizar($_POST['nome']);
            $telefone = sanitizar($_POST['telefone']);
            $motivo_bloqueio = sanitizar($_POST['motivo_bloqueio']);
            $observacoes = isset($_POST['observacoes']) ? sanitizar($_POST['observacoes']) : '';
            
            // Validações
            if ($id_contato <= 0) {
                throw new Exception("ID do contato inválido");
            }
            
            if (empty($nome)) {
                throw new Exception("Nome é obrigatório");
            }
            
            if (empty($telefone)) {
                throw new Exception("Telefone é obrigatório");
            }
            
            if (!validarTelefone($telefone)) {
                throw new Exception("Telefone inválido");
            }
            
            if (empty($motivo_bloqueio)) {
                throw new Exception("Motivo do bloqueio é obrigatório");
            }
            
            // Limpar telefone (apenas números)
            $telefone_limpo = preg_replace('/[^0-9]/', '', $telefone);
            
            // Verificar se o contato existe e pertence ao usuário
            $sql_verificar = "SELECT id FROM lista_negra 
                             WHERE id = ? AND usuario_api = ?";
            $stmt_verificar = mysqli_prepare($conn, $sql_verificar);
            mysqli_stmt_bind_param($stmt_verificar, "is", $id_contato, $usuario_api);
            mysqli_stmt_execute($stmt_verificar);
            $resultado_verificar = mysqli_stmt_get_result($stmt_verificar);
            
            if (mysqli_num_rows($resultado_verificar) == 0) {
                throw new Exception("Contato não encontrado ou sem permissão para editar");
            }
            
            // Verificar se não existe outro contato com o mesmo telefone (exceto o atual)
            $sql_verificar_telefone = "SELECT id FROM lista_negra 
                                      WHERE telefone = ? AND usuario_api = ? AND id != ? AND status = 'ativo'";
            $stmt_verificar_telefone = mysqli_prepare($conn, $sql_verificar_telefone);
            mysqli_stmt_bind_param($stmt_verificar_telefone, "ssi", $telefone_limpo, $usuario_api, $id_contato);
            mysqli_stmt_execute($stmt_verificar_telefone);
            $resultado_verificar_telefone = mysqli_stmt_get_result($stmt_verificar_telefone);
            
            if (mysqli_num_rows($resultado_verificar_telefone) > 0) {
                throw new Exception("Já existe outro contato bloqueado com este telefone");
            }
            
            // Atualizar contato
            $sql_atualizar = "UPDATE lista_negra 
                             SET nome = ?, telefone = ?, motivo_bloqueio = ?, observacoes = ?, data_atualizacao = NOW()
                             WHERE id = ? AND usuario_api = ?";
            
            $stmt_atualizar = mysqli_prepare($conn, $sql_atualizar);
            mysqli_stmt_bind_param($stmt_atualizar, "ssssís", 
                $nome, $telefone_limpo, $motivo_bloqueio, $observacoes, $id_contato, $usuario_api);
            
            if (mysqli_stmt_execute($stmt_atualizar)) {
                if (mysqli_affected_rows($conn) > 0) {
                    // Log da operação
                    error_log("Lista Negra - Contato editado: ID $id_contato, Usuario: $usuario_api");
                    
                    header("Location: lista_negra.php?sucesso=contato_atualizado&nome=" . urlencode($nome));
                } else {
                    throw new Exception("Nenhuma alteração foi feita");
                }
            } else {
                throw new Exception("Erro ao atualizar contato: " . mysqli_error($conn));
            }
            
        } catch (Exception $e) {
            header("Location: lista_negra.php?erro=" . urlencode($e->getMessage()));
        }
        break;
    
    // ==========================================
    // EXCLUIR/REMOVER CONTATO DA LISTA NEGRA
    // ==========================================
    case 'excluir':
        try {
            // Obter dados do formulário
            $id_contato = (int)$_POST['id_contato'];
            
            // Validações
            if ($id_contato <= 0) {
                throw new Exception("ID do contato inválido");
            }
            
            // Verificar se o contato existe e pertence ao usuário
            $sql_verificar = "SELECT nome FROM lista_negra 
                             WHERE id = ? AND usuario_api = ?";
            $stmt_verificar = mysqli_prepare($conn, $sql_verificar);
            mysqli_stmt_bind_param($stmt_verificar, "is", $id_contato, $usuario_api);
            mysqli_stmt_execute($stmt_verificar);
            $resultado_verificar = mysqli_stmt_get_result($stmt_verificar);
            
            if (mysqli_num_rows($resultado_verificar) == 0) {
                throw new Exception("Contato não encontrado ou sem permissão para excluir");
            }
            
            $dados_contato = mysqli_fetch_assoc($resultado_verificar);
            $nome_contato = $dados_contato['nome'];
            
            // Opção 1: Exclusão definitiva (descomente se preferir)
            $sql_excluir = "DELETE FROM lista_negra WHERE id = ? AND usuario_api = ?";
            
            // Opção 2: Exclusão lógica (recomendado - mantém histórico)
            //$sql_excluir = "UPDATE lista_negra 
             //              SET status = 'inativo', data_atualizacao = NOW() 
              //             WHERE id = ? AND usuario_api = ?";
            
            $stmt_excluir = mysqli_prepare($conn, $sql_excluir);
            mysqli_stmt_bind_param($stmt_excluir, "is", $id_contato, $usuario_api);
            
            if (mysqli_stmt_execute($stmt_excluir)) {
                if (mysqli_affected_rows($conn) > 0) {
                    // Log da operação
                    error_log("Lista Negra - Contato removido: ID $id_contato, Usuario: $usuario_api, Nome: $nome_contato");
                    
                    header("Location: lista_negra.php?sucesso=contato_removido&nome=" . urlencode($nome_contato));
                } else {
                    throw new Exception("Contato não foi removido");
                }
            } else {
                throw new Exception("Erro ao remover contato: " . mysqli_error($conn));
            }
            
        } catch (Exception $e) {
            header("Location: lista_negra.php?erro=" . urlencode($e->getMessage()));
        }
        break;
    
    // ==========================================
    // REGISTRAR TENTATIVA DE CONTATO
    // ==========================================
    case 'registrar_tentativa':
        try {
            $telefone = sanitizar($_POST['telefone']);
            
            if (empty($telefone)) {
                throw new Exception("Telefone é obrigatório");
            }
            
            $telefone_limpo = preg_replace('/[^0-9]/', '', $telefone);
            
            // Atualizar contador de tentativas
            $sql_tentativa = "UPDATE lista_negra 
                             SET tentativas_contato = tentativas_contato + 1, 
                                 ultima_tentativa = NOW(),
                                 data_atualizacao = NOW()
                             WHERE telefone = ? AND usuario_api = ? AND status = 'ativo'";
            
            $stmt_tentativa = mysqli_prepare($conn, $sql_tentativa);
            mysqli_stmt_bind_param($stmt_tentativa, "ss", $telefone_limpo, $usuario_api);
            
            if (mysqli_stmt_execute($stmt_tentativa)) {
                $tentativas_atualizadas = mysqli_affected_rows($conn);
                
                // Log da operação
                if ($tentativas_atualizadas > 0) {
                    error_log("Lista Negra - Tentativa registrada: Usuario: $usuario_api, Telefone: $telefone_limpo");
                }
                
                // Retornar JSON para chamadas AJAX
                if (isset($_POST['ajax']) && $_POST['ajax'] == '1') {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'sucesso' => true,
                        'tentativas_atualizadas' => $tentativas_atualizadas
                    ]);
                    exit;
                } else {
                    header("Location: lista_negra.php?sucesso=tentativa_registrada");
                }
            } else {
                throw new Exception("Erro ao registrar tentativa: " . mysqli_error($conn));
            }
            
        } catch (Exception $e) {
            if (isset($_POST['ajax']) && $_POST['ajax'] == '1') {
                header('Content-Type: application/json');
                echo json_encode([
                    'sucesso' => false,
                    'erro' => $e->getMessage()
                ]);
                exit;
            } else {
                header("Location: lista_negra.php?erro=" . urlencode($e->getMessage()));
            }
        }
        break;
    
    // ==========================================
    // REATIVAR CONTATO
    // ==========================================
    case 'reativar':
        try {
            $id_contato = (int)$_POST['id_contato'];
            
            if ($id_contato <= 0) {
                throw new Exception("ID do contato inválido");
            }
            
            // Verificar se o contato existe e está inativo
            $sql_verificar = "SELECT nome FROM lista_negra 
                             WHERE id = ? AND usuario_api = ? AND status = 'inativo'";
            $stmt_verificar = mysqli_prepare($conn, $sql_verificar);
            mysqli_stmt_bind_param($stmt_verificar, "is", $id_contato, $usuario_api);
            mysqli_stmt_execute($stmt_verificar);
            $resultado_verificar = mysqli_stmt_get_result($stmt_verificar);
            
            if (mysqli_num_rows($resultado_verificar) == 0) {
                throw new Exception("Contato não encontrado ou já está ativo");
            }
            
            $dados_contato = mysqli_fetch_assoc($resultado_verificar);
            $nome_contato = $dados_contato['nome'];
            
            // Reativar contato
            $sql_reativar = "UPDATE lista_negra 
                            SET status = 'ativo', data_atualizacao = NOW() 
                            WHERE id = ? AND usuario_api = ?";
            
            $stmt_reativar = mysqli_prepare($conn, $sql_reativar);
            mysqli_stmt_bind_param($stmt_reativar, "is", $id_contato, $usuario_api);
            
            if (mysqli_stmt_execute($stmt_reativar)) {
                error_log("Lista Negra - Contato reativado: ID $id_contato, Usuario: $usuario_api");
                header("Location: lista_negra.php?sucesso=contato_reativado&nome=" . urlencode($nome_contato));
            } else {
                throw new Exception("Erro ao reativar contato: " . mysqli_error($conn));
            }
            
        } catch (Exception $e) {
            header("Location: lista_negra.php?erro=" . urlencode($e->getMessage()));
        }
        break;
    
    // ==========================================
    // AÇÃO INVÁLIDA
    // ==========================================
    default:
        header("Location: lista_negra.php?erro=acao_invalida");
        break;
}

// Fechar conexão
mysqli_close($conn);
exit;

// =============================================
// FUNÇÕES AUXILIARES
// =============================================

/**
 * Função para verificar se um telefone está na lista negra
 * Uso: verificarListaNegra($telefone, $usuario_api)
 */
function verificarListaNegra($telefone, $usuario_api) {
    global $conn;
    
    $telefone_limpo = preg_replace('/[^0-9]/', '', $telefone);
    
    $sql = "SELECT id, nome, motivo_bloqueio FROM lista_negra 
            WHERE telefone = ? AND usuario_api = ? AND status = 'ativo'";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $telefone_limpo, $usuario_api);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($resultado) > 0) {
        return mysqli_fetch_assoc($resultado);
    }
    
    return false;
}

/**
 * Função para obter estatísticas da lista negra
 * Uso: obterEstatisticas($usuario_api)
 */
function obterEstatisticas($usuario_api) {
    global $conn;
    
    $sql = "SELECT 
                COUNT(*) as total_bloqueios,
                COUNT(CASE WHEN status = 'ativo' THEN 1 END) as bloqueios_ativos,
                COUNT(CASE WHEN status = 'inativo' THEN 1 END) as bloqueios_inativos,
                SUM(tentativas_contato) as total_tentativas,
                MAX(data_bloqueio) as ultimo_bloqueio
            FROM lista_negra 
            WHERE usuario_api = ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $usuario_api);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    
    return mysqli_fetch_assoc($resultado);
}

?>

<!-- 
=============================================
EXEMPLOS DE USO EM JAVASCRIPT/AJAX
=============================================

// Registrar tentativa de contato via AJAX
function registrarTentativa(telefone, usuario_api) {
    fetch('processa_lista_negra.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `acao=registrar_tentativa&telefone=${telefone}&usuario_api=${usuario_api}&ajax=1`
    })
    .then(response => response.json())
    .then(data => {
        if (data.sucesso) {
            console.log('Tentativa registrada com sucesso');
        } else {
            console.error('Erro:', data.erro);
        }
    });
}

// Verificar se telefone está na lista negra
function verificarTelefone(telefone, usuario_api) {
    // Esta função precisaria de uma rota específica ou ser implementada 
    // como uma função separada que retorna JSON
}
-->