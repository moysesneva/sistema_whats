<?php
#include 'conn.php';
#ini_set('display_errors', 1);
#error_reporting(E_ALL);

// Buscar dados atuais do banco de dados para preencher o formulário
$query = "SELECT * FROM config";
$result = mysqli_query($conn, $query);
$config = mysqli_fetch_assoc($result);

// Decodifica os feature_items se estiverem em formato JSON no banco
$feature_items_array = array();
if (isset($config['feature_items']) && !empty($config['feature_items'])) {
    $feature_items_array = json_decode($config['feature_items'], true);
    if (!is_array($feature_items_array)) {
        $feature_items_array = array();
    }
}
// Garante que temos pelo menos 3 itens no array (podemos adicionar mais conforme necessário)
while (count($feature_items_array) < 3) {
    $feature_items_array[] = '';
}

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtém os valores do formulário
    $telefone = $_POST['telefone'];
    $endereco = $_POST['endereco'];
    $tema = (int)$_POST['tema'];
    $hero_title = $_POST['hero_title'];
    $hero_subtitle = $_POST['hero_subtitle'];
    $services_title = $_POST['services_title'];
    $services_description = $_POST['services_description'];
    $texto_vendas = $_POST['texto_vendas'];
    
    // Novos campos de texto
    $card1_title = $_POST['card1_title'];
    $card1_description = $_POST['card1_description'];
    $card2_title = $_POST['card2_title'];
    $card2_description = $_POST['card2_description'];
    $card3_title = $_POST['card3_title'];
    $card3_description = $_POST['card3_description'];
    $feature_title = $_POST['feature_title'];
    $feature_description = $_POST['feature_description'];
    
    // Para feature_items (array), convertemos para JSON para armazenar no banco
    $feature_items = array();
    if (isset($_POST['feature_items']) && is_array($_POST['feature_items'])) {
        foreach ($_POST['feature_items'] as $item) {
            if (!empty($item)) {
                $feature_items[] = $item;
            }
        }
    }
    // Usar JSON_UNESCAPED_UNICODE para preservar caracteres acentuados e especiais
    $feature_items_json = json_encode($feature_items, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    
    // Tratamento especial para o vídeo do YouTube: se o checkbox de apagar estiver marcado, definimos como vazio
    if (isset($_POST['apagar_video']) && $_POST['apagar_video'] == 1) {
        $video_youtube = '';
    } else {
        $video_youtube = $_POST['video_youtube'];
    }
    
    /**
     * Processa o upload de arquivos, salvando em um diretório e armazenando um caminho diferente no banco
     * Retorna string vazia se não houver novo arquivo (para não atualizar no banco de dados)
     * 
     * @param string $campo_arquivo Nome do campo de upload no formulário
     * @param string $campo_atual Nome do campo com o valor atual
     * @param string $caminho_bd Caminho que será armazenado no banco de dados
     * @param string $caminho_servidor Caminho real onde o arquivo será salvo no servidor
     * @return string|null Caminho para armazenar no banco de dados ou null se não houver alteração
     */
    function processarUpload($campo_arquivo, $campo_atual, $caminho_bd = 'assets/logos/', $caminho_servidor = '../../assets/logos/') {
        // Se não houver arquivo enviado (ou tiver erro), retorna NULL para indicar que não deve atualizar no banco
        if (!isset($_FILES[$campo_arquivo]) || $_FILES[$campo_arquivo]['error'] != 0 || empty($_FILES[$campo_arquivo]['name'])) {
            return null; // Retorna null para indicar "não alterar no banco"
        }
        
        // Se chegou aqui, é porque tem um novo arquivo para processar
        // Verifica se o diretório existe, se não, cria
        if (!is_dir($caminho_servidor)) {
            mkdir($caminho_servidor, 0755, true);
        }
        
        $nome_arquivo = basename($_FILES[$campo_arquivo]['name']);
        $caminho_completo_servidor = $caminho_servidor . $nome_arquivo;
        
        // Move o arquivo enviado para o diretório físico no servidor
        if (move_uploaded_file($_FILES[$campo_arquivo]['tmp_name'], $caminho_completo_servidor)) {
            // Retorna o caminho que será armazenado no banco de dados
            return $caminho_bd . $nome_arquivo;
        }
        
        // Se falhou o upload, retorna null para não alterar no banco
        return null;
    }
    
    // Processar uploads de arquivos - somente quando houver novos arquivos enviados
    // Parâmetros: campo do arquivo, campo atual, caminho para BD, caminho no servidor
    $card1_icon = processarUpload('card1_icon_file', 'card1_icon_atual', 'assets/logos/', '../../assets/logos/');
    $card2_icon = processarUpload('card2_icon_file', 'card2_icon_atual', 'assets/logos/', '../../assets/logos/');
    $card3_icon = processarUpload('card3_icon_file', 'card3_icon_atual', 'assets/logos/', '../../assets/logos/');
    $feature_image = processarUpload('feature_image_file', 'feature_image_atual', 'assets/images/', '../../assets/images/');
    $caminho_modelo = processarUpload('caminho_modelo', 'caminho_modelo_atual', 'uploads/', 'uploads/');
    
    // Inicia a string SQL
    $sql = "UPDATE config SET ";
    $updates = array();
    
    // O tema é um número, então sempre é atualizado
    $updates[] = "tema = $tema";
    
    // Para cada campo, verificamos se ele não está vazio antes de incluí-lo no SQL
    // IMPORTANTE: Só incluir campos que não estejam vazios, com exceção do campo video_youtube
    
    // Tratar o campo de telefone apenas se não estiver vazio
    if (!empty($telefone)) {
        $updates[] = "telefone = '" . mysqli_real_escape_string($conn, $telefone) . "'";
    }

    // Tratar o campo de endereço apenas se não estiver vazio
    if (!empty($endereco)) {
        $updates[] = "endereco = '" . mysqli_real_escape_string($conn, $endereco) . "'";
    }
    
    // Tratar o campo de modelo apenas se tiver um novo arquivo
    if ($caminho_modelo !== null) {
        $updates[] = "caminho_modelo = '" . mysqli_real_escape_string($conn, $caminho_modelo) . "'";
    }
    
    // Tratar o campo de título do hero apenas se não estiver vazio
    if (!empty($hero_title)) {
        $updates[] = "hero_title = '" . mysqli_real_escape_string($conn, $hero_title) . "'";
    }
    
    // Tratar o campo de subtítulo do hero apenas se não estiver vazio
    if (!empty($hero_subtitle)) {
        $updates[] = "hero_subtitle = '" . mysqli_real_escape_string($conn, $hero_subtitle) . "'";
    }
    
    // Tratar o campo de título dos serviços apenas se não estiver vazio
    if (!empty($services_title)) {
        $updates[] = "services_title = '" . mysqli_real_escape_string($conn, $services_title) . "'";
    }
    
    // Tratar o campo de descrição dos serviços apenas se não estiver vazio
    if (!empty($services_description)) {
        $updates[] = "services_description = '" . mysqli_real_escape_string($conn, $services_description) . "'";
    }
    
    // Tratar o campo de texto de vendas apenas se não estiver vazio
    if (!empty($texto_vendas)) {
        $updates[] = "texto_vendas = '" . mysqli_real_escape_string($conn, $texto_vendas) . "'";
    }
    
    // Para o vídeo do YouTube, tratamento especial:
    // Se o checkbox de apagar estiver marcado, definimos como vazio
    // Se não estiver marcado, incluímos o valor atual, mesmo que vazio
    if (isset($_POST['apagar_video']) && $_POST['apagar_video'] == 1) {
        $updates[] = "video_youtube = ''";
    } else {
        $updates[] = "video_youtube = '" . mysqli_real_escape_string($conn, $video_youtube) . "'";
    }
    
    // Card 1 - apenas se os campos não estiverem vazios
    // Para arquivos, verificamos se o valor não é NULL (NULL = não atualizar no banco)
    if ($card1_icon !== null) {
        $updates[] = "card1_icon = '" . mysqli_real_escape_string($conn, $card1_icon) . "'";
    }
    
    if (!empty($card1_title)) {
        $updates[] = "card1_title = '" . mysqli_real_escape_string($conn, $card1_title) . "'";
    }
    
    if (!empty($card1_description)) {
        $updates[] = "card1_description = '" . mysqli_real_escape_string($conn, $card1_description) . "'";
    }
    
    // Card 2 - apenas se os campos não estiverem vazios
    if ($card2_icon !== null) {
        $updates[] = "card2_icon = '" . mysqli_real_escape_string($conn, $card2_icon) . "'";
    }
    
    if (!empty($card2_title)) {
        $updates[] = "card2_title = '" . mysqli_real_escape_string($conn, $card2_title) . "'";
    }
    
    if (!empty($card2_description)) {
        $updates[] = "card2_description = '" . mysqli_real_escape_string($conn, $card2_description) . "'";
    }
    
    // Card 3 - apenas se os campos não estiverem vazios
    if ($card3_icon !== null) {
        $updates[] = "card3_icon = '" . mysqli_real_escape_string($conn, $card3_icon) . "'";
    }
    
    if (!empty($card3_title)) {
        $updates[] = "card3_title = '" . mysqli_real_escape_string($conn, $card3_title) . "'";
    }
    
    if (!empty($card3_description)) {
        $updates[] = "card3_description = '" . mysqli_real_escape_string($conn, $card3_description) . "'";
    }
    
    // Seção de benefícios - apenas se os campos não estiverem vazios
    if ($feature_image !== null) {
        $updates[] = "feature_image = '" . mysqli_real_escape_string($conn, $feature_image) . "'";
    }
    
    if (!empty($feature_title)) {
        $updates[] = "feature_title = '" . mysqli_real_escape_string($conn, $feature_title) . "'";
    }
    
    if (!empty($feature_description)) {
        $updates[] = "feature_description = '" . mysqli_real_escape_string($conn, $feature_description) . "'";
    }
    
    // Para feature_items, só atualizamos se o array não estiver vazio
    if (!empty($feature_items)) {
        $escaped_json = mysqli_real_escape_string($conn, $feature_items_json);
        $updates[] = "feature_items = '$escaped_json'";
    }
    
    // Completa a consulta SQL apenas se houver campos para atualizar
    if (!empty($updates)) {
        $sql .= implode(", ", $updates);
        
        // Execute a consulta SQL
        $resultado = mysqli_query($conn, $sql);
        
        if ($resultado) {
            echo "<div class='alert alert-success'>Configurações atualizadas com sucesso!</div>";
        } else {
            echo "<div class='alert alert-danger'>Erro ao atualizar configurações: " . mysqli_error($conn) . "</div>";
        }
    }
}
?>

    <style>
        .form-section {
            margin-bottom: 30px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        h4 {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #dee2e6;
        }
    </style>
</head>
<body>
    <div class="container mt-4 mb-4">
        <h2>Configurações do Site</h2>
        <p class="text-muted">Personalize os elementos do seu site.</p>
        
        <!-- Formulário HTML -->
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-section">
                <h4>Configurações Gerais</h4>
                <div class="form-group">
                    <label for="telefone">Telefone:</label>
                    <input type="text" class="form-control" id="telefone" name="telefone" value="<?php echo isset($config['telefone']) ? htmlspecialchars($config['telefone']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="endereco">Endereço:</label>
                    <input type="text" class="form-control" id="endereco" name="endereco" value="<?php echo isset($config['endereco']) ? htmlspecialchars($config['endereco']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="tema">Tema:</label>
                    <select class="form-control" id="tema" name="tema">
                        <?php for($i = 1; $i <= 6; $i++): ?>
                            <option value="<?php echo $i; ?>" <?php echo (isset($config['tema']) && $config['tema'] == $i) ? 'selected' : ''; ?>>
                                Tema <?php echo $i; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="caminho_modelo">Modelo (Arquivo):</label>
                    <input type="file" class="form-control" id="caminho_modelo" name="caminho_modelo">
                    <?php if(isset($config['caminho_modelo']) && !empty($config['caminho_modelo'])): ?>
                        <small class="form-text text-muted">
                            Arquivo atual: <?php echo basename($config['caminho_modelo']); ?>
                        </small>
                        <input type="hidden" name="caminho_modelo_atual" value="<?php echo htmlspecialchars($config['caminho_modelo']); ?>">
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="form-section">
                <h4>Seção Hero</h4>
                <div class="form-group">
                    <label for="hero_title">Título do Hero:</label>
                    <input type="text" class="form-control" id="hero_title" name="hero_title" value="<?php echo isset($config['hero_title']) ? htmlspecialchars($config['hero_title']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="hero_subtitle">Subtítulo do Hero:</label>
                    <input type="text" class="form-control" id="hero_subtitle" name="hero_subtitle" value="<?php echo isset($config['hero_subtitle']) ? htmlspecialchars($config['hero_subtitle']) : ''; ?>">
                </div>
            </div>
            
            <div class="form-section">
                <h4>Seção Serviços</h4>
                <div class="form-group">
                    <label for="services_title">Título dos Serviços:</label>
                    <input type="text" class="form-control" id="services_title" name="services_title" value="<?php echo isset($config['services_title']) ? htmlspecialchars($config['services_title']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="services_description">Descrição dos Serviços:</label>
                    <textarea class="form-control" id="services_description" name="services_description" rows="3"><?php echo isset($config['services_description']) ? htmlspecialchars($config['services_description']) : ''; ?></textarea>
                </div>
            </div>
            
            <div class="form-section">
                <h4>Card 1</h4>
                <div class="form-group">
                    <label for="card1_icon_file">Ícone do Card 1 (Arquivo):</label>
                    <input type="file" class="form-control" id="card1_icon_file" name="card1_icon_file">
                    <?php if(isset($config['card1_icon']) && !empty($config['card1_icon'])): ?>
                        <small class="form-text text-muted">
                            Arquivo atual: <?php echo basename($config['card1_icon']); ?>
                        </small>
                        <input type="hidden" name="card1_icon_atual" value="<?php echo htmlspecialchars($config['card1_icon']); ?>">
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="card1_title">Título do Card 1:</label>
                    <input type="text" class="form-control" id="card1_title" name="card1_title" value="<?php echo isset($config['card1_title']) ? htmlspecialchars($config['card1_title']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="card1_description">Descrição do Card 1:</label>
                    <textarea class="form-control" id="card1_description" name="card1_description" rows="3"><?php echo isset($config['card1_description']) ? htmlspecialchars($config['card1_description']) : ''; ?></textarea>
                </div>
            </div>
            
            <div class="form-section">
                <h4>Card 2</h4>
                <div class="form-group">
                    <label for="card2_icon_file">Ícone do Card 2 (Arquivo):</label>
                    <input type="file" class="form-control" id="card2_icon_file" name="card2_icon_file">
                    <?php if(isset($config['card2_icon']) && !empty($config['card2_icon'])): ?>
                        <small class="form-text text-muted">
                            Arquivo atual: <?php echo basename($config['card2_icon']); ?>
                        </small>
                        <input type="hidden" name="card2_icon_atual" value="<?php echo htmlspecialchars($config['card2_icon']); ?>">
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="card2_title">Título do Card 2:</label>
                    <input type="text" class="form-control" id="card2_title" name="card2_title" value="<?php echo isset($config['card2_title']) ? htmlspecialchars($config['card2_title']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="card2_description">Descrição do Card 2:</label>
                    <textarea class="form-control" id="card2_description" name="card2_description" rows="3"><?php echo isset($config['card2_description']) ? htmlspecialchars($config['card2_description']) : ''; ?></textarea>
                </div>
            </div>
            
            <div class="form-section">
                <h4>Card 3</h4>
                <div class="form-group">
                    <label for="card3_icon_file">Ícone do Card 3 (Arquivo):</label>
                    <input type="file" class="form-control" id="card3_icon_file" name="card3_icon_file">
                    <?php if(isset($config['card3_icon']) && !empty($config['card3_icon'])): ?>
                        <small class="form-text text-muted">
                            Arquivo atual: <?php echo basename($config['card3_icon']); ?>
                        </small>
                        <input type="hidden" name="card3_icon_atual" value="<?php echo htmlspecialchars($config['card3_icon']); ?>">
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="card3_title">Título do Card 3:</label>
                    <input type="text" class="form-control" id="card3_title" name="card3_title" value="<?php echo isset($config['card3_title']) ? htmlspecialchars($config['card3_title']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="card3_description">Descrição do Card 3:</label>
                    <textarea class="form-control" id="card3_description" name="card3_description" rows="3"><?php echo isset($config['card3_description']) ? htmlspecialchars($config['card3_description']) : ''; ?></textarea>
                </div>
            </div>
            
            <div class="form-section">
                <h4>Seção de Benefícios</h4>
                <div class="form-group">
                    <label for="feature_image_file">Imagem do Benefício (Arquivo):</label>
                    <input type="file" class="form-control" id="feature_image_file" name="feature_image_file">
                    <?php if(isset($config['feature_image']) && !empty($config['feature_image'])): ?>
                        <small class="form-text text-muted">
                            Arquivo atual: <?php echo basename($config['feature_image']); ?>
                        </small>
                        <input type="hidden" name="feature_image_atual" value="<?php echo htmlspecialchars($config['feature_image']); ?>">
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="feature_title">Título do Benefício:</label>
                    <input type="text" class="form-control" id="feature_title" name="feature_title" value="<?php echo isset($config['feature_title']) ? htmlspecialchars($config['feature_title']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="feature_description">Descrição do Benefício:</label>
                    <textarea class="form-control" id="feature_description" name="feature_description" rows="3"><?php echo isset($config['feature_description']) ? htmlspecialchars($config['feature_description']) : ''; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>Itens de Benefícios:</label>
                    <?php foreach ($feature_items_array as $index => $item): ?>
                    <div class="input-group mb-2">
                        <input type="text" class="form-control" name="feature_items[]" value="<?php echo htmlspecialchars($item); ?>" placeholder="Item de benefício <?php echo $index + 1; ?>">
                        <?php if ($index > 2): ?>
                        <div class="input-group-append">
                            <button class="btn btn-outline-danger remove-item" type="button">Remover</button>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="add-feature-item">+ Adicionar Item</button>
                </div>
            </div>
            
            <div class="form-section">
                <h4>Seção de Vendas</h4>
                <div class="form-group">
                    <label for="texto_vendas">Texto de Vendas:</label>
                    <textarea class="form-control" id="texto_vendas" name="texto_vendas" rows="3"><?php echo isset($config['texto_vendas']) ? htmlspecialchars($config['texto_vendas']) : ''; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="video_youtube">Link do Vídeo (YouTube):</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="video_youtube" name="video_youtube" value="<?php echo isset($config['video_youtube']) ? htmlspecialchars($config['video_youtube']) : ''; ?>">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="apagar_video" name="apagar_video" value="1">
                                    <label class="custom-control-label" for="apagar_video">Apagar vídeo</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <small class="form-text text-muted">Marque a caixa para apagar o link atual do vídeo.</small>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary btn-lg">Salvar Alterações</button>
        </form>
    </div>

    <script src="../files/bower_components/jquery/js/jquery.min.js"></script>
    <script src="../files/bower_components/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
    // JavaScript para adicionar ou remover itens de benefícios dinamicamente
    document.addEventListener('DOMContentLoaded', function() {
        // Adicionar novo item
        document.getElementById('add-feature-item').addEventListener('click', function() {
            var container = this.previousElementSibling;
            var clone = container.cloneNode(true);
            var input = clone.querySelector('input');
            input.value = '';
            
            // Adicionar botão de remover se não existir
            if (!clone.querySelector('.remove-item')) {
                var appendDiv = document.createElement('div');
                appendDiv.className = 'input-group-append';
                var removeBtn = document.createElement('button');
                removeBtn.className = 'btn btn-outline-danger remove-item';
                removeBtn.type = 'button';
                removeBtn.textContent = 'Remover';
                appendDiv.appendChild(removeBtn);
                clone.appendChild(appendDiv);
            }
            
            this.parentNode.insertBefore(clone, this);
            setupRemoveButtons();
        });
        
        // Configurar botões de remover
        function setupRemoveButtons() {
            document.querySelectorAll('.remove-item').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    this.closest('.input-group').remove();
                });
            });
        }
        
        // Desabilitar o campo de vídeo se a opção de apagar estiver marcada
        var checkboxApagar = document.getElementById('apagar_video');
        var campoVideo = document.getElementById('video_youtube');
        
        checkboxApagar.addEventListener('change', function() {
            if (this.checked) {
                campoVideo.disabled = true;
                campoVideo.style.backgroundColor = '#e9ecef';
            } else {
                campoVideo.disabled = false;
                campoVideo.style.backgroundColor = '';
            }
        });
        
        // Inicializar
        setupRemoveButtons();
    });
    </script>
</body>
</html>