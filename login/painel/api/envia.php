<?php
error_reporting(0);
ini_set("display_errors", 0 );
include '../conn.php';
include '../funcoes.php';
include 'api_funcao.php';
include '../config_dados.php';




$usuario_api = $_POST['usuario'];   
$telefone = $_POST['telefone'];
 
?>






<?php


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 $usuario_api = $_POST['usuario'];   
 #$telefone = $_POST['telefone'];
 #$telefone = '553184767330';
############# BUSCA DE LOGIN
$sql = "SELECT * FROM login WHERE usuario_api = '$usuario_api' ";
$query = mysqli_query($conn,$sql);
$total = mysqli_num_rows($query);

while($lista_login = mysqli_fetch_array($query)){


$tempo = $lista_login['tempo_final'];
$despedida = $lista_login['IA_despedida'];
$agenda_verfica = $lista_login['agenda_verfica'];

} 
#######################################
######################################
#agora envi a mensagemde despedida
 
// Ajustar o fuso horário para o Brasil
mysqli_query($conn, "SET time_zone = '-03:00'");

// Definir a consulta
#$sql = "SELECT * FROM clientes WHERE usuario_api = '$usuario_api' AND situacao = '1' AND time_atendimento <= DATE_SUB(NOW(), INTERVAL '$tempo' MINUTE) LIMIT 1";
$sql = "SELECT * FROM clientes WHERE usuario_api = '$usuario_api' AND (situacao = '1' OR situacao IS NULL) AND time_atendimento <= DATE_SUB(NOW(), INTERVAL '$tempo' MINUTE) LIMIT 1";

// Executar a consulta
$query = mysqli_query($conn, $sql);
$total = mysqli_num_rows($query);

if (!$query) {
    die("Erro na consulta SQL: " . mysqli_error($conn));
}

while($lista_login = mysqli_fetch_array($query)){

$id = $lista_login['id'];
$telefone = $lista_login['telefone'];
######################################
########## atualizar a siotuação do cliente
######################################



}
if($total > 0){

$sql = "UPDATE clientes SET situacao = '2' WHERE id = '$id'";
$query = mysqli_query($conn,$sql);


if($query){
  
    

$sql = "INSERT INTO envio (comando,telefone,msg,status,usuario_api) VALUES ('MsgTexto','$telefone','$despedida','1','$usuario_api')";
$query = mysqli_query($conn,$sql);

$sql = "DELETE FROM ia_historico WHERE usuario_api='$usuario_api' AND telefone_usuario = '$telefone'";
$query = mysqli_query($conn,$sql);


}
}
}

$usuario_api = $_POST['usuario'];   

$sql = "SELECT * FROM login WHERE usuario_api = '$usuario_api' ";
$query = mysqli_query($conn,$sql);
$total = mysqli_num_rows($query);

while($lista_login = mysqli_fetch_array($query)){



$tempo_verificar = $lista_login['tempo_verifica'];

}   
 
 
 
 
 

#$usuario_api = 'agenda_3184767335'; // Usuário específico para filtrar
$data_atual = date('Y-m-d'); // Data atual
#$data_atual = '2024-11-18';
$hora_atual = date('H:i'); // Hora atual (apenas horas e minutos)
$usuario_api = $_POST['usuario'];   
$telefone = $_POST['telefone'];
// Consulta SQL para encontrar o horário mais próximo


// Variáveis
#$usuario_api = 'agenda_3184767335';
#$tempo_verificar = 10; // Tempo em minutos para o lembrete (exemplo: 10 minutos)

// Data e hora atuais
$data_hora_atual = date('Y-m-d H:i:s');

// Query SQL para buscar agendamentos
$sql = "
    SELECT *, 
           TIMESTAMP(data, horario) AS agendamento_completo, 
           TIMESTAMP(data, horario) - INTERVAL $tempo_verificar MINUTE AS lembrete_ajustado 
    FROM agendamento 
    WHERE usuario_api = '$usuario_api'
      AND TIMESTAMP(data, horario) - INTERVAL $tempo_verificar MINUTE <= '$data_hora_atual'
      AND lembrete = '0'
    ORDER BY agendamento_completo ASC
    LIMIT 1
