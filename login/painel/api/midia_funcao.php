<?php

function enviar_midia($user_id, $servidor, $porta, $token, $telefone, $tipo, $arquivo, $legenda = null) {
    $url = "https://{$servidor}:{$porta}";

    $dados = json_encode([
        'action' => 'EnviarMidia',
        'usuario' => $user_id,
        'token' => $token,
        'message' => [
            'telefone' => $telefone,
            'tipo' => $tipo,
            'arquivo' => $arquivo,
            'legenda' => $legenda
        ]
    ]);

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $dados);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($curl, CURLOPT_TIMEOUT, 300);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

    $resposta = curl_exec($curl);
    $erro = curl_error($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    if ($erro) {
        return ['status' => 'error', 'erro' => $erro];
    }

    if ($http_code != 200) {
        return ['status' => 'error', 'erro' => "HTTP $http_code"];
    }

    $resultado = json_decode($resposta, true);
    return $resultado ?: ['status' => 'error', 'erro' => 'Resposta inválida'];
}

function verificar_tamanho_arquivo($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_NOBODY, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

    curl_exec($curl);
    $tamanho = curl_getinfo($curl, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    if ($http_code == 200 && $tamanho > 0) {
        return $tamanho;
    }

    return false;
}

function baixar_midia_inteligente($url, $tipo = 'mídia') {
    echo "🔍 Analisando $tipo: $url\n";

    $tamanho = verificar_tamanho_arquivo($url);
    if ($tamanho) {
        $tamanho_mb = $tamanho / (1024 * 1024);
        echo "📊 Tamanho: " . number_format($tamanho_mb, 2) . " MB\n";

        if ($tamanho_mb > 2) {
            echo "⚡ Enviando URL direta (arquivo grande)\n";
            return $url;
        }
    }

    echo "📥 Baixando arquivo no PHP...\n";
    return baixar_midia_url($url, $tipo);
}

function baixar_midia_url($url, $tipo = 'mídia') {
    echo "📥 Baixando $tipo: $url\n";

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 300);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0');

    curl_setopt($curl, CURLOPT_PROGRESSFUNCTION, function($resource, $download_size, $downloaded, $upload_size, $uploaded) {
        if ($download_size > 0) {
            $percent = round(($downloaded / $download_size) * 100);
            echo "\r📊 Progresso: $percent% (" . number_format($downloaded / 1024, 0) . "KB)";
        }
    });
    curl_setopt($curl, CURLOPT_NOPROGRESS, false);

    $conteudo = curl_exec($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $erro = curl_error($curl);
    curl_close($curl);

    echo "\n";

    if ($erro) {
        echo "❌ Erro ao baixar: $erro\n";
        return false;
    }

    if ($http_code != 200) {
        echo "❌ HTTP Code: $http_code\n";
        return false;
    }

    echo "✅ Baixado: " . number_format(strlen($conteudo) / 1024, 2) . " KB\n";
    return base64_encode($conteudo);
}
?>
