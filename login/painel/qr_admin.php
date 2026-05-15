<?php


$sql_busca_usuario = "SELECT * FROM login WHERE login = '$login'";
$query_busca_usuario = mysqli_query($conn, $sql_busca_usuario);
$total_busca_usuario = mysqli_num_rows($query_busca_usuario);

while($rows_usuarios = mysqli_fetch_array($query_busca_usuario)) {
    $nome          = Priletra($rows_usuarios['nome']);
    $img_perfil    = $rows_usuarios['perfil_img'];
    $autorizado    = $rows_usuarios['autorizado'];
    $tipo          = $rows_usuarios['tipo'];
    $usuario_api   = $rows_usuarios['usuario_api'];
    $situacao      = $rows_usuarios['situacao'];
    $email         = $rows_usuarios['email'];
    $qrcode        = isset($rows_usuarios['qrcode']) ? $rows_usuarios['qrcode'] : '';
    $tempo_code    = isset($rows_usuarios['tempo_code']) ? $rows_usuarios['tempo_code'] : '';
    $qr_data       = isset($rows_usuarios['qr_data']) ? $rows_usuarios['qr_data'] : '';
    $qr_quantidade = isset($rows_usuarios['qr_quantidade']) ? $rows_usuarios['qr_quantidade'] : 0;
}




$sql_config = "SELECT * FROM config";
$query_config = mysqli_query($conn, $sql_config);
$total_config = mysqli_num_rows($query_config);

while($rows_config = mysqli_fetch_array($query_config)) {
    $servidor  = Priletra($rows_config['ip_vps']);
    $porta  = $rows_config['porta'];
    $nova_porta  = $rows_config['nova_porta'];
    $token  = $rows_config['chave'];
    $chave_painel  = $rows_config['chave_painel'];
    $webhook  = $rows_config['webhook'];
    $google  = $rows_config['google'];
    $link_pagamento  = $rows_config['link_pagamento'];
}


if (isset($_POST['status_atual'])) {
    if($_POST['acao'] == 'ativar'){

$user_id =  $usuario_api     ;

$qr_response  =  abrir_instancia_terminal($usuario_api,$servidor,$porta,$token);
#echo $qr_response; 
#exit();


$sql = "UPDATE login SET situacao = 'ativado' WHERE login='$login'";

$query = mysqli_query($conn,$sql);

VaiPara('qrcode.php');
  
}}

// Obtém o mês e ano atual
$mes_atual = date('m');
$ano_atual = date('Y');

// Query para contar os envios do mês atual
$sql_envios = "SELECT COUNT(*) as total_envios 
               FROM envio 
               WHERE usuario_api = '$usuario_api' 
               AND MONTH(data_envio) = '$mes_atual' 
               AND YEAR(data_envio) = '$ano_atual'";

$query_envios = mysqli_query($conn, $sql_envios);
$total_envios = 0;

if ($query_envios) {
    $row_envios = mysqli_fetch_assoc($query_envios);
    $total_envios = $row_envios['total_envios'];
}


if (isset($_POST['status_atual'])) {
    if($_POST['acao'] == 'desativar'){

$user_id =  $usuario_api     ;

    $qr_response  =  fechar_instancia($user_id,$servidor,$porta,$token);
#echo $qr_response; 
  
VaiPara('qrcode.php');


#$sql = "UPDATE login SET situacao = 'desativado' WHERE login='$login'";

#$query = mysqli_query($conn,$sql);
  

}}

#print_r($_REQUEST);
if($tipo == '3'){
    VaiPara('perfil.php');
}

include 'menu.php';

if($total_busca_usuario != 1){
    VaiPara('login.php');
}
if($autorizado != 2){
    VaiPara('desbloquar.php');
}

if(empty($email)){
    if($tipo == '2'){
        VaiPara('perfil.php?pagina_nome=24');
    }
}


if($tipo == '2'){
if($situacao != 'ativado'){
    VaiPara('perfil.php');
}}
?>

