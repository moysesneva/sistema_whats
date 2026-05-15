<?php
require_once __DIR__ . '/auth_guard.php';
session_start();
include 'funcoes.php';

if(!isset($_SESSION['login'])) {
    VaiPara('login.php');
} 

$login = $_SESSION['login'];
include 'conn.php';
include 'estilo.php';
include 'css_de_icones.php';

if (isset($_GET['pagina_nome'])) {
    $pagina_nome_recebe = $_GET['pagina_nome'];
} else {
    $pagina_nome_recebe = 0;    
}

// Busca informações do usuário
$stmt_busca_usuario = $conn->prepare("SELECT * FROM login WHERE login = ?");
$stmt_busca_usuario->bind_param("s", $login);
$stmt_busca_usuario->execute();
$query_busca_usuario = $stmt_busca_usuario->get_result();
$total_busca_usuario = $query_busca_usuario->num_rows;

while($rows_usuarios = $query_busca_usuario->fetch_array()) {
    $nome = Priletra($rows_usuarios['nome']);
    $img_perfil = $rows_usuarios['perfil_img'];
    $autorizado = $rows_usuarios['autorizado'];
    $tipo = $rows_usuarios['tipo'];
    $usuario_api = $rows_usuarios['usuario_api'];
}

include 'menu.php';

if($total_busca_usuario != 1){
    VaiPara('login.php');
}
if($autorizado != 2){
    VaiPara('desbloquar.php');
}



$stmt_bprof = $conn->prepare("SELECT * FROM profissional WHERE telefone = ?");
$stmt_bprof->bind_param("s", $login);
$stmt_bprof->execute();
$sql_busca_profs = $stmt_bprof->get_result();
$total_busca_profs = $sql_busca_profs->num_rows;
$stmt_bprof->close();

while($rows_usuarios = $sql_busca_profs->fetch_array()) {
    $id_profissional  = $rows_usuarios['id'];
}







// Filtros
$filtro_data_inicio = isset($_GET['data_inicio']) ? $_GET['data_inicio'] : date('Y-m-d');
$filtro_data_fim = isset($_GET['data_fim']) ? $_GET['data_fim'] : date('Y-m-d', strtotime('+60 days'));
$filtro_profissional = isset($_GET['profissional']) ? $_GET['profissional'] : '';
$filtro_confirmacao = isset($_GET['confirmacao']) ? $_GET['confirmacao'] : '';

// Query base com prepared statements
$sql_agendamentos = "SELECT * FROM agendamento WHERE data BETWEEN ? AND ? AND id_profissional = ?";
$tipos_ag = "ssi";
$params_ag = [$filtro_data_inicio, $filtro_data_fim, $id_profissional];

if ($filtro_profissional) {
    $sql_agendamentos .= " AND id_profissional = ?";
    $tipos_ag .= "i";
    $params_ag[] = (int)$filtro_profissional;
}
if ($filtro_confirmacao !== '') {
    $sql_agendamentos .= " AND confirmacao = ?";
    $tipos_ag .= "s";
    $params_ag[] = $filtro_confirmacao;
}

$sql_agendamentos .= " ORDER BY data DESC, horario ASC";
$stmt_ag = $conn->prepare($sql_agendamentos);
$stmt_ag->bind_param($tipos_ag, ...$params_ag);
$stmt_ag->execute();
$query_agendamentos = $stmt_ag->get_result();
$stmt_ag->close();




?>





