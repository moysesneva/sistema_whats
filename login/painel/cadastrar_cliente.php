<?php
include 'conn.php';

$nome_pre = isset($_GET['nome']) ? trim($_GET['nome']) : '';
$telefone_pre = isset($_GET['telefone']) ? trim($_GET['telefone']) : '';
$mensagem = '';

// Processar cadastro se foi enviado via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome']);
    $telefone = trim($_POST['telefone']);
    $email = trim($_POST['email']);
    $id_agendamento = trim($_POST['id_agendamento']);
    $usuario_api = trim($_POST['usuario_api']);
    
    // Validações básicas
    if (empty($nome) || empty($telefone)) {
        $mensagem = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Nome e telefone são obrigatórios.</div>';
    } else {
        // Verificar se telefone já existe
        $sql_check = "SELECT id FROM clientes WHERE telefone = ?";
        if ($stmt_check = mysqli_prepare($conn, $sql_check)) {
            mysqli_stmt_bind_param($stmt_check, "s", $telefone);
            mysqli_stmt_execute($stmt_check);
            $result_check = mysqli_stmt_get_result($stmt_check);
            
            if (mysqli_num_rows($result_check) > 0) {
                $mensagem = '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> Este telefone já está cadastrado no sistema.</div>';
            } else {
                // Inserir novo cliente
                $sql_insert = "INSERT INTO clientes (nome, telefone, email, id_agendamento, usuario_api, data_cadastro) VALUES (?, ?, ?, ?, ?, NOW())";
                if ($stmt_insert = mysqli_prepare($conn, $sql_insert)) {
                    mysqli_stmt_bind_param($stmt_insert, "sssss", $nome, $telefone, $email, $id_agendamento, $usuario_api);
                    
                    if (mysqli_stmt_execute($stmt_insert)) {
                        $novo_cliente_id = mysqli_insert_id($conn);
                        $mensagem = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Cliente cadastrado com sucesso!</div>';
                        
                        // Auto-redirecionar para agendamento após 2 segundos
                        echo '<script>
                                setTimeout(function() {
                                    window.location.href = "agendamento.php";
                                }, 2000);
                              </script>';
                    } else {
                        $mensagem = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Erro ao cadastrar cliente. Tente novamente.</div>';
                    }
                    mysqli_stmt_close($stmt_insert);
                }
            }
            mysqli_stmt_close($stmt_check);
        }
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Novo Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
        }
        
        .header-top {
            background: linear-gradient(120deg, #007bff, #0056b3);
            padding: 20px 0;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .header-top h1 {
            color: white;
            font-size: 1.8rem;
            font-weight: 600;
            margin: 0;
        }
        
        .main-container {
            max-width: 600px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        .booking-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            padding: 40px 30px;
            margin-bottom: 20px;
        }
        
        .section-title {
            color: #007bff;
            font-size: 1.5rem;
            font-weight: 600;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .section-title i {
            font-size: 2rem;
            display: block;
            margin-bottom: 10px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: flex;
            align-items: center;
            color: #343a40;
            font-weight: 500;
            margin-bottom: 10px;
        }
        
        .form-group label i {
            margin-right: 8px;
            color: #007bff;
            font-size: 1.1rem;
        }
        
        .form-control {
            padding: 15px;
            font-size: 1rem;
            border: 2px solid #e8e8e8;
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 4px rgba(0,123,255,0.1);
            outline: none;
        }
        
        .btn-primary {
            width: 100%;
            padding: 16px;
            font-size: 1.1rem;
            font-weight: 600;
            background: linear-gradient(120deg, #007bff, #0056b3);
            border: none;
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .btn-secondary {
            width: 100%;
            padding: 12px;
            font-size: 1rem;
            margin-top: 10px;
            border-radius: 12px;
        }
        
        .help-text {
            font-size: 0.85rem;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <header class="header-top">
        <h1><i class="fas fa-user-plus"></i> Cadastrar Novo Cliente</h1>
    </header>

    <div class="main-container">
        <div class="booking-card">
            <div class="section-title">
                <i class="fas fa-user-plus"></i>
                Dados do Novo Cliente
            </div>
            
            <?= $mensagem; ?>
            
            <form method="POST" id="formCadastro">
                <div class="form-group">
                    <label for="nome">
                        <i class="fas fa-user"></i> 
                        Nome Completo *
                    </label>
                    <input type="text" class="form-control" id="nome" name="nome" 
                           value="<?= htmlspecialchars($nome_pre); ?>" 
                           placeholder="Digite o nome completo" required>
                    <div class="help-text">
                        <i class="fas fa-info-circle"></i>
                        Nome como aparecerá nos agendamentos
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="telefone">
                        <i class="fas fa-phone"></i> 
                        Telefone *
                    </label>
                    <input type="text" class="form-control" id="telefone" name="telefone" 
                           value="<?= htmlspecialchars($telefone_pre); ?>" 
                           placeholder="(11) 99999-9999" required>
                    <div class="help-text">
                        <i class="fas fa-info-circle"></i>
                        Telefone para contato e identificação
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i> 
                        E-mail (opcional)
                    </label>
                    <input type="email" class="form-control" id="email" name="email" 
                           placeholder="cliente@email.com">
                </div>
                
                <div class="form-group">
                    <label for="id_agendamento">
                        <i class="fas fa-key"></i> 
                        ID de Agendamento
                    </label>
                    <input type="text" class="form-control" id="id_agendamento" name="id_agendamento" 
                           placeholder="ID único para agendamentos">
                    <div class="help-text">
                        <i class="fas fa-info-circle"></i>
                        Identificador único para o sistema de agendamentos
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="usuario_api">
                        <i class="fas fa-cogs"></i> 
                        Usuário API (opcional)
                    </label>
                    <input type="text" class="form-control" id="usuario_api" name="usuario_api" 
                           placeholder="Identificador da API">
                    <div class="help-text">
                        <i class="fas fa-info-circle"></i>
                        Para integração com sistemas externos
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Cadastrar Cliente
                </button>
                
                <a href="agendamento.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Voltar para Busca
                </a>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    // Máscara para telefone
    $('#telefone').on('input', function() {
        let value = this.value.replace(/\D/g, '');
        value = value.replace(/^(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3');
        this.value = value;
    });
    
    // Gerar ID de agendamento automaticamente se estiver vazio
    $('#nome, #telefone').on('input', function() {
        if ($('#id_agendamento').val() === '') {
            const nome = $('#nome').val().toLowerCase().replace(/\s+/g, '');
            const telefone = $('#telefone').val().replace(/\D/g, '');
            if (nome && telefone) {
                const id = nome.substring(0, 3) + '_' + telefone.substring(-4);
                $('#id_agendamento').val(id);
            }
        }
    });
    
    // Validação do formulário
    $('#formCadastro').on('submit', function(e) {
        const nome = $('#nome').val().trim();
        const telefone = $('#telefone').val().trim();
        
        if (nome.length < 3) {
            e.preventDefault();
            alert('O nome deve ter pelo menos 3 caracteres.');
            return false;
        }
        
        if (telefone.length < 10) {
            e.preventDefault();
            alert('Por favor, digite um telefone válido.');
            return false;
        }
    });
    </script>

</body>
</html>