<?php
session_start();
include 'funcoes.php';

if(!isset($_SESSION['login'])) {
    VaiPara('login.php');
}
$login = $_SESSION['login'];

include 'conn.php';
include 'config_dados.php';
include 'estilo.php';
include 'css_de_icones.php';

if (isset($_GET['pagina_nome'])) {
    $pagina_nome_recebe = $_GET['pagina_nome'];
} else {
    $pagina_nome_recebe = 0;
}

$sql_busca_usuario = "SELECT * FROM login WHERE login = '$login'";
$query_busca_usuario = mysqli_query($conn, $sql_busca_usuario);
$total_busca_usuario = mysqli_num_rows($query_busca_usuario);

while($rows_usuarios = mysqli_fetch_array($query_busca_usuario)) {
    $nome         = Priletra($rows_usuarios['nome']);
    $img_perfil   = $rows_usuarios['perfil_img'];
    $autorizado   = $rows_usuarios['autorizado'];
    $tipo         = $rows_usuarios['tipo'];
    $funcao       = $rows_usuarios['funcao'];
    $usuario_api  = $rows_usuarios['$usuario_api'];
    $plano        = $rows_usuarios['plano'];
    $modo_atuante = $rows_usuarios['modo_atuante'];
}

include 'menu.php';

if($total_busca_usuario != 1) {
    VaiPara('login.php');
}
if($autorizado != 2) {
    VaiPara('desbloquar.php');
}

include 'header.php';

?>

<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['acao_robo'])) {
        $acao = $_POST['acao_robo'];
        $status_robo = ($acao == 'ativar') ? 'IA' : 'desativado';
        $login = $_SESSION['login'];
        $sql_atualizar_status = "UPDATE login SET funcao = '$status_robo' WHERE login = '$login' ";
        $resultado_status = mysqli_query($conn, $sql_atualizar_status);

        $login2 = 'agenda_'.$login;
        $sql_atualizar_status = "UPDATE clientes SET time_resposta = '' WHERE usuario_api = '$login2'";
        $resultado_status = mysqli_query($conn, $sql_atualizar_status);

        if ($resultado_status) {
            if ($acao == 'ativar') {
                echo '<div class="alert alert-success" role="alert">
                        <i class="feather icon-check-circle"></i> Robô de notificações ativado! Agora respondendo automaticamente.
                      </div>';
            } else {
                echo '<div class="alert alert-warning" role="alert">
                        <i class="feather icon-alert-triangle"></i> Robô pausado! Não está mais respondendo notificações.
                      </div>';
            }
        } else {
            echo '<div class="alert alert-danger" role="alert">
                    <i class="feather icon-x-circle"></i> Erro ao atualizar status do robô de notificações.
                  </div>';
        }
    }

    if (isset($_POST['modulo_selecionado']) && !empty($_POST['modulo_selecionado'])) {
        $id_modulo = (int) $_POST['modulo_selecionado'];

        $sql_busca_modulo = "
            SELECT nome_modulo 
            FROM planos_clientes 
            WHERE id = $id_modulo 
            AND tipo = '1' 
            AND nome_plano = '$plano'
            LIMIT 1
        ";
        $res_modulo = mysqli_query($conn, $sql_busca_modulo);

        if ($res_modulo && mysqli_num_rows($res_modulo) > 0) {
            $modulo_info = mysqli_fetch_assoc($res_modulo);
            $nome_modulo = $modulo_info['nome_modulo'];

            $sql_verifica_usuario = "SELECT id FROM funcao WHERE login = '$login' LIMIT 1";
            $res_verifica_usuario = mysqli_query($conn, $sql_verifica_usuario);

            if ($res_verifica_usuario && mysqli_num_rows($res_verifica_usuario) > 0) {
                $sql_atualizar_funcao = "
                    UPDATE login 
                    SET modo_atuante = '$nome_modulo'
                    WHERE login = '$login'
                ";
                $resultado_funcao = mysqli_query($conn, $sql_atualizar_funcao);
                $acao_realizada = "atualizada";
            } else {
                $sql_inserir_funcao = "
                    INSERT INTO funcao (funcao, id_funcao, login)
                    VALUES ('$nome_modulo', $id_modulo, '$login')
                ";
                $resultado_funcao = mysqli_query($conn, $sql_inserir_funcao);
                $acao_realizada = "inserida";
            }

            if ($resultado_funcao) {
                VaiPara('modo.php');
                echo '<div class="alert alert-success" role="alert">
                        <i class="feather icon-check-circle"></i> Função ' . $acao_realizada . ' com sucesso! Tipo: <strong>' . htmlspecialchars($nome_modulo, ENT_QUOTES) . '</strong>
                      </div>';
            } else {
                echo '<div class="alert alert-danger" role="alert">
                        <i class="feather icon-x-circle"></i> Erro ao processar função: ' . mysqli_error($conn) . '
                      </div>';
            }
        } else {
            echo '<div class="alert alert-danger" role="alert">
                    <i class="feather icon-x-circle"></i> Módulo não encontrado no plano selecionado.
                  </div>';
        }
    }

    if (isset($_POST['modoRobo']) && !empty($_POST['modoRobo'])) {
        $modoRobo = $_POST['modoRobo'];

        $sql_atualizar_modo = "UPDATE login SET funcao = '$modoRobo' WHERE login = '$login'";
        $resultado_modo = mysqli_query($conn, $sql_atualizar_modo);

        if ($resultado_modo) {
            echo '<div class="alert alert-success" role="alert">
                    <i class="feather icon-check-circle"></i> Modo do robô atualizado com sucesso!
                  </div>';
        } else {
            echo '<div class="alert alert-danger" role="alert">
                    <i class="feather icon-x-circle"></i> Erro ao atualizar o modo do robô.
                  </div>';
        }
    }
}

