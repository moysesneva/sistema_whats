<?php
// ARQUIVO DE EMERGÊNCIA - DELETE APÓS O USO
include 'conn.php';

$usuario = '5511994040494';

$stmt = $conn->prepare("SELECT login, code_autorizado, autorizado FROM login WHERE login = ?");
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();
$dados = $result->fetch_assoc();
$stmt->close();

if ($dados) {
    $stmt_update = $conn->prepare("UPDATE login SET autorizado = '2' WHERE login = ?");
    $stmt_update->bind_param("s", $usuario);
    $result_update = $stmt_update->execute();
    $stmt_update->close();

    if ($result_update) {
        echo "<h2 style='color:green;font-family:sans-serif'>✅ Usuário desbloqueado com sucesso!</h2>";
        echo "<p style='font-family:sans-serif'>Usuário <b>" . htmlspecialchars($usuario, ENT_QUOTES, 'UTF-8') . "</b> agora pode fazer login diretamente sem código.</p>";
        echo "<p style='font-family:sans-serif'>⚠️ <b>DELETE este arquivo do servidor agora!</b></p>";
        echo "<p style='font-family:sans-serif'><a href='login.php'>Ir para o Login</a></p>";
    } else {
        echo "<h2 style='color:red;font-family:sans-serif'>❌ Erro ao desbloquear.</h2>";
        echo "<p>" . $conn->error . "</p>";
    }
} else {
    echo "<h2 style='color:orange;font-family:sans-serif'>⚠️ Usuário não encontrado: " . htmlspecialchars($usuario, ENT_QUOTES, 'UTF-8') . "</h2>";
    
    $todos = mysqli_query($conn, "SELECT login, autorizado, code_autorizado FROM login");
    echo "<p style='font-family:sans-serif'>Usuários no banco:</p><table border='1' cellpadding='5' style='font-family:sans-serif'>";
    echo "<tr><th>Login</th><th>Autorizado</th><th>Código</th></tr>";
    while ($row = mysqli_fetch_assoc($todos)) {
        echo "<tr><td>" . htmlspecialchars($row['login'], ENT_QUOTES, 'UTF-8') . "</td><td>" . htmlspecialchars($row['autorizado'], ENT_QUOTES, 'UTF-8') . "</td><td>" . htmlspecialchars($row['code_autorizado'], ENT_QUOTES, 'UTF-8') . "</td></tr>";
    }
    echo "</table>";
}

mysqli_close($conn);
?>