<?php $css_extra = '    <link rel="stylesheet" type="text/css" href="../files/assets/icon/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="../files/assets/vendor/toastr/toastr.min.css">
    <style>
        #qrCodeContainer img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0 auto;
            width: 300px;
            height: 300px;
        }
        #instanciaImage {
  transition: transform 0.3s ease;
}

#instanciaImage {
  transition: transform 0.3s ease;
  /* Inicialmente sem transformação */
  transform: scale(1);
}


    </style>'; ?>
<?php include 'header.php'; ?>


                                    <!-- Cabeçalho da página -->
                                    <div class="page-header card">
                                        <div class="row align-items-center">
                                            <div class="col-md-8">
                                                <div class="page-header-title">
                                                    <h5 class="m-b-10">Gerenciamento de QR Code</h5>
                                                    <p class="m-b-0"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Conteúdo principal -->
                                    <div class="page-body">
                                        <div class="row">
                                            <!-- Cartão de Status da Instância -->
                                            <div class="col-md-6 col-xl-4">
                                                <div class="card bg-c-blue order-card">
                                                    <div class="card-block">
                                                        <h6 class="m-b-20">Status da Instância</h6>
                                                        <h2 class="text-right">
                                                            <i class="feather icon-smartphone f-left"></i>
                                                            <?php
                                                            $status_text = "Desconhecido";
                                                            $status_icon = "question-circle";
                                                            $status_color = "secondary";
                                                            
                                                            if ($situacao == 'ativado') {
                                                                $status_text = "Ativado";
                                                                $status_icon = "check-circle";
                                                                $status_color = "success";
                                                            } elseif ($situacao == 'desativado') {
                                                                $status_text = "Desativado";
                                                                $status_icon = "times-circle";
                                                                $status_color = "danger";
                                                            } elseif ($situacao == 'aguarde') {
                                                                $status_text = "Aguardando";
                                                                $status_icon = "clock";
                                                                $status_color = "warning";
                                                            } elseif ($situacao == 'bloqueado') {
                                                                $status_text = "Bloqueado";
                                                                $status_icon = "lock";
                                                                $status_color = "dark";
                                                            }
                                                            ?>
                                                            <span class="text-white"><?php echo $status_text; ?></span>
                                                        </h2>
                                                        <p class="m-b-0 text-white">
                                                            <span class="badge badge-<?php echo $status_color; ?> m-r-10">
                                                                <i class="fa fa-<?php echo $status_icon; ?>"></i>
                                                            </span>
                                                            Última atualização: <?php echo $qr_data ? date('d/m/Y H:i', strtotime($qr_data)) : 'N/A'; ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Cartão de Informações do Cliente -->
                                            <div class="col-md-6 col-xl-4">
                                                <div class="card bg-c-green order-card">
                                                    <div class="card-block">
                                                        <h6 class="m-b-20">Informações do Cliente</h6>
                                                        <h2 class="text-right">
                                                            <i class="feather icon-user f-left"></i>
                                                            <span class="text-white"><?php echo $nome; ?></span>
                                                        </h2>
                                                        <p class="m-b-0 text-white">
                                                            <span class="m-r-10">
                                                                <i class="feather icon-mail"></i> <?php echo $email; ?>
                                                            </span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Cartão de Estatísticas -->
                                            <div class="col-md-6 col-xl-4">
                                                <div class="card bg-c-yellow order-card">
                                                    <div class="card-block">
                                                        <h6 class="m-b-20">Estatísticas</h6>
                                                        <h2 class="text-right">
                                                            <i class="feather icon-bar-chart f-left"></i>
                                                            <span class="text-white"><?php echo $total_envios; ?></span>
                                                        </h2>
                                                        <p class="m-b-0 text-white">
                                                            <span class="m-r-10">
                                                                <i class="feather icon-refresh-cw"></i> Mensagens enviadas este mês
                                                            </span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                      <!-- Cartão de Detalhes da Instância -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Detalhes da Instância</h5>
                <div class="card-header-right">
                    <ul class="list-unstyled card-option">
                        <li><i class="feather icon-maximize full-card"></i></li>
                        <li><i class="feather icon-minus minimize-card"></i></li>
                        <li><i class="feather icon-refresh-cw reload-card"></i></li>
                    </ul>
                </div>
            </div>
            <div class="card-block">
                <div class="row justify-content-center">
                    <div class="col-md-8 text-center">
                        <?php 
                        if($tipo == 1 && $usuario_api == Null){ 
                            $usuario_login = So_numeros($login);     
                            $usuario_login = $termo . $usuario_login;    
                        ?>
                        <!-- Formulário para criar ADM -->
                        <div class="mt-4">
                            <form action="acoes/acao.php" method="POST">
                                <input type="hidden" name="opcao" value="criar_usuario_adm">
                                <input type="hidden" name="usuario" value="<?=$login;?>">
                                <input type="hidden" name="usuario_api" value="<?=$usuario_login;?>">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="feather icon-user-plus m-r-5"></i> Criar ADM
                                </button>
                            </form>
                        </div>
                        
                        <?php } else { ?>
                            <?php if($situacao == 'ativado'){ ?>
                            <!-- Seção de QR Code - Destaque Principal -->
                            <div class="mb-4">
                                <div class="qr-code-section p-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 15px; box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);">
                                    <h6 class="text-white mb-3">
                                        <i class="feather icon-smartphone m-r-5"></i>
                                        Conectar Dispositivo
                                    </h6>
                                    <button type="button" class="btn btn-light btn-lg shadow-sm" id="btnGerarQRCode" 
                                        style="border-radius: 10px; font-weight: 600; transition: all 0.3s ease;">
                                        <i class="feather icon-refresh-cw m-r-10"></i>
                                        Gerar QR Code
                                    </button>
                                    <p class="text-white-50 mt-2 mb-0 small">
                                        Escaneie o código para conectar
                                    </p>
                                </div>
                            </div>
                            <?php } ?>
                            
                            <!-- Controles da Instância -->
                            <div class="mt-4">
                                <h6 class="mb-3">Controles da Instância</h6>
                                <form method="POST" action="" class="d-flex justify-content-center">
                                    <input type="hidden" name="status_atual" value="<?php echo $status_text; ?>">
                                    
                                    <!-- Botão Ativar -->
                                    <button type="submit" name="acao" value="ativar" 
                                        class="btn btn-success btn-lg mr-3">
                                        <i class="feather icon-play m-r-5"></i>Abrir Instância
                                    </button>

                                    <!-- Botão Desativar -->
                                    <button type="submit" name="acao" value="desativar" 
                                        class="btn btn-danger btn-lg">
                                        <i class="feather icon-square m-r-5"></i>Fechar Instância
                                    </button>
                                </form>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos adicionais para o botão QR Code */
#btnGerarQRCode:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2) !important;
}

