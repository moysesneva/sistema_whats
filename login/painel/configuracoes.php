<?php
session_start();
include 'funcoes.php';

if(!isset($_SESSION['login'])) {
VaiPara('login.php');
} 
#$_SESSION['tipo_menu'] = 1;
$login = $_SESSION['login'];

include 'conn.php';

include 'estilo.php';

include 'css_de_icones.php';

if (isset($_GET['pagina_nome'])) {
$pagina_nome_recebe = $_GET['pagina_nome'];
}else{
$pagina_nome_recebe = 0;    
}

$stmt_busca_usuario = $conn->prepare("SELECT * FROM login WHERE login = ?");
$stmt_busca_usuario->bind_param("s", $login);
$stmt_busca_usuario->execute();
$query_busca_usuario = $stmt_busca_usuario->get_result();
$total_busca_usuario = $query_busca_usuario->num_rows;

while($rows_usuarios = $query_busca_usuario->fetch_array()) {
    $nome  = Priletra($rows_usuarios['nome']);
    $img_perfil  = $rows_usuarios['perfil_img'];
    $autorizado  = $rows_usuarios['autorizado'];
    $tipo  = $rows_usuarios['tipo'];

}
#####DEFINIMOS QUE  O TIPO DO MENU
## 1 É O ADM
## 2 É  O USUARIO
include 'menu.php';

if($total_busca_usuario != 1){
    VaiPara('login.php');
}
if($autorizado != 2){
 VaiPara('desbloquar.php');
}

?>
<?php include 'header.php'; ?>

    <style>

        .form-header {
            text-align: center;
            margin-bottom: 35px;
        }

        .form-header h2 {
            color: #333;
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .form-header p {
            color: #666;
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            color: #333;
            font-weight: 500;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .form-group label::before {
            content: '';
            display: inline-block;
            width: 4px;
            height: 4px;
            background: #667eea;
            border-radius: 50%;
            margin-right: 8px;
        }

        .file-input-wrapper {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .file-input {
            display: none;
        }

        .file-input-label {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            border: 2px dashed #667eea;
            border-radius: 10px;
            background: #f8f9ff;
            cursor: pointer;
            transition: all 0.3s ease;
            color: #667eea;
            font-weight: 500;
        }

        .file-input-label:hover {
            border-color: #764ba2;
            background: #f0f2ff;
            color: #764ba2;
        }

        .file-input-label::before {
            content: '📁';
            font-size: 1.5rem;
            margin-right: 10px;
        }

        .text-input {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #fafbfc;
        }

        .text-input:focus {
            outline: none;
            border-color: #667eea;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .select-input {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 1rem;
            background: #fafbfc;
            cursor: pointer;
            transition: all 0.3s ease;
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 15px center;
            background-repeat: no-repeat;
            background-size: 16px;
            padding-right: 50px;
        }

        .select-input:focus {
            outline: none;
            border-color: #667eea;
            background-color: #fff;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .submit-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        @media (max-width: 480px) {
            .form-container {
                padding: 25px;
                margin: 10px;
            }
            
            .form-header h2 {
                font-size: 1.6rem;
            }
        }
    </style>
    <div class="form-container">
        <div class="form-header">
            <h2>⚙️ Configurações da Página</h2>
            <p>Personalize a aparência da sua empresa</p>
        </div>
        
       <form action="configuracoes_confirma.php" method="POST" enctype="multipart/form-data">
    <div class="form-group">
        <label for="logo">Logo da Empresa</label>
        <div class="file-input-wrapper">
            <input type="file" name="logo" id="logo" class="file-input" accept="image/*">
            <label for="logo" class="file-input-label">
                Clique para selecionar um novo logo
            </label>
        </div>
    </div>

    <div class="form-group">
        <label for="nome_empresa">Nome da Empresa</label>
        <input type="text" name="nome_empresa" id="nome_empresa" class="text-input" placeholder="Digite o nome da sua empresa">
    </div>
    
<div class="form-group">
    <label for="numero_whatsapp">Número para redirecionamento</label>
    <input type="tel" name="numero_whatsapp" id="numero_whatsapp" class="text-input"
           placeholder="Ex: 5511999998888"
           pattern="[0-9]{10,15}" 
           oninput="this.value = this.value.replace(/[^0-9]/g, '')" 
           required>
    <small class="form-text text-muted">
        Digite o número do WhatsApp para onde o cliente será redirecionado no celular logo após concluir o agendamento.  
        Use apenas números, incluindo código do país e DDD.  
        Exemplo: <b>5511999998888</b>.
    </small>
</div>

    <div class="form-group">
        <div>
            <label for="tema">Escolha o Tema</label>
            <select name="tema" id="tema" class="select-input">
                <option value="">Selecione um tema...</option>
                <option value="1">🎨 Roxo e Azul</option>
                <option value="2">🌿 Verde e Aqua</option>
                <option value="3">🔥 Vermelho e Laranja</option>
                <option value="4">💧 Azul Escuro e Ciano</option>
                <option value="5">💜 Roxo e Rosa</option>
                <option value="6">⚙️ Cinza e Amarelo</option>
            </select>
        </div>
    </div>

    <button type="submit" class="submit-btn">
        💾 Salvar Configurações
    </button>
</form>
    </div>

    <script>
        // Melhora a experiência do input de arquivo
        document.getElementById('logo').addEventListener('change', function(e) {
            const label = document.querySelector('.file-input-label');
            if (e.target.files.length > 0) {
                label.innerHTML = `✅ ${e.target.files[0].name}`;
                label.style.borderColor = '#28a745';
                label.style.color = '#28a745';
            }
        });

        // Adiciona animação de foco nos inputs
        const inputs = document.querySelectorAll('input, select');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });
    </script>

<?php include 'footer.php'; ?>