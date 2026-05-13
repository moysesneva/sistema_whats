<?php
include 'login/painel/conn.php';

include 'login/painel/funcoes.php';


$idd = $_POST['idd'];
$nome = $_POST['nome'];
#print_r($_REQUEST);

$sql_busca_clientes = "SELECT * FROM clientes WHERE id_agendamento = '$idd'";
$query_busca_clientes = mysqli_query($conn, $sql_busca_clientes);
$total_busca_clientes = mysqli_num_rows($query_busca_clientes);


#echo 'Total ' . $total_busca_clientes;



if($total_busca_clientes == 1){
    
$sql = "UPDATE clientes SET nome = '$nome' WHERE id_agendamento = '$idd'";
$query = mysqli_query($conn,$sql);
if($query){
  
VaiPara('agendar.php?id='.$idd);   
}#if($totalmail > 0){



}else{
    
VaiPara('agendar.php?id='.$idd);    
} 
