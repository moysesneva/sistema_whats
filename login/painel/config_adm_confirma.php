<?php
include 'conn.php'; // Inclui a conexão com o banco de dados
include 'funcoes.php'; // Inclui a função VaiPara

// Define as páginas de redirecionamento
$certo = 'config_adm.php?confirmacao=atualizado';
$erro = 'config_adm.php?erro=atualizado';

// Verifica se estamos processando o formulário unificado
if (isset($_POST['editar_tudo'])) {
    // Coleta todos os dados do formulário
    $ip_vps = isset($_POST['ip_vps']) ? mysqli_real_escape_string($conn, $_POST['ip_vps']) : '';
    $porta = isset($_POST['porta']) ? mysqli_real_escape_string($conn, $_POST['porta']) : '';
    $webhook = isset($_POST['webhook']) ? mysqli_real_escape_string($conn, $_POST['webhook']) : '';
    $chave = isset($_POST['chave']) ? mysqli_real_escape_string($conn, $_POST['chave']) : '';
    
    // Prepara a query para atualizar todos os campos
    $sql_update = "UPDATE config SET 
                   ip_vps = '$ip_vps',
                   porta = '$porta',
                   webhook = '$webhook',
                   chave = '$chave'";
    
    $query_update = mysqli_query($conn, $sql_update);
    
    // Redireciona dependendo do resultado da query
    if ($query_update) {
        VaiPara($certo);
    } else {
        VaiPara($erro);
    }
}

// Verifica se estamos entrando no modo de edição
if (isset($_POST['modo_edicao']) && $_POST['modo_edicao'] == 1) {
    // Redireciona para o modo de edição
    VaiPara('config_adm.php?modo=edicao');
    exit;
}

// Verifica se o formulário de domínio da VPS foi enviado
if (isset($_POST['dominio_vps'])) {
    $dominio_vps = $_POST['dominio_vps'];

    // Atualiza o domínio da VPS no banco de dados
    $sql_update_dominio = "UPDATE config SET ip_vps = '$dominio_vps'";
    $query_dominio = mysqli_query($conn, $sql_update_dominio);

    // Redireciona dependendo do resultado da query
    if ($query_dominio) {
        VaiPara($certo);
    } else {
        VaiPara($erro);
    }
}

// Verifica se o formulário de porta da VPS foi enviado
if (isset($_POST['opcao_porta'])) {
    // Se a opção for "Trocar", pega o valor da nova porta
    if ($_POST['opcao_porta'] == 'trocar' && isset($_POST['nova_porta'])) {
        $nova_porta = $_POST['nova_porta'];
        $sql_update_porta = "UPDATE config SET nova_porta = '$nova_porta'";
        $query_porta = mysqli_query($conn, $sql_update_porta);

        // Redireciona dependendo do resultado da query
        if ($query_porta) {
            VaiPara($certo);
        } else {
            VaiPara($erro);
        }
    } else {
        // Mantém a porta 5000 se "Manter" for selecionado
        VaiPara($certo);
    }
}

// Verifica se o formulário de porta da VPS foi enviado
if (isset($_POST['porta'])) {
    // Se a opção for "Trocar", pega o valor da nova porta
    
       
        $sql_update_porta = "UPDATE config SET nova_porta = '0'";
        $query_porta = mysqli_query($conn, $sql_update_porta);

        // Redireciona dependendo do resultado da query
        if ($query_porta) {
            VaiPara($certo);
        } else {
            VaiPara($erro);
        }
    } else {
        // Mantém a porta 5000 se "Manter" for selecionado
        VaiPara($certo);
    }




// Verifica se o formulário de porta da VPS foi enviado
if (isset($_POST['webhook'])) {
    // Se a opção for "Trocar", pega o valor da nova porta
            $webhook = mysqli_real_escape_string($conn, $_POST['webhook']);

       
        $sql_update_porta = "UPDATE config SET webhook = '$webhook'";
        $query_porta = mysqli_query($conn, $sql_update_porta);

        // Redireciona dependendo do resultado da query
        if ($query_porta) {
            VaiPara($certo);
        } else {
            VaiPara($erro);
        }
    } else {
        // Mantém a porta 5000 se "Manter" for selecionado
        VaiPara($certo);
    }




// Verifica se o formulário de porta da VPS foi enviado
if (isset($_POST['opcao_porta'])) {
    // Se a opção for "Trocar", pega o valor da nova porta
    if ($_POST['opcao_porta'] == 'manter' && isset($_POST['nova_porta'])) {
        #$nova_porta = $_POST['nova_porta'];
        $nova_porta = '2222';
        $sql_update_porta = "UPDATE config SET nova_porta = '$nova_porta'";
        $query_porta = mysqli_query($conn, $sql_update_porta);

        // Redireciona dependendo do resultado da query
        if ($query_porta) {
            VaiPara($certo);
        } else {
            VaiPara($erro);
        }
    } else {
        // Mantém a porta 5000 se "Manter" for selecionado
        VaiPara($certo);
    }
}




// Verifica se o formulário de chave da API foi enviado
if (isset($_POST['chave_api_edita_codigo'])) {
    $chave_api = $_POST['chave_api_edita_codigo'];

    // Atualiza a chave da API no banco de dados
    $sql_update_chave_api = "UPDATE config SET chave = '$chave_api'";
    $query_chave_api = mysqli_query($conn, $sql_update_chave_api);

    // Redireciona dependendo do resultado da query
    if ($query_chave_api) {
        VaiPara($certo);
    } else {
        VaiPara($erro);
    }
}

// Verifica se o formulário de caminho de arquivo modelo foi enviado
if (isset($_POST['arquivo_modelo'])) {
    $arquivo_modelo = $_POST['arquivo_modelo'];

    // Atualiza o caminho do arquivo modelo no banco de dados
    $sql_update_arquivo_modelo = "UPDATE config SET caminho_modelo = '$arquivo_modelo'";
    $query_arquivo_modelo = mysqli_query($conn, $sql_update_arquivo_modelo);

    // Redireciona dependendo do resultado da query
    if ($query_arquivo_modelo) {
        VaiPara($certo);
    } else {
        VaiPara($erro);
    }
}
?>
