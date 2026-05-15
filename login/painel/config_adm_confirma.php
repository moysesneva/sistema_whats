<?php
require_once __DIR__ . '/auth_guard.php';
include 'conn.php';
include 'funcoes.php';

$certo = 'config_adm.php?confirmacao=atualizado';
$erro = 'config_adm.php?erro=atualizado';

if (isset($_POST['editar_tudo'])) {
    $ip_vps  = isset($_POST['ip_vps'])  ? trim($_POST['ip_vps'])  : '';
    $porta   = isset($_POST['porta'])   ? trim($_POST['porta'])   : '';
    $webhook = isset($_POST['webhook']) ? trim($_POST['webhook']) : '';
    $chave   = isset($_POST['chave'])   ? trim($_POST['chave'])   : '';

    $stmt = mysqli_prepare($conn,
        "UPDATE config SET ip_vps = ?, porta = ?, webhook = ?, chave = ?");
    mysqli_stmt_bind_param($stmt, "ssss", $ip_vps, $porta, $webhook, $chave);

    if (mysqli_stmt_execute($stmt)) {
        VaiPara($certo);
    } else {
        VaiPara($erro);
    }
}

if (isset($_POST['modo_edicao']) && $_POST['modo_edicao'] == 1) {
    VaiPara('config_adm.php?modo=edicao');
    exit;
}

if (isset($_POST['dominio_vps'])) {
    $dominio_vps = trim($_POST['dominio_vps']);

    $stmt = mysqli_prepare($conn, "UPDATE config SET ip_vps = ?");
    mysqli_stmt_bind_param($stmt, "s", $dominio_vps);

    if (mysqli_stmt_execute($stmt)) {
        VaiPara($certo);
    } else {
        VaiPara($erro);
    }
}

if (isset($_POST['opcao_porta'])) {
    if ($_POST['opcao_porta'] == 'trocar' && isset($_POST['nova_porta'])) {
        $nova_porta = trim($_POST['nova_porta']);
        $stmt = mysqli_prepare($conn, "UPDATE config SET nova_porta = ?");
        mysqli_stmt_bind_param($stmt, "s", $nova_porta);

        if (mysqli_stmt_execute($stmt)) {
            VaiPara($certo);
        } else {
            VaiPara($erro);
        }
    } else {
        VaiPara($certo);
    }
}

if (isset($_POST['porta'])) {
    $stmt = mysqli_prepare($conn, "UPDATE config SET nova_porta = '0'");
    if (mysqli_stmt_execute($stmt)) {
        VaiPara($certo);
    } else {
        VaiPara($erro);
    }
} else {
    VaiPara($certo);
}

if (isset($_POST['webhook'])) {
    $webhook = trim($_POST['webhook']);
    $stmt = mysqli_prepare($conn, "UPDATE config SET webhook = ?");
    mysqli_stmt_bind_param($stmt, "s", $webhook);

    if (mysqli_stmt_execute($stmt)) {
        VaiPara($certo);
    } else {
        VaiPara($erro);
    }
} else {
    VaiPara($certo);
}

if (isset($_POST['opcao_porta'])) {
    if ($_POST['opcao_porta'] == 'manter' && isset($_POST['nova_porta'])) {
        $nova_porta = '2222';
        $stmt = mysqli_prepare($conn, "UPDATE config SET nova_porta = ?");
        mysqli_stmt_bind_param($stmt, "s", $nova_porta);

        if (mysqli_stmt_execute($stmt)) {
            VaiPara($certo);
        } else {
            VaiPara($erro);
        }
    } else {
        VaiPara($certo);
    }
}

if (isset($_POST['chave_api_edita_codigo'])) {
    $chave_api = trim($_POST['chave_api_edita_codigo']);

    $stmt = mysqli_prepare($conn, "UPDATE config SET chave = ?");
    mysqli_stmt_bind_param($stmt, "s", $chave_api);

    if (mysqli_stmt_execute($stmt)) {
        VaiPara($certo);
    } else {
        VaiPara($erro);
    }
}

if (isset($_POST['arquivo_modelo'])) {
    $arquivo_modelo = trim($_POST['arquivo_modelo']);

    $stmt = mysqli_prepare($conn, "UPDATE config SET caminho_modelo = ?");
    mysqli_stmt_bind_param($stmt, "s", $arquivo_modelo);

    if (mysqli_stmt_execute($stmt)) {
        VaiPara($certo);
    } else {
        VaiPara($erro);
    }
}
?>
