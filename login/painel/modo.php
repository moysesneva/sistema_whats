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

$stmt_busca_usuario = $conn->prepare("SELECT * FROM login WHERE login = ?");
$stmt_busca_usuario->bind_param("s", $login);
$stmt_busca_usuario->execute();
$query_busca_usuario = $stmt_busca_usuario->get_result();
$total_busca_usuario = $query_busca_usuario->num_rows;

while($rows_usuarios = $query_busca_usuario->fetch_array()) {
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
        $stmt_st = $conn->prepare("UPDATE login SET funcao = ? WHERE login = ?");
        $stmt_st->bind_param("ss", $status_robo, $login);
        $resultado_status = $stmt_st->execute();
        $stmt_st->close();

        $login2 = 'agenda_'.$login;
        $stmt_cli = $conn->prepare("UPDATE clientes SET time_resposta = '' WHERE usuario_api = ?");
        $stmt_cli->bind_param("s", $login2);
        $resultado_status = $stmt_cli->execute();
        $stmt_cli->close();

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

        $stmt_mod = $conn->prepare("SELECT nome_modulo FROM planos_clientes WHERE id = ? AND tipo = '1' AND nome_plano = ? LIMIT 1");
        $stmt_mod->bind_param("is", $id_modulo, $plano);
        $stmt_mod->execute();
        $res_modulo = $stmt_mod->get_result();
        $stmt_mod->close();

        if ($res_modulo && $res_modulo->num_rows > 0) {
            $modulo_info = $res_modulo->fetch_assoc();
            $nome_modulo = $modulo_info['nome_modulo'];

            $stmt_ver = $conn->prepare("SELECT id FROM funcao WHERE login = ? LIMIT 1");
            $stmt_ver->bind_param("s", $login);
            $stmt_ver->execute();
            $res_verifica_usuario = $stmt_ver->get_result();
            $stmt_ver->close();

            if ($res_verifica_usuario && $res_verifica_usuario->num_rows > 0) {
                $stmt_upd = $conn->prepare("UPDATE login SET modo_atuante = ? WHERE login = ?");
                $stmt_upd->bind_param("ss", $nome_modulo, $login);
                $resultado_funcao = $stmt_upd->execute();
                $stmt_upd->close();
                $acao_realizada = "atualizada";
            } else {
                $stmt_ins = $conn->prepare("INSERT INTO funcao (funcao, id_funcao, login) VALUES (?, ?, ?)");
                $stmt_ins->bind_param("sis", $nome_modulo, $id_modulo, $login);
                $resultado_funcao = $stmt_ins->execute();
                $stmt_ins->close();
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

        $stmt_modo = $conn->prepare("UPDATE login SET funcao = ? WHERE login = ?");
        $stmt_modo->bind_param("ss", $modoRobo, $login);
        $resultado_modo = $stmt_modo->execute();
        $stmt_modo->close();

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

$stmt_bu2 = $conn->prepare("SELECT * FROM login WHERE login = ?");
$stmt_bu2->bind_param("s", $login);
$stmt_bu2->execute();
$query_busca_usuario = $stmt_bu2->get_result();
$stmt_bu2->close();
$funcao_atual = 'desativado';
$status_robo = false;

if ($query_busca_usuario && $query_busca_usuario->num_rows > 0) {
    $rows_usuarios = $query_busca_usuario->fetch_array();
    $nome = $rows_usuarios['nome'];
    $funcao_atual = $rows_usuarios['funcao'];
    $status_robo = ($funcao_atual == 'IA');
}

$stmt_fa = $conn->prepare("SELECT funcao, id_funcao FROM funcao WHERE login = ? LIMIT 1");
$stmt_fa->bind_param("s", $login);
$stmt_fa->execute();
$res_funcao_atual = $stmt_fa->get_result();
$stmt_fa->close();
$funcao_ativa = null;
$id_funcao_ativa = null;

if ($res_funcao_atual && $res_funcao_atual->num_rows > 0) {
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
                            $stmt_mod = $conn->prepare("SELECT id, nome_modulo FROM planos_clientes WHERE tipo = '1' AND nome_plano = ? ORDER BY nome_modulo ASC");
                            $stmt_mod->bind_param("s", $plano);
                            $stmt_mod->execute();
                            $res_modulos = $stmt_mod->get_result();
                            $stmt_mod->close();

                            if ($res_modulos && $res_modulos->num_rows > 0) {
                                while ($modulo = $res_modulos->fetch_assoc()) {
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
                    $stmt_lm = $conn->prepare("SELECT id, nome_modulo, date FROM planos_clientes WHERE tipo = '1' AND nome_plano = ? ORDER BY nome_modulo ASC");
                    $stmt_lm->bind_param("s", $plano);
                    $stmt_lm->execute();
                    $res_lista = $stmt_lm->get_result();
                    $stmt_lm->close();

                    if ($res_lista && $res_lista->num_rows > 0) {
                        while ($modulo = $res_lista->fetch_assoc()) {
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
        $stmt_dbg = $conn->prepare("SELECT * FROM funcao WHERE login = ?");
        $stmt_dbg->bind_param("s", $login);
        $stmt_dbg->execute();
        $res_debug = $stmt_dbg->get_result();
        $stmt_dbg->close();

        if ($res_debug && $res_debug->num_rows > 0) {
            $debug_data = $res_debug->fetch_assoc();
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