";


$query = mysqli_query($conn, $sql);

// Verifica se há resultados
if (mysqli_num_rows($query) > 0) {
    while ($row = mysqli_fetch_assoc($query)) {
       $id = $row['id'];
       $telefone= $row['cliente_telefone'];
        $profissional_nome = $row['profissional_nome'];
        $data =  $row['data'] ;
         $horario = $row['horario'] ;
        
        /*echo "Usuário API: " . $row['usuario_api'] . "\n";
        echo "Login: " . $row['login'] . "\n";
        echo "Dia: " . $row['dia'] . "\n";
        echo "Horário: " . $row['horario'] . "\n";
       
        echo "Cargo: " . $row['profissional_cargo'] . "\n";
        
        echo "Nome Cliente: " . $row['cliente_nome'] . "\n";
        echo "Data: " . $row['data'] . "\n";
        echo "ID Profissional: " . $row['id_profissional'] . "\n";
        echo "Confirmação: " . $row['confirmacao'] . "\n";
        echo "Lembrete: " . $row['lembrete'] . "\n";*/
    }    
 
 $sql_busca_usuario = "SELECT * FROM login WHERE usuario_api = '$usuario_api'";
    $query_busca_usuario = mysqli_query($conn, $sql_busca_usuario);
    if ($row_usuario = mysqli_fetch_array($query_busca_usuario)) {
        $login = $row_usuario['login'];
       $agenda_verfica = $row_usuario['agenda_verfica'];
    }
                
     
     
     
         $sql_busca_clientes = "SELECT * FROM clientes WHERE telefone = '$telefone' AND usuario_api = '$usuario_api'";
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
            
    $agenda_verfica =  novo_texto($agenda_verfica, $nome, $agendamento, $profissional);       
    
function formatarTexto($texto) {
    // Remove todas as quebras de linha e espaços extras
    $texto = preg_replace('/\s+/', ' ', trim($texto));
    
    // Adiciona "Sim" e "Não" ao final
    $texto .= "\nSim\nNão";

    return $texto;
}


$agenda_verfica2 =  novo_texto($agenda_verfica, $nome, $agendamento, $profissional);       
$agenda_verfica3 = formatarTexto($agenda_verfica2);
#$agenda_verifica3  = "\nSim\nNão";
#$agenda_verfica3 = $agenda_verfica2 . $agenda_verifica3;
                         
$sql = "INSERT INTO envio (comando,telefone,msg,status,usuario_api) VALUES ('Enquete','$telefone','$agenda_verfica3','1','$usuario_api')";
$query = mysqli_query($conn,$sql);   

$sql = "UPDATE agendamento SET lembrete = '2' WHERE id='$id'";
$query = mysqli_query($conn, $sql);


    
} else {
   # echo "Nenhum agendamento encontrado para as condições fornecidas.";
}

 
 
 
 
 
 
 
 
 
 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 $usuario_api = $_POST['usuario'];    
    
    $sql_config = "SELECT * FROM envio WHERE status = '1' AND usuario_api = '$usuario_api'  ORDER BY id DESC";
    $query_config = mysqli_query($conn, $sql_config);
    $total_config = mysqli_num_rows($query_config);

    while ($rows_usuarios = mysqli_fetch_array($query_config)) {
        $id_msg = $rows_usuarios['id'];
        $telefone = $rows_usuarios['telefone'];
        $msg = $rows_usuarios['msg'];
        $status = $rows_usuarios['status'];
        $usuario = $rows_usuarios['usuario_api'];
         $comando = $rows_usuarios['comando'];


}



if ($total_config > 0){
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);    

    
$response = [
            'id' => '1',
            'id_msg' => $id_msg,
            'comando' => $comando,
            'msg' => $msg,
            'telefone' => $telefone,
            'url' => null,
            'tipo' => 'MsgTexto'
        ];    
   // Enviar a resposta em formato JSON
        echo json_encode($response);
    
         $sql = "UPDATE envio SET status = '2' WHERE id='$id_msg'";
        $query = mysqli_query($conn, $sql);
    
}}
 

 ?>
 










 