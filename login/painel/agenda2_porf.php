<?php
session_start();
include 'funcoes.php';

if(!isset($_SESSION['login'])) {
VaiPara('login.php');
} 
#error_reporting(0);
#ini_set("display_errors", 0 );
#$_SESSION['tipo_menu'] = 1;
$login = $_SESSION['login'];


include 'conn.php';
include 'config_dados.php';





include 'estilo.php';

include 'css_de_icones.php';



if (isset($_GET['pagina_nome'])) {
$pagina_nome_recebe = $_GET['pagina_nome'];
}else{
$pagina_nome_recebe = 0;    
}


$sql_busca_usuario = "SELECT * FROM login WHERE login = '$login'";
$query_busca_usuario = mysqli_query($conn, $sql_busca_usuario);
$total_busca_usuario = mysqli_num_rows($query_busca_usuario);

while($rows_usuarios = mysqli_fetch_array($query_busca_usuario)) {
    $nome  = Priletra($rows_usuarios['nome']);
    $img_perfil  = $rows_usuarios['perfil_img'];
    $autorizado  = $rows_usuarios['autorizado'];
    $tipo  = $rows_usuarios['tipo'];

}


$sql_busca_prof = "SELECT * FROM  profissional WHERE telefone = '$login'";
$sql_busca_profs = mysqli_query($conn, $sql_busca_prof);
$total_busca_profs = mysqli_num_rows($sql_busca_profs);

