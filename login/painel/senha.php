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




    <button class="back-button" onclick="history.back()" title="Voltar">
        <i class="material-icons">arrow_back</i>
    </button>

    <div class="password-container">
        <div class="header">
            <div class="header-icon">
                <i class="material-icons">lock_reset</i>
            </div>
            <h2>Atualizar Senha</h2>
            <p>Crie uma nova senha segura para sua conta</p>
        </div>

        <form id="formSenha" action="atualizar_senha.php" method="POST" onsubmit="return validarSenhas()">
            <!-- Campo para Nova Senha -->
            <div class="form-group">
                <label for="senha" class="form-label">
                    <i class="material-icons">lock</i>
                    Nova Senha
                </label>
                <div class="input-container">
                    <div class="input-prefix">
                        <i class="material-icons">lock</i>
                    </div>
                    <input type="password" class="form-control" id="senha" name="senha" 
                           placeholder="Digite sua nova senha" required 
                           oninput="checkPasswordStrength(this.value)">
                    <button type="button" class="password-toggle" onclick="togglePassword('senha')">
                        <i class="material-icons">visibility</i>
                    </button>
                </div>
                
                <!-- Indicador de Força da Senha -->
                <div class="password-strength" id="passwordStrength">
                    <div class="strength-bar" id="strengthBar"></div>
                </div>
                <div class="strength-text" id="strengthText"></div>

                <!-- Requisitos da Senha -->
                <div class="password-requirements" id="passwordRequirements">
                    <div class="requirements-title">Requisitos da senha:</div>
                    <div class="requirement" id="req-length">
                        <i class="material-icons">circle</i>
                        Pelo menos 8 caracteres
                    </div>
                    <div class="requirement" id="req-uppercase">
                        <i class="material-icons">circle</i>
                        Uma letra maiúscula
                    </div>
                    <div class="requirement" id="req-lowercase">
                        <i class="material-icons">circle</i>
                        Uma letra minúscula
                    </div>
                    <div class="requirement" id="req-number">
                        <i class="material-icons">circle</i>
                        Um número
                    </div>
                    <div class="requirement" id="req-special">
                        <i class="material-icons">circle</i>
                        Um caractere especial (!@#$%^&*)
                    </div>
                </div>
            </div>

            <!-- Campo para Confirmar Senha -->
            <div class="form-group">
                <label for="confirmar_senha" class="form-label">
                    <i class="material-icons">lock_clock</i>
                    Confirmar Nova Senha
                </label>
                <div class="input-container">
                    <div class="input-prefix">
                        <i class="material-icons">lock_clock</i>
                    </div>
                    <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha" 
                           placeholder="Confirme sua nova senha" required 
                           oninput="checkPasswordMatch()">
                    <button type="button" class="password-toggle" onclick="togglePassword('confirmar_senha')">
                        <i class="material-icons">visibility</i>
                    </button>
                </div>
            </div>

            <!-- Mensagens de Erro e Sucesso -->
            <div class="error-message" id="mensagemErro">
                <i class="material-icons">error</i>
                <span id="errorText">As senhas não coincidem. Tente novamente.</span>
            </div>

            <div class="success-message" id="mensagemSucesso">
                <i class="material-icons">check_circle</i>
                <span>Senhas coincidem perfeitamente!</span>
            </div>

            <!-- Botão de Envio -->
            <button type="submit" class="btn-submit" id="submitBtn">
                <i class="material-icons">security</i>
                <span id="submitText">Atualizar Senha</span>
                <div class="loading" id="loading"></div>
            </button>
        </form>
    </div>

    <script>
        // Função para alternar visibilidade da senha
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const button = input.nextElementSibling;
            const icon = button.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.textContent = 'visibility_off';
            } else {
                input.type = 'password';
                icon.textContent = 'visibility';
            }
        }

        // Função para verificar força da senha
        function checkPasswordStrength(password) {
            const strengthIndicator = document.getElementById('passwordStrength');
            const strengthBar = document.getElementById('strengthBar');
            const strengthText = document.getElementById('strengthText');
            const requirements = document.getElementById('passwordRequirements');
            
            if (password.length === 0) {
                strengthIndicator.classList.remove('show');
                strengthText.classList.remove('show');
                requirements.classList.remove('show');
                return;
            }
            
            strengthIndicator.classList.add('show');
            strengthText.classList.add('show');
            requirements.classList.add('show');
            
            // Verificar requisitos
            checkRequirement('req-length', password.length >= 8);
            checkRequirement('req-uppercase', /[A-Z]/.test(password));
            checkRequirement('req-lowercase', /[a-z]/.test(password));
            checkRequirement('req-number', /\d/.test(password));
            checkRequirement('req-special', /[!@#$%^&*(),.?":{}|<>]/.test(password));
            
            // Calcular força
            let strength = 0;
            if (password.length >= 8) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) strength++;
            
            // Atualizar indicador visual
            strengthBar.className = 'strength-bar';
            strengthText.className = 'strength-text show';
            
            switch (strength) {
                case 0:
                case 1:
                    strengthBar.classList.add('strength-weak');
                    strengthText.textContent = 'Senha muito fraca';
                    strengthText.style.color = '#ff6b6b';
                    break;
                case 2:
                    strengthBar.classList.add('strength-weak');
                    strengthText.textContent = 'Senha fraca';
                    strengthText.style.color = '#ff6b6b';
                    break;
                case 3:
                    strengthBar.classList.add('strength-medium');
                    strengthText.textContent = 'Senha média';
                    strengthText.style.color = '#feca57';
                    break;
                case 4:
                    strengthBar.classList.add('strength-strong');
                    strengthText.textContent = 'Senha forte';
                    strengthText.style.color = '#48dbfb';
                    break;
                case 5:
                    strengthBar.classList.add('strength-very-strong');
                    strengthText.textContent = 'Senha muito forte';
                    strengthText.style.color = '#1dd1a1';
                    break;
            }
        }

        // Função para verificar requisitos individuais
        function checkRequirement(reqId, isValid) {
            const requirement = document.getElementById(reqId);
            const icon = requirement.querySelector('i');
            
            if (isValid) {
                requirement.classList.add('valid');
                icon.textContent = 'check_circle';
            } else {
                requirement.classList.remove('valid');
                icon.textContent = 'circle';
            }
        }

        // Função para verificar se as senhas coincidem
        function checkPasswordMatch() {
            const senha = document.getElementById('senha').value;
            const confirmarSenha = document.getElementById('confirmar_senha').value;
            const errorMsg = document.getElementById('mensagemErro');
            const successMsg = document.getElementById('mensagemSucesso');
            
            if (confirmarSenha.length === 0) {
                errorMsg.style.display = 'none';
                successMsg.style.display = 'none';
                return;
            }
            
            if (senha !== confirmarSenha) {
                errorMsg.style.display = 'flex';
                successMsg.style.display = 'none';
            } else {
                errorMsg.style.display = 'none';
                successMsg.style.display = 'flex';
            }
        }

        // Função principal de validação
        function validarSenhas() {
            const senha = document.getElementById("senha").value;
            const confirmarSenha = document.getElementById("confirmar_senha").value;
            const errorMsg = document.getElementById("mensagemErro");
            const errorText = document.getElementById("errorText");
            const submitBtn = document.getElementById("submitBtn");
            const submitText = document.getElementById("submitText");
            const loading = document.getElementById("loading");
            
            // Verificar se as senhas coincidem
            if (senha !== confirmarSenha) {
                errorText.textContent = "As senhas não coincidem. Tente novamente.";
                errorMsg.style.display = "flex";
                return false;
            }
            
            // Verificar força mínima da senha
            if (senha.length < 8) {
                errorText.textContent = "A senha deve ter pelo menos 8 caracteres.";
                errorMsg.style.display = "flex";
                return false;
            }
            
            // Mostrar loading
            submitBtn.disabled = true;
            submitText.style.display = 'none';
            loading.style.display = 'block';
            
            // Simular delay de processamento
            setTimeout(() => {
                submitText.style.display = 'block';
                loading.style.display = 'none';
                submitBtn.disabled = false;
            }, 2000);
            
            return true;
        }

        // Adicionar listener para Enter
        document.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('formSenha').submit();
            }
        });

        // Animação de entrada
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.querySelector('.password-container');
            container.style.opacity = '0';
            container.style.transform = 'translateY(30px)';
            
            setTimeout(() => {
                container.style.transition = 'all 0.6s ease';
                container.style.opacity = '1';
                container.style.transform = 'translateY(0)';
            }, 100);
        });
    </script>

<?php include 'footer.php'; ?>