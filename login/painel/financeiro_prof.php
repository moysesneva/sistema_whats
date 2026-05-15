<?php
session_start();
include 'funcoes.php';

if(!isset($_SESSION['login'])) {
VaiPara('login.php');
} 
#error_reporting(1);
ini_set("display_errors", 0);
#$_SESSION['tipo_menu'] = 1;
$login = $_SESSION['login'];


include 'conn.php';





include 'estilo.php';

include 'css_de_icones.php';



if (isset($_GET['pagina_nome'])) {
$pagina_nome_recebe = $_GET['pagina_nome'];
}else{
$pagina_nome_recebe = 0;    
}


$stmt_user = mysqli_prepare($conn, "SELECT * FROM login WHERE login = ?");
mysqli_stmt_bind_param($stmt_user, "s", $login);
mysqli_stmt_execute($stmt_user);
$query_busca_usuario = mysqli_stmt_get_result($stmt_user);
$total_busca_usuario = mysqli_num_rows($query_busca_usuario);

while($rows_usuarios = $query_busca_usuario->fetch_array()) {
    $nome  = Priletra($rows_usuarios['nome']);
    $img_perfil  = $rows_usuarios['perfil_img'];
    $autorizado  = $rows_usuarios['autorizado'];
    $tipo  = $rows_usuarios['tipo'];

}




#####DEFINIMOS QUE  O TIPO DO MENU
## 1 É O ADM
## 2 É  O USUARIO
include 'menu.php';


if($total_busca_usuario != 1){
    VaiPara('login.php');
}
if($autorizado != 2){
 VaiPara('desbloquar.php');
}


$stmt_prof = mysqli_prepare($conn, "SELECT * FROM profissional WHERE telefone = ?");
mysqli_stmt_bind_param($stmt_prof, "s", $login);
mysqli_stmt_execute($stmt_prof);
$sql_busca_profs = mysqli_stmt_get_result($stmt_prof);
$total_busca_profs = mysqli_num_rows($sql_busca_profs);

while($rows_usuarios = mysqli_fetch_array($sql_busca_profs)) {
    $id_profissional  = $rows_usuarios['id'];
        $login  = $rows_usuarios['login'];


}



?>



<?php

$stmt_ag = mysqli_prepare($conn, "SELECT * FROM agendamento WHERE id_profissional = ?");
mysqli_stmt_bind_param($stmt_ag, "s", $id_profissional);
mysqli_stmt_execute($stmt_ag);
$query = mysqli_stmt_get_result($stmt_ag);
$total_financeiro = mysqli_num_rows($query);


if($total_financeiro == 0){
    
   VaiPara('sem_financas.php');
    
}
?>
<?php include 'header.php'; ?>



















    














<?php

    
    



// Verificação de segurança
if (!isset($conn) || !$conn) {
    echo '<div class="alert alert-danger">Erro de conexão com o banco de dados.</div>';
    return;
}

if (!isset($login) || empty($login)) {
    echo '<div class="alert alert-danger">Erro: Usuário não logado.</div>';
    return;
}

// Obtém os filtros
$mes_atual = isset($_GET['mes']) ? intval($_GET['mes']) : intval(date('m'));
$ano_atual = isset($_GET['ano']) ? intval($_GET['ano']) : intval(date('Y'));
$profissional_filtro = isset($_GET['profissional']) ? trim($_GET['profissional']) : '';
$data_inicio = isset($_GET['data_inicio']) ? $_GET['data_inicio'] : '';
$data_fim = isset($_GET['data_fim']) ? $_GET['data_fim'] : '';
$mostrar_graficos = isset($_GET['graficos']) ? $_GET['graficos'] : '1';

// Função para meses em português
function getNomeMes($mes) {
    $meses = [
        1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
        5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
        9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
    ];
    return isset($meses[intval($mes)]) ? $meses[intval($mes)] : 'Mês Inválido';
}

// Validar datas para evitar injeção via campo de data
function validarDataFinanceiroPro($data) {
    if (empty($data)) return '';
    $d = DateTime::createFromFormat('Y-m-d', $data);
    return ($d && $d->format('Y-m-d') === $data) ? $data : '';
}
$data_inicio = validarDataFinanceiroPro($data_inicio);
$data_fim = validarDataFinanceiroPro($data_fim);

// Buscar profissionais únicos
$stmt_prof_q = mysqli_prepare($conn,
    "SELECT DISTINCT profissional_nome, id_profissional FROM agendamento WHERE login = ? AND id_profissional = ? AND profissional_nome IS NOT NULL AND profissional_nome != ''");
mysqli_stmt_bind_param($stmt_prof_q, "ss", $login, $id_profissional);
mysqli_stmt_execute($stmt_prof_q);
$query_profissionais = mysqli_stmt_get_result($stmt_prof_q);

// Construir WHERE com parâmetros
$where_conditions = ["id_profissional = ?"];
$where_params = [$id_profissional];
$where_types = "s";

// Filtro por profissional
if (!empty($profissional_filtro)) {
    $where_conditions[] = "profissional_nome = ?";
    $where_params[] = $profissional_filtro;
    $where_types .= "s";
}

// Filtro por período
if (!empty($data_inicio) && !empty($data_fim)) {
    $where_conditions[] = "data BETWEEN ? AND ?";
    $where_params[] = $data_inicio;
    $where_params[] = $data_fim;
    $where_types .= "ss";
} else {
    $mes_str = str_pad($mes_atual, 2, '0', STR_PAD_LEFT);
    $like_data = "$ano_atual-$mes_str-%";
    $where_conditions[] = "data LIKE ?";
    $where_params[] = $like_data;
    $where_types .= "s";
}

// Só registros com valor
$where_conditions[] = "valor_servico IS NOT NULL";
$where_conditions[] = "valor_servico > 0";

$where_clause = "WHERE " . implode(" AND ", $where_conditions);

// Query principal com prepared statement
$sql_financeiro = "SELECT 
    data,
    valor_servico,
    cliente_nome,
    profissional_nome,
    confirmacao,
    servico_id,
    duracao_minutos
    FROM agendamento 
    $where_clause
    ORDER BY data DESC";

$stmt_financeiro = mysqli_prepare($conn, $sql_financeiro);
mysqli_stmt_bind_param($stmt_financeiro, $where_types, ...$where_params);
mysqli_stmt_execute($stmt_financeiro);
$query_financeiro = mysqli_stmt_get_result($stmt_financeiro);
$total_encontrados = $query_financeiro ? mysqli_num_rows($query_financeiro) : 0;



if($total_encontrados == 0){
    
VaiPara('sem_financas_mes.php');    
}

