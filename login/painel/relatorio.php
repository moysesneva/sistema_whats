<?php
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
$sql_busca_usuario = "SELECT * FROM login WHERE login = '$login'";
$query_busca_usuario = mysqli_query($conn, $sql_busca_usuario);
$total_busca_usuario = mysqli_num_rows($query_busca_usuario);

while($rows_usuarios = mysqli_fetch_array($query_busca_usuario)) {
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

// Filtros
$filtro_data_inicio = isset($_GET['data_inicio']) ? $_GET['data_inicio'] : date('Y-m-d');
$filtro_data_fim = isset($_GET['data_fim']) ? $_GET['data_fim'] : date('Y-m-d', strtotime('+60 days'));
$filtro_profissional = isset($_GET['profissional']) ? $_GET['profissional'] : '';
$filtro_confirmacao = isset($_GET['confirmacao']) ? $_GET['confirmacao'] : '';

// Query base
$sql_agendamentos = "SELECT * FROM agendamento WHERE data BETWEEN '$filtro_data_inicio' AND '$filtro_data_fim' AND usuario_api = '$usuario_api'";

if ($filtro_profissional) {
    $sql_agendamentos .= " AND id_profissional = '$filtro_profissional'";
}
if ($filtro_confirmacao !== '') {
    $sql_agendamentos .= " AND confirmacao = '$filtro_confirmacao'";
}

$sql_agendamentos .= " ORDER BY data DESC, horario ASC";
$query_agendamentos = mysqli_query($conn, $sql_agendamentos);
?>

<?php $css_extra = '    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.7.0/css/buttons.dataTables.min.css">'; ?>
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
                                                    <input type="hidden" name="pagina_nome" value="<?= $pagina_nome_recebe ?>">
                                                    <div class="row">
                                                        <div class="col-lg-3 col-md-6">
                                                            <div class="form-group">
                                                                <label>Data Início</label>
                                                                <input type="date" name="data_inicio" class="form-control" value="<?= $filtro_data_inicio ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3 col-md-6">
                                                            <div class="form-group">
                                                                <label>Data Fim</label>
                                                                <input type="date" name="data_fim" class="form-control" value="<?= $filtro_data_fim ?>">
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
                                                $sql_total = "
                                                    SELECT COUNT(*) as total 
                                                    FROM agendamento 
                                                    WHERE data BETWEEN '$filtro_data_inicio' AND '$filtro_data_fim'
                                                      AND usuario_api = '$usuario_api'
                                                ";
                                                $query_total = mysqli_query($conn, $sql_total);
                                                $total_agendamentos = mysqli_fetch_assoc($query_total)['total'];

                                                $sql_confirmados = "
                                                    SELECT COUNT(*) as total 
                                                    FROM agendamento 
                                                    WHERE data BETWEEN '$filtro_data_inicio' AND '$filtro_data_fim'
                                                      AND confirmacao = 1
                                                      AND usuario_api = '$usuario_api'
                                                ";
                                                $query_confirmados = mysqli_query($conn, $sql_confirmados);
                                                $total_confirmados = mysqli_fetch_assoc($query_confirmados)['total'];

                                                $sql_pendentes = "
                                                    SELECT COUNT(*) as total 
                                                    FROM agendamento 
                                                    WHERE data BETWEEN '$filtro_data_inicio' AND '$filtro_data_fim'
                                                      AND confirmacao = 0
                                                      AND usuario_api = '$usuario_api'
                                                ";
                                                $query_pendentes = mysqli_query($conn, $sql_pendentes);
                                                $total_pendentes = mysqli_fetch_assoc($query_pendentes)['total'];
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
                                                                    <td><?= $row['horario'] ?></td>
                                                                    <td><?= $row['cliente_nome'] ?></td>
                                                                    <td><?= $row['cliente_telefone'] ?></td>
                                                                    <td><?= $row['profissional_nome'] ?></td>
                                                                    <td><?= $row['profissional_cargo'] ?></td>
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
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>


<?php include 'footer.php'; ?>