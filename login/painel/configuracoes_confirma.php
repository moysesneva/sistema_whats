<?php
session_start();
$login = $_SESSION['login'];

// 1. INCLUIR O ARQUIVO DE CONEXÃO
require 'conn.php';

// 2. VERIFICAR SE O USUÁRIO ESTÁ LOGADO


// 3. VERIFICAR SE O FORMULÁRIO FOI ENVIADO
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 4. OBTER O ID DO USUÁRIO DA SESSÃO
    $id_usuario = $_SESSION['id']; // ID do usuário logado
    $caminho_logo_db = null; // Inicia a variável do logo como nula

    // 5. PROCESSAR O UPLOAD DO LOGO (SE UM ARQUIVO FOI ENVIADO E VÁLIDO)
    // Esta lógica permanece a mesma, pois já é condicional
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0 && !empty($_FILES['logo']['name'])) {
        $diretorio_upload = 'uploads/';
        if (!is_dir($diretorio_upload)) {
            mkdir($diretorio_upload, 0755, true);
        }
        $nome_arquivo = basename($_FILES['logo']['name']);
        $extensao = strtolower(pathinfo($nome_arquivo, PATHINFO_EXTENSION));
        $novo_nome_arquivo = "logo_" . $id_usuario . "_" . time() . "." . $extensao;
        $caminho_logo_db = $diretorio_upload . $novo_nome_arquivo;
        $tipos_permitidos = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($extensao, $tipos_permitidos)) {
            if (!move_uploaded_file($_FILES['logo']['tmp_name'], $caminho_logo_db)) {
                echo "Erro ao mover o arquivo de logo.";
                $caminho_logo_db = null;
            }
        } else {
            echo "Tipo de arquivo de logo não permitido.";
            $caminho_logo_db = null;
        }
    }

    // 6. CONSTRUIR A QUERY DE UPDATE DINAMICAMENTE
    $campos = [];
    $tipos = "";
    $valores = [];

    // VERIFICAÇÃO 1: Adiciona 'nome_empresa' APENAS se não estiver vazio
    if (!empty(trim($_POST['nome_empresa']))) {
        $campos[] = "nome_empresa = ?";
        $tipos .= "s";
        $valores[] = trim($_POST['nome_empresa']);
    }
    
    // VERIFICAÇÃO 2: Adiciona 'tema' APENAS se não estiver vazio
    if (!empty($_POST['tema'])) {
        $campos[] = "tema = ?";
        $tipos .= "s";
        $valores[] = $_POST['tema'];
    }
    
      if (!empty($_POST['numero_whatsapp'])) {
        $campos[] = "numero_bot = ?";
        $tipos .= "s";
        $valores[] = $_POST['numero_whatsapp'];
    }
    
    
    // VERIFICAÇÃO 3: Adiciona 'logo' APENAS se o upload foi bem-sucedido
    if ($caminho_logo_db !== null) {
        $campos[] = "logo = ?";
        $tipos .= "s";
        $valores[] = $caminho_logo_db;
    }

    // 7. VERIFICAR SE HÁ ALGO PARA ATUALIZAR
    if (empty($campos)) {
        // Se o array $campos estiver vazio, nada foi alterado pelo usuário.
        // Apenas redireciona de volta.
        header("Location: configuracoes.php?status=sem_alteracoes");
        exit();
    }

    // 8. FINALIZAR A CONSTRUÇÃO DA QUERY (COM O WHERE SEGURO)
    
    $sql = "UPDATE login SET " . implode(", ", $campos) . " WHERE login = ?";
$tipos .= "s"; // 's' para o tipo string do 'login'
$valores[] = $login;




    // 9. PREPARAR E EXECUTAR A QUERY COM MYSQLI
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        // Associa os valores aos parâmetros da query
        mysqli_stmt_bind_param($stmt, $tipos, ...$valores);

        // Executa a query
        if (mysqli_stmt_execute($stmt)) {
            // Sucesso! Redireciona de volta para a página de configurações
            header("Location: configuracoes.php?status=sucesso");
            exit();
        } else {
            // Falha na execução
            echo "Erro ao atualizar as configurações: " . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
    } else {
        // Falha na preparação da query
        echo "Erro ao preparar a query: " . mysqli_error($conn);
    }

    // 10. FECHAR A CONEXÃO
    mysqli_close($conn);

} else {
    // Se o acesso não for via POST, redireciona
    header("Location: configuracoes.php");
    exit();
}
?>