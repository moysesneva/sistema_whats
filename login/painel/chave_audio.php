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

<!-- Formulário para escolher o serviço de IA e salvar -->
<div class="container mt-5">
    <div class="row">
        <div class="col-md-6">
            <h3>Escolha o Serviço de IA Ativo</h3>
            <form action="processar_ia.php" method="post">
                <div class="form-group">
                    <label for="ia">Selecione o modelo de IA</label>
                    <select class="form-control" id="ia" name="ia">
                        <option value="gpt4">GPT-4</option>
                        <option value="IBM">IBM WHATSON</option>
                        <!-- Adicione mais modelos de IA conforme necessário -->
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Salvar</button>
            </form>
        </div>
    </div>
</div>

   <!-- Formulário para inserir chave e selecionar modelo de IA -->
<div class="container mt-5">
    <div class="row">
        <div class="col-md-6">
            <h3>Adicionar Serviço com Chave</h3>
            <form action="adicionar_chave_ia.php" method="post">
                <div class="form-group">
                    <label for="chave">Chave</label>
                  <textarea class="form-control" id="chave" name="chave" placeholder="Insira a chave" rows="3"></textarea>

                </div>
                <div class="form-group">
                    <label for="modeloIa">Selecione o modelo de IA</label>
                    <select class="form-control" id="modeloIa" name="modeloIa">
                      <option value="gpt4">GPT-4</option>
                        <option value="IBM">IBM WHATSON</option>
                        <!-- Adicione mais modelos de IA conforme necessário -->
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Adicionar</button>
            </form>
        </div>
    </div>
</div>

<!-- Lista de Chave e Serviço de IA com botão "Deletar" -->
<div class="container mt-5">
    <div class="row">
        <div class="col-md-6">
            <h3>Lista de Chaves e Modelos de IA</h3>
            <ul class="list-group">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Chave: abc**** Modelo: Gemini
                    <button class="btn btn-danger btn-sm">Deletar</button>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Chave: xyz**** Modelo: GPT-4
                    <button class="btn btn-danger btn-sm">Deletar</button>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Chave: 123**** Modelo: Grok
                    <button class="btn btn-danger btn-sm">Deletar</button>
                </li>
            </ul>

<?php include 'footer.php'; ?>