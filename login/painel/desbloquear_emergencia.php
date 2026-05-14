<?php
// ARQUIVO DE EMERGÊNCIA - DELETE APÓS O USO
include 'conn.php';

$usuario = '5511994040494';

// Lê o código atual e desbloqueia
$sql = "SELECT login, code_autorizado, autorizado FROM login WHERE login = '$usuario'";
$query = mysqli_query($conn, $sql);
$dados = mysqli_fetch_assoc($query);

if ($dados) {
    // Atualiza para autorizado=2 (sem necessidade de código)
    $update = "UPDATE login SET autorizado = '2' WHERE login = '$usuario'";
    $result = mysqli_query($conn, $update);

    if ($result) {
        echo "<h2 style='color:green;font-family:sans-serif'>✅ Usuário desbloqueado com sucesso!</h2>";
        echo "<p style='font-family:sans-serif'>Usuário <b>$usuario</b> agora pode fazer login diretamente sem código.</p>";
        echo "<p style='font-family:sans-serif'>⚠️ <b>DELETE este arquivo do servidor agora!</b></p>";
        echo "<p style='font-family:sans-serif'><a href='login.php'>Ir para o Login</a></p>";
    } else {
        echo "<h2 style='color:red;font-family:sans-serif'>❌ Erro ao desbloquear.</h2>";
        echo "<p>" . mysqli_error($conn) . "</p>";
    }
} else {
    echo "<h2 style='color:orange;font-family:sans-serif'>⚠️ Usuário não encontrado: $usuario</h2>";
    
    // Lista todos os usuários disponíveis
    $todos = mysqli_query($conn, "SELECT login, autorizado, code_autorizado FROM login");
    echo "<p style='font-family:sans-serif'>Usuários no banco:</p><table border='1' cellpadding='5' style='font-family:sans-serif'>";
    echo "<tr><th>Login</th><th>Autorizado</th><th>Código</th></tr>";
    while ($row = mysqli_fetch_assoc($todos)) {
        echo "<tr><td>{$row['login']}</td><td>{$row['autorizado']}</td><td>{$row['code_autorizado']}</td></tr>";
    }
    echo "</table>";
}

mysqli_close($conn);
?>
