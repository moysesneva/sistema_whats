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




include 'bloqueio.php';

$sql_busca_config = "SELECT * FROM config";
$query_busca_config = mysqli_query($conn, $sql_busca_config);
$total_busca_config = mysqli_num_rows($query_busca_config);

while($rows_config = mysqli_fetch_array($query_busca_config)) {
    $chave  = $rows_config['chave'];
    $validade  = $rows_config['validade'];
    $link_pagamento = $rows_config['link_pagamento'];
    $preco  = $rows_config['preco'];
    $telefone  = $rows_config['telefone'];
    $imagem_dados = $rows_config['caminho_modelo'];
    $hero_title = isset($rows_config['hero_title']) ? $rows_config['hero_title'] : 'Sistema de Agendamento Inteligente com IA';
    $hero_subtitle = isset($rows_config['hero_subtitle']) ? $rows_config['hero_subtitle'] : 'Simplifique sua gestão de agendamentos com nosso sistema que entende texto, áudio, imagens e muito mais. A escolha perfeita para otimizar seu atendimento.';
    $services_title = isset($rows_config['services_title']) ? $rows_config['services_title'] : 'Sistema Inteligente de Agendamentos';
    $services_description = isset($rows_config['services_description']) ? $rows_config['services_description'] : 'Nosso sistema utiliza inteligência artificial para compreender áudio, texto e imagens, oferecendo agendamentos e cancelamentos de forma prática e automatizada.';
}


?>
<?php include 'header.php'; ?>


          <?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recebe os dados do formulário
    $preco = isset($_POST['preco']) ? trim($_POST['preco']) : null;
    $telefone = isset($_POST['telefone']) ? trim($_POST['telefone']) : null;
    $link_pagamento = isset($_POST['link_pagamento']) ? trim($_POST['link_pagamento']) : null;
    $hero_title = isset($_POST['hero_title']) ? trim($_POST['hero_title']) : null;
    $hero_subtitle = isset($_POST['hero_subtitle']) ? trim($_POST['hero_subtitle']) : null;
    $services_title = isset($_POST['services_title']) ? trim($_POST['services_title']) : null;
    $services_description = isset($_POST['services_description']) ? trim($_POST['services_description']) : null;

    // Verifica e processa a imagem de fundo
    if (isset($_FILES['imagem_fundo']) && $_FILES['imagem_fundo']['error'] === UPLOAD_ERR_OK) {
        $imagemTemp = $_FILES['imagem_fundo']['tmp_name'];
        $imagemNome = basename($_FILES['imagem_fundo']['name']);
        $caminhoDestino = "uploads/" . $imagemNome;

        // Move o arquivo para o diretório de uploads
        if (move_uploaded_file($imagemTemp, $caminhoDestino)) {
            $imagemPath = $caminhoDestino; // Caminho da imagem para salvar no banco
        } else {
            echo "Erro ao fazer upload da imagem.";
            $imagemPath = null;
        }
    } else {
        $imagemPath = null;
    }


    if ($imagemPath == Null){
        $imagemPath = $imagem_dados;
    }

    // Atualização no banco de dados
    $stmt_upd_cfg = $conn->prepare("UPDATE config SET preco = ?, telefone = ?, caminho_modelo = ?, hero_title = ?, hero_subtitle = ?, services_title = ?, services_description = ?");
    $stmt_upd_cfg->bind_param("sssssss", $preco, $telefone, $imagemPath, $hero_title, $hero_subtitle, $services_title, $services_description);
    $query = $stmt_upd_cfg->execute();
    $stmt_upd_cfg->close();

    if ($query) {
        VaiPara('pagina_venda.php?pagina_nome=27');
    } else {
        echo "Erro ao atualizar os dados no banco de dados: " . mysqli_error($conn);
    }
}
?>

   
       
<?php
include 'parte1.php'

?>



<?php
include 'planos_dados.php'

?>































  
  
  
     </div>
        </div>     


<!-- Custom CSS -->
<style>
    .container {
        margin: auto;
        padding: 20px;
    }

    .card {
        border: none;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .card-header {
        font-weight: 600;
        padding: 15px 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-control {
        padding: 10px 15px;
        border-radius: 5px;
        border: 1px solid #e0e0e0;
        font-size: 14px;
    }

    .form-control:focus {
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        border-color: #4e73df;
    }

    .btn-primary {
        background-color: #4e73df;
        border-color: #4e73df;
        padding: 10px 25px;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-primary:hover {
        background-color: #3a5fd7;
        border-color: #3a5fd7;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(78, 115, 223, 0.3);
    }

    label {
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
    }
</style>

<?php include 'footer.php'; ?>