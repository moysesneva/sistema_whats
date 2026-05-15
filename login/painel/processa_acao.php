<?php
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
    #$usuario = mysqli_real_escape_string($conn, $_POST['usuario']);
    #$comando = mysqli_real_escape_string($conn, $_POST['comando']);
        $user_id =  $_POST['usuario'] ;
        $comando = $_POST['comando'] ;
        $usuario =  $_POST['usuario'] ;


    #iniciar
    #parar
    #bloquear
    #deletar


if($comando == 'bloquear'){


            // Atualiza o status do bot para ativado
            $sql_update = "UPDATE login SET situacao = 'bloqueado' ,tipo='3' WHERE usuario_api = '$usuario' AND tipo IN ('2', '3')";
        $query = mysqli_query($conn, $sql_update);
        fechar_instancia($user_id, $servidor,$porta,$token);
       # VaiPara('listar_bot.php');

}






  if($comando == 'parar'){

        fechar_instancia($user_id, $servidor,$porta,$token);

            // Atualiza o status do bot para ativado
            $sql_update = "UPDATE login SET situacao = 'desativado' WHERE usuario_api = '$usuario' AND tipo IN ('2', '3')";
        $query = mysqli_query($conn, $sql_update);
        #VaiPara('listar_bot.php');



}



    if($comando == 'iniciar'){

        abrir_instancia_terminal($user_id, $servidor,$porta,$token);

            // Atualiza o status do bot para ativado
        $sql_update = "UPDATE login SET  tipo ='2', situacao = 'ativado',funcao = 'IA' WHERE usuario_api = '$usuario' AND tipo IN ('2', '3')";
        $query = mysqli_query($conn, $sql_update);
              


}

    if($comando == 'deletar'){

        $sql = "DELETE FROM login WHERE usuario_api = '$usuario'";
        $query = mysqli_query($conn, $sql);

}




    
   }

      echo 'oi';

?>
