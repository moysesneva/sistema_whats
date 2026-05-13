<?php
// dados da credencial
$client_id     = "106056944943-8stg3lo5cllnce5co6pu8lvo8p63oasq.apps.googleusercontent.com";
$redirect_uri  = "https://editacodigo.com.br/calendario";
$scope         = "https://www.googleapis.com/auth/calendar";

// pega um state opcional (ex: ?state=cliente1.com.br)
$state = isset($_GET['state']) ? trim($_GET['state']) : '';

// monta URL de autorização
$params = http_build_query([
    'client_id'     => $client_id,
    'redirect_uri'  => $redirect_uri,
    'response_type' => 'code',
    'scope'         => $scope,
    'access_type'   => 'offline',
    'prompt'        => 'consent',
    'state'         => $state
]);

$auth_url = "https://accounts.google.com/o/oauth2/auth?$params";
?>
<!DOCTYPE html>
<html lang="pt-br">
<head><meta charset="utf-8"><title>Autorizar Google Agenda</title></head>
<body>
  <h2>1) Autorize o acesso ao Google Agenda</h2>
  <p><a href="<?= htmlspecialchars($auth_url) ?>" target="_blank">
    Clique aqui para autorizar
  </a></p>
  <p>Você será levado de volta a: <code><?= htmlspecialchars($redirect_uri) ?></code></p>
</body>
</html>
