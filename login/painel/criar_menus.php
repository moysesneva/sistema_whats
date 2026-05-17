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

#include 'bloqueio.php';

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

  <div class="container">
    <h2>Criar Novo Menu</h2>

    <!-- Formulário para criar um novo menu -->
    <form action="inserir_menu.php" method="POST">
        
        <!-- Campo para Nome do Menu -->
        <div class="form-group">
            <label for="menu">Nome do Menu:</label>
            <input type="text" class="form-control" id="menu" name="menu" placeholder="Digite o nome do menu" required>
            <small class="form-text text-muted">
                Insira o nome que aparecerá no menu.
            </small>
        </div>

        <!-- Campo para a Página do Menu -->
        <div class="form-group">
            <label for="menu_pagina">Página do Menu:</label>
            <input type="text" class="form-control" id="menu_pagina" name="menu_pagina" placeholder="Digite o caminho do arquivo ex: config_adm.php" required>
            <small class="form-text text-muted">
                Insira o caminho do arquivo que será carregado ao clicar no menu (ex: config_adm.php).
            </small>
        </div>

        <!-- Campo para Tipo de Acesso -->
        <div class="form-group">
            <label for="tipo">Tipo de Acesso:</label>
            <select class="form-control" id="tipo" name="tipo" required>
                <option value="" selected>Selecione o Tipo de Acesso</option>
                <option value="1">Administrador</option>
                <option value="2">Usuário</option>
                 <option value="5">Profissional</option>
                <option value="3">Inadimplente</option>
            </select>
            <small class="form-text text-muted">
                Selecione o tipo de acesso que terá permissão para ver o menu.
            </small>
        </div>

        <!-- Campo para Ordem do Menu -->
        <div class="form-group">
            <label for="ordem">Ordem do Menu:</label>
            <input type="text" class="form-control" id="ordem" name="ordem" placeholder="Digite a ordem do menu (ex: 1, 2, 3)" required>
            <small class="form-text text-muted">
                Defina a ordem de exibição no menu (ex: 1, 2, 3).
            </small>
        </div>

        <!-- Campo para Ícone do Menu -->
        <div class="form-group">
            <label for="icone_menu">Ícone do Menu:</label>
            <select class="form-control" id="icone_menu" name="icone_menu" required>
    <option value="" selected>Selecione um Ícone</option>
    <option value="feather icon-shield">Escudo (Segurança)</option>
    <option value="fa fa-key">Chave</option>
    <option value="fa fa-qrcode">QR Code</option>
    <option value="feather icon-plus-circle">Círculo com Sinal de Mais</option>
    <option value="feather icon-list">Lista</option>
    <option value="feather icon-lock">Cadeado</option>
    <option value="feather icon-log-out">Sair</option>
    <option value="feather icon-menu">Menu</option>
    <option value="feather icon-settings">Configurações</option>
    <option value="feather icon-home">Casa (Início)</option>
    <option value="fa fa-user">Usuário</option>
    <option value="fa fa-users">Usuários (Múltiplos)</option>
    <option value="fa fa-envelope">Envelope (Mensagens)</option>
    <option value="fa fa-bell">Sino (Notificações)</option>
    <option value="fa fa-cog">Engrenagem (Configurações Gerais)</option>
    <option value="feather icon-search">Lupa (Busca)</option>
    <option value="fa fa-calendar">Calendário</option>
    <option value="fa fa-dashboard">Painel de Controle (Dashboard)</option>
    <option value="fa fa-file">Arquivo</option>
    <option value="fa fa-folder">Pasta</option>
    <option value="feather icon-edit">Editar</option>
    <option value="fa fa-trash">Lixeira (Excluir)</option>
    <option value="fa fa-download">Baixar</option>
    <option value="fa fa-upload">Carregar</option>
    <option value="feather icon-bar-chart">Gráfico</option>
    <option value="fa fa-credit-card">Cartão de Crédito (Pagamento)</option>
    <option value="fa fa-money">Dinheiro</option>
    <option value="fa fa-shopping-cart">Carrinho de Compras</option>
    <option value="fa fa-lock">Cadeado (Proteção)</option>
</select>

            <small class="form-text text-muted">
                Escolha o ícone que será exibido ao lado do nome do menu.
            </small>
        </div>

        <!-- Botão para enviar o formulário -->
        <button type="submit" class="btn btn-primary">Criar Menu</button>
    </form>

<?php include 'footer.php'; ?>