<?php
session_start();
include 'funcoes.php';
$login = $_SESSION['login'];
include 'conn.php';
include 'config_dados.php';


function somenteNumeros($texto) {
    // Remove tudo que não for número
    return preg_replace('/\D/', '', $texto);
}
// cadastrar_profissional_confirma.php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $nomeProfissional = mysqli_real_escape_string($conn, $_POST['nomeProfissional']);
    $codigoPais = mysqli_real_escape_string($conn, $_POST['codigoPais']);
    $telefoneProfissional = mysqli_real_escape_string($conn, $_POST['telefoneProfissional']);
    $especialidadeProfissional = mysqli_real_escape_string($conn, $_POST['especialidadeProfissional']);
    
    // Remove caracteres não numéricos do telefone
    $telefoneLimpo = $codigoPais.$telefoneProfissional;
    #$telefoneLimpo = preg_replace('/[^0-9]/', '', $telefoneProfissional);
    
    // Gera um usuario_api único para o profissional
    $usuario_api = 'agenda_' . $login;
    $telefoneLimpo = somenteNumeros($telefoneLimpo);

    // Inicia transação
    mysqli_begin_transaction($conn);
    
    try {
        // 1. Inserir na tabela profissional
        $sql_profissional = "INSERT INTO profissional (usuario_api, login, profissional_nome, profissional_cargo, telefone, codigo_pais) 
                            VALUES ('$usuario_api', '$login', '$nomeProfissional', '$especialidadeProfissional', '$telefoneLimpo', '$codigoPais')";
        
        if (!mysqli_query($conn, $sql_profissional)) {
            throw new Exception("Erro ao inserir profissional: " . mysqli_error($conn));
        }
        
        // 2. Inserir na tabela login com senha padrão 123456 e tipo 5
        $senha_padrao = '123456'; // Em produção, usar hash
        $tipo = 5;
        
        
        $sql = "SELECT * FROM login WHERE login = '$telefoneLimpo'";
        $query = mysqli_query($conn, $sql);
        $total = mysqli_num_rows($query);
        
      
        if($total == 0){
            
        
        $sql_login = "INSERT INTO login (login, senha, tipo,perfil_img, usuario_api, nome, autorizado,modo_atuante) 
                     VALUES ('$telefoneLimpo', '$senha_padrao', '$tipo','img/perfil.png', '$usuario_api', '$nomeProfissional', 2,'prof')";
     
        
        if (!mysqli_query($conn, $sql_login)) {
            throw new Exception("Erro ao inserir login: " . mysqli_error($conn));
        }}
        
        // Confirma transação
        mysqli_commit($conn);
        
        echo "<script>
                alert('Profissional cadastrado com sucesso!');
                window.location.href = 'listar_profissionais.php';
              </script>";
        
    } catch (Exception $e) {
        // Desfaz transação em caso de erro
        mysqli_rollback($conn);
        
        echo "<script>
                alert('Erro ao cadastrar profissional: " . $e->getMessage() . "');
                window.history.back();
              </script>";
    }
}
?>