// Query para evolução mensal (últimos 12 meses)
$stmt_evolucao = mysqli_prepare($conn, "SELECT 
    DATE_FORMAT(data, '%Y-%m') as mes_ano,
    DATE_FORMAT(data, '%m') as mes,
    DATE_FORMAT(data, '%Y') as ano,
    SUM(valor_servico) as total,
    COUNT(*) as quantidade
    FROM agendamento 
    WHERE login = ? AND id_profissional = ?
    AND valor_servico IS NOT NULL 
    AND valor_servico > 0
    AND data >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
    GROUP BY DATE_FORMAT(data, '%Y-%m')
    ORDER BY mes_ano DESC");
mysqli_stmt_bind_param($stmt_evolucao, "ss", $login, $id_profissional);
mysqli_stmt_execute($stmt_evolucao);
$query_evolucao = mysqli_stmt_get_result($stmt_evolucao);

// Construir WHERE clause para análise de serviços (datas já validadas)
$where_clause_servicos = '';
$servicos_params = [$login, $id_profissional];
$servicos_types = "ss";
if (!empty($data_inicio) && !empty($data_fim)) {
    $where_clause_servicos = "AND a.data BETWEEN ? AND ?";
    $servicos_params[] = $data_inicio;
    $servicos_params[] = $data_fim;
    $servicos_types .= "ss";
} else {
    $mes_str = str_pad($mes_atual, 2, '0', STR_PAD_LEFT);
    $like_servico = "$ano_atual-$mes_str-%";
    $where_clause_servicos = "AND a.data LIKE ?";
    $servicos_params[] = $like_servico;
    $servicos_types .= "s";
}

// Query para análise de serviços mais vendidos
$sql_servicos_vendidos = "SELECT 
    s.id,
    s.nome,
    s.descricao,
    s.valor as valor_tabela,
    s.duracao_minutos,
    s.categoria,
    COUNT(a.servico_id) as total_vendas,
    COALESCE(SUM(a.valor_servico), 0) as faturamento_total,
    COALESCE(AVG(a.valor_servico), 0) as ticket_medio,
    COALESCE(SUM(a.duracao_minutos), 0) as tempo_total
    FROM servicos s
    LEFT JOIN agendamento a ON s.id = a.servico_id 
    AND a.login = ? AND a.id_profissional = ?
    AND a.valor_servico IS NOT NULL 
    AND a.valor_servico > 0
    $where_clause_servicos
    WHERE s.login = ? AND s.ativo = 1
    GROUP BY s.id, s.nome, s.descricao, s.valor, s.duracao_minutos, s.categoria
    ORDER BY total_vendas DESC, faturamento_total DESC";

$stmt_servicos = mysqli_prepare($conn, $sql_servicos_vendidos);
$sv_params = array_merge($servicos_params, [$login]);
$sv_types = $servicos_types . "s";
mysqli_stmt_bind_param($stmt_servicos, $sv_types, ...$sv_params);
mysqli_stmt_execute($stmt_servicos);
$query_servicos_vendidos = mysqli_stmt_get_result($stmt_servicos);

// Arrays para armazenar dados
$total_geral = 0;
$total_mes_atual = 0;
$total_dia_atual = 0;
$agendamentos_confirmados = 0;
$agendamentos_pendentes = 0;
$agendamentos_cancelados = 0;
$vendas_profissional = [];
$vendas_dia = [];
$dados_grafico_diario = [];
$dados_grafico_profissional = [];
$evolucao_mensal = [];
$servicos_vendidos = [];
$dados_grafico_servicos = [];

$data_hoje = date('Y-m-d');

// Processar evolução mensal
if ($query_evolucao) {
    while($row = mysqli_fetch_array($query_evolucao)) {
        $evolucao_mensal[] = [
            'mes_ano' => $row['mes_ano'],
            'mes' => getNomeMes(intval($row['mes'])),
            'ano' => $row['ano'],
            'total' => floatval($row['total']),
            'quantidade' => intval($row['quantidade'])
        ];
    }
}

// Processar dados de serviços vendidos
if ($query_servicos_vendidos) {
    while($row = mysqli_fetch_array($query_servicos_vendidos)) {
        $servicos_vendidos[] = [
            'id' => $row['id'],
            'nome' => $row['nome'],
            'descricao' => $row['descricao'],
            'valor_tabela' => floatval($row['valor_tabela']),
            'duracao_minutos' => intval($row['duracao_minutos']),
            'categoria' => $row['categoria'],
            'total_vendas' => intval($row['total_vendas']),
            'faturamento_total' => floatval($row['faturamento_total']),
            'ticket_medio' => floatval($row['ticket_medio']),
            'tempo_total' => intval($row['tempo_total'])
        ];
    }
}

// Processar resultados principais
if ($query_financeiro && $total_encontrados > 0) {
    while($row = mysqli_fetch_array($query_financeiro)) {
        $valor = floatval($row['valor_servico']);
        $data = $row['data'];
        $profissional = $row['profissional_nome'];
        $status = intval($row['confirmacao']);
        
        // Total geral
        $total_geral += $valor;
        
        // Total do mês atual (se estiver no filtro de mês/ano)
        if (empty($data_inicio) && empty($data_fim)) {
            $total_mes_atual += $valor;
        } else {
            $total_mes_atual = $total_geral; // Se filtro personalizado, considerar como "mês atual"
        }
        
        // Total do dia atual
        if ($data == $data_hoje) {
            $total_dia_atual += $valor;
        }
        
        // Contadores por status
        if ($status == 1 || $status == 3) {
            $agendamentos_confirmados++;
        } elseif ($status == 2) {
            $agendamentos_cancelados++;
        } else {
            $agendamentos_pendentes++;
        }
        
        // Agrupamento por profissional
        if (!empty($profissional)) {
            if (!isset($vendas_profissional[$profissional])) {
                $vendas_profissional[$profissional] = [
                    'total' => 0, 
                    'quantidade' => 0, 
                    'confirmados' => 0, 
                    'cancelados' => 0,
                    'tempo_total' => 0
                ];
            }
            $vendas_profissional[$profissional]['total'] += $valor;
            $vendas_profissional[$profissional]['quantidade']++;
            $vendas_profissional[$profissional]['tempo_total'] += intval($row['duracao_minutos']);
            
            if ($status == 1 || $status == 3) {
                $vendas_profissional[$profissional]['confirmados']++;
            } elseif ($status == 2) {
                $vendas_profissional[$profissional]['cancelados']++;
            }
        }
        
        // Agrupamento por dia
        if (!isset($vendas_dia[$data])) {
            $vendas_dia[$data] = ['total' => 0, 'quantidade' => 0];
        }
        $vendas_dia[$data]['total'] += $valor;
        $vendas_dia[$data]['quantidade']++;
    }
}

// Preparar dados para gráficos
if ($mostrar_graficos == '1') {
    // Dados para gráfico diário (últimos 30 dias do período)
    $dados_temp = array_slice($vendas_dia, -30, 30, true);
    foreach($dados_temp as $data => $info) {
        $dados_grafico_diario[] = [
            'data' => date('d/m', strtotime($data)),
            'valor' => $info['total']
        ];
    }
    
    // Dados para gráfico por profissional
    foreach($vendas_profissional as $nome => $dados) {
        $dados_grafico_profissional[] = [
            'nome' => $nome,
            'valor' => $dados['total']
        ];
    }
    
    // Dados para gráfico de serviços (top 10)
    $top_servicos = array_slice($servicos_vendidos, 0, 10);
    foreach($top_servicos as $servico) {
        if ($servico['total_vendas'] > 0) {
            $dados_grafico_servicos[] = [
                'nome' => $servico['nome'],
                'vendas' => $servico['total_vendas'],
                'faturamento' => $servico['faturamento_total']
            ];
        }
    }
}

// Identificar serviços mais e menos vendidos
$servico_mais_vendido = ['nome' => '', 'vendas' => 0, 'faturamento' => 0];
$servico_menos_vendido = ['nome' => '', 'vendas' => PHP_INT_MAX, 'faturamento' => 0];

foreach($servicos_vendidos as $servico) {
    if ($servico['total_vendas'] > 0) {
        // Mais vendido
        if ($servico['total_vendas'] > $servico_mais_vendido['vendas']) {
            $servico_mais_vendido = [
                'nome' => $servico['nome'],
                'vendas' => $servico['total_vendas'],
                'faturamento' => $servico['faturamento_total']
            ];
        }
        
        // Menos vendido (entre os que venderam)
        if ($servico['total_vendas'] < $servico_menos_vendido['vendas']) {
            $servico_menos_vendido = [
                'nome' => $servico['nome'],
                'vendas' => $servico['total_vendas'],
                'faturamento' => $servico['faturamento_total']
            ];
        }
    }
}

// Se não há menos vendido, resetar
if ($servico_menos_vendido['vendas'] == PHP_INT_MAX) {
    $servico_menos_vendido = ['nome' => '', 'vendas' => 0, 'faturamento' => 0];
}

// Cálculos finais
$dias_no_mes = date('t', mktime(0, 0, 0, $mes_atual, 1, $ano_atual));
$media_diaria = $dias_no_mes > 0 && $total_mes_atual > 0 ? $total_mes_atual / $dias_no_mes : 0;

// Melhor dia
$melhor_dia = ['data' => '', 'valor' => 0];
foreach($vendas_dia as $data => $info) {
    if($info['total'] > $melhor_dia['valor']) {
        $melhor_dia['data'] = $data;
        $melhor_dia['valor'] = $info['total'];
    }
}

// Melhor profissional
$melhor_profissional = ['nome' => '', 'valor' => 0];
foreach($vendas_profissional as $nome => $dados) {
    if($dados['total'] > $melhor_profissional['valor']) {
        $melhor_profissional['nome'] = $nome;
        $melhor_profissional['valor'] = $dados['total'];
    }
}

// Ticket médio geral
$ticket_medio_geral = $total_encontrados > 0 ? $total_geral / $total_encontrados : 0;
?>

<!-- Dashboard Financeiro Completo -->
<div class="page-header">
    <div class="row align-items-end">
        <div class="col-lg-8">
            <div class="page-header-title">
                <i class="feather icon-trending-up bg-c-blue"></i>
                <div class="d-inline">
                    <h5>Dashboard Financeiro Avançado</h5>
                    <span>Relatório completo de vendas, faturamento e análises</span>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="page-header-breadcrumb">
                <ul class="breadcrumb-title">
                    <li class="breadcrumb-item">
                        <a href="index.php"><i class="feather icon-home"></i></a>
                    </li>
                    <li class="breadcrumb-item"><a href="#!">Financeiro</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Botões de Exportação -->
<div class="row mb-3">
    <div class="col-md-12 text-right">
        <div class="btn-group">
            <button type="button" class="btn btn-success" onclick="exportarPDF()">
                <i class="feather icon-file-text"></i> Exportar PDF
            </button>
         
            <button type="button" class="btn btn-warning" onclick="window.print()">
                <i class="feather icon-printer"></i> Imprimir
            </button>
        </div>
    </div>
</div>

<!-- Cards de Análise de Serviços -->


<!-- Filtros Avançados -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="feather icon-filter"></i> Filtros Avançados</h5>
                <div class="card-header-right">
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-info" onclick="abrirCalendario()">
                            <i class="feather icon-calendar"></i> Calendário
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="toggleGraficos()">
                            <i class="feather icon-bar-chart-2"></i> <?=$mostrar_graficos == '1' ? 'Ocultar' : 'Mostrar'?> Gráficos
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-block">
                <form method="GET" action="" id="filtroForm">
                    <input type="hidden" name="pagina_nome" value="<?=isset($pagina_nome_recebe) ? $pagina_nome_recebe : ''?>">
                    <input type="hidden" name="graficos" value="<?=$mostrar_graficos?>">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Mês</label>
                                <select name="mes" class="form-control" onchange="limparDatasPersonalizadas()">
                                    <?php for($i = 1; $i <= 12; $i++): ?>
                                        <option value="<?=$i?>" <?=($i == $mes_atual) ? 'selected' : ''?>>
                                            <?=getNomeMes($i)?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Ano</label>
                                <select name="ano" class="form-control" onchange="limparDatasPersonalizadas()">
                                    <?php for($i = 2020; $i <= date('Y') + 1; $i++): ?>
                                        <option value="<?=$i?>" <?=($i == $ano_atual) ? 'selected' : ''?>><?=$i?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Data Início</label>
                                <input type="date" name="data_inicio" class="form-control" value="<?=$data_inicio?>" onchange="limparMesAno()">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Data Fim</label>
                                <input type="date" name="data_fim" class="form-control" value="<?=$data_fim?>" onchange="limparMesAno()">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Profissional</label>
                                <select name="profissional" class="form-control">
                                    <option value="">Todos</option>
                                    <?php 
                                    if ($query_profissionais) {
                                        mysqli_data_seek($query_profissionais, 0);
                                        while($prof = mysqli_fetch_array($query_profissionais)): 
                                    ?>
                                        <option value="<?=htmlspecialchars($prof['profissional_nome'])?>" <?=($prof['profissional_nome'] == $profissional_filtro) ? 'selected' : ''?>>
                                            <?=htmlspecialchars($prof['profissional_nome'])?>
                                        </option>
                                    <?php 
                                        endwhile; 
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="feather icon-search"></i> Filtrar
                                    </button>
                                    <a href="?pagina_nome=<?=isset($pagina_nome_recebe) ? $pagina_nome_recebe : ''?>" class="btn btn-secondary btn-block mt-1">
                                        <i class="feather icon-refresh-cw"></i> Limpar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



<!-- Cards de KPIs -->
<div class="row">
    <div class="col-md-6 col-xl-3">
        <div class="card bg-c-blue order-card">
            <div class="card-block">
                <h6 class="m-b-20">Faturamento Total</h6>
                <h2 class="text-right">
                    <i class="feather icon-dollar-sign f-left"></i>
                    <span>R$ <?=number_format($total_geral, 2, ',', '.')?></span>
                </h2>
                <p class="m-b-0">
                    <?php if (!empty($data_inicio) && !empty($data_fim)): ?>
                        <?=date('d/m/Y', strtotime($data_inicio))?> a <?=date('d/m/Y', strtotime($data_fim))?>
                    <?php else: ?>
                        <?=getNomeMes($mes_atual)?> de <?=$ano_atual?>
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card bg-c-green order-card">
            <div class="card-block">
                <h6 class="m-b-20">Ticket Médio</h6>
                <h2 class="text-right">
                    <i class="feather icon-trending-up f-left"></i>
                    <span>R$ <?=number_format($ticket_medio_geral, 2, ',', '.')?></span>
                </h2>
                <p class="m-b-0"><?=$total_encontrados?> atendimentos</p>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card bg-c-yellow order-card">
            <div class="card-block">
                <h6 class="m-b-20">Faturamento Hoje</h6>
                <h2 class="text-right">
                    <i class="feather icon-calendar f-left"></i>
                    <span>R$ <?=number_format($total_dia_atual, 2, ',', '.')?></span>
                </h2>
                <p class="m-b-0"><?=date('d/m/Y')?></p>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card bg-c-pink order-card">
            <div class="card-block">
                <h6 class="m-b-20">Média Diária</h6>
                <h2 class="text-right">
                    <i class="feather icon-bar-chart f-left"></i>
                    <span>R$ <?=number_format($media_diaria, 2, ',', '.')?></span>
                </h2>
                <p class="m-b-0">Projeção mensal</p>
            </div>
        </div>
    </div>
</div>

<!-- Gráficos (se habilitados) -->
<?php if ($mostrar_graficos == '1' && ($total_encontrados > 0 || count($evolucao_mensal) > 0)): ?>
<div class="row">
    <!-- Gráfico de Evolução Mensal -->
    <?php if (count($evolucao_mensal) > 0): ?>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Evolução do Faturamento (Últimos 12 Meses)</h5>
            </div>
            <div class="card-block">
                <canvas id="graficoEvolucao" height="120"></canvas>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Gráfico por Profissional -->
    <?php if (count($dados_grafico_profissional) > 0): ?>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Faturamento por Profissional</h5>
            </div>
            <div class="card-block">
                <canvas id="graficoProfissional" height="120"></canvas>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Segunda linha de gráficos -->
<div class="row">
    <!-- Gráfico Diário -->
    <?php if (count($dados_grafico_diario) > 0): ?>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Faturamento Diário (Últimos 30 Dias)</h5>
            </div>
            <div class="card-block">
                <canvas id="graficoDiario" height="100"></canvas>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Gráfico de Serviços -->
    <?php if (count($dados_grafico_servicos) > 0): ?>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Top 10 Serviços</h5>
            </div>
            <div class="card-block">
                <canvas id="graficoServicos" height="150"></canvas>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<!-- Estatísticas e Insights -->
<div class="row">
    <div class="col-md-3">
        <div class="card">
            <div class="card-header">
                <h5>Status dos Agendamentos</h5>
            </div>
            <div class="card-block">
                <div class="row text-center">
                    <div class="col-md-12 mb-3">
                        <h4 class="text-c-green"><?=$agendamentos_confirmados?></h4>
                        <p class="text-muted m-b-0">Confirmados</p>
                    </div>
                    <div class="col-md-6">
                        <h4 class="text-c-yellow"><?=$agendamentos_pendentes?></h4>
                        <p class="text-muted m-b-0">Pendentes</p>
                    </div>
                    <div class="col-md-6">
                        <h4 class="text-c-red"><?=$agendamentos_cancelados?></h4>
                        <p class="text-muted m-b-0">Cancelados</p>
                    </div>
                </div>
                <?php 
                $total_agendamentos = $agendamentos_confirmados + $agendamentos_pendentes + $agendamentos_cancelados;
                $perc_confirmados = $total_agendamentos > 0 ? ($agendamentos_confirmados / $total_agendamentos) * 100 : 0;
                $perc_cancelados = $total_agendamentos > 0 ? ($agendamentos_cancelados / $total_agendamentos) * 100 : 0;
                $perc_pendentes = $total_agendamentos > 0 ? ($agendamentos_pendentes / $total_agendamentos) * 100 : 0;
                ?>
                <div class="progress m-t-20" style="height: 12px;">
                    <div class="progress-bar bg-success" style="width: <?=$perc_confirmados?>%"></div>
                    <div class="progress-bar bg-warning" style="width: <?=$perc_pendentes?>%"></div>
                    <div class="progress-bar bg-danger" style="width: <?=$perc_cancelados?>%"></div>
                </div>
                <p class="text-muted m-t-10 m-b-0">
                    <small>Taxa de confirmação: <?=number_format($perc_confirmados, 1)?>%</small>
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card">
            <div class="card-header">
                <h5>Melhor Dia</h5>
            </div>
            <div class="card-block text-center">
                <?php if($melhor_dia['valor'] > 0): ?>
                    <h3 class="text-c-green">R$ <?=number_format($melhor_dia['valor'], 2, ',', '.')?></h3>
                    <p class="text-muted"><?=date('d/m/Y', strtotime($melhor_dia['data']))?></p>
                    <small class="text-muted"><?=ucfirst(strftime('%A', strtotime($melhor_dia['data'])))?></small>
                <?php else: ?>
                    <h3 class="text-muted">Sem dados</h3>
                    <p class="text-muted">Nenhum faturamento no período</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card">
            <div class="card-header">
                <h5>Melhor Profissional</h5>
            </div>
            <div class="card-block text-center">
                <?php if($melhor_profissional['valor'] > 0): ?>
                    <h3 class="text-c-blue">R$ <?=number_format($melhor_profissional['valor'], 2, ',', '.')?></h3>
                    <p class="text-muted"><strong><?=htmlspecialchars($melhor_profissional['nome'])?></strong></p>
                    <?php 
                    $perc_melhor_prof = $total_geral > 0 ? ($melhor_profissional['valor'] / $total_geral) * 100 : 0;
                    ?>
                    <small class="text-muted"><?=number_format($perc_melhor_prof, 1)?>% do total</small>
                <?php else: ?>
                    <h3 class="text-muted">Sem dados</h3>
                    <p class="text-muted">Nenhum profissional registrado</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card">
            <div class="card-header">
                <h5>Resumo do Período</h5>
            </div>
            <div class="card-block text-center">
                <h4 class="text-c-purple"><?=$total_encontrados?></h4>
                <p class="text-muted m-b-10">Total de Atendimentos</p>
                
                <?php 
                $tempo_total = 0;
                foreach($vendas_profissional as $dados) {
                    $tempo_total += $dados['tempo_total'];
                }
                $horas_trabalhadas = $tempo_total > 0 ? round($tempo_total / 60, 1) : 0;
                ?>
                <h5 class="text-c-green"><?=$horas_trabalhadas?>h</h5>
                <p class="text-muted m-b-0">Horas Trabalhadas</p>
            </div>
        </div>
    </div>
</div>

<?php if($total_encontrados > 0): ?>
<?php if($total_encontrados > 0): ?>



<!-- Análise Detalhada de Serviços -->
<?php #if(count($servicos_vendidos) > 0): ?>
<?php if(count($servicos_vendidos) == 'esconde'): ?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Análise Detalhada de Serviços</h5>
                <div class="card-header-right">
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" onclick="ordenarServicos('vendas')">Por Vendas</button>
                        <button class="btn btn-outline-success" onclick="ordenarServicos('faturamento')">Por Faturamento</button>
                        <button class="btn btn-outline-info" onclick="ordenarServicos('ticket')">Por Ticket Médio</button>
                    </div>
                </div>
            </div>
            <div class="card-block">
                <div class="table-responsive">
                    <table class="table table-hover" id="tabelaServicos">
                        <thead>
                            <tr>
                                <th>Ranking</th>
                                <th>Serviço</th>
                                <th>Categoria</th>
                                <th>Valor Tabela</th>
                                <th>Vendas</th>
                                <th>Faturamento</th>
                                <th>Ticket Médio</th>
                                <th>Duração</th>
                                <th>Tempo Total</th>
                                <th>Performance</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                            <?php 
                            $ranking = 1;
                            $total_vendas_servicos = array_sum(array_column($servicos_vendidos, 'total_vendas'));
                            
                            foreach($servicos_vendidos as $servico):
                                $performance = $total_vendas_servicos > 0 ? ($servico['total_vendas'] / $total_vendas_servicos) * 100 : 0;
                                $horas = $servico['tempo_total'] > 0 ? round($servico['tempo_total'] / 60, 1) : 0;
                                
                                // Ícone de ranking
                                $icone_ranking = '';
                                $classe_linha = '';
                                if ($ranking == 1) {
                                    $icone_ranking = '<i class="feather icon-award text-warning"></i>';
                                    $classe_linha = 'table-warning';
                                } elseif ($ranking == 2) {
                                    $icone_ranking = '<i class="feather icon-award text-secondary"></i>';
                                    $classe_linha = 'table-light';
                                } elseif ($ranking == 3) {
                                    $icone_ranking = '<i class="feather icon-award text-c-brown"></i>';
                                    $classe_linha = 'table-light';
                                } else {
                                    $icone_ranking = $ranking . 'º';
                                }
                                
                                // Status baseado nas vendas
                                $status_badge = '';
                                if ($servico['total_vendas'] == 0) {
                                    $status_badge = '<span class="badge badge-danger">Não vendido</span>';
                                    $classe_linha = 'table-danger';
                                } elseif ($servico['total_vendas'] == 1) {
                                    $status_badge = '<span class="badge badge-warning">Pouco vendido</span>';
                                } elseif ($servico['total_vendas'] >= 10) {
                                    $status_badge = '<span class="badge badge-success">Top vendas</span>';
                                } else {
                                    $status_badge = '<span class="badge badge-info">Vendas normais</span>';
                                }
                            ?>
                            <tr class="<?=$classe_linha?>" data-vendas="<?=$servico['total_vendas']?>" data-faturamento="<?=$servico['faturamento_total']?>" data-ticket="<?=$servico['ticket_medio']?>">
                                <td class="text-center"><?=$icone_ranking?></td>
                                <td>
                                    <strong><?=htmlspecialchars($servico['nome'])?></strong>
                                    <?php if (!empty($servico['descricao'])): ?>
                                        <br><small class="text-muted"><?=htmlspecialchars($servico['descricao'])?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($servico['categoria'])): ?>
                                        <span class="badge badge-light"><?=htmlspecialchars($servico['categoria'])?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-c-blue">R$ <?=number_format($servico['valor_tabela'], 2, ',', '.')?></td>
                                <td>
                                    <span class="badge badge-primary"><?=$servico['total_vendas']?></span>
                                    <?=$status_badge?>
                                </td>
                                <td class="text-c-green font-weight-bold">R$ <?=number_format($servico['faturamento_total'], 2, ',', '.')?></td>
                                <td>
                                    <?php if ($servico['total_vendas'] > 0): ?>
                                        R$ <?=number_format($servico['ticket_medio'], 2, ',', '.')?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><?=$servico['duracao_minutos']?>min</td>
                                <td><?=$horas?>h</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="mr-2"><?=number_format($performance, 1)?>%</span>
                                        <div class="progress flex-grow-1" style="height: 6px; width: 60px;">
                                            <div class="progress-bar bg-info" style="width: <?=$performance?>%"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            
                            <?php 
                            $ranking++;
                            endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>

<!-- Relatório Detalhado por Profissional -->
<?php if(count($vendas_profissional) > 0): ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Relatório Detalhado por Profissional</h5>
            </div>
            <div class="card-block">
                <div class="table-responsive">
                    <table class="table table-hover" id="tabelaProfissionais">
                        <thead>
                            <tr>
                                <th>Ranking</th>
                                <th>Profissional</th>
                                <th>Faturamento</th>
                                <th>Atendimentos</th>
                                <th>Ticket Médio</th>
                                <th>Horas Trabalhadas</th>
                                <th>Taxa Confirmação</th>
                                <th>Performance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // Ordena por faturamento
                            uasort($vendas_profissional, function($a, $b) {
                                return $b['total'] <=> $a['total'];
                            });
                            
                            $ranking = 1;
                            foreach($vendas_profissional as $nome => $dados):
                                $ticket_medio = $dados['quantidade'] > 0 ? $dados['total'] / $dados['quantidade'] : 0;
                                $taxa_confirmacao = $dados['quantidade'] > 0 ? ($dados['confirmados'] / $dados['quantidade']) * 100 : 0;
                                $horas = $dados['tempo_total'] > 0 ? round($dados['tempo_total'] / 60, 1) : 0;
                                $percentual_faturamento = $total_geral > 0 ? ($dados['total'] / $total_geral) * 100 : 0;
                                
                                // Ícone de ranking
                                $icone_ranking = '';
                                if ($ranking == 1) $icone_ranking = '<i class="feather icon-award text-warning"></i>';
                                elseif ($ranking == 2) $icone_ranking = '<i class="feather icon-award text-secondary"></i>';
                                elseif ($ranking == 3) $icone_ranking = '<i class="feather icon-award text-c-brown"></i>';
                                else $icone_ranking = $ranking . 'º';
                            ?>
                            <tr>
                                <td class="text-center"><?=$icone_ranking?></td>
                                <td><strong><?=htmlspecialchars($nome)?></strong></td>
                                <td class="text-c-green font-weight-bold">R$ <?=number_format($dados['total'], 2, ',', '.')?></td>
                                <td><span class="badge badge-primary"><?=$dados['quantidade']?></span></td>
                                <td>R$ <?=number_format($ticket_medio, 2, ',', '.')?></td>
                                <td><?=$horas?>h</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="mr-2"><?=number_format($taxa_confirmacao, 1)?>%</span>
                                        <div class="progress flex-grow-1" style="height: 6px; width: 60px;">
                                            <div class="progress-bar bg-success" style="width: <?=$taxa_confirmacao?>%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="mr-2"><?=number_format($percentual_faturamento, 1)?>%</span>
                                        <div class="progress flex-grow-1" style="height: 6px; width: 60px;">
                                            <div class="progress-bar bg-info" style="width: <?=$percentual_faturamento?>%"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php 
                            $ranking++;
                            endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Tabela de Vendas Detalhada -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 id="tituloTabelaVendas">Histórico de Vendas (<?=$total_encontrados?> registros)</h5>
                <div class="card-header-right">
                    <div class="btn-group btn-group-sm" role="group">
                        <button class="btn btn-outline-secondary active" onclick="filtrarTabela('todos')">
                            <i class="feather icon-list"></i> Todos
                        </button>
                        <button class="btn btn-outline-success" onclick="filtrarTabela('confirmado')">
                            <i class="feather icon-check"></i> Confirmados
                        </button>
                        <button class="btn btn-outline-warning" onclick="filtrarTabela('pendente')">
                            <i class="feather icon-clock"></i> Pendentes
                        </button>
                        <button class="btn btn-outline-danger" onclick="filtrarTabela('cancelado')">
                            <i class="feather icon-x"></i> Cancelados
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-block">
                <div class="table-responsive">
                    <table class="table table-hover" id="tabelaVendas">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Cliente</th>
                                <th>Profissional</th>
                                <th>Duração</th>
                                <th>Valor</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            mysqli_data_seek($query_financeiro, 0);
                            while($row = mysqli_fetch_array($query_financeiro)):
                                // Determinar status e classe
                                if($row['confirmacao'] == 1 || $row['confirmacao'] == 3) {
                                    $status_class = 'confirmado';
                                    $status_badge = '<span class="badge badge-success">Confirmado</span>';
                                } elseif($row['confirmacao'] == 2) {
                                    $status_class = 'cancelado';
                                    $status_badge = '<span class="badge badge-danger">Cancelado</span>';
                                } else {
                                    $status_class = 'pendente';
                                    $status_badge = '<span class="badge badge-warning">Pendente</span>';
                                }
                            ?>
                            <tr class="linha-status" data-status="<?=$status_class?>">
                                <td><?=date('d/m/Y', strtotime($row['data']))?></td>
                                <td><?=htmlspecialchars($row['cliente_nome'])?></td>
                                <td><?=htmlspecialchars($row['profissional_nome'])?></td>
                                <td><?=$row['duracao_minutos']?>min</td>
                                <td class="text-c-green font-weight-bold">R$ <?=number_format($row['valor_servico'], 2, ',', '.')?></td>
                                <td><?=$status_badge?></td>
                              
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php else: ?>
<div class="row">
    <div class="col-md-12">
        <div class="alert alert-warning">
            <h5><i class="feather icon-alert-triangle"></i> Nenhum dado encontrado</h5>
            <p>Não foram encontrados registros para o período selecionado.</p>
            <div class="mt-3">
                <button class="btn btn-primary" onclick="abrirCalendario()">
                    <i class="feather icon-calendar"></i> Selecionar Outro Período
                </button>
                <button class="btn btn-secondary" onclick="limparFiltros()">
                    <i class="feather icon-refresh-cw"></i> Limpar Filtros
                </button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Modal do Calendário -->
<div class="modal fade" id="calendarioModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="feather icon-calendar"></i> Selecionar Período</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <label>Data de Início:</label>
                        <input type="date" class="form-control" id="modalDataInicio" value="<?=$data_inicio?>">
                    </div>
                    <div class="col-md-6">
                        <label>Data de Fim:</label>
                        <input type="date" class="form-control" id="modalDataFim" value="<?=$data_fim?>">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <label>Períodos Pré-definidos:</label>
                        <div class="btn-group btn-group-sm btn-block" role="group">
                            <button type="button" class="btn btn-outline-primary" onclick="setDataPeriodo('hoje')">Hoje</button>
                            <button type="button" class="btn btn-outline-primary" onclick="setDataPeriodo('ontem')">Ontem</button>
                            <button type="button" class="btn btn-outline-primary" onclick="setDataPeriodo('semana')">Esta Semana</button>
                            <button type="button" class="btn btn-outline-primary" onclick="setDataPeriodo('mes')">Este Mês</button>
                        </div>
                        <div class="btn-group btn-group-sm btn-block mt-2" role="group">
                            <button type="button" class="btn btn-outline-secondary" onclick="setDataPeriodo('semana_passada')">Semana Passada</button>
                            <button type="button" class="btn btn-outline-secondary" onclick="setDataPeriodo('mes_passado')">Mês Passado</button>
                            <button type="button" class="btn btn-outline-secondary" onclick="setDataPeriodo('trimestre')">Trimestre</button>
                            <button type="button" class="btn btn-outline-secondary" onclick="setDataPeriodo('ano')">Este Ano</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="aplicarDataPeriodo()">Aplicar Período</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Detalhes -->
<div class="modal fade" id="detalhesModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalhes do Atendimento</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="detalhesConteudo">
                <!-- Conteúdo carregado via JavaScript -->
            </div>
        </div>
    </div>
</div>

<!-- Estilos -->
<style>
.order-card { color: #fff; }
.order-card .card-block { padding: 1.25rem; }
.order-card i { font-size: 26px; }
.f-left { float: left; }
.bg-c-blue { background: linear-gradient(45deg, #4099ff, #73b4ff); }
.bg-c-green { background: linear-gradient(45deg, #2ed8b6, #59e0c5); }
.bg-c-yellow { background: linear-gradient(45deg, #FFB64D, #ffcb80); }
.bg-c-pink { background: linear-gradient(45deg, #FF5370, #ff869a); }
.text-c-green { color: #2ed8b6 !important; }
.text-c-red { color: #FF5370 !important; }
.text-c-blue { color: #4099ff !important; }
.text-c-yellow { color: #FFB64D !important; }
.text-c-purple { color: #7c4dff !important; }
.text-c-brown { color: #8d6e63 !important; }
.badge-success { background-color: #2ed8b6; }
.badge-warning { background-color: #FFB64D; }
.badge-danger { background-color: #FF5370; }
.badge-primary { background-color: #4099ff; }
.progress { border-radius: 10px; }

.status-confirmado { background-color: rgba(46, 216, 182, 0.1); }
.status-cancelado { background-color: rgba(255, 83, 112, 0.1); }
.status-pendente { background-color: rgba(255, 182, 77, 0.1); }

.btn-outline-secondary.active {
    background-color: #6c757d !important;
    border-color: #6c757d !important;
}

/* Estilos para botões de filtro */
.btn-group .btn.active {
    background-color: #007bff !important;
    color: white !important;
    border-color: #007bff !important;
}

.btn-outline-success.active {
    background-color: #28a745 !important;
    border-color: #28a745 !important;
}

.btn-outline-warning.active {
    background-color: #ffc107 !important;
    border-color: #ffc107 !important;
    color: #212529 !important;
}

.btn-outline-danger.active {
    background-color: #dc3545 !important;
    border-color: #dc3545 !important;
}

.btn-outline-primary.active {
    background-color: #007bff !important;
    border-color: #007bff !important;
}

.btn-outline-info.active {
    background-color: #17a2b8 !important;
    border-color: #17a2b8 !important;
}

.btn-outline-secondary.active {
    background-color: #6c757d !important;
    border-color: #6c757d !important;
}

/* Estilos para análise de serviços */
.table-warning {
    background-color: rgba(255, 193, 7, 0.1);
}

.table-light {
    background-color: rgba(248, 249, 250, 0.5);
}

.table-danger {
    background-color: rgba(220, 53, 69, 0.1);
}

/* Animações para botões de ordenação */
.btn-group .btn {
    transition: all 0.3s ease;
}

.btn-group .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Estilos para cards de serviços */
.order-card .f-left {
    margin-right: 10px;
}

/* Responsividade para gráficos */
@media (max-width: 768px) {
    .card-block canvas {
        height: 200px !important;
    }
}

@media print {
    .btn-group, .card-header-right, .modal, .no-print {
        display: none !important;
    }
    .card { 
        border: 1px solid #ddd !important; 
        page-break-inside: avoid;
    }
}
</style>

<!-- Scripts -->
<script src="../files/assets/vendor/chart.js/chart.min.js"></script>
<script src="../files/assets/vendor/jspdf/jspdf.umd.min.js"></script>

<script>
// Variáveis globais para os dados
const dadosEvolucao = <?=json_encode(array_reverse($evolucao_mensal))?>;
const dadosProfissional = <?=json_encode($dados_grafico_profissional)?>;
const dadosDiario = <?=json_encode($dados_grafico_diario)?>;

// Inicializar gráficos se estiverem habilitados
<?php if ($mostrar_graficos == '1'): ?>
document.addEventListener('DOMContentLoaded', function() {
    inicializarGraficos();
});
<?php endif; ?>

// Inicializar filtros da tabela
document.addEventListener('DOMContentLoaded', function() {
    // Marcar o botão "Todos" como ativo por padrão
    const botaoTodos = document.querySelector('.btn-group .btn[onclick*="todos"]');
    if (botaoTodos) {
        botaoTodos.classList.add('active');
    }
});

function inicializarGraficos() {
    // Gráfico de Evolução Mensal
    if (dadosEvolucao.length > 0 && document.getElementById('graficoEvolucao')) {
        const ctx1 = document.getElementById('graficoEvolucao').getContext('2d');
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: dadosEvolucao.map(d => d.mes + '/' + d.ano),
                datasets: [{
                    label: 'Faturamento',
                    data: dadosEvolucao.map(d => d.total),
                    borderColor: '#4099ff',
                    backgroundColor: 'rgba(64, 153, 255, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'R$ ' + value.toLocaleString('pt-BR');
                            }
                        }
                    }
                }
            }
        });
    }

    // Gráfico por Profissional
    if (dadosProfissional.length > 0 && document.getElementById('graficoProfissional')) {
        const ctx2 = document.getElementById('graficoProfissional').getContext('2d');
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: dadosProfissional.map(d => d.nome),
                datasets: [{
                    data: dadosProfissional.map(d => d.valor),
                    backgroundColor: ['#4099ff', '#2ed8b6', '#FFB64D', '#FF5370', '#7c4dff']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    // Gráfico Diário
    if (dadosDiario.length > 0 && document.getElementById('graficoDiario')) {
        const ctx3 = document.getElementById('graficoDiario').getContext('2d');
        new Chart(ctx3, {
            type: 'bar',
            data: {
                labels: dadosDiario.map(d => d.data),
                datasets: [{
                    label: 'Faturamento Diário',
                    data: dadosDiario.map(d => d.valor),
                    backgroundColor: '#2ed8b6'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'R$ ' + value.toLocaleString('pt-BR');
                            }
                        }
                    }
                }
            }
        });
    }

    // Gráfico de Serviços
    if (dadosServicos.length > 0 && document.getElementById('graficoServicos')) {
        const ctx4 = document.getElementById('graficoServicos').getContext('2d');
        new Chart(ctx4, {
            type: 'bar',
            data: {
                labels: dadosServicos.map(d => d.nome.length > 15 ? d.nome.substring(0, 15) + '...' : d.nome),
                datasets: [{
                    label: 'Vendas',
                    data: dadosServicos.map(d => d.vendas),
                    backgroundColor: '#FFB64D'
                }]
            },
            options: {
                responsive: true,
                indexAxis: 'y',
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
}

// Funções de filtro e ação
function toggleGraficos() {
    const atual = new URLSearchParams(window.location.search).get('graficos') || '1';
    const novo = atual === '1' ? '0' : '1';
    const url = new URL(window.location);
    url.searchParams.set('graficos', novo);
    window.location.href = url.toString();
}

function abrirCalendario() {
    $('#calendarioModal').modal('show');
}

function setDataPeriodo(periodo) {
    const hoje = new Date();
    let dataInicio, dataFim;
    
    switch(periodo) {
        case 'hoje':
            dataInicio = dataFim = hoje.toISOString().split('T')[0];
            break;
        case 'ontem':
            const ontem = new Date(hoje.getTime() - 24*60*60*1000);
            dataInicio = dataFim = ontem.toISOString().split('T')[0];
            break;
        case 'semana':
            const inicioSemana = new Date(hoje.setDate(hoje.getDate() - hoje.getDay()));
            const fimSemana = new Date(hoje.setDate(inicioSemana.getDate() + 6));
            dataInicio = inicioSemana.toISOString().split('T')[0];
            dataFim = fimSemana.toISOString().split('T')[0];
            break;
        case 'semana_passada':
            const inicioSemanaPassada = new Date(hoje.setDate(hoje.getDate() - hoje.getDay() - 7));
            const fimSemanaPassada = new Date(hoje.setDate(inicioSemanaPassada.getDate() + 6));
            dataInicio = inicioSemanaPassada.toISOString().split('T')[0];
            dataFim = fimSemanaPassada.toISOString().split('T')[0];
            break;
        case 'mes':
            dataInicio = new Date(hoje.getFullYear(), hoje.getMonth(), 1).toISOString().split('T')[0];
            dataFim = new Date(hoje.getFullYear(), hoje.getMonth() + 1, 0).toISOString().split('T')[0];
            break;
        case 'mes_passado':
            dataInicio = new Date(hoje.getFullYear(), hoje.getMonth() - 1, 1).toISOString().split('T')[0];
            dataFim = new Date(hoje.getFullYear(), hoje.getMonth(), 0).toISOString().split('T')[0];
            break;
        case 'trimestre':
            const trimestre = Math.floor(hoje.getMonth() / 3);
            dataInicio = new Date(hoje.getFullYear(), trimestre * 3, 1).toISOString().split('T')[0];
            dataFim = new Date(hoje.getFullYear(), trimestre * 3 + 3, 0).toISOString().split('T')[0];
            break;
        case 'ano':
            dataInicio = new Date(hoje.getFullYear(), 0, 1).toISOString().split('T')[0];
            dataFim = new Date(hoje.getFullYear(), 11, 31).toISOString().split('T')[0];
            break;
    }
    
    document.getElementById('modalDataInicio').value = dataInicio;
    document.getElementById('modalDataFim').value = dataFim;
}

function aplicarDataPeriodo() {
    const dataInicio = document.getElementById('modalDataInicio').value;
    const dataFim = document.getElementById('modalDataFim').value;
    
    if(dataInicio && dataFim) {
        const url = new URL(window.location);
        url.searchParams.set('data_inicio', dataInicio);
        url.searchParams.set('data_fim', dataFim);
        url.searchParams.delete('mes');
        url.searchParams.delete('ano');
        window.location.href = url.toString();
    }
}

function limparDatasPersonalizadas() {
    document.querySelector('input[name="data_inicio"]').value = '';
    document.querySelector('input[name="data_fim"]').value = '';
}

function limparMesAno() {
    // Não limpar automaticamente para permitir uso conjunto
}

function limparFiltros() {
    const url = new URL(window.location);
    url.search = '?pagina_nome=' + (new URLSearchParams(window.location.search).get('pagina_nome') || '');
    window.location.href = url.toString();
}

function filtrarTabela(tipo) {
    const tabela = document.getElementById('tabelaVendas');
    if (!tabela) return;
    
    const linhas = tabela.querySelectorAll('tbody tr.linha-status');
    
    // Remover classe ativa de todos os botões
    const botoes = document.querySelectorAll('.card-header-right .btn-group .btn');
    botoes.forEach(btn => btn.classList.remove('active'));
    
    // Adicionar classe ativa ao botão clicado
    if (event && event.target) {
        event.target.classList.add('active');
    }
    
    let visíveis = 0;
    
    // Filtrar linhas
    linhas.forEach(function(linha) {
        const statusLinha = linha.getAttribute('data-status');
        
        if(tipo === 'todos') {
            linha.style.display = '';
            visíveis++;
        } else {
            if(statusLinha === tipo) {
                linha.style.display = '';
                visíveis++;
            } else {
                linha.style.display = 'none';
            }
        }
    });
    
    // Atualizar contador na interface
    const titulo = document.getElementById('tituloTabelaVendas');
    if (titulo) {
        const total = <?=$total_encontrados?>;
        if (tipo === 'todos') {
            titulo.textContent = `Histórico de Vendas (${total} registros)`;
        } else {
            titulo.textContent = `Histórico de Vendas (${visíveis} de ${total} registros - ${tipo}s)`;
        }
    }
}

function exportarPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    
    // Título
    doc.setFontSize(18);
    doc.text('Relatório Financeiro Completo', 20, 20);
    
    // Período
    doc.setFontSize(12);
    doc.text('Período: <?=!empty($data_inicio) && !empty($data_fim) ? date("d/m/Y", strtotime($data_inicio)) . " a " . date("d/m/Y", strtotime($data_fim)) : getNomeMes($mes_atual) . "/" . $ano_atual?>', 20, 30);
    
    // KPIs Financeiros
    doc.setFontSize(14);
    doc.text('KPIs Financeiros:', 20, 45);
    doc.setFontSize(10);
    doc.text('• Faturamento Total: R$ <?=number_format($total_geral, 2, ",", ".")?>', 25, 55);
    doc.text('• Ticket Médio: R$ <?=number_format($ticket_medio_geral, 2, ",", ".")?>', 25, 62);
    doc.text('• Total de Atendimentos: <?=$total_encontrados?>', 25, 69);
    doc.text('• Faturamento Hoje: R$ <?=number_format($total_dia_atual, 2, ",", ".")?>', 25, 76);
    
    // Status dos Agendamentos
    doc.setFontSize(14);
    doc.text('Status dos Agendamentos:', 20, 90);
    doc.setFontSize(10);
    doc.text('• Confirmados: <?=$agendamentos_confirmados?>', 25, 100);
    doc.text('• Pendentes: <?=$agendamentos_pendentes?>', 25, 107);
    doc.text('• Cancelados: <?=$agendamentos_cancelados?>', 25, 114);
    
    // Análise de Serviços
    doc.setFontSize(14);
    doc.text('Análise de Serviços:', 20, 128);
    doc.setFontSize(10);
    doc.text('• Serviço Mais Vendido: <?=htmlspecialchars($servico_mais_vendido["nome"])?> (<?=$servico_mais_vendido["vendas"]?> vendas)', 25, 138);
    doc.text('• Faturamento do Top Serviço: R$ <?=number_format($servico_mais_vendido["faturamento"], 2, ",", ".")?>', 25, 145);
    doc.text('• Serviço Menos Vendido: <?=htmlspecialchars($servico_menos_vendido["nome"])?> (<?=$servico_menos_vendido["vendas"]?> vendas)', 25, 152);
    doc.text('• Total de Serviços Ativos: <?=count($servicos_vendidos)?>', 25, 159);
    
    // Melhor Profissional
    doc.setFontSize(14);
    doc.text('Melhor Profissional:', 20, 173);
    doc.setFontSize(10);
    doc.text('• Nome: <?=htmlspecialchars($melhor_profissional["nome"])?>', 25, 183);
    doc.text('• Faturamento: R$ <?=number_format($melhor_profissional["valor"], 2, ",", ".")?>', 25, 190);
    
    // Rodapé
    doc.setFontSize(8);
    doc.text('Relatório gerado em: <?=date("d/m/Y H:i:s")?>', 20, 280);
    
    // Salvar
    doc.save('relatorio-financeiro-completo-<?=date("Y-m-d")?>.pdf');
}

function exportarCSV() {
    let csv = 'RELATÓRIO FINANCEIRO COMPLETO\n';
    csv += 'Período,<?=!empty($data_inicio) && !empty($data_fim) ? date("d/m/Y", strtotime($data_inicio)) . " a " . date("d/m/Y", strtotime($data_fim)) : getNomeMes($mes_atual) . "/" . $ano_atual?>\n\n';
    
    csv += 'KPIs FINANCEIROS\n';
    csv += 'Faturamento Total,R$ <?=number_format($total_geral, 2, ",", ".")?>\n';
    csv += 'Ticket Médio,R$ <?=number_format($ticket_medio_geral, 2, ",", ".")?>\n';
    csv += 'Total de Atendimentos,<?=$total_encontrados?>\n';
    csv += 'Faturamento Hoje,R$ <?=number_format($total_dia_atual, 2, ",", ".")?>\n\n';
    
    csv += 'STATUS DOS AGENDAMENTOS\n';
    csv += 'Confirmados,<?=$agendamentos_confirmados?>\n';
    csv += 'Pendentes,<?=$agendamentos_pendentes?>\n';
    csv += 'Cancelados,<?=$agendamentos_cancelados?>\n\n';
    
    csv += 'ANÁLISE DE SERVIÇOS\n';
    csv += 'Serviço,Vendas,Faturamento,Ticket Médio,Duração,Categoria\n';
    
    // Adicionar dados dos serviços
    servicosDetalhados.forEach(function(servico) {
        csv += `"${servico.nome}",${servico.total_vendas},"R$ ${servico.faturamento_total.toLocaleString('pt-BR')}","R$ ${servico.ticket_medio.toLocaleString('pt-BR')}",${servico.duracao_minutos}min,"${servico.categoria || '-'}"\n`;
    });
    
    csv += '\nDETALHES POR PROFISSIONAL\n';
    csv += 'Profissional,Faturamento,Atendimentos,Confirmados,Cancelados\n';
    
    // Adicionar dados dos profissionais
    <?php foreach($vendas_profissional as $nome => $dados): ?>
    csv += '"<?=addslashes($nome)?>","R$ <?=number_format($dados["total"], 2, ",", ".")?>",<?=$dados["quantidade"]?>,<?=$dados["confirmados"]?>,<?=$dados["cancelados"]?>\n';
    <?php endforeach; ?>
    
    // Download do CSV
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', 'relatorio-financeiro-<?=date("Y-m-d")?>.csv');
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function ordenarServicos(tipo) {
    const tabela = document.getElementById('tabelaServicos');
    if (!tabela) return;
    
    const tbody = tabela.querySelector('tbody');
    const linhas = Array.from(tbody.querySelectorAll('tr'));
    
    // Remover classe ativa de todos os botões
    const botoes = document.querySelectorAll('.card-header-right .btn-group .btn');
    botoes.forEach(btn => btn.classList.remove('active'));
    
    // Adicionar classe ativa ao botão clicado
    if (event && event.target) {
        event.target.classList.add('active');
    }
    
    // Ordenar linhas
    linhas.sort((a, b) => {
        let valorA, valorB;
        
        switch(tipo) {
            case 'vendas':
                valorA = parseInt(a.dataset.vendas) || 0;
                valorB = parseInt(b.dataset.vendas) || 0;
                break;
            case 'faturamento':
                valorA = parseFloat(a.dataset.faturamento) || 0;
                valorB = parseFloat(b.dataset.faturamento) || 0;
                break;
            case 'ticket':
                valorA = parseFloat(a.dataset.ticket) || 0;
                valorB = parseFloat(b.dataset.ticket) || 0;
                break;
            default:
                return 0;
        }
        
        return valorB - valorA; // Ordem decrescente
    });
    
    // Reordenar as linhas na tabela
    linhas.forEach((linha, index) => {
        // Atualizar ranking
        const celulaRanking = linha.querySelector('td:first-child');
        if (index === 0) {
            celulaRanking.innerHTML = '<i class="feather icon-award text-warning"></i>';
        } else if (index === 1) {
            celulaRanking.innerHTML = '<i class="feather icon-award text-secondary"></i>';
        } else if (index === 2) {
            celulaRanking.innerHTML = '<i class="feather icon-award text-c-brown"></i>';
        } else {
            celulaRanking.textContent = (index + 1) + 'º';
        }
        
        tbody.appendChild(linha);
    });
}

function verDetalhes(servicoId) {
    const conteudo = `
        <div class="text-center">
            <h5>Detalhes do Atendimento</h5>
            <p><strong>ID do Serviço:</strong> ${servicoId}</p>
            <p class="text-muted">Carregando informações detalhadas...</p>
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Carregando...</span>
            </div>
        </div>
    `;
    document.getElementById('detalhesConteudo').innerHTML = conteudo;
    $('#detalhesModal').modal('show');
    
    // Simular carregamento (aqui você faria uma requisição AJAX real)
    setTimeout(() => {
        document.getElementById('detalhesConteudo').innerHTML = `
            <div>
                <p><strong>ID do Serviço:</strong> ${servicoId}</p>
                <p><strong>Status:</strong> <span class="badge badge-success">Confirmado</span></p>
                <p><strong>Data:</strong> ${new Date().toLocaleDateString('pt-BR')}</p>
                <p><strong>Observações:</strong> Atendimento realizado com sucesso.</p>
            </div>
        `;
    }, 1500);
}
</script>

<?php include 'footer.php'; ?>