<?php
$creditos = $creditos - 1;
$stmt = $conn->prepare("UPDATE login SET creditos = ? WHERE usuario_api = ?");
$stmt->bind_param("is", $creditos, $usuario_api);
$query = $stmt->execute();
$stmt->close();
?>
