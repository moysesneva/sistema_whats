<?php
function criarEventoGoogleCalendar($client_id, $client_secret, $refresh_token, $titulo, $descricao, $local, $data, $hora_inicio, $hora_fim, $timeZone = 'America/Sao_Paulo') {
    // 🔑 Obter access_token
    $tokenUrl = "https://oauth2.googleapis.com/token";
    $postFields = http_build_query([
        'client_id'     => $client_id,
        'client_secret' => $client_secret,
        'refresh_token' => $refresh_token,
        'grant_type'    => 'refresh_token'
    ]);

    $ch = curl_init($tokenUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
    $tokenResponse = curl_exec($ch);
    curl_close($ch);

    $tokenData = json_decode($tokenResponse, true);
    if (empty($tokenData['access_token'])) {
        return [
            'success' => false,
            'error'   => "Erro ao obter access_token: " . $tokenResponse
        ];
    }
    $accessToken = $tokenData['access_token'];

    // 📅 Montar datas no formato RFC 3339
    $startDateTime = "{$data}T{$hora_inicio}-03:00";
    $endDateTime   = "{$data}T{$hora_fim}-03:00";

    // 📄 Dados do evento
    $evento = [
        "summary"     => $titulo,
        "location"    => $local,
        "description" => $descricao,
        "start"       => [
            "dateTime" => $startDateTime,
            "timeZone" => $timeZone
        ],
        "end"         => [
            "dateTime" => $endDateTime,
            "timeZone" => $timeZone
        ]
    ];

    // 🚀 Enviar para API do Google Calendar
    $ch = curl_init("https://www.googleapis.com/calendar/v3/calendars/primary/events");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($evento));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer {$accessToken}",
        "Content-Type: application/json"
    ]);
    $response   = curl_exec($ch);
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [
        'success'    => $statusCode >= 200 && $statusCode < 300,
        'statusCode' => $statusCode,
        'response'   => json_decode($response, true)
    ];
}
?>
