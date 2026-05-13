<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edita Código Instalação do Site </title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e9ecef;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #343a40;
        }
        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            max-width: 500px;
            width: 100%;
            text-align: center;
        }
        h1 {
            margin-bottom: 20px;
            color: #007bff;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 5px;
            text-align: left;
            color: #495057;
        }
        input[type="text"], input[type="password"] {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 16px;
            transition: border-color 0.2s;
        }
        input[type="text"]:focus, input[type="password"]:focus {
            border-color: #80bdff;
            outline: none;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.2s;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Instalação do Site <b>SISTEMA AGENDAMENTO</b></h1>
<h2><a href="https://editacodigo.com.br/" target="_blank">Edita Código</a></h2>
        <form action="process_install.php" method="post">
            <label for="host">Host do Banco de Dados:</label>
            <input type="text" id="host" name="host" required>
            <label for="user">Usuário do Banco de Dados:</label>
            <input type="text" id="user" name="user" required>
            <label for="password">Senha do Banco de Dados:</label>
            <input type="password" id="password" name="password" required>
            <label for="dbname">Nome do Banco de Dados:</label>
            <input type="text" id="dbname" name="dbname" required>
            <input type="submit" value="Instalar">
        </form>
    </div>
</body>
</html>
