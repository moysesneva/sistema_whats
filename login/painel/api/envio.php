<?php


/*
echo "Script iniciado\n";
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Usando caminhos absolutos para os includes
include '/home2/tilsco63/mygpt4.store/login/painel/conn.php';
include '/home2/tilsco63/mygpt4.store/login/painel/funcoes.php';
include '/home2/tilsco63/mygpt4.store/login/painel/api/api_funcao.php';
include '/home2/tilsco63/mygpt4.store/login/painel/config_dados.php';

set_time_limit(0); // Permite tempo de execução ilimitado

while (true) {
    // Certifique-se de que as variáveis abaixo são definidas corretamente no arquivo 'config_dados.php'
    $servidor_recebido = $ip_vps . ':' . $nova_porta . '/webhook';
    $servidor = barra($servidor_recebido);

    $sql_config = "SELECT * FROM envio WHERE status = '1'";
    $query_config = mysqli_query($conn, $sql_config);
    $total_config = mysqli_num_rows($query_config);

    while ($rows_usuarios = mysqli_fetch_array($query_config)) {
        $id_msg = $rows_usuarios['id'];
        $telefone = $rows_usuarios['telefone'];
        $msg = $rows_usuarios['msg'];
        $status = $rows_usuarios['status'];
        $usuario = $rows_usuarios['usuario_api'];

        $sql = "UPDATE envio SET status = '2' WHERE id='$id_msg'";
        $query = mysqli_query($conn, $sql);

        $resposta = EnviarMsg($telefone, $msg, $id_msg, $usuario, $token, $servidor);

        if (strpos($resposta, 'Erro:') !== false) {
            // Exibe a mensagem de erro
            echo "Ocorreu um erro: " . $resposta;
            $sql = "UPDATE envio SET status = '1' WHERE id='$id_msg'";
            $query = mysqli_query($conn, $sql);
        } else {
            $sql = "UPDATE envio SET status = '2' WHERE id='$id_msg'";
            $query = mysqli_query($conn, $sql);
        }
    }

    // Intervalo de 1 segundo
    sleep(1);
}

echo "Script finalizado\n";
?>
