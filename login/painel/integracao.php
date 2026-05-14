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





include 'estilo.php';

include 'css_de_icones.php';

#print_r($_REQUEST);

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
    $google_cal  = $rows_usuarios['google_cal'];

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

<?php
// Buscar email integrado atual
$sql_check = "SELECT google_cal FROM login WHERE login = '$login'";
$result_check = mysqli_query($conn, $sql_check);
$google_cal_atual = '';

if ($result_check && mysqli_num_rows($result_check) > 0) {
    $row = mysqli_fetch_assoc($result_check);
    $google_cal_atual = $row['google_cal'];
}

// Processar remoção da integração
if (isset($_POST['apagar_integracao'])) {
    $sql = "UPDATE login SET google_cal = '' WHERE login = '$login'";
    $query = mysqli_query($conn, $sql);
    
    if ($query) {
        echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                mostrarSucesso("✅ Integração removida com sucesso!");
            });
        </script>';
    } else {
        echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                mostrarErro("❌ Erro ao remover integração. Tente novamente.");
            });
        </script>';
    }
}

// Processar confirmação do email
if (isset($_POST['email_gmail'])) {
    $email_gmail = mysqli_real_escape_string($conn, $_POST['email_gmail']);
    
    // Validar se é um email do Gmail
    if (filter_var($email_gmail, FILTER_VALIDATE_EMAIL) && 
        (strpos($email_gmail, '@gmail.com') !== false || strpos($email_gmail, '@googlemail.com') !== false)) {
        
        $sql = "UPDATE login SET google_cal = '$email_gmail' WHERE login = '$login'";
        $query = mysqli_query($conn, $sql);
        
        if ($query) {
            echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    mostrarSucesso("✅ Email do Gmail salvo com sucesso!");
                });
            </script>';
        } else {
            echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    mostrarErro("❌ Erro ao salvar email. Tente novamente.");
                });
            </script>';
        }
    } else {
        echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                mostrarErro("❌ Por favor, insira um email válido do Gmail.");
            });
        </script>';
    }
}
?>

<?php
if (isset($_POST['email_gmail']) || isset($_POST['apagar_integracao'])) {


    // (1) Atribui false (simulado, apenas para este momento)
    $_POST['email_gmail'] = false;

    // (2) Dá refresh na página SEM reenviar o POST
    echo '<script>window.location.href = window.location.pathname;</script>';
    exit;
}
?>

<?php include 'header.php'; ?>









<style>
.google-integration-container {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 40px;
    margin: 30px 0;
    box-shadow: 0 15px 35px rgba(102, 126, 234, 0.3);
    color: white;
    position: relative;
    overflow: hidden;
}

.google-integration-container::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="20" cy="20" r="1" fill="white" opacity="0.1"/><circle cx="80" cy="80" r="1" fill="white" opacity="0.1"/><circle cx="40" cy="60" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    pointer-events: none;
    z-index: 1;
}

.google-integration-content {
    position: relative;
    z-index: 2;
}

.google-header {
    text-align: center;
    margin-bottom: 35px;
}

.google-title {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
    font-size: 2.2rem;
    font-weight: 700;
    margin-bottom: 10px;
}

