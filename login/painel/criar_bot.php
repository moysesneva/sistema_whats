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


include 'bloqueio.php';


?>

<?php $css_extra = '    <link rel="stylesheet" href="..\files\assets\vendor\intl-tel-input\css\intlTelInput.css">'; ?>
<?php include 'header.php'; ?>




<div class="container">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h2 class="mb-0"><i class="feather icon-user-plus mr-2"></i>Criar Bot</h2>
        </div>
        <div class="card-body">
            <form action="criar_bot_confirma.php" method="POST">
                <div class="form-group mb-4">
                    <label for="nome_cliente" class="font-weight-bold"><i class="feather icon-user mr-1"></i>Nome do Cliente:</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="feather icon-user"></i></span>
                        </div>
                        <input type="text" class="form-control" id="nome_cliente" name="nome_cliente" placeholder="Digite o nome do cliente" required>
                    </div>
                    <small class="form-text text-muted">
                        <i class="feather icon-info mr-1"></i>Insira o nome completo do cliente.
                    </small>
                </div>

                <div class="form-group mb-4">
                    <label for="telefone_cliente" class="font-weight-bold"><i class="feather icon-phone mr-1"></i>Telefone (será o login):</label>
                    <input type="tel" class="form-control" id="telefone_cliente" name="telefone_cliente" required>
                    <input type="hidden" id="codigo_pais" name="codigo_pais">
                    <small class="form-text text-muted">
                        <i class="feather icon-info mr-1"></i>Este número será utilizado como o login do cliente.
                    </small>
                </div>

                <div class="form-group mb-4">
                    <label for="email_cliente" class="font-weight-bold"><i class="feather icon-mail mr-1"></i>Email do Cliente:</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="feather icon-mail"></i></span>
                        </div>
                        <input type="email" class="form-control" id="email_cliente" name="email_cliente" placeholder="exemplo@dominio.com" required>
                    </div>
                    <div class="alert alert-info mt-2" style="font-weight: bold; color: #0056b3; background-color: #eaf4ff; border: 1px solid #007bff;">
                        <i class="feather icon-alert-circle mr-1"></i> Este email será utilizado como identificador único para o pagamento. Insira um email válido no formato exemplo@dominio.com.
                    </div>
                </div>
<div class="form-group mb-4">
  <label for="creditos" class="font-weight-bold">
    <i class="feather icon-dollar-sign mr-1"></i>Créditos de IA:
  </label>
  <div class="input-group">
    <div class="input-group-prepend">
      <span class="input-group-text">
        <i class="feather icon-dollar-sign"></i>
      </span>
    </div>
    <input
      type="number"
      class="form-control"
      id="creditos"
      name="creditos"
      min="0"
      step="1"
      placeholder="Insira o número de créditos"
    >
  </div>
  <small class="form-text text-muted">
    <i class="feather icon-alert-triangle mr-1"></i>Informe quantos créditos devem ser disponibilizados.
  </small>
</div>





<div class="form-group mb-4">
  <label for="plano" class="font-weight-bold">
    <i class="feather icon-layers mr-1"></i>Plano:
  </label>
  <div class="input-group">
    <div class="input-group-prepend">
      <span class="input-group-text">
        <i class="feather icon-layers"></i>
      </span>
    </div>
    <select class="form-control" id="plano" name="plano">
      <option value="">Selecione o plano</option>
      <option value="plano1">Plano 1</option>
      <option value="plano2">Plano 2</option>
      <option value="plano3">Plano 3</option>
    </select>
  </div>
  <small class="form-text text-muted">
    <i class="feather icon-alert-triangle mr-1"></i>Escolha um dos três planos disponíveis.
  </small>
</div>










                <div class="form-group mb-4">
                    <label for="senha_padrao" class="font-weight-bold"><i class="feather icon-lock mr-1"></i>Senha Padrão:</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="feather icon-lock"></i></span>
                        </div>
                        <input type="text" class="form-control" id="senha_padrao" name="senha_padrao" value="123456" readonly>
                    </div>
                    <small class="form-text text-muted">
                        <i class="feather icon-alert-triangle mr-1"></i>Ao criar o bot, a senha padrão é <strong>123456</strong>. Por favor, altere-a posteriormente para garantir a segurança.
                    </small>
                </div>

                <button type="submit" class="btn btn-primary btn-lg btn-block mt-4 shadow-sm">
                    <i class="feather icon-check-circle mr-2"></i>Criar Bot
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    .card {
        border-radius: 10px;
        overflow: hidden;
    }
    .card-header {
        padding: 15px 20px;
    }
    .form-control {
        border-radius: 5px;
        padding: 10px 15px;
        height: auto;
        transition: all 0.3s ease;
    }
    .form-control:focus {
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        border-color: #80bdff;
    }
    .input-group-text {
        background-color: #f8f9fa;
        border-radius: 5px 0 0 5px;
    }
    .btn-primary {
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 123, 255, 0.3) !important;
    }
    label {
        margin-bottom: 8px;
    }
    .iti {
        width: 100%;
    }
    /* Correção para o dropdown do telefone */
    .iti__country-list {
        position: absolute;
        z-index: 9999;
        max-height: 200px;
    }
    /* Garantir espaço entre campos para o dropdown */
    .form-group {
        margin-bottom: 35px;
    }
    /* Ajustes para o posicionamento do telefone */
    .iti--separate-dial-code .iti__selected-flag {
        background-color: #f8f9fa;
    }
</style>

<!-- jQuery local (necessário antes dos scripts inline) -->
<script src="../files/bower_components/jquery/js/jquery.min.js"></script>
<!-- Adicionar JS do intl-tel-input -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
<script>
    $(document).ready(function() {
        var input = document.querySelector("#telefone_cliente");
        var iti = window.intlTelInput(input, {
            initialCountry: "br",
            preferredCountries: ["br", "pt", "us", "gb", "es"],
            separateDialCode: true,
            dropdownContainer: document.body,
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
        });

        // Remover o evento de input que estava formatando o telefone
        $('#telefone_cliente').off('input');

        // Impedir a inserção de caracteres não numéricos no campo de telefone
        $('#telefone_cliente').on('input', function(e) {
            var value = $(this).val().replace(/\D/g, '');
            $(this).val(value);
        });

        // Modificar o comportamento do formulário no envio
        $('form').on('submit', function(e) {
            e.preventDefault();
            
            // Obter o código do país (sem o +) e o número de telefone limpo
            var countryData = iti.getSelectedCountryData();
            var dialCode = countryData.dialCode; // sem o +
            var phoneNumber = input.value.replace(/\D/g, ''); // remove qualquer caractere não numérico
            
            // Combinar o código do país com o número de telefone para criar um número único
            var fullPhoneNumber = dialCode + phoneNumber;
            
            // Atualizar o valor do campo de telefone
            $("#telefone_cliente").val(fullPhoneNumber);
            
            // Remover o campo oculto código do país para não enviá-lo
            $("#codigo_pais").remove();
            
            // Enviar o formulário
            this.submit();
        });
    });
</script>

<?php include 'footer.php'; ?>