while($rows_usuarios = mysqli_fetch_array($sql_busca_profs)) {
    $id_profissional  = $rows_usuarios['id'];

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




?>
<?php include 'header.php'; ?>




 <link rel="stylesheet" href="../files/assets/vendor/font-awesome-6/css/all.min.css">

   
   
   
   
   <?php

// Função para conectar ao banco de dados
function conectarDB() {
    include 'conn.php';
    return $conn;
}

// Obter o valor de 'login' da sessão ou de outro local
if (isset($_SESSION['login'])) {
    $login = $_SESSION['login'];
} else {
    // Se 'login' não estiver definido, redirecionar ou exibir uma mensagem de erro
    die("Usuário não logado.");
}

// Obter a data selecionada pelo usuário ou usar a data atual como padrão
$data_selecionada = $_GET['data'] ?? date('Y-m-d');

// Preparar a consulta SQL para buscar agendamentos
$conn = conectarDB();

$sql = "SELECT * FROM agendamento WHERE	id_profissional = '$id_profissional' ORDER BY horario ASC";
$result = mysqli_query($conn, $sql);

// Inicializar o array de eventos
$eventos = [];

// Loop através dos resultados e organizar os eventos por data
while ($row = $result->fetch_assoc()) {
    // Extrair a data e concatenar as informações
    $data = date('Y-m-d', strtotime($row['data'])); // Formato Y-m-d
    $horario = $row['horario'];
    $servico = $row['profissional_cargo']; // Serviço com quebra de linha
    
    // Inicializar o array para a data se ainda não existir
    if (!isset($eventos[$data])) {
        $eventos[$data] = [];
    }
    
    // Adicionar o serviço ao array da data
    $eventos[$data][] = htmlspecialchars($horario." - ".$servico); // Usar htmlspecialchars para segurança
}

/***********************************************
 * LÓGICA DO CALENDÁRIO
 ***********************************************/
date_default_timezone_set('America/Sao_Paulo');
setlocale(LC_TIME, 'pt_BR.utf8', 'Portuguese_Brazil');

// Verifica se recebeu mês/ano via GET
$mes = isset($_GET['m']) ? (int)$_GET['m'] : date('n');
$ano = isset($_GET['y']) ? (int)$_GET['y'] : date('Y');

// Ajusta se o mês ficar fora do intervalo [1..12]
if ($mes < 1) {
    $mes = 12;
    $ano--;
} elseif ($mes > 12) {
    $mes = 1;
    $ano++;
}

// Total de dias do mês e dia da semana do 1º dia
$qtdDias = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
$primeiroDiaSemana = date('w', strtotime("$ano-$mes-1"));

// Filtra apenas eventos do mês/ano atual
$eventosDoMes = [];
$eventosOriginais = []; // Array para manter a contagem original
foreach ($eventos as $data => $listaEventos) {
    [$y, $m, $d] = explode('-', $data);
    if ((int)$y === $ano && (int)$m === $mes) {
        $eventosOriginais[$data] = $listaEventos; // Guarda todos os eventos
        $eventosDoMes[$data] = array_slice($listaEventos, 0, 3); // Limita a 3 para exibição
    }
}

function obterNomeMes($mes) {
    $meses = [
        1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
        5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
        9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
    ];
    return $meses[$mes] ?? '';
}

function obterCorEvento($index) {
    $cores = [
        '#1976d2', '#388e3c', '#f57c00', '#d32f2f', 
        '#7b1fa2', '#00796b', '#fbc02d', '#5d4037'
    ];
    return $cores[$index % count($cores)];
}
?>

    <div class="calendar-container">
        <div class="calendar-header">
            <div class="calendar-nav">
                <a class="nav-btn" href="?m=<?php echo $mes - 1; ?>&y=<?php echo $ano; ?>">
                    <span class="material-icons">chevron_left</span>
                    Anterior
                </a>
                
                <h1 class="month-title"><?php echo obterNomeMes($mes) . " " . $ano; ?></h1>
                
                <a class="nav-btn" href="?m=<?php echo $mes + 1; ?>&y=<?php echo $ano; ?>">
                    Próximo
                    <span class="material-icons">chevron_right</span>
                </a>
            </div>
        </div>

        <div class="calendar-grid">
            <div class="weekdays">
                <div class="weekday">Dom</div>
                <div class="weekday">Seg</div>
                <div class="weekday">Ter</div>
                <div class="weekday">Qua</div>
                <div class="weekday">Qui</div>
                <div class="weekday">Sex</div>
                <div class="weekday">Sáb</div>
            </div>

            <div class="days-grid">
                <?php
                $dia = 1;
                $totalCells = 42; // 6 semanas × 7 dias
                
                for ($i = 0; $i < $totalCells; $i++) {
                    if ($i < $primeiroDiaSemana) {
                        echo "<div class='day-cell empty-day'></div>";
                    } elseif ($dia <= $qtdDias) {
                        $dataAtual = sprintf('%04d-%02d-%02d', $ano, $mes, $dia);
                        $isToday = ($dataAtual == date('Y-m-d'));
                        $todayClass = $isToday ? 'today' : '';
                        
                        echo "<div class='day-cell $todayClass'>";
                        echo "<div class='day-number'>$dia</div>";
                        
                        if (isset($eventosDoMes[$dataAtual])) {
                            echo "<div class='events-container'>";
                            $eventCount = 0;
                            foreach ($eventosDoMes[$dataAtual] as $evento) {
                                if ($eventCount < 3) {
                                    echo "<div class='event' title='" . htmlspecialchars($evento) . "'>$evento</div>";
                                    $eventCount++;
                                }
                            }
                            
                            // Mostrar quantos eventos a mais existem usando o array original
                            $totalEventos = count($eventosOriginais[$dataAtual] ?? []);
                            if ($totalEventos > 3) {
                                $maisEventos = $totalEventos - 3;
                                echo "<div class='more-events'>+$maisEventos mais</div>";
                            }
                            echo "</div>";
                        }
                        
                        echo "<a href='calendario2_prof.php?data=$dataAtual' class='day-link'></a>";
                        echo "</div>";
                        $dia++;
                    } else {
                        echo "<div class='day-cell empty-day'></div>";
                    }
                }
                ?>
            </div>
        </div>
    </div>

    <script>
        // Adicionar animações suaves ao carregar
        document.addEventListener('DOMContentLoaded', function() {
            const dayCells = document.querySelectorAll('.day-cell:not(.empty-day)');
            dayCells.forEach((cell, index) => {
                cell.style.opacity = '0';
                cell.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    cell.style.transition = 'all 0.3s ease';
                    cell.style.opacity = '1';
                    cell.style.transform = 'translateY(0)';
                }, index * 50);
            });
        });
    </script>

<?php include 'footer.php'; ?>