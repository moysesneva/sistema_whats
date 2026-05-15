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




    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    <!-- DASHBOARD DE MÓDULOS -->
<div class="row">
    <!-- ÚLTIMO MÓDULO BAIXADO -->
    <div class="col-md-12 col-xl-6">
        <div class="card bg-primary text-white">
            <div class="card-block">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h4 class="text-white">Último Módulo Baixado</h4>
                        <?php
      
                        
$sql_busca_modulos = "SELECT * FROM modulo_atual";
$query = mysqli_query($conn, $sql_busca_modulos);
// $total = mysqli_num_rows($query);

while ($rows_usuarios = mysqli_fetch_array($query)) {
    $nome_modulo = $rows_usuarios['nome_modulo'];
    $versao = $rows_usuarios['versao'];
    $date_down = $rows_usuarios['date_down'];
}

                        
                        
                        // Dados fictícios para o último módulo baixado
                        $ultimo_modulo = [
                            'nome' => $nome_modulo,
                            'versao' => $versao,
                            'data_download' => $date_down
                        ];
                        
                        $data_formatada = date('d/m/Y H:i', strtotime($ultimo_modulo['data_download']));
                        ?>
                        <h3 class="text-white mb-2 mt-3"><?php echo $ultimo_modulo['nome']; ?></h3>
                        <p class="mb-0">Versão: <?php echo $ultimo_modulo['versao']; ?></p>
                        <p class="mb-0">Baixado em: <?php echo $data_formatada; ?></p>
                     

                    </div>
                    <div class="col-4 text-right">
                        <i class="feather icon-download-cloud" style="font-size: 70px;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- ESTATÍSTICAS DOS MÓDULOS -->
    <div class="col-md-12 col-xl-6">
        <div class="card">
            <div class="card-header">
                  <h2 class="text-primary">Precisa de Ajuda?</h2>
    <p class="text-muted mb-0">Se tiver dúvidas, solicite um suporte.</p>
    <a href="contato.php"target='blank' class="btn btn-success mt-3">Solicitar Suporte</a>
                <div class="card-header-right">
                    <ul class="list-unstyled card-option">
                        <li><i class="feather icon-maximize full-card"></i></li>
                        <li><i class="feather icon-minus minimize-card"></i></li>
                        <li><i class="feather icon-refresh-cw reload-card"></i></li>
                    </ul>
                </div>
            </div>
                <div class="card-block">
                    <div class="row align-items-center">
                       
                       
                    </div>
                    <div class="row align-items-center mt-3">
                      

                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- LISTA DE MÓDULOS INSTALADOS -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Módulos Instalados</h5>
                <div class="card-header-right">
                    <ul class="list-unstyled card-option">
                        <li><i class="feather icon-maximize full-card"></i></li>
                        <li><i class="feather icon-minus minimize-card"></i></li>
                        <li><i class="feather icon-refresh-cw reload-card"></i></li>
                    </ul>
                </div>
            </div>
            <div class="card-block">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nome do Módulo</th>
                                <th>Versão</th>
                                <th>Data de Instalação</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php

$sql_busca_modulos = "SELECT * FROM modulos_lista";
$query = mysqli_query($conn, $sql_busca_modulos);

while ($modulo = mysqli_fetch_array($query)) {
    $nome = $modulo['nome_modulo'];
    $versao = $modulo['versao'];
    $data_instalacao = $modulo['date_install'];

    // Formata a data se estiver em formato compatível
    $data_formatada = date('d/m/Y H:i', strtotime($data_instalacao));

    echo "<tr>";
    echo "<td>{$nome}</td>";
    echo "<td>{$versao}</td>";
    echo "<td>{$data_formatada}</td>";
    echo "</tr>";
}
?>

                        </tbody>
                    </table>

<?php include 'footer.php'; ?>