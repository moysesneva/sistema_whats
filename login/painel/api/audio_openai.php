<?php


function salvar_audio_temporario($audioBase64) {
    // Decodifica o áudio base64
    $audioDecodificado = base64_decode($audioBase64);

    // Define a pasta onde o áudio será salvo
    $pasta = __DIR__ . '/audio/';
    
    // Cria a pasta se não existir
    if (!is_dir($pasta)) {
        mkdir($pasta, 0755, true);
    }

    // Gerar um nome de arquivo único
    $nomeArquivo = $pasta . uniqid('audio_', true) . '.mp3'; // Você pode ajustar a extensão se souber o formato
    
    // Salva o áudio na pasta
    file_put_contents($nomeArquivo, $audioDecodificado);

    return $nomeArquivo;
}


$caminho_audio = salvar_audio_temporario($audio_recebido);


$audio_file_path = $caminho_audio;


$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.openai.com/v1/audio/transcriptions',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => array(
    'file' => new CURLFILE($audio_file_path),
    'model' => 'whisper-1'
  ),
  CURLOPT_HTTPHEADER => array(
    'Authorization: Bearer ' . $chave
  ),
));

$response = curl_exec($curl);

curl_close($curl);


$msg =  $response;


?>