$sql_busca_usuario = "SELECT * FROM login WHERE login = '$login'";
$query_busca_usuario = mysqli_query($conn, $sql_busca_usuario);
$funcao_atual = 'desativado';
$status_robo = false;

if ($query_busca_usuario && mysqli_num_rows($query_busca_usuario) > 0) {
    $rows_usuarios = mysqli_fetch_array($query_busca_usuario);
    $nome = $rows_usuarios['nome'];
    $funcao_atual = $rows_usuarios['funcao'];
    $status_robo = ($funcao_atual == 'IA');
}

$sql_funcao_atual = "SELECT funcao, id_funcao FROM funcao WHERE login = '$login' LIMIT 1";
$res_funcao_atual = mysqli_query($conn, $sql_funcao_atual);
$funcao_ativa = null;
$id_funcao_ativa = null;

if ($res_funcao_atual && mysqli_num_rows($res_funcao_atual) > 0) {
    $funcao_data = mysqli_fetch_assoc($res_funcao_atual);
    $funcao_ativa = $funcao_data['funcao'];
    $id_funcao_ativa = $funcao_data['id_funcao'];
}
?>

<!-- INFORMAÇÃO DO PLANO ATUAL -->
<div class="alert alert-info" role="alert">
    <i class="feather icon-info"></i> <strong>Plano Ativo:</strong> <?php echo strtoupper($plano); ?> | <strong>Usuário:</strong> <?php echo htmlspecialchars($login, ENT_QUOTES); ?>
</div>

<!-- STATUS ATUAL DO ROBÔ -->
<div class="card">
    <div class="card-header">
        <h5>Status do Robô de Notificações</h5>
        <div class="card-header-right">
            <ul class="list-unstyled card-option">
                <li><i class="feather icon-maximize full-card"></i></li>
                <li><i class="feather icon-minus minimize-card"></i></li>
                <li><i class="feather icon-refresh-cw reload-card"></i></li>
            </ul>
        </div>
    </div>
    <div class="card-block">
        <div class="row">
            <div class="col-md-6">
                <div class="d-flex align-items-center">
                    <?php if ($status_robo): ?>
                        <div class="status-indicator bg-success"></div>
                        <span class="ml-2"><strong>Status:</strong> Respondendo Notificações</span>
                    <?php else: ?>
                        <div class="status-indicator bg-danger"></div>
                        <span class="ml-2"><strong>Status:</strong> Não Respondendo</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-6">
                <?php if ($funcao_ativa): ?>
                    <span><strong>Função Ativa:</strong> <?php echo htmlspecialchars($modo_atuante, ENT_QUOTES); ?></span>
                <?php else: ?>
                    <span class="text-muted">Nenhuma função definida para este usuário</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- CONTROLES RÁPIDOS DO ROBÔ -->
