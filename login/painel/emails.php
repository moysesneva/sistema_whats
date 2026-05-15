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

if($tipo != 1){
    VaiPara('login.php');
}

?>

<?php
// Processar formulário
if(isset($_POST['salvar'])) {
    $email = trim($_POST['email']);
    $senha_app = trim($_POST['senha_app']);
    $smtp_host = trim($_POST['smtp_host']);
    $smtp_port = (int)$_POST['smtp_port'];
    $smtp_secure = trim($_POST['smtp_secure']);
    
    // Verifica se já existe configuração
    $sql_check = "SELECT id FROM email_config WHERE login = ?";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "s", $login);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);
    
    if(mysqli_num_rows($result_check) > 0) {
        // Atualiza configuração existente
        $sql = "UPDATE email_config 
                SET email = ?, senha_app = ?, smtp_host = ?, smtp_port = ?, smtp_secure = ? 
                WHERE login = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssiss", 
            $email, $senha_app, $smtp_host, $smtp_port, $smtp_secure, $login);
    } else {
        // Insere nova configuração
        $sql = "INSERT INTO email_config 
                (login, email, senha_app, smtp_host, smtp_port, smtp_secure) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssis", 
            $login, $email, $senha_app, $smtp_host, $smtp_port, $smtp_secure);
    }
    
    if(mysqli_stmt_execute($stmt)) {
        $mensagem = "Configurações de email salvas com sucesso!";
        $tipo_mensagem = "success";
    } else {
        $mensagem = "Erro ao salvar configurações de email.";
        $tipo_mensagem = "danger";
    }
}

// Função para testar as configurações de email
if(isset($_POST['testar'])) {
    require 'PHPMailer/PHPMailer.php';
    require 'PHPMailer/SMTP.php';
    require 'PHPMailer/Exception.php';

    $sql_config = "SELECT * FROM email_config WHERE login = ?";
    $stmt_config = mysqli_prepare($conn, $sql_config);
    mysqli_stmt_bind_param($stmt_config, "s", $login);
    mysqli_stmt_execute($stmt_config);
    $result_config = mysqli_stmt_get_result($stmt_config);
    $config = mysqli_fetch_assoc($result_config);

    if($config) {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $config['smtp_host'];
            $mail->SMTPAuth = true;
            $mail->Username = $config['email'];
            $mail->Password = $config['senha_app'];
            $mail->SMTPSecure = $config['smtp_secure'];
            $mail->Port = $config['smtp_port'];
            $mail->CharSet = 'UTF-8';

            $mail->setFrom($config['email'], 'Teste de Configuração');
            $mail->addAddress($config['email']);

            $mail->isHTML(true);
            $mail->Subject = 'Teste de Configuração de Email';
            $mail->Body = 'Se você recebeu este email, suas configurações de SMTP estão funcionando corretamente!';

            $mail->send();
            $mensagem = "Email de teste enviado com sucesso! Verifique sua caixa de entrada.";
            $tipo_mensagem = "success";
        } catch (Exception $e) {
            $mensagem = "Erro ao enviar email de teste: " . $mail->ErrorInfo;
            $tipo_mensagem = "danger";
        }
    }
}

// Buscar configuração atual
$sql_config = "SELECT * FROM email_config WHERE login = ?";
$stmt_config = mysqli_prepare($conn, $sql_config);
mysqli_stmt_bind_param($stmt_config, "s", $login);
mysqli_stmt_execute($stmt_config);
$result_config = mysqli_stmt_get_result($stmt_config);
$config = mysqli_fetch_assoc($result_config);
?>
<?php include 'header.php'; ?>

                                    <div class="page-header">
                                        <div class="row align-items-end">
                                            <div class="col-lg-8">
                                                <div class="page-header-title">
                                                    <div class="d-inline">
                                                        <h4>Configuração de Email</h4>
                                                        <span>Configure seu email para receber notificações do sistema</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="page-body">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h5>Configurações do Servidor SMTP</h5>
                                                        <span>Preencha os dados do seu servidor de email</span>
                                                    </div>
                                                    <div class="card-block">
                                                        <?php if(isset($mensagem)): ?>
                                                        <div class="alert alert-<?php echo $tipo_mensagem; ?> alert-dismissible fade show">
                                                            <?php echo $mensagem; ?>
                                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <?php endif; ?>

                                                        <form method="post" class="form-material">
                                                            <div class="form-group form-default">
                                                                <input type="email" name="email" class="form-control" required value="<?php echo $config['email'] ?? ''; ?>">
                                                                <span class="form-bar"></span>
                                                                <label class="float-label">Email</label>
                                                                <small class="text-muted">Use seu email do Gmail</small>
                                                            </div>

                                                            <div class="form-group form-default">
                                                                <input type="password" name="senha_app" class="form-control" required>
                                                                <span class="form-bar"></span>
                                                                <label class="float-label">Senha de App</label>
                                                                <small class="text-muted">
                                                                    Use uma senha de app do Gmail. 
                                                                    <a href="https://support.google.com/accounts/answer/185833" target="_blank">
                                                                        Como criar uma senha de app?
                                                                    </a>
                                                                </small>
                                                            </div>

                                                            <div class="form-group form-default">
                                                                <input type="text" name="smtp_host" class="form-control" required value="<?php echo $config['smtp_host'] ?? 'smtp.gmail.com'; ?>">
                                                                <span class="form-bar"></span>
                                                                <label class="float-label">Servidor SMTP</label>
                                                            </div>

                                                               <div class="row">
    <div class="col-sm-6">
        <div class="form-group form-default">
            <input type="number" name="smtp_port" class="form-control" required 
                   value="<?php echo $config['smtp_port'] ?? '587'; ?>">
            <span class="form-bar"></span>
            <label class="float-label">Porta SMTP</label>
        </div>
    </div>

    <div class="col-sm-6">
        <div class="form-group form-default">
            <select name="smtp_secure" class="form-control" required>
                <option value="tls" <?php echo ($config['smtp_secure'] ?? 'tls') == 'tls' ? 'selected' : ''; ?>>
                    TLS
                </option>
                <option value="ssl" <?php echo ($config['smtp_secure'] ?? '') == 'ssl' ? 'selected' : ''; ?>>
                    SSL
                </option>
            </select>
            <span class="form-bar"></span>
            <label class="float-label">Segurança</label>
        </div>
    </div>
</div>

                                                            <div class="form-group">
                                                                <button type="submit" name="salvar" class="btn btn-primary btn-round waves-effect waves-light">
                                                                    <i class="feather icon-save"></i> Salvar Configurações
                                                                </button>

                                                                <?php if($config): ?>
                                                               
                                                                <button type="button" class="btn btn-warning btn-round waves-effect waves-light" data-toggle="modal" data-target="#editConfigModal">
                                                                   <i class="feather icon-edit"></i> Editar Configurações
                                                                </button>
                                                                <?php endif; ?>
                                                            </div>
                                                        </form>

<?php include 'footer.php'; ?>