<?php $css_extra = '    <link rel="stylesheet" type="text/css" href="../files/bower_components/datatables.net-bs4/css/dataTables.bootstrap4.min.css">';
?>
<?php include 'header.php'; ?>



                                    <!-- CONTEÚDO PRINCIPAL MODERNIZADO -->
                                    <div class="page-body" style="padding: 2rem;">
                                        <div class="container-fluid">
                                            
                                            <!-- Header da Página -->
                                            <div class="modern-card">
                                                <div class="page-header">
                                                    <h5><i class="feather icon-calendar"></i> Relatório de Agendamentos</h5>
                                                    <span>Visualize e exporte os dados de agendamentos com interface moderna e intuitiva</span>
                                                </div>
                                            </div>

                                            <!-- Seção de Filtros -->
                                            <div class="filter-section">
                                                <h6 class="section-title">
                                                    <i class="feather icon-filter"></i>
                                                    Filtros de Pesquisa
                                                </h6>
                                                <form method="GET">
                                                    <input type="hidden" name="pagina_nome" value="<?= htmlspecialchars($pagina_nome_recebe, ENT_QUOTES, 'UTF-8') ?>">
                                                    <div class="row">
                                                        <div class="col-lg-3 col-md-6">
                                                            <div class="form-group">
                                                                <label>Data Início</label>
                                                                <input type="date" name="data_inicio" class="form-control" value="<?= htmlspecialchars($filtro_data_inicio, ENT_QUOTES, 'UTF-8') ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3 col-md-6">
                                                            <div class="form-group">
                                                                <label>Data Fim</label>
                                                                <input type="date" name="data_fim" class="form-control" value="<?= htmlspecialchars($filtro_data_fim, ENT_QUOTES, 'UTF-8') ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3 col-md-6">
                                                            <div class="form-group">
                                                                <label>Status de Confirmação</label>
                                                                <select name="confirmacao" class="form-control">
                                                                    <option value="">Todos os Status</option>
                                                                    <option value="1" <?= $filtro_confirmacao === '1' ? 'selected' : '' ?>>Confirmado</option>
                                                                    <option value="0" <?= $filtro_confirmacao === '0' ? 'selected' : '' ?>>Não Confirmado</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3 col-md-6">
                                                            <div class="form-group">
                                                                <label>&nbsp;</label>
                                                                <button type="submit" class="btn btn-filter btn-block">
                                                                    <i class="feather icon-search"></i> Aplicar Filtros
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>

                                            <!-- Dashboard de Estatísticas -->
                                            <div class="stats-grid">
                                                <?php
                                                // Estatísticas (mantendo a lógica original)
                                                $stmt_tot = $conn->prepare("SELECT COUNT(*) as total FROM agendamento WHERE data BETWEEN ? AND ? AND id_profissional = ?");
                                                $stmt_tot->bind_param("ssi", $filtro_data_inicio, $filtro_data_fim, $id_profissional);
                                                $stmt_tot->execute();
                                                $total_agendamentos = $stmt_tot->get_result()->fetch_assoc()['total'];
                                                $stmt_tot->close();

                                                $stmt_conf = $conn->prepare("SELECT COUNT(*) as total FROM agendamento WHERE data BETWEEN ? AND ? AND confirmacao = 1 AND id_profissional = ?");
                                                $stmt_conf->bind_param("ssi", $filtro_data_inicio, $filtro_data_fim, $id_profissional);
                                                $stmt_conf->execute();
                                                $total_confirmados = $stmt_conf->get_result()->fetch_assoc()['total'];
                                                $stmt_conf->close();

                                                $stmt_pend = $conn->prepare("SELECT COUNT(*) as total FROM agendamento WHERE data BETWEEN ? AND ? AND confirmacao = 0 AND id_profissional = ?");
                                                $stmt_pend->bind_param("ssi", $filtro_data_inicio, $filtro_data_fim, $id_profissional);
                                                $stmt_pend->execute();
                                                $total_pendentes = $stmt_pend->get_result()->fetch_assoc()['total'];
                                                $stmt_pend->close();
                                                ?>

                                                <div class="stat-card total">
                                                    <div class="stat-icon total">
                                                        <i class="feather icon-calendar"></i>
                                                    </div>
                                                    <div class="stat-number"><?= number_format($total_agendamentos) ?></div>
                                                    <div class="stat-label">Total de Agendamentos</div>
                                                </div>

                                                <div class="stat-card confirmed">
                                                    <div class="stat-icon confirmed">
                                                        <i class="feather icon-check-circle"></i>
                                                    </div>
                                                    <div class="stat-number"><?= number_format($total_confirmados) ?></div>
                                                    <div class="stat-label">Agendamentos Confirmados</div>
                                                </div>

                                                <div class="stat-card pending">
                                                    <div class="stat-icon pending">
                                                        <i class="feather icon-clock"></i>
                                                    </div>
                                                    <div class="stat-number"><?= number_format($total_pendentes) ?></div>
                                                    <div class="stat-label">Agendamentos Pendentes</div>
                                                </div>
                                            </div>

                                            <!-- Tabela de Resultados -->
                                            <div class="table-container">
                                                <h6 class="section-title">
                                                    <i class="feather icon-list"></i>
                                                    Detalhes dos Agendamentos
                                                </h6>
                                                <div class="table-responsive">
                                                    <table id="relatorio-table" class="table">
                                                        <thead>
                                                            <tr>
                                                                <th><i class="feather icon-calendar"></i> Data</th>
                                                                <th><i class="feather icon-clock"></i> Horário</th>
                                                                <th><i class="feather icon-user"></i> Cliente</th>
                                                                <th><i class="feather icon-phone"></i> Telefone</th>
                                                                <th><i class="feather icon-briefcase"></i> Profissional</th>
                                                                <th><i class="feather icon-award"></i> Cargo</th>
                                                                <th><i class="feather icon-info"></i> Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php 
                                                            // Reiniciar a query para exibir os resultados (mantendo a lógica original)
                                                            $query_agendamentos = mysqli_query($conn, $sql_agendamentos);
                                                            
                                                            while($row = mysqli_fetch_array($query_agendamentos)) { 
                                                            ?>
                                                                <tr>
                                                                    <td><strong><?= date('d/m/Y', strtotime($row['data'])) ?></strong></td>
                                                                    <td><?= htmlspecialchars($row['horario'], ENT_QUOTES, 'UTF-8') ?></td>
                                                                    <td><?= htmlspecialchars($row['cliente_nome'], ENT_QUOTES, 'UTF-8') ?></td>
                                                                    <td><?= htmlspecialchars($row['cliente_telefone'], ENT_QUOTES, 'UTF-8') ?></td>
                                                                    <td><?= htmlspecialchars($row['profissional_nome'], ENT_QUOTES, 'UTF-8') ?></td>
                                                                    <td><?= htmlspecialchars($row['profissional_cargo'], ENT_QUOTES, 'UTF-8') ?></td>
                                                                    <td>
                                                                        <?php if ($row['confirmacao'] == 1) { ?>
                                                                            <span class="badge badge-success">
                                                                                <i class="feather icon-check"></i> Confirmado
                                                                            </span>
                                                                        <?php } elseif ($row['confirmacao'] == 2) { ?>
                                                                            <span class="badge badge-danger">
                                                                                <i class="feather icon-x"></i> Cancelado
                                                                            </span>
                                                                        <?php } else { ?>
                                                                            <span class="badge badge-warning">
                                                                                <i class="feather icon-clock"></i> Pendente
                                                                            </span>
                                                                        <?php } ?>
                                                                    </td>
                                                                </tr>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts originais mantidos -->
    <script src="../files/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="../files/bower_components/datatables.net-buttons/js/dataTables.buttons.min.js"></script>


<?php include 'footer.php'; ?>