.qr-code-section {
    transition: transform 0.3s ease;
}

.qr-code-section:hover {
    transform: translateY(-1px);
}

/* Melhorias nos botões de ação da instância */
.btn-success:hover, .btn-danger:hover {
    transform: translateY(-1px);
    transition: all 0.2s ease;
}
</style>
<!-- Modal para exibir a imagem da instância -->
<!-- Modal para exibir a imagem da instância -->
<div class="modal fade" id="printInstanciaModal" tabindex="-1" role="dialog" aria-labelledby="printInstanciaLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="printInstanciaLabel">Print da Instância</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center">
        <!-- A imagem que será manipulada -->
        <img id="instanciaImage" src="" alt="Imagem" style="max-width: 100%; height: auto; cursor: pointer;">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>



    <!-- Modal para exibir o QR Code -->
    <div class="modal fade" id="qrCodeModal" tabindex="-1" role="dialog" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="qrCodeModalLabel">QR Code da Instância</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <div id="qrCodeContainer">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Carregando...</span>
                        </div>
                        <p class="mt-2">Carregando QR Code...</p>
                    </div>
                    <div class="mt-3">
                        <p class="text-muted">Escaneie este QR Code com seu WhatsApp para conectar sua instância</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-primary" id="btnRefreshQR">
                        <i class="feather icon-refresh-cw"></i> Atualizar QR Code
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->

<?php include 'footer.php'; ?>