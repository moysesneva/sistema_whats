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

#print_r($_REQUEST);

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
    $google_cal  = $rows_usuarios['google_cal'];

}
include 'menu.php';


if($total_busca_usuario != 1){
    VaiPara('login.php');
}
if($autorizado != 2){
 VaiPara('desbloquar.php');
}




?>

<?php
$stmt_check = mysqli_prepare($conn, "SELECT google_cal FROM login WHERE login = ?");
mysqli_stmt_bind_param($stmt_check, "s", $login);
mysqli_stmt_execute($stmt_check);
$result_check = mysqli_stmt_get_result($stmt_check);
$google_cal_atual = '';

if ($result_check && mysqli_num_rows($result_check) > 0) {
    $row = mysqli_fetch_assoc($result_check);
    $google_cal_atual = $row['google_cal'];
}

if (isset($_POST['apagar_integracao'])) {
    $stmt_apagar = mysqli_prepare($conn, "UPDATE login SET google_cal = '' WHERE login = ?");
    mysqli_stmt_bind_param($stmt_apagar, "s", $login);
    $query = mysqli_stmt_execute($stmt_apagar);

    if ($query) {
        echo '<script nonce="'. ($GLOBALS['csp_nonce'] ?? '') .'">
            document.addEventListener("DOMContentLoaded", function() {
                mostrarSucesso("✅ Integração removida com sucesso!");
            });
        </script>';
    } else {
        echo '<script nonce="'. ($GLOBALS['csp_nonce'] ?? '') .'">
            document.addEventListener("DOMContentLoaded", function() {
                mostrarErro("❌ Erro ao remover integração. Tente novamente.");
            });
        </script>';
    }
}

if (isset($_POST['email_gmail'])) {
    $email_gmail = trim($_POST['email_gmail']);

    if (filter_var($email_gmail, FILTER_VALIDATE_EMAIL) && 
        (strpos($email_gmail, '@gmail.com') !== false || strpos($email_gmail, '@googlemail.com') !== false)) {

        $stmt_save = mysqli_prepare($conn, "UPDATE login SET google_cal = ? WHERE login = ?");
        mysqli_stmt_bind_param($stmt_save, "ss", $email_gmail, $login);
        $query = mysqli_stmt_execute($stmt_save);

        if ($query) {
            echo '<script nonce="'. ($GLOBALS['csp_nonce'] ?? '') .'">
                document.addEventListener("DOMContentLoaded", function() {
                    mostrarSucesso("✅ Email do Gmail salvo com sucesso!");
                });
            </script>';
        } else {
            echo '<script nonce="'. ($GLOBALS['csp_nonce'] ?? '') .'">
                document.addEventListener("DOMContentLoaded", function() {
                    mostrarErro("❌ Erro ao salvar email. Tente novamente.");
                });
            </script>';
        }
    } else {
        echo '<script nonce="'. ($GLOBALS['csp_nonce'] ?? '') .'">
            document.addEventListener("DOMContentLoaded", function() {
                mostrarErro("❌ Por favor, insira um email válido do Gmail.");
            });
        </script>';
    }
}
?>

<?php
if (isset($_POST['email_gmail']) || isset($_POST['apagar_integracao'])) {
    $_POST['email_gmail'] = false;
    echo '<script nonce="'. ($GLOBALS['csp_nonce'] ?? '') .'">window.location.href = window.location.pathname;</script>';
    exit;
}
?>

<?php include 'header.php'; ?>
