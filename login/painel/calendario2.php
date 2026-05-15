<?php
session_start();
include 'funcoes.php';

if(!isset($_SESSION['login'])) {
VaiPara('login.php');
} 
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

$stmt_busca_usuario = $conn->prepare("SELECT * FROM login WHERE login = ?");
$stmt_busca_usuario->bind_param("s", $login);
$stmt_busca_usuario->execute();
$query_busca_usuario = $stmt_busca_usuario->get_result();
$total_busca_usuario = $query_busca_usuario->num_rows;

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
$sql = "SELECT * FROM agendamento WHERE login = ? AND data = ? ORDER BY horario ASC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ss", $login, $data_selecionada);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Fechar a conexão após obter os resultados
mysqli_close($conn);

?>

    <div class="page-container">
        <a href="javascript:history.back()" class="back-button">
            <span class="material-icons">arrow_back</span>
            Voltar ao Calendário
        </a>

        <div class="header">
            <h1>Agendamentos do Dia</h1>
            
            <form method="GET" action="" class="date-filter">
                <label for="data">Selecione uma data:</label>
                <input type="date" name="data" id="data" class="date-input" value="<?= htmlspecialchars($_GET['data'] ?? date('Y-m-d')) ?>">
                <button type="submit" class="btn-search">
                    <span class="material-icons">search</span>
                    Buscar
                </button>
            </form>
        </div>

        <div class="appointments-container">
            <div class="appointments-header">
                <div class="date-display">
                    <?php
                    $data_formatada = date('d/m/Y', strtotime($data_selecionada));
                    $dia_semana = date('w', strtotime($data_selecionada));
                    $nomes_dias = ['Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'];
                    echo $nomes_dias[$dia_semana] . ', ' . $data_formatada;
                    ?>
                </div>
                <div class="appointments-count">
                    <?php
                    $total_agendamentos = mysqli_num_rows($result);
                    echo $total_agendamentos . ' agendamento' . ($total_agendamentos != 1 ? 's' : '') . ' encontrado' . ($total_agendamentos != 1 ? 's' : '');
                    ?>
                </div>
            </div>

            <div class="appointments-grid">
                <?php
                // Verificar se há resultados
                if (mysqli_num_rows($result) > 0) {
                    // Loop através dos resultados
                    while ($row = mysqli_fetch_assoc($result)) {
                        // Extrair os dados
                        $id = $row['id'];
                        $cliente_nome = $row['cliente_nome'];
                        $servico = $row['profissional_cargo'];
                        $profissional_nome = $row['profissional_nome'];
                        $data = date('d/m/Y', strtotime($row['data']));
                        $horario = $row['horario'];
                        $cliente_telefone = preg_replace('/\D/', '', $row['cliente_telefone']);
                        $whatsapp_link = "https://wa.me/" . $cliente_telefone;
                        $status = $row['confirmacao'];
                        
                        // Definir status de confirmação
                        if ($status == '1') {
                            $confirmacao_class = 'status-confirmed';
                            $confirmacao_text = 'Confirmado';
                            $confirmacao_icon = 'check_circle';
                        } elseif ($status == '2') {
                            $confirmacao_class = 'status-not-confirmed';
                            $confirmacao_text = 'Não Confirmado';
                            $confirmacao_icon = 'cancel';
                        } else {
                            $confirmacao_class = 'status-no-response';
                            $confirmacao_text = 'Sem Resposta';
                            $confirmacao_icon = 'help';
                        }

                        echo '<div class="appointment-card">';
                        
                        echo '<div class="appointment-header">';
                        echo '<div class="client-info">';
                        echo '<div class="client-name">' . htmlspecialchars($cliente_nome) . '</div>';
                        echo '<div class="service-name">' . htmlspecialchars($servico) . '</div>';
                        echo '</div>';
                        echo '<div class="appointment-time">';
                        echo '<span class="material-icons">schedule</span>';
                        echo htmlspecialchars($horario);
                        echo '</div>';
                        echo '</div>';

                        echo '<div class="appointment-details">';
                        echo '<div class="detail-item">';
                        echo '<span class="material-icons detail-icon">person</span>';
                        echo '<span class="detail-text">Profissional: ' . htmlspecialchars($profissional_nome) . '</span>';
                        echo '</div>';
                        echo '<div class="detail-item">';
                        echo '<span class="material-icons detail-icon">event</span>';
                        echo '<span class="detail-text">Data: ' . $data . '</span>';
                        echo '</div>';
                        echo '</div>';

                        echo '<div class="appointment-actions">';
                        echo '<div class="status-badge ' . $confirmacao_class . '">';
                        echo '<span class="material-icons" style="font-size: 16px; margin-right: 5px;">' . $confirmacao_icon . '</span>';
                        echo $confirmacao_text;
                        echo '</div>';
                        
                        echo '<div class="action-buttons">';
                        echo '<a href="' . htmlspecialchars($whatsapp_link) . '" target="_blank" class="btn-whatsapp">';
                        echo '<span class="material-icons">chat</span>';
                        echo 'WhatsApp';
                        echo '</a>';
                        echo '<a href="cancelar_agendamento.php?id=' . $id . '" class="btn-cancel" onclick="return confirm(\'Tem certeza que deseja cancelar este agendamento?\')">';
                        echo '<span class="material-icons">cancel</span>';
                        echo 'Cancelar';
                        echo '</a>';
                        echo '</div>';
                        echo '</div>';
                        
                        echo '</div>';
                    }
                } else {
                    // Se não houver agendamentos
                    echo '<div class="no-appointments">';
                    echo '<div class="material-icons no-appointments-icon">event_busy</div>';
                    echo '<h3>Nenhum agendamento encontrado</h3>';
                    echo '<p>Não há agendamentos para esta data.</p>';
                    echo '</div>';
                }
                ?>

<?php include 'footer.php'; ?>