<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h5>Controles do Robô de Notificações</h5>
    </div>
    <div class="card-block">
        <div class="row">
            <div class="col-md-6">
                <h6>Ativar/Desativar Respostas Automáticas</h6>
                <form method="post" action="" style="display: inline;">
                    <?php if (!$status_robo): ?>
                        <button type="submit" name="acao_robo" value="ativar" class="btn btn-success btn-lg">
                            <i class="feather icon-message-circle"></i> Ativar Respostas
                        </button>
                    <?php else: ?>
                        <button type="submit" name="acao_robo" value="desativar" class="btn btn-danger btn-lg">
                            <i class="feather icon-pause-circle"></i> Pausar Respostas
                        </button>
                    <?php endif; ?>
                </form>
            </div>

            <div class="col-md-6">
                <form method="post" action="">
                    <h6>Selecionar Tipo de Função</h6>
                    <div class="form-group">
                        <select name="modulo_selecionado" class="form-control" required>
                            <option value="">Escolha o tipo de função</option>
                            <?php
                            $sql_modulos = "
                                SELECT id, nome_modulo 
                                FROM planos_clientes 
                                WHERE tipo = '1' 
                                AND nome_plano = '$plano'
                                ORDER BY nome_modulo ASC
                            ";
                            $res_modulos = mysqli_query($conn, $sql_modulos);

                            if ($res_modulos && mysqli_num_rows($res_modulos) > 0) {
                                while ($modulo = mysqli_fetch_assoc($res_modulos)) {
                                    $selected = ($modulo['id'] == $id_funcao_ativa) ? 'selected' : '';
                                    echo "<option value='{$modulo['id']}' {$selected}>" .
                                         htmlspecialchars($modulo['nome_modulo'], ENT_QUOTES) .
                                         "</option>";
                                }
                            } else {
                                echo "<option value='' disabled>Nenhuma função disponível neste plano</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="feather icon-settings"></i> Definir Tipo de Função
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php if($login == '123'): ?>
<!-- LISTA DE TIPOS DE FUNÇÃO DISPONÍVEIS -->
<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h5>Tipos de Funções Disponíveis no <?php echo strtoupper($plano); ?></h5>
    </div>
    <div class="card-block">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Tipo de Função</th>
                        <th>Data de Adição</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql_lista_modulos = "
                        SELECT id, nome_modulo, date
                        FROM planos_clientes 
                        WHERE tipo = '1' 
                        AND nome_plano = '$plano'
                        ORDER BY nome_modulo ASC
                    ";
                    $res_lista = mysqli_query($conn, $sql_lista_modulos);

                    if ($res_lista && mysqli_num_rows($res_lista) > 0) {
                        while ($modulo = mysqli_fetch_assoc($res_lista)) {
                            $is_active = ($modulo['id'] == $id_funcao_ativa);
                            $status_badge = $is_active ?
                                '<span class="badge badge-success">Em Uso</span>' :
                                '<span class="badge badge-secondary">Disponível</span>';
                            $data_formatada = date('d/m/Y H:i', strtotime($modulo['date']));

                            echo "<tr" . ($is_active ? ' class="table-success"' : '') . ">";
                            echo "<td>" . htmlspecialchars($modulo['nome_modulo'], ENT_QUOTES) . "</td>";
                            echo "<td>{$data_formatada}</td>";
                            echo "<td>{$status_badge}</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3' class='text-center'>Nenhum tipo de função disponível neste plano</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- INFORMAÇÕES DEBUG (OPCIONAL - REMOVER EM PRODUÇÃO) -->
<?php if (isset($_GET['debug'])): ?>
<div class="card" style="margin-top: 20px; border-color: #ffc107;">
    <div class="card-header" style="background-color: #fff3cd;">
        <h6><i class="feather icon-info"></i> Debug - Informações da Tabela Funcao</h6>
    </div>
    <div class="card-block">
        <?php
        $sql_debug = "SELECT * FROM funcao WHERE login = '$login'";
        $res_debug = mysqli_query($conn, $sql_debug);

        if ($res_debug && mysqli_num_rows($res_debug) > 0) {
            $debug_data = mysqli_fetch_assoc($res_debug);
            echo "<small>";
            echo "<strong>ID:</strong> " . $debug_data['id'] . "<br>";
            echo "<strong>Função:</strong> " . htmlspecialchars($debug_data['funcao'], ENT_QUOTES) . "<br>";
            echo "<strong>ID Função:</strong> " . $debug_data['id_funcao'] . "<br>";
            echo "<strong>Login:</strong> " . htmlspecialchars($debug_data['login'], ENT_QUOTES) . "<br>";
            echo "</small>";
        } else {
            echo "<small class='text-muted'>Nenhum registro encontrado na tabela funcao para este login.</small>";
        }
        ?>
    </div>
</div>
<?php endif; ?>

<style>
.status-indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    display: inline-block;
}
.bg-success { background-color: #28a745 !important; }
.bg-danger { background-color: #dc3545 !important; }
.table-success { background-color: rgba(40, 167, 69, 0.1); }
.badge { font-size: 0.875rem; padding: 0.375rem 0.75rem; }
.badge-success { background-color: #28a745; color: white; }
.badge-secondary { background-color: #6c757d; color: white; }
.card-header h5 { margin-bottom: 0; }
.btn-lg { padding: 0.75rem 1.5rem; font-size: 1.125rem; }
.form-group label { font-weight: 500; margin-bottom: 0.5rem; }
.alert-info { border-color: #bee5eb; background-color: #d1ecf1; color: #0c5460; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('button[value="desativar"]').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            if (!confirm('Tem certeza que deseja pausar as respostas automáticas?')) {
                e.preventDefault();
            }
        });
    });

    var formFuncao = document.querySelector('form[method="post"]');
    if (formFuncao) {
        formFuncao.addEventListener('submit', function(e) {
            var select = this.querySelector('select[name="modulo_selecionado"]');
            if (select && !select.value) {
                alert('Por favor, selecione um tipo de função!');
                e.preventDefault();
            }
        });
    }
});
</script>

<?php include 'footer.php'; ?>