.google-calendar-icon {
    width: 60px;
    height: 60px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.google-subtitle {
    font-size: 1.1rem;
    opacity: 0.9;
    font-weight: 500;
}

.google-form-container {
    background: rgba(255, 255, 255, 0.15);
    border-radius: 15px;
    padding: 30px;
    backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.google-input-group {
    margin-bottom: 25px;
}

.google-label {
    display: block;
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 10px;
    color: rgba(255, 255, 255, 0.95);
}

.google-email-input {
    width: 100%;
    padding: 15px 20px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 12px;
    font-size: 1.1rem;
    background: rgba(255, 255, 255, 0.1);
    color: white;
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
}

.google-email-input::placeholder {
    color: rgba(255, 255, 255, 0.7);
}

.google-email-input:focus {
    outline: none;
    border-color: rgba(255, 255, 255, 0.6);
    background: rgba(255, 255, 255, 0.2);
    box-shadow: 0 0 20px rgba(255, 255, 255, 0.2);
}

.google-email-input.valid {
    border-color: #4ade80;
    box-shadow: 0 0 20px rgba(74, 222, 128, 0.3);
}

.google-email-input.invalid {
    border-color: #f87171;
    box-shadow: 0 0 20px rgba(248, 113, 113, 0.3);
}

.google-validation-message {
    margin-top: 10px;
    font-size: 0.9rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
    min-height: 20px;
}

.google-validation-message.valid {
    color: #4ade80;
}

.google-validation-message.invalid {
    color: #f87171;
}

.google-confirm-btn {
    width: 100%;
    padding: 15px 30px;
    background: linear-gradient(135deg, #4ade80 0%, #22d3ee 100%);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 5px 20px rgba(74, 222, 128, 0.4);
    opacity: 0;
    transform: scale(0.9);
    pointer-events: none;
}

.google-confirm-btn.show {
    opacity: 1;
    transform: scale(1);
    pointer-events: all;
}

.google-confirm-btn:hover {
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 8px 30px rgba(74, 222, 128, 0.5);
}

.google-confirm-btn:active {
    transform: translateY(-1px) scale(0.98);
}

.google-message {
    padding: 15px 20px;
    border-radius: 10px;
    margin-top: 20px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 10px;
    animation: slideIn 0.5s ease;
}

.google-message.success {
    background: rgba(74, 222, 128, 0.2);
    border: 1px solid rgba(74, 222, 128, 0.4);
    color: #4ade80;
}

.google-message.error {
    background: rgba(248, 113, 113, 0.2);
    border: 1px solid rgba(248, 113, 113, 0.4);
    color: #f87171;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.google-loading {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    border-top-color: white;
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.google-integrated-container {
    background: rgba(74, 222, 128, 0.15);
    border-radius: 15px;
    padding: 30px;
    backdrop-filter: blur(15px);
    border: 1px solid rgba(74, 222, 128, 0.3);
}

.google-integrated-header {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 20px;
}

.google-integrated-icon {
    width: 50px;
    height: 50px;
    background: rgba(74, 222, 128, 0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    border: 1px solid rgba(74, 222, 128, 0.4);
}

.google-integrated-info h3 {
    margin: 0;
    font-size: 1.3rem;
    font-weight: 600;
    color: #4ade80;
}

.google-integrated-info p {
    margin: 5px 0 0 0;
    font-size: 0.9rem;
    opacity: 0.8;
}

.google-current-email {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    padding: 15px;
    margin: 20px 0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.google-email-display {
    font-size: 1.1rem;
    font-weight: 500;
    color: white;
    flex: 1;
}

.google-email-icon {
    width: 35px;
    height: 35px;
    background: rgba(74, 222, 128, 0.3);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

.google-remove-btn {
    background: linear-gradient(135deg, #f87171 0%, #ef4444 100%);
    color: white;
    border: none;
    border-radius: 12px;
    padding: 12px 25px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(248, 113, 113, 0.4);
    display: flex;
    align-items: center;
    gap: 8px;
}

.google-remove-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(248, 113, 113, 0.5);
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
}

.google-remove-btn:active {
    transform: translateY(0);
}

.google-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-top: 25px;
}

.google-change-btn {
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
    color: white;
    border: none;
    border-radius: 12px;
    padding: 12px 25px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(251, 191, 36, 0.4);
    display: flex;
    align-items: center;
    gap: 8px;
}

.google-change-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(251, 191, 36, 0.5);
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

@media (max-width: 768px) {
    .google-integration-container {
        padding: 25px;
        margin: 20px 0;
    }
    
    .google-title {
        font-size: 1.8rem;
    }
    
    .google-calendar-icon {
        width: 50px;
        height: 50px;
        font-size: 1.5rem;
    }
    
    .google-form-container {
        padding: 20px;
    }
}
</style>

<div class="google-integration-container">
    <div class="google-integration-content">
        <div class="google-header">
            <h2 class="google-title">
                <div class="google-calendar-icon">📅</div>
                Integração com Google
            </h2>
            <p class="google-subtitle">Configure sua conta do Gmail para sincronização</p>
        </div>

        <?php if (!empty($google_cal_atual)): ?>
            <!-- Estado: Email já integrado -->
            <div class="google-integrated-container">
                <div class="google-integrated-header">
                    <div class="google-integrated-icon">✅</div>
                    <div class="google-integrated-info">
                        <h3>Integração Ativa</h3>
                        <p>Sua conta está conectada com o Google Calendar</p>
                    </div>
                </div>

                <div class="google-current-email">
                    <div class="google-email-icon">📧</div>
                    <div class="google-email-display"><?= htmlspecialchars($google_cal_atual) ?></div>
                </div>

                <div class="google-actions">
                    <button type="button" id="changeEmailBtn" class="google-change-btn">
                        🔄 Alterar Email
                    </button>
                    
                    <form method="post" style="display: inline;">
                        <button type="submit" name="apagar_integracao" class="google-remove-btn" 
                                onclick="return confirm('⚠️ Tem certeza que deseja remover a integração com o Google Calendar?')">
                            🗑️ Remover Integração
                        </button>
                    </form>
                </div>
            </div>

            <!-- Formulário para alterar email (inicialmente oculto) -->
            <div id="changeEmailForm" class="google-form-container" style="display: none; margin-top: 20px;">
                <form method="post" id="googleForm">
                    <div class="google-input-group">
                        <label for="emailGmail" class="google-label">Novo Email do Gmail</label>
                        <input 
                            type="email" 
                            id="emailGmail" 
                            name="email_gmail"
                            class="google-email-input" 
                            placeholder="exemplo@gmail.com"
                            autocomplete="email"
                            value="<?= htmlspecialchars($google_cal_atual) ?>"
                        >
                        <div id="validationMessage" class="google-validation-message"></div>
                    </div>

                    <div style="display: flex; gap: 15px;">
                        <button type="submit" id="confirmBtn" class="google-confirm-btn" style="opacity: 1; transform: scale(1); pointer-events: all;">
                            <span id="btnText">✓ Atualizar Email</span>
                            <span id="btnLoading" class="google-loading" style="display: none;"></span>
                        </button>
                        
                        <button type="button" id="cancelChangeBtn" class="google-remove-btn" style="background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);">
                            ❌ Cancelar
                        </button>
                    </div>
                </form>
            </div>

        <?php else: ?>
            <!-- Estado: Nenhum email integrado -->
            <form method="post" id="googleForm" class="google-form-container">
                <div class="google-input-group">
                    <label for="emailGmail" class="google-label">Email do Gmail</label>
                    <input 
                        type="email" 
                        id="emailGmail" 
                        name="email_gmail"
                        class="google-email-input" 
                        placeholder="exemplo@gmail.com"
                        autocomplete="email"
                    >
                    <div id="validationMessage" class="google-validation-message"></div>
                </div>

                <button type="submit" id="confirmBtn" class="google-confirm-btn">
                    <span id="btnText">✓ Confirmar Email</span>
                    <span id="btnLoading" class="google-loading" style="display: none;"></span>
                </button>
            </form>
        <?php endif; ?>

        <div id="messageContainer"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const emailInput = document.getElementById('emailGmail');
    const validationMessage = document.getElementById('validationMessage');
    const confirmBtn = document.getElementById('confirmBtn');
    const form = document.getElementById('googleForm');
    const btnText = document.getElementById('btnText');
    const btnLoading = document.getElementById('btnLoading');
    
    // Elementos para alterar email
    const changeEmailBtn = document.getElementById('changeEmailBtn');
    const changeEmailForm = document.getElementById('changeEmailForm');
    const cancelChangeBtn = document.getElementById('cancelChangeBtn');

    // Verificar se os elementos existem antes de adicionar event listeners
    if (emailInput && validationMessage && confirmBtn && form) {
        // Validação em tempo real
        emailInput.addEventListener('input', function() {
            const email = this.value.trim();
            validateGmailEmail(email);
        });

        // Validação ao perder o foco
        emailInput.addEventListener('blur', function() {
            const email = this.value.trim();
            if (email) {
                validateGmailEmail(email);
            }
        });

        // Submissão do formulário
        form.addEventListener('submit', function(e) {
            const email = emailInput.value.trim();
            
            if (!email || !isValidGmail(email)) {
                e.preventDefault();
                mostrarErro('❌ Por favor, insira um email válido do Gmail');
                return;
            }

            // Mostrar loading
            if (btnText && btnLoading) {
                btnText.style.display = 'none';
                btnLoading.style.display = 'inline-block';
                confirmBtn.disabled = true;
            }
        });
    }

    // Funcionalidade para alterar email
    if (changeEmailBtn && changeEmailForm) {
        changeEmailBtn.addEventListener('click', function() {
            changeEmailForm.style.display = 'block';
            this.style.display = 'none';
            
            // Auto-validar o email atual
            if (emailInput) {
                validateGmailEmail(emailInput.value.trim());
            }
        });
    }

    // Cancelar alteração
    if (cancelChangeBtn && changeEmailForm && changeEmailBtn) {
        cancelChangeBtn.addEventListener('click', function() {
            changeEmailForm.style.display = 'none';
            changeEmailBtn.style.display = 'inline-flex';
            
            // Limpar mensagens
            if (validationMessage) {
                validationMessage.innerHTML = '';
            }
            const messageContainer = document.getElementById('messageContainer');
            if (messageContainer) {
                messageContainer.innerHTML = '';
            }
        });
    }

    function validateGmailEmail(email) {
        if (!emailInput || !validationMessage || !confirmBtn) return;

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const isValidEmail = emailRegex.test(email);
        const isGmail = email.toLowerCase().includes('@gmail.com') || email.toLowerCase().includes('@googlemail.com');
        
        // Limpar classes anteriores
        emailInput.classList.remove('valid', 'invalid');
        validationMessage.classList.remove('valid', 'invalid');
        
        if (!email) {
            validationMessage.innerHTML = '';
            confirmBtn.classList.remove('show');
            return;
        }
        
        if (!isValidEmail) {
            emailInput.classList.add('invalid');
            validationMessage.classList.add('invalid');
            validationMessage.innerHTML = '❌ Formato de email inválido';
            confirmBtn.classList.remove('show');
        } else if (!isGmail) {
            emailInput.classList.add('invalid');
            validationMessage.classList.add('invalid');
            validationMessage.innerHTML = '❌ Por favor, use um email do Gmail (@gmail.com)';
            confirmBtn.classList.remove('show');
        } else {
            emailInput.classList.add('valid');
            validationMessage.classList.add('valid');
            validationMessage.innerHTML = '✅ Email do Gmail válido';
            confirmBtn.classList.add('show');
        }
    }

    function isValidGmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const isValidEmail = emailRegex.test(email);
        const isGmail = email.toLowerCase().includes('@gmail.com') || email.toLowerCase().includes('@googlemail.com');
        return isValidEmail && isGmail;
    }
});

function mostrarSucesso(mensagem) {
    const container = document.getElementById('messageContainer');
    if (container) {
        container.innerHTML = `<div class="google-message success">${mensagem}</div>`;
    }
}

function mostrarErro(mensagem) {
    const container = document.getElementById('messageContainer');
    if (container) {
        container.innerHTML = `<div class="google-message error">${mensagem}</div>`;
    }
    
    // Reset do botão
    const btnText = document.getElementById('btnText');
    const btnLoading = document.getElementById('btnLoading');
    const confirmBtn = document.getElementById('confirmBtn');
    
    if (btnText && btnLoading && confirmBtn) {
        btnText.style.display = 'inline';
        btnLoading.style.display = 'none';
        confirmBtn.disabled = false;
    }
}
</script>

<?php include 'footer.php'; ?>