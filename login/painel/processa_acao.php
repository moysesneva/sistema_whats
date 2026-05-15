<?php
require_once __DIR__ . '/auth_guard.php';
session_start();
include 'conn.php';  // Inclui o arquivo de conexão ao banco de dados
include 'funcoes.php';  // Inclui o arquivo com a função `VaiPara()`
include 'api/editacodigo.php';

// Verifica se o usuário está logado
if(!isset($_SESSION['login'])) {
    header('Location: login.php');
    exit;
}

$sql_config = "SELECT * FROM config";
$query_config = mysqli_query($conn, $sql_config);
$total_config = mysqli_num_rows($query_config);

// Verifica se existem resultados e atribui os valores às variáveis
if ($total_config > 0) {
    $rows_config = mysqli_fetch_array($query_config);
    
    $servidor = $rows_config['ip_vps'];
    $porta = $rows_config['porta'];
    $nova_porta = $rows_config['nova_porta'];
    $token = $rows_config['chave'];
    $caminho_modelo = $rows_config['caminho_modelo'];
    $chave_painel = $rows_config['chave_painel'];
    $webhook = $rows_config['webhook'];
    $validade = $rows_config['validade'];
    $webhook_completo = $rows_config['webhook_completo'];
    $google = $rows_config['google']; // Caso exista este campo
    
    }


// Verifica se os parâmetros necessários foram enviados
if(isset($_POST['usuario']) && isset($_POST['comando'])) {
    #$usuario = trim($_POST['usuario']);
    #$comando = trim($_POST['comando']);
        $user_id =  $_POST['usuario'] ;
        $comando = $_POST['comando'] ;
        $usuario =  $_POST['usuario'] ;


    #iniciar
    #parar
    #bloquear
    #deletar


if($comando == 'bloquear'){


            // Atualiza o status do bot para ativado
            $stmt_blq = $conn->prepare("UPDATE login SET situacao = 'bloqueado', tipo = '3' WHERE usuario_api = ? AND tipo IN ('2', '3')");
            $stmt_blq->bind_param("s", $usuario);
            $query = $stmt_blq->execute();
            $stmt_blq->close();
        fechar_instancia($user_id, $servidor,$porta,$token);
       # VaiPara('listar_bot.php');

}






  if($comando == 'parar'){

        fechar_instancia($user_id, $servidor,$porta,$token);

            // Atualiza o status do bot para ativado
            $stmt_par = $conn->prepare("UPDATE login SET situacao = 'desativado' WHERE usuario_api = ? AND tipo IN ('2', '3')");
            $stmt_par->bind_param("s", $usuario);
            $query = $stmt_par->execute();
            $stmt_par->close();
        #VaiPara('listar_bot.php');



}



    if($comando == 'iniciar'){

        abrir_instancia_terminal($user_id, $servidor,$porta,$token);

            // Atualiza o status do bot para ativado
        $stmt_ini = $conn->prepare("UPDATE login SET tipo = '2', situacao = 'ativado', funcao = 'IA' WHERE usuario_api = ? AND tipo IN ('2', '3')");
        $stmt_ini->bind_param("s", $usuario);
        $query = $stmt_ini->execute();
        $stmt_ini->close();
              


}

    if($comando == 'deletar'){

        $stmt_del = $conn->prepare("DELETE FROM login WHERE usuario_api = ?");
        $stmt_del->bind_param("s", $usuario);
        $query = $stmt_del->execute();
        $stmt_del->close();

}




    
   }

      echo 'oi';

?>
