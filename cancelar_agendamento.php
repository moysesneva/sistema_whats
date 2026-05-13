<?php
include 'conn.php';
include 'funcoes.php';

// Verifica se o formulário foi enviado via POST e se o ID do agendamento foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id_agendamento = intval($_POST['id']); // Sanitiza o ID do agendamento
$idd = $_POST['idd'];
    // Verifica se o agendamento existe
    $sql_busca_agendamento = "SELECT * FROM agendamento WHERE id = '$id_agendamento'";
    $query_busca_agendamento = mysqli_query($conn, $sql_busca_agendamento);

    if (mysqli_num_rows($query_busca_agendamento) > 0) {
        // Remove o agendamento da tabela
        $sql_cancelar = "DELETE FROM agendamento WHERE id = '$id_agendamento'";
        if (mysqli_query($conn, $sql_cancelar)) {
            // Redireciona para cancelar.php com status de cancelado
            
            
      if ($query_busca_agendamento && mysqli_num_rows($query_busca_agendamento) > 0) {
    // Itera pelos resultados da consulta com um while
    while ($row = mysqli_fetch_assoc($query_busca_agendamento)) {
        // Extrai os dados para cada coluna da tabela
        $id = $row['id'];
        $usuario_api = $row['usuario_api'];
        $login = $row['login'];
        $dia = $row['dia'];
        $horario = $row['horario'];
        $profissional_nome = $row['profissional_nome'];
        $profissional_cargo = $row['profissional_cargo'];
        $telefone = $row['cliente_telefone'];
        $cliente_nome = $row['cliente_nome'];
        $data = $row['data'];
        $id_profissional = $row['id_profissional'];


    }
} else {
    echo "Nenhum agendamento encontrado.";
}      
            
   $sql_busca_usuario = "SELECT * FROM login WHERE usuario_api = '$usuario_api'";
    $query_busca_usuario = mysqli_query($conn, $sql_busca_usuario);
    if ($row_usuario = mysqli_fetch_array($query_busca_usuario)) {
        $login = $row_usuario['login'];
        $agenda_cancela = $row_usuario['agenda_cancela'];
    }
                
     
     
     
         $sql_busca_clientes = "SELECT * FROM clientes WHERE telefone = '$telefone'";
    $query_busca_clientes = mysqli_query($conn, $sql_busca_clientes);
    if ($row = mysqli_fetch_array($query_busca_clientes)) {
        $nome = $row['nome'];
        $telefone = $row['telefone'];
    }
    
    
    
    
    
    
       function novo_texto($string, $nome, $agendamento, $profissional) {
            $substituicoes = [
                '{nome}' => $nome,
                '{agendamento}' => $agendamento,
                '{profissional}' => $profissional
            ];
            return str_replace(array_keys($substituicoes), array_values($substituicoes), $string);
        }

        function formatar_data_brasileira($data) {
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
                return date("d/m/Y", strtotime($data));
            } else {
                return $data;
            }
        }
        
            
             $data_formatada = formatar_data_brasileira($data);
        $agendamento = $horario . " " . $data_formatada;
        $profissional = $profissional_nome;
            
    $agenda_cancela =    novo_texto($agenda_cancela, $nome, $agendamento, $profissional);      
            
            
            
        // Inserir mensagem de confirmação na tabela de envio
        $sql = "INSERT INTO envio (comando, telefone, msg, status, usuario_api) VALUES ('MsgTexto', '$telefone', '$agenda_cancela', '1', '$usuario_api')";
        $query = mysqli_query($conn, $sql);       
            
            
            
            
            
            
            
            
            
            
            
            
            
            VaiPara("cancelar.php?id=$idd&status=cancelado");
            exit;
        } else {
            // Redireciona com mensagem de erro
            VaiPara("cancelar.php?id=$idd&status=erro");
            exit;
        }
    } else {
        // Redireciona caso o agendamento não seja encontrado
        VaiPara("cancelar.php?id=$idd&status=naoencontrado");
        exit;
    }
} else {
    // Redireciona caso o método não seja POST ou o ID não seja fornecido
    VaiPara("cancelar.php?status=erro");
    exit;
}
?>
