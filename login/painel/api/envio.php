<?php
require_once __DIR__ . '/api_auth.php';


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

        $stmt_upd1 = $conn->prepare("UPDATE envio SET status = '2' WHERE id = ?");
        $stmt_upd1->bind_param("i", $id_msg);
        $stmt_upd1->execute();
        $stmt_upd1->close();

        $resposta = EnviarMsg($telefone, $msg, $id_msg, $usuario, $token, $servidor);

        if (strpos($resposta, 'Erro:') !== false) {
            echo "Ocorreu um erro: " . $resposta;
            $stmt_upd2 = $conn->prepare("UPDATE envio SET status = '1' WHERE id = ?");
            $stmt_upd2->bind_param("i", $id_msg);
            $stmt_upd2->execute();
            $stmt_upd2->close();
        } else {
            $stmt_upd3 = $conn->prepare("UPDATE envio SET status = '2' WHERE id = ?");
            $stmt_upd3->bind_param("i", $id_msg);
            $stmt_upd3->execute();
            $stmt_upd3->close();
        }
    }

    // Intervalo de 1 segundo
    sleep(1);
}

echo "Script finalizado\n";
*/
