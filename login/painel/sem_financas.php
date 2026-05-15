<?php
require_once __DIR__ . '/auth_guard.php';
session_start();
include 'funcoes.php';

if(!isset($_SESSION['login'])) {
VaiPara('login.php');
} 
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

    <style>
    body {
            background-color: #f5f5f5;
            font-family: 'Arial', sans-serif;
      
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
    
      .aviso {
            background-color: #ffffff;
            border-left: 6px solid #FF5722;
            padding: 20px 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            text-align: center;
        }

        .aviso h2 {
            color: #333333;
            margin: 0;
            font-size: 18px;
        }

        .aviso span {
            color: #FF5722;
            font-weight: bold;
        }
    </style>

    <div class="aviso">
        <h2>🚫 <span>Não há dados de agendamento nem dados financeiros</span> para este usuário ainda.</h2>

<?php include 'footer.php'; ?>