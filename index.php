<?php
error_reporting(0);
ini_set("display_errors", 0 );
include 'login/painel/conn.php';
include 'login/painel/estilo.php';
include 'login/painel/css_de_icones.php';
include 'login/painel/config_dados.php';
include 'login/painel/funcoes.php';
include 'login/painel/menu.php';

// Ajuste dos caminhos para os recursos
$icon = 'login/painel/'.$icon; 
$logo = 'login/painel/'.$logo; 

// Busca configurações do banco de dados
$sql_busca_config = "SELECT * FROM config";
$query_busca_config = mysqli_query($conn, $sql_busca_config);
$total_busca_config = mysqli_num_rows($query_busca_config);

while($rows_config = mysqli_fetch_array($query_busca_config)) {
    $chave = $rows_config['chave'];
    $validade = $rows_config['validade'];
    $link_pagamento = $rows_config['link_pagamento'];
    $preco = $rows_config['preco'];
    $telefone = $rows_config['telefone'];
    $caminho_modelo = $rows_config['caminho_modelo'];

    // Seção Hero
    $hero_title = $rows_config['hero_title'];
    $hero_subtitle = $rows_config['hero_subtitle'];

    // Seção Serviços
    $services_title = $rows_config['services_title'];
    $services_description = $rows_config['services_description'];

    // Contato
    $telefone = $rows_config['telefone'];

    // Vendas
    $tipo_vendas = $rows_config['tipo_vendas'];
    $texto_vendas = $rows_config['texto_vendas'];
    $video_youtube = $rows_config['video_youtube'];

    // Tema de cores
    $tema = (int)$rows_config['tema'];

    // Telefone sem formatação para WhatsApp
    $telefone_whatsapp = str_replace(['(', ')', ' ', '-'], '', $telefone);

    // Definição de variáveis para conteúdo da página
    $hero_title = isset($rows_config['hero_title']) && !empty($rows_config['hero_title']) ? 
        $rows_config['hero_title'] : 'Robô de Atendimento Inteligente com IA';

    $hero_subtitle = isset($rows_config['hero_subtitle']) && !empty($rows_config['hero_subtitle']) ? 
        $rows_config['hero_subtitle'] : 'Automatize seu atendimento com nosso sistema que entende texto, áudio, imagens e muito mais. A escolha perfeita para aumentar vendas e melhorar o relacionamento com seus clientes.';

    $services_title = isset($rows_config['services_title']) && !empty($rows_config['services_title']) ? 
        $rows_config['services_title'] : 'Sistema Inteligente de Atendimento';

    $services_description = isset($rows_config['services_description']) && !empty($rows_config['services_description']) ? 
        $rows_config['services_description'] : 'Nosso robô utiliza inteligência artificial para compreender áudio, texto e imagens, oferecendo atendimento e vendas de forma automática e personalizada, 24 horas por dia.';

    $hero_background = !empty($rows_config['caminho_modelo']) ? 'login/painel/' . $rows_config['caminho_modelo'] : 'https://img.freepik.com/vetores-gratis/fundo-de-wireframe-geometrico-abstrato.jpg';

    // Definição do tema de cores (1 a 6)
    $tema = isset($rows_config['tema']) ? (int)$rows_config['tema'] : 4;

    // Definição das cores de acordo com o tema selecionado
    switch ($tema) {
        case 1: // Roxo e Azul (Default)
            $primary_color = '#3a0ca3';
            $secondary_color = '#4cc9f0';
            $accent_color = '#f72585';
            $dark_color = '#2b2d42';
            $light_color = '#f8f9fa';
            $gradient = 'linear-gradient(120deg, #7209b7, #3a0ca3)';
            $gradient_hover = 'linear-gradient(120deg, #3a0ca3, #7209b7)';
            break;
        case 2: // Verde e Aqua
            $primary_color = '#06d6a0';
            $secondary_color = '#1b9aaa';
            $accent_color = '#ff9f1c';
            $dark_color = '#1d3557';
            $light_color = '#f1faee';
            $gradient = 'linear-gradient(120deg, #06d6a0, #1b9aaa)';
            $gradient_hover = 'linear-gradient(120deg, #1b9aaa, #06d6a0)';
            break;
        case 3: // Vermelho e Laranja
            $primary_color = '#e63946';
            $secondary_color = '#f77f00';
            $accent_color = '#fcbf49';
            $dark_color = '#003049';
            $light_color = '#f1faee';
            $gradient = 'linear-gradient(120deg, #e63946, #f77f00)';
            $gradient_hover = 'linear-gradient(120deg, #f77f00, #e63946)';
            break;
        case 4: // Azul Escuro e Ciano
            $primary_color = '#003459';
            $secondary_color = '#00a8e8';
            $accent_color = '#ff6b6b';
            $dark_color = '#00171f';
            $light_color = '#f5f5f5';
            $gradient = 'linear-gradient(120deg, #003459, #00a8e8)';
            $gradient_hover = 'linear-gradient(120deg, #00a8e8, #003459)';
            break;
        case 5: // Roxo e Rosa
            $primary_color = '#9b5de5';
            $secondary_color = '#f15bb5';
            $accent_color = '#fee440';
            $dark_color = '#1b1b1b';
            $light_color = '#f8f9fa';
            $gradient = 'linear-gradient(120deg, #9b5de5, #f15bb5)';
            $gradient_hover = 'linear-gradient(120deg, #f15bb5, #9b5de5)';
            break;
        case 6: // Cinza e Amarelo
            $primary_color = '#2b2d42';
            $secondary_color = '#ffd166';
            $accent_color = '#ef476f';
            $dark_color = '#191923';
            $light_color = '#edf2f4';
            $gradient = 'linear-gradient(120deg, #2b2d42, #1a1a2e)';
            $gradient_hover = 'linear-gradient(120deg, #1a1a2e, #2b2d42)';
            break;
        default: // Roxo e Azul (Default)
            $primary_color = '#3a0ca3';
            $secondary_color = '#4cc9f0';
            $accent_color = '#f72585';
            $dark_color = '#2b2d42';
            $light_color = '#f8f9fa';
            $gradient = 'linear-gradient(120deg, #7209b7, #3a0ca3)';
            $gradient_hover = 'linear-gradient(120deg, #3a0ca3, #7209b7)';
    }
}

// Valores padrão (originais)
$default_values = array(
    'card1_icon' => 'assets/logos/icon1.png',
    'card1_title' => 'Reconhecimento Avançado',
    'card1_description' => 'Muito além do texto: uma IA capaz de compreender comandos por voz, escrita e imagens com máxima eficiência.',
    
    'card2_icon' => 'assets/logos/icon2.png',
    'card2_title' => 'Vendas Automáticas',
    'card2_description' => 'Realize vendas e atendimentos em poucos segundos com a ajuda de nossa IA, que identifica necessidades e oferece soluções personalizadas para cada cliente.',
    
    'card3_icon' => 'assets/logos/icon3.png',
    'card3_title' => 'Disponibilidade 24/7',
    'card3_description' => 'Seu negócio disponível a qualquer momento, pronto para atender, resolver dúvidas e fechar vendas mesmo quando você não está trabalhando.',
    
    'feature_image' => 'assets/images/feature_1.png',
    'feature_title' => 'Assistente Inteligente para Vendas e Atendimento',
    'feature_description' => 'Nosso robô atua como um funcionário virtual, capaz de entender comandos de áudio, texto e imagens, oferecendo uma experiência prática e humanizada para atender seus clientes e aumentar suas vendas.',
    'feature_items' => array(
        'Atendimento personalizado com linguagem natural.',
        'Identificação de oportunidades de vendas.',
        'Disponível 24 horas por dia, 7 dias por semana.'
    )
);

// Consulta as configurações do banco de dados
$query = "SELECT * FROM config";
$result = mysqli_query($conn, $query);

// Se houver configurações no banco
if ($result && mysqli_num_rows($result) > 0) {
    $config = mysqli_fetch_assoc($result);
    
    // Função para obter valor do DB ou valor padrão se estiver vazio
    function getConfigValue($config, $field, $default) {
        return (!empty($config[$field])) ? $config[$field] : $default;
    }
    
    // Cards
    $card1_icon = getConfigValue($config, 'card1_icon', $default_values['card1_icon']);
    $card1_title = getConfigValue($config, 'card1_title', $default_values['card1_title']);
    $card1_description = getConfigValue($config, 'card1_description', $default_values['card1_description']);
    
    $card2_icon = getConfigValue($config, 'card2_icon', $default_values['card2_icon']);
    $card2_title = getConfigValue($config, 'card2_title', $default_values['card2_title']);
    $card2_description = getConfigValue($config, 'card2_description', $default_values['card2_description']);
    
    $card3_icon = getConfigValue($config, 'card3_icon', $default_values['card3_icon']);
    $card3_title = getConfigValue($config, 'card3_title', $default_values['card3_title']);
    $card3_description = getConfigValue($config, 'card3_description', $default_values['card3_description']);
    
    // Seção de benefícios
    $feature_image = getConfigValue($config, 'feature_image', $default_values['feature_image']);
    $feature_title = getConfigValue($config, 'feature_title', $default_values['feature_title']);
    $feature_description = getConfigValue($config, 'feature_description', $default_values['feature_description']);
    
    // Itens de benefícios (array JSON)
    if (!empty($config['feature_items'])) {
        $feature_items_json = $config['feature_items'];
        $feature_items = json_decode($feature_items_json, true);
        
        if (!is_array($feature_items)) {
            $feature_items = $default_values['feature_items'];
        }
    } else {
        $feature_items = $default_values['feature_items'];
    }
    
    $telefone = getConfigValue($config, 'telefone', '');
    $tema = getConfigValue($config, 'tema', 1);
    $caminho_modelo = getConfigValue($config, 'caminho_modelo', '');
    $hero_title = getConfigValue($config, 'hero_title', '');
    $hero_subtitle = getConfigValue($config, 'hero_subtitle', '');
    $services_title = getConfigValue($config, 'services_title', '');
    $services_description = getConfigValue($config, 'services_description', '');
    $texto_vendas = getConfigValue($config, 'texto_vendas', '');
    $video_youtube = getConfigValue($config, 'video_youtube', '');
    
} else {
    // Se não houver configurações, usa os valores padrão
    $card1_icon = $default_values['card1_icon'];
    $card1_title = $default_values['card1_title'];
    $card1_description = $default_values['card1_description'];
    
    $card2_icon = $default_values['card2_icon'];
    $card2_title = $default_values['card2_title'];
    $card2_description = $default_values['card2_description'];
    
    $card3_icon = $default_values['card3_icon'];
    $card3_title = $default_values['card3_title'];
    $card3_description = $default_values['card3_description'];
    
    $feature_image = $default_values['feature_image'];
    $feature_title = $default_values['feature_title'];
    $feature_description = $default_values['feature_description'];
    $feature_items = $default_values['feature_items'];
    
    $telefone = '';
    $tema = 1;
    $caminho_modelo = '';
    $hero_title = '';
    $hero_subtitle = '';
    $services_title = '';
    $services_description = '';
    $texto_vendas = '';
    $video_youtube = '';
}

// Inicializa o array de planos
$planos = [];
// Consulta apenas os planos ativos
$sql_planos = "SELECT * FROM planos_online WHERE ativo = 1 ORDER BY preco ASC";
$result_planos = mysqli_query($conn, $sql_planos);

if ($result_planos && mysqli_num_rows($result_planos) > 0) {
    while ($plano = mysqli_fetch_assoc($result_planos)) {
        $id_plano = $plano['id'];

        // Consulta as features do plano
        $features = [];
        $sql_features = "SELECT feature 
                           FROM planos_features 
                          WHERE id_plano = $id_plano
                       ORDER BY id";
        $result_features = mysqli_query($conn, $sql_features);

        if ($result_features && mysqli_num_rows($result_features) > 0) {
            while ($feature = mysqli_fetch_assoc($result_features)) {
                $features[] = $feature['feature'];
            }
        }

        // Monta a estrutura incluindo o código de pagamento
        $planos[] = [
            'titulo'    => $plano['titulo'],
            'preco'     => 'R$ ' . number_format($plano['preco'], 2, ',', '.'),
            'icone'     => '/login/painel/' . $small_logo,
            'features'  => $features,
            'code_pag'  => $plano['code_pag']
        ];
    }
}

// Seção de contato
$contact_title = 'Precisa de ajuda? Fale conosco no WhatsApp!';
$contact_description = 'Estamos disponíveis para responder suas dúvidas ou fornecer mais informações. Clique no botão abaixo para falar diretamente conosco via WhatsApp.';
$contact_button_text = 'Fale pelo WhatsApp';

// Rodapé
$footer_copyright = 'Copyright © ' . date('Y') . '. Todos os direitos reservados.';

// Menu itens
$menu_items = [
    ['href' => '#main', 'label' => 'Início'],
    ['href' => '#services', 'label' => 'Importante'],
    ['href' => '#features', 'label' => 'Benefícios'],
    ['href' => '#video-section', 'label' => 'Demonstração'],
    ['href' => '#pricing', 'label' => 'Preços'],
    ['href' => '#contact', 'label' => 'Contato']
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title><?php echo $titulo; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Robô de Atendimento Inteligente com IA">
    <meta name="keywords" content="atendimento, inteligência artificial, robô, automação, vendas, chatbot">
    <meta name="author" content="<?php echo $titulo; ?>">
    
    <!-- Favicon -->
    <link rel="icon" href="<?php echo $icon; ?>" type="image/x-icon">
    <link rel="shortcut icon" href="<?php echo $icon; ?>" type="image/x-icon">
    <link rel="apple-touch-icon" href="<?php echo $icon; ?>">
    
    <!-- Fontes -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- AOS CSS - Animações ao rolar -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    
    <!-- Estilos personalizados totalmente responsivos -->
    <style>
        :root {
            --primary-color: <?php echo $primary_color; ?>;
            --secondary-color: <?php echo $secondary_color; ?>;
            --accent-color: <?php echo $accent_color; ?>;
            --dark-color: <?php echo $dark_color; ?>;
            --light-color: <?php echo $light_color; ?>;
            --white: #ffffff;
            --gradient: <?php echo $gradient; ?>;
            --gradient-hover: <?php echo $gradient_hover; ?>;
            --shadow: 0 10px 30px rgba(0,0,0,0.1);
            --transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html {
            scroll-behavior: smooth;
        }
        
        body {
            font-family: 'Montserrat', sans-serif;
            color: #444;
            line-height: 1.8;
            overflow-x: hidden;
            background-color: var(--light-color);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        /* Container personalizado para melhor responsividade */
        .container {
            width: 100%;
            padding-right: 15px;
            padding-left: 15px;
            margin-right: auto;
            margin-left: auto;
        }
        
        /* Navbar Responsiva */
        .navbar {
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: var(--shadow);
            padding: 1rem 0;
            transition: var(--transition);
            position: fixed;
            width: 100%;
            z-index: 1000;
            top: 0;
        }
        
        .navbar-scrolled {
            padding: 0.5rem 0;
            background-color: rgba(255, 255, 255, 0.98);
        }
        
        .navbar-brand img {
            max-height: 40px;
            width: auto;
            transition: var(--transition);
        }
        
        .navbar-toggler {
            border: none;
            padding: 0.25rem 0.5rem;
            font-size: 1.25rem;
        }
        
        .navbar-toggler:focus {
            outline: none;
            box-shadow: none;
        }
        
        .navbar-nav {
            align-items: center;
        }
        
        .navbar-nav .nav-item {
            margin: 0.5rem 0;
        }
        
        .navbar-nav .nav-link {
            color: var(--dark-color);
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: 30px;
            transition: var(--transition);
            font-size: 0.9rem;
            letter-spacing: 0.3px;
            text-align: center;
            display: block;
        }
        
        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            color: var(--primary-color);
            background-color: rgba(58, 12, 163, 0.1);
        }
        
        /* Hero Section Responsiva */
        .hero-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            padding: 80px 0 40px;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--gradient);
            opacity: 0.85;
            z-index: 1;
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 900px;
            margin: 0 auto;
            text-align: center;
            padding: 0 1rem;
        }
        
        .hero-title {
            font-size: clamp(1.75rem, 5vw, 4rem);
            font-weight: 800;
            margin-bottom: 1.5rem;
            color: var(--white);
            line-height: 1.2;
            text-shadow: 0 2px 15px rgba(0,0,0,0.2);
        }
        
        .hero-subtitle {
            font-size: clamp(1rem, 2.5vw, 1.3rem);
            color: var(--white);
            margin-bottom: 2rem;
            opacity: 0.9;
            font-weight: 400;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.6;
        }
        
        /* Botões Responsivos */
        .btn-custom {
            padding: 0.875rem 2rem;
            border-radius: 50px;
            font-weight: 700;
            transition: var(--transition);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0.5rem;
            font-size: clamp(0.75rem, 2vw, 0.9rem);
            position: relative;
            overflow: hidden;
            z-index: 1;
            display: inline-block;
            text-decoration: none;
            white-space: nowrap;
        }
        
        .btn-custom::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 0%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.1);
            transition: all 0.5s ease;
            z-index: -1;
        }
        
        .btn-custom:hover::before {
            width: 100%;
        }
        
        .btn-primary-custom {
            background: var(--gradient);
            border: none;
            color: var(--white);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }
        
        .btn-primary-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            color: var(--white);
            text-decoration: none;
        }
        
        .btn-secondary-custom {
            background: var(--secondary-color);
            border: none;
            color: var(--white);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        .btn-secondary-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
            color: var(--white);
            text-decoration: none;
        }
        
        /* Seções Responsivas */
        section {
            padding: 4rem 0;
            position: relative;
        }
        
        .section-title {
            margin-bottom: 3rem;
            text-align: center;
        }
        
        .section-title h2 {
            font-size: clamp(1.5rem, 4vw, 3rem);
            font-weight: 800;
            margin-bottom: 1.5rem;
            color: var(--dark-color);
            position: relative;
            display: inline-block;
        }
        
        .section-title h2::after {
            content: '';
            width: 60px;
            height: 4px;
            background: var(--gradient);
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            border-radius: 50px;
        }
        
        .section-title p {
            font-size: clamp(1rem, 2vw, 1.2rem);
            color: #555;
            max-width: 800px;
            margin: 1.5rem auto 0;
            font-weight: 400;
            padding: 0 1rem;
        }
        
        /* Serviços Responsivos */
        #services {
            background-color: var(--white);
            position: relative;
            overflow: hidden;
        }
        
        .service-card {
            background-color: var(--white);
            border-radius: 20px;
            padding: 2.5rem 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.05);
            transition: var(--transition);
            height: 100%;
            position: relative;
            z-index: 1;
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.03);
        }
        
        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.1);
        }
        
        .service-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.03), rgba(0, 0, 0, 0.05));
            border-radius: 50%;
            position: relative;
        }
        
        .service-icon img {
            max-width: 50px;
            height: auto;
            filter: drop-shadow(0px 5px 15px rgba(0,0,0,0.1));
        }
        
        .service-title {
            font-size: clamp(1.25rem, 3vw, 1.6rem);
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--primary-color);
            text-align: center;
        }
        
        .service-description {
            color: #555;
            text-align: center;
            font-size: clamp(0.9rem, 2vw, 1.05rem);
            line-height: 1.8;
        }
        
        /* Features Responsivas */
        .feature-img {
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
            position: relative;
            margin-bottom: 2rem;
        }
        
        .feature-img img {
            width: 100%;
            height: auto;
            display: block;
            transition: var(--transition);
        }
        
        .feature-img:hover img {
            transform: scale(1.05);
        }
        
        .feature-content {
            padding: 2rem 0;
        }
        
        .feature-title {
            font-size: clamp(1.5rem, 4vw, 2.5rem);
            font-weight: 800;
            margin-bottom: 1.5rem;
            color: var(--dark-color);
            line-height: 1.3;
        }
        
        .feature-description {
            margin-bottom: 2rem;
            color: #555;
            font-size: clamp(1rem, 2vw, 1.1rem);
            line-height: 1.8;
        }
        
        .feature-list {
            list-style: none;
            padding: 0;
            margin-bottom: 2rem;
        }
        
        .feature-list li {
            padding: 0.75rem 0;
            position: relative;
            padding-left: 2rem;
            font-size: clamp(0.95rem, 2vw, 1.1rem);
            font-weight: 500;
            color: #444;
        }
        
        .feature-list li i {
            position: absolute;
            left: 0;
            top: 0.875rem;
            color: var(--secondary-color);
            font-size: 1.1rem;
        }

        /* Seção de vídeo Responsiva */
        #video-section {
            background-color: var(--light-color);
            position: relative;
            overflow: hidden;
        }
        
        .video-content {
            text-align: center;
            max-width: 1000px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
            padding: 0 1rem;
        }
        
        .video-title {
            font-size: clamp(1.5rem, 4vw, 2.5rem);
            font-weight: 800;
            margin-bottom: 1.5rem;
            color: var(--dark-color);
            line-height: 1.3;
        }
        
        .video-description {
            font-size: clamp(1rem, 2vw, 1.2rem);
            color: #555;
            margin-bottom: 2rem;
            font-weight: 400;
        }
        
        .video-wrapper {
            position: relative;
            padding-bottom: 56.25%; /* 16:9 */
            height: 0;
            overflow: hidden;
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
            margin-top: 2rem;
        }
        
        .video-wrapper iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }
        
        /* Preços Responsivos */
        #pricing {
            background-color: var(--white);
            position: relative;
            overflow: hidden;
        }
        
        .pricing-card {
            background-color: var(--white);
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.08);
            transition: var(--transition);
            overflow: hidden;
            margin: 0 auto 2rem;
            position: relative;
            z-index: 1;
            height: 100%;
            max-width: 400px;
        }
        
        .pricing-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.12);
        }
        
        .pricing-header {
            background: var(--gradient);
            color: var(--white);
            padding: 3rem 1.5rem 2.5rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .pricing-title {
            font-size: clamp(1.5rem, 3vw, 2.2rem);
            font-weight: 800;
            margin-bottom: 1rem;
            position: relative;
            z-index: 2;
        }
        
        .pricing-price {
            font-size: clamp(2rem, 5vw, 3rem);
            font-weight: 800;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 2;
            text-shadow: 0 3px 10px rgba(0,0,0,0.15);
        }
        
        .pricing-icon {
            width: 70px;
            height: 70px;
            margin: 0 auto 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            position: relative;
            z-index: 2;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .pricing-icon img {
            max-width: 45px;
            height: auto;
            filter: brightness(0) invert(1);
        }
        
        .pricing-body {
            padding: 2rem 1.5rem;
            position: relative;
            z-index: 1;
        }
        
        .pricing-features {
            list-style: none;
            padding: 0;
            margin-bottom: 2rem;
        }
        
        .pricing-features li {
            padding: 0.875rem 0;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            position: relative;
            padding-left: 2rem;
            text-align: left;
            font-size: clamp(0.9rem, 2vw, 1.05rem);
        }
        
        .pricing-features li:last-child {
            border-bottom: none;
        }
        
        .pricing-features li i {
            position: absolute;
            left: 0;
            top: 1rem;
            color: var(--secondary-color);
            font-size: 1rem;
        }
        
        .pricing-card.featured {
            transform: scale(1.05);
        }
        
        .pricing-card.featured:hover {
            transform: scale(1.05) translateY(-10px);
        }
        
        .featured-badge {
            position: absolute;
            top: 0;
            right: 20px;
            background: var(--accent-color);
            color: var(--white);
            padding: 0.5rem 1rem;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-radius: 0 0 15px 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            z-index: 3;
        }
        
        /* Contato Responsivo */
        #contact {
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.01), rgba(0, 0, 0, 0.03));
            position: relative;
            overflow: hidden;
        }
        
        .contact-content {
            text-align: center;
            max-width: 800px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
            padding: 0 1rem;
        }
        
        .contact-title {
            font-size: clamp(1.5rem, 4vw, 3rem);
            font-weight: 800;
            margin-bottom: 1.5rem;
            color: var(--dark-color);
            line-height: 1.3;
        }
        
        .contact-description {
            font-size: clamp(1rem, 2vw, 1.2rem);
            color: #555;
            margin-bottom: 2rem;
            font-weight: 400;
        }
        
        .whatsapp-btn {
            background: linear-gradient(135deg, #25D366, #128C7E);
            border: none;
            box-shadow: 0 15px 30px rgba(37, 211, 102, 0.3);
            position: relative;
            overflow: hidden;
            padding: 1rem 2.5rem;
            font-size: clamp(0.9rem, 2vw, 1rem);
        }
        
        .whatsapp-btn:hover {
            background: linear-gradient(135deg, #22c55e, #0d9488);
            transform: translateY(-3px);
            box-shadow: 0 20px 40px rgba(37, 211, 102, 0.4);
            color: var(--white);
        }
        
        .contact-phone {
            font-size: clamp(0.95rem, 2vw, 1.2rem);
            margin-top: 2rem;
            color: #555;
            font-weight: 500;
            background-color: rgba(255, 255, 255, 0.7);
            padding: 1rem 2rem;
            border-radius: 50px;
            display: inline-block;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }
        
        /* Footer Responsivo */
        footer {
            background: var(--dark-color);
            color: #fff;
            padding: 3rem 0 2rem;
            position: relative;
            overflow: hidden;
        }
        
        .footer-logo {
            margin-bottom: 2rem;
            position: relative;
            z-index: 1;
            text-align: center;
        }
        
        .footer-logo img {
            max-height: 60px;
            width: auto;
            filter: drop-shadow(0px 5px 10px rgba(0,0,0,0.2));
        }
        
        .footer-copyright {
            font-size: clamp(0.875rem, 2vw, 1rem);
            opacity: 0.8;
            margin-top: 2rem;
            text-align: center;
            position: relative;
            z-index: 1;
            letter-spacing: 0.5px;
            padding: 0 1rem;
        }
        
        /* Botão Voltar ao Topo Responsivo */
        .back-to-top {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--gradient);
            color: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
            text-decoration: none;
        }
        
        .back-to-top.active {
            opacity: 1;
            visibility: visible;
        }
        
        .back-to-top:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
            color: var(--white);
        }
        
        /* Popup de Oferta Especial Responsivo */
        .special-offer-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.85);
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            padding: 1rem;
        }
        
        .special-offer-overlay.show {
            opacity: 1;
            visibility: visible;
        }
        
        .special-offer-container {
            width: 100%;
            max-width: 450px;
            background: white;
            border-radius: 12px;
            position: relative;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
            transform: translateY(20px);
            transition: transform 0.4s ease;
            overflow: hidden;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .special-offer-overlay.show .special-offer-container {
            transform: translateY(0);
        }
        
        .special-offer-tag {
            position: absolute;
            top: 15px;
            left: -35px;
            background: var(--accent-color);
            color: white;
            padding: 5px 40px;
            font-size: 0.75rem;
            font-weight: 700;
            transform: rotate(-45deg);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .special-offer-close {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 30px;
            height: 30px;
            background: rgba(0, 0, 0, 0.1);
            border: none;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: #444;
            transition: all 0.3s ease;
            z-index: 2;
        }
        
        .special-offer-close:hover {
            background: rgba(0, 0, 0, 0.2);
        }
        
        .special-offer-content {
            padding: 2rem 1.5rem 1.5rem;
            text-align: center;
        }
        
        .special-offer-urgency {
            display: flex;
            justify-content: space-around;
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px dashed #eee;
        }
        
        .urgency-counter {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .counter-number {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary-color);
            line-height: 1;
        }
        
        .counter-text {
            font-size: 0.75rem;
            color: #666;
            text-align: center;
            line-height: 1.3;
            margin-top: 0.25rem;
        }
        
        .countdown-timer {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .countdown-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #e74c3c;
            background: #fff8f8;
            padding: 0.25rem 1rem;
            border-radius: 5px;
            border: 1px solid #ffdddd;
        }
        
        .countdown-label {
            font-size: 0.75rem;
            color: #666;
            margin-top: 0.25rem;
        }
        
        .special-offer-content h3 {
            color: var(--primary-color);
            font-size: clamp(1.25rem, 3vw, 1.5rem);
            font-weight: 700;
            margin-bottom: 0.75rem;
        }
        
        .special-offer-content p {
            color: #555;
            margin-bottom: 1.25rem;
            font-size: clamp(0.875rem, 2vw, 1rem);
            line-height: 1.5;
        }
        
        .special-offer-content p strong {
            color: var(--primary-color);
            font-weight: 700;
        }
        
        #specialOfferForm {
            margin-bottom: 1rem;
        }
        
        #specialOfferForm input {
            width: 100%;
            padding: 0.75rem 1rem;
            margin-bottom: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }
        
        #specialOfferForm input:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 52, 89, 0.1);
        }
        
        .btn-special-offer {
            width: 100%;
            padding: 0.875rem;
            background: var(--gradient);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
            animation: pulse-button 2s infinite;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        
        .security-badge {
            display: inline-block;
            margin-top: 1rem;
            font-size: 0.75rem;
            color: #777;
        }
        
        .security-badge i {
            color: #66BB6A;
            margin-right: 0.25rem;
        }
        
        @keyframes pulse-button {
            0% { box-shadow: 0 0 0 0 rgba(0, 52, 89, 0.5); }
            70% { box-shadow: 0 0 0 10px rgba(0, 52, 89, 0); }
            100% { box-shadow: 0 0 0 0 rgba(0, 52, 89, 0); }
        }
        
        /* Animações personalizadas */
        @keyframes float {
            0% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-15px);
            }
            100% {
                transform: translateY(0px);
            }
        }
        
        .float-animation {
            animation: float 6s ease-in-out infinite;
        }
        
        /* Media Queries para Responsividade Aprimorada */
        @media (min-width: 576px) {
            .container {
                max-width: 540px;
            }
        }
        
        @media (min-width: 768px) {
            .container {
                max-width: 720px;
            }
            
            .navbar-nav .nav-item {
                margin: 0 0.5rem;
            }
            
            .hero-content {
                padding: 0 2rem;
            }
            
            section {
                padding: 6rem 0;
            }
            
            .service-card {
                padding: 3rem 2rem;
            }
            
            .feature-content {
                padding: 2rem 0 2rem 3rem;
            }
        }
        
        @media (min-width: 992px) {
            .container {
                max-width: 960px;
            }
            
            .navbar-nav .nav-link {
                padding: 0.75rem 1.25rem;
            }
            
            section {
                padding: 7.5rem 0;
            }
        }
        
        @media (min-width: 1200px) {
            .container {
                max-width: 1140px;
            }
            
            section {
                padding: 7.5rem 0;
            }
        }
        
        @media (max-width: 991px) {
            .navbar-nav {
                background-color: rgba(255, 255, 255, 0.98);
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
                padding: 1rem;
                border-radius: 15px;
                box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08);
                margin-top: 1rem;
            }
            
            .navbar-collapse {
                margin-top: 1rem;
            }
        }
        
        @media (max-width: 767px) {
            .hero-section {
                padding: 100px 0 60px;
            }
            
            .btn-custom {
                padding: 0.75rem 1.5rem;
                margin: 0.25rem;
                display: block;
                width: 100%;
                max-width: 280px;
                margin-left: auto;
                margin-right: auto;
            }
            
            .pricing-card.featured {
                transform: none;
            }
            
            .pricing-card.featured:hover {
                transform: translateY(-10px);
            }
        }
        
        @media (max-width: 575px) {
            .special-offer-content {
                padding: 1.5rem 1rem;
            }
            
            .special-offer-urgency {
                flex-direction: column;
                gap: 1rem;
            }
            
            .back-to-top {
                width: 45px;
                height: 45px;
                font-size: 1.1rem;
                bottom: 15px;
                right: 15px;
            }
        }
        
        /* Ajuste para imagem em telas pequenas */
        @media (max-width: 767px) {
            .float-animation {
                animation: none;
            }
        }
        
        /* Modo escuro do navegador */
        @media (prefers-color-scheme: dark) {
            :root {
                --light-color: #f5f5f5;
            }
        }
    </style>
</head>

<body>
    <!-- Popup de oferta especial com cronômetro -->
    <div class="special-offer-overlay" id="specialOfferPopup">
        <div class="special-offer-container">
            <button class="special-offer-close"><i class="fas fa-times"></i></button>
            <div class="special-offer-tag">Oferta Especial</div>
            <div class="special-offer-content">
                <div class="special-offer-urgency">
                    <div class="urgency-counter">
                        <div class="counter-number">3</div>
                        <div class="counter-text">vagas disponíveis<br>de 10</div>
                    </div>
                    <div class="countdown-timer">
                        <div class="countdown-value" id="countdownTimer">05:00</div>
                        <div class="countdown-label">Tempo restante</div>
                    </div>
                </div>
                
                <h3>Temos uma oferta especial para você!</h3>
                <p>Não perca a oportunidade! Você é um dos <strong>poucos selecionados</strong> para esta oferta exclusiva.</p>
                
                <form id="specialOfferForm" method="post" action="salvar_contato.php">
                    <input type="text" name="nome" placeholder="Seu nome" required>
                    <input type="email" name="email" placeholder="Seu e-mail" required>
                    <input type="tel" name="telefone" placeholder="Seu WhatsApp" required>
                    <button type="submit" class="btn-special-offer">GARANTIR MINHA OFERTA AGORA</button>
                </form>
                <div class="security-badge">
                    <i class="fas fa-lock"></i> Seus dados estão seguros
                </div>
            </div>
        </div>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="#main">
                <img src="<?php echo $logo; ?>" alt="<?php echo $titulo; ?> Logo">
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <?php foreach($menu_items as $item): ?>
                    <li class="nav-item">
                        <a class="nav-link page-scroll" href="<?php echo $item['href']; ?>"><?php echo $item['label']; ?></a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
<section class="hero-section" id="main" style="background-image: url('<?php echo !empty($hero_background) ? $hero_background : 'images/default-bg.jpg'; ?>');">
    <div class="container">
        <div class="hero-content" data-aos="fade-up" data-aos-duration="1000">
            <h1 class="hero-title" data-aos="fade-up" data-aos-delay="200">
                <?php echo !empty($hero_title) ? $hero_title : 'Sistema de Agendamento Inteligente com IA'; ?>
            </h1>
            <p class="hero-subtitle" data-aos="fade-up" data-aos-delay="400">
                <?php echo !empty($hero_subtitle) ? $hero_subtitle : 'Simplifique sua gestão de agendamentos com nosso sistema que entende texto, áudio, imagens e integração ao Google Calendar. A escolha perfeita para otimizar seu atendimento.'; ?>
            </p>
            <div data-aos="fade-up" data-aos-delay="600">
                <a href="login/painel/login.php" class="btn btn-custom btn-primary-custom">Fazer Login</a>
                <a href="login/painel/cadastro_conta.php" class="btn btn-custom btn-secondary-custom">Cadastrar Agora</a>
            </div>
        </div>
    </div>
</section>


    <!-- Serviços Section -->
    <section id="services">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2><?php echo $services_title; ?></h2>
                <p><?php echo $services_description; ?></p>
            </div>

            <div class="row">
                <!-- Serviço 1 -->
                <div class="col-md-4 mb-4">
                    <div class="service-card" data-aos="fade-up" data-aos-delay="200">
                        <div class="service-icon">
                            <img src="<?php echo $card1_icon; ?>" alt="<?php echo $card1_title; ?>">
                        </div>
                        <h3 class="service-title"><?php echo $card1_title; ?></h3>
                        <p class="service-description"><?php echo $card1_description; ?></p>
                    </div>
                </div>

                <!-- Serviço 2 -->
                <div class="col-md-4 mb-4">
                    <div class="service-card" data-aos="fade-up" data-aos-delay="400">
                        <div class="service-icon">
                            <img src="<?php echo $card2_icon; ?>" alt="<?php echo $card2_title; ?>">
                        </div>
                        <h3 class="service-title"><?php echo $card2_title; ?></h3>
                        <p class="service-description"><?php echo $card2_description; ?></p>
                    </div>
                </div>

                <!-- Serviço 3 -->
                <div class="col-md-4 mb-4">
                    <div class="service-card" data-aos="fade-up" data-aos-delay="600">
                        <div class="service-icon">
                            <img src="<?php echo $card3_icon; ?>" alt="<?php echo $card3_title; ?>">
                        </div>
                        <h3 class="service-title"><?php echo $card3_title; ?></h3>
                        <p class="service-description"><?php echo $card3_description; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <div class="feature-img" data-aos="fade-right" data-aos-duration="1200">
                        <img class="img-fluid rounded-lg float-animation" src="<?php echo $feature_image; ?>" alt="<?php echo $feature_title; ?>">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="feature-content" data-aos="fade-left" data-aos-duration="1200">
                        <h2 class="feature-title"><?php echo $feature_title; ?></h2>
                        <p class="feature-description"><?php echo $feature_description; ?></p>
                        <ul class="feature-list">
                            <?php foreach($feature_items as $item): ?>
                            <li><i class="fas fa-check-circle"></i> <?php echo $item; ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <a href="login/painel/cadastro_conta.php" class="btn btn-custom btn-primary-custom">Cadastre Agora</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Video Section -->
    <section id="video-section">
        <div class="container">
            <div class="video-content" data-aos="fade-up">
                <h2 class="video-title">Demonstração do Sistema</h2>
                <p class="video-description"><?php echo $texto_vendas; ?></p>
                <?php
                // Só exibe se houver algo em $video_youtube
                if (!empty($video_youtube)):
                    // Tenta extrair o ID do vídeo (aceita watch?v=… ou youtu.be/…)
                    $video_id = null;
                    if (preg_match('/(?:youtu\.be\/|v=)([A-Za-z0-9_-]{11})/', $video_youtube, $m)) {
                        $video_id = $m[1];
                    }

                    if ($video_id):
                        // Monta a URL de embed
                        $embed_url = "https://www.youtube.com/embed/{$video_id}";
                ?>
                    <div class="video-wrapper" data-aos="zoom-in" data-aos-delay="200">
                        <iframe 
                            src="<?= htmlspecialchars($embed_url) ?>" 
                            title="YouTube video player" 
                            frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                            allowfullscreen>
                        </iframe>
                    </div>
                <?php
                    else:
                        echo '<p class="text-danger">URL de vídeo inválida.</p>';
                    endif;
                endif;
                ?>
                
                <div class="mt-4" data-aos="fade-up" data-aos-delay="400">
                    <a href="login/painel/cadastro_conta.php" class="btn btn-custom btn-primary-custom">Experimente Agora</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2>Nossos Planos</h2>
                <p>Escolha o plano ideal para o seu negócio</p>
            </div>

            <div class="row justify-content-center">
                <?php 
                $i = 0;
                foreach($planos as $plano): 
                    $delay    = 200 + ($i * 200);
                    $featured = ($i == 1) ? 'featured' : '';
                ?>
                <div class="col-lg-4 col-md-6 mb-4 d-flex align-items-stretch">
                    <div class="pricing-card <?php echo $featured; ?>" 
                         data-aos="zoom-in" 
                         data-aos-duration="1000" 
                         data-aos-delay="<?php echo $delay; ?>">
                        <?php if($featured): ?>
                        <div class="featured-badge">Mais Popular</div>
                        <?php endif; ?>
                        <div class="pricing-header text-center">
                            <div class="pricing-icon mb-3">
                                <img src="<?php echo $plano['icone']; ?>" alt="<?php echo $plano['titulo']; ?>">
                            </div>
                            <h3 class="pricing-title"><?php echo $plano['titulo']; ?></h3>
                            <p class="pricing-price"><?php echo $plano['preco']; ?></p>
                        </div>
                        <div class="pricing-body d-flex flex-column">
                            <ul class="pricing-features list-unstyled mb-4">
                                <?php foreach($plano['features'] as $feature): ?>
                                <li class="mb-2">
                                    <i class="fas fa-check-circle text-success mr-2"></i>
                                    <?php echo $feature; ?>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            <a 
                                href="login/painel/cadastro_conta.php?code_pag=<?php echo urlencode($plano['code_pag']); ?>" 
                                class="btn btn-custom btn-primary-custom btn-block mt-auto"
                            >
                                Assinar Agora
                            </a>
                        </div>
                    </div>
                </div>
                <?php 
                    $i++;
                endforeach; 
                ?>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact">
        <div class="container">
            <div class="contact-content" data-aos="fade-up">
                <h2 class="contact-title"><?php echo $contact_title; ?></h2>
                <p class="contact-description"><?php echo $contact_description; ?></p>
                <a href="https://wa.me/<?php echo $telefone_whatsapp; ?>" target="_blank" class="btn btn-custom whatsapp-btn" data-aos="zoom-in" data-aos-delay="200">
                    <i class="fab fa-whatsapp mr-2"></i> <?php echo $contact_button_text; ?>
                </a>
                <p class="contact-phone" data-aos="fade-up" data-aos-delay="400">
                    Nosso número de contato: <strong><?php echo $telefone; ?></strong>
                </p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-logo">
                <img src="<?php echo $logo; ?>" alt="<?php echo $titulo; ?> Logo">
            </div>
            <p class="footer-copyright"><?php echo $footer_copyright; ?></p>
        </div>
    </footer>

    <!-- Botão Voltar ao Topo -->
    <a href="#main" class="back-to-top page-scroll">
        <i class="fas fa-arrow-up"></i>
    </a>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <!-- AOS JS - Animações ao rolar -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    
    <!-- Scripts personalizados -->
    <script>
       $(document).ready(function() {
    // Inicializar AOS
    AOS.init({
        once: true,
        offset: 50,
        duration: 800,
        easing: 'ease-in-out',
        disable: 'mobile'
    });
    
    // Rolagem suave para links de âncora - CUSTOMIZADO PARA SEUS MENUS ESPECÍFICOS
    $('a.page-scroll, a[href^="#"]').on('click', function(e) {
        var target = $(this.getAttribute('href'));
        var clickedLink = $(this);
        
        if (target.length) {
            e.preventDefault();
            
            // Calcular altura da navbar
            var navHeight = 0;
            if ($('.navbar').length && $('.navbar').is(':visible')) {
                navHeight = $('.navbar').outerHeight(true);
            }
            
            var sectionId = target.attr('id');
            var sectionTop = target.offset().top;
            var targetOffset;
            
            // CONFIGURAÇÃO ESPECÍFICA PARA CADA SEÇÃO
            switch(sectionId) {
                case 'main':
                    // Início - vai para o topo absoluto
                    targetOffset = 0;
                    break;
                    
                case 'services':
                    // Importante - ajuste médio
                    targetOffset = sectionTop - navHeight - 30;
                    break;
                    
                case 'features':
                    // Benefícios - ajuste padrão
                    targetOffset = sectionTop - navHeight - 25;
                    break;
                    
                case 'video-section':
                    // Demonstração - mais espaço para vídeo aparecer bem
                    targetOffset = sectionTop - navHeight - 40;
                    break;
                    
                case 'pricing':
                    // Preços - ajuste padrão
                    targetOffset = sectionTop - navHeight - 30;
                    break;
                    
                case 'contact':
                    // Contato - última seção, pode ir mais próximo
                    targetOffset = sectionTop - navHeight - 20;
                    break;
                    
                default:
                    // Fallback para outras seções
                    targetOffset = sectionTop - navHeight - 30;
            }
            
            // Debug específico para suas seções
            console.log('=== SCROLL PARA: ' + sectionId.toUpperCase() + ' ===');
            console.log('Posição da seção:', sectionTop);
            console.log('Altura navbar:', navHeight);
            console.log('Offset calculado:', targetOffset);
            console.log('========================================');
            
            // Garantir que não vá além do topo
            if (targetOffset < 0) {
                targetOffset = 0;
            }
            
            $('html, body').animate({
                scrollTop: targetOffset
            }, 600, function() {
                console.log('✅ Chegou em:', $(window).scrollTop(), 'para seção:', sectionId);
            });
        }
    });

    // Função de scroll da janela - OTIMIZADO
    var scrollTimeout;
    $(window).on('scroll', function() {
        // Debounce reduzido para resposta mais rápida
        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(function() {
            var scrollPos = $(window).scrollTop();
            
            // Navbar scroll efeito
            if (scrollPos > 50) {
                $('.navbar').addClass('navbar-scrolled');
            } else {
                $('.navbar').removeClass('navbar-scrolled');
            }
            
            // Botão voltar ao topo
            if (scrollPos > 300) {
                $('.back-to-top').addClass('active').fadeIn();
            } else {
                $('.back-to-top').removeClass('active').fadeOut();
            }
            
            // Ativar item do menu baseado na seção - SINCRONIZADO COM SUAS SEÇÕES
            var current = '';
            var navHeight = 0;
            if ($('.navbar').length && $('.navbar').is(':visible')) {
                navHeight = $('.navbar').outerHeight(true);
            }
            
            // Verificar cada seção específica com os mesmos ajustes do scroll
            var sections = ['main', 'services', 'features', 'video-section', 'pricing', 'contact'];
            
            sections.forEach(function(sectionId) {
                var sectionElement = $('#' + sectionId);
                if (sectionElement.length) {
                    var sectionTop = sectionElement.offset().top;
                    var sectionHeight = sectionElement.outerHeight();
                    var sectionStart, sectionEnd;
                    
                    // Usar os MESMOS valores de ajuste do scroll para cada seção
                    switch(sectionId) {
                        case 'main':
                            sectionStart = 0;
                            sectionEnd = sectionTop + sectionHeight;
                            break;
                            
                        case 'services':
                            sectionStart = sectionTop - navHeight - 30;
                            sectionEnd = sectionTop + sectionHeight;
                            break;
                            
                        case 'features':
                            sectionStart = sectionTop - navHeight - 25;
                            sectionEnd = sectionTop + sectionHeight;
                            break;
                            
                        case 'video-section':
                            sectionStart = sectionTop - navHeight - 40;
                            sectionEnd = sectionTop + sectionHeight;
                            break;
                            
                        case 'pricing':
                            sectionStart = sectionTop - navHeight - 30;
                            sectionEnd = sectionTop + sectionHeight;
                            break;
                            
                        case 'contact':
                            sectionStart = sectionTop - navHeight - 20;
                            sectionEnd = sectionTop + sectionHeight;
                            break;
                            
                        default:
                            sectionStart = sectionTop - navHeight - 30;
                            sectionEnd = sectionTop + sectionHeight;
                    }
                    
                    // Se está dentro da área da seção
                    if (scrollPos >= sectionStart && scrollPos < sectionEnd) {
                        current = sectionId;
                    }
                }
            });
            
            // Atualizar nav links ativos apenas se mudou
            if (current) {
                var activeLink = $('.navbar-nav .nav-link[href="#' + current + '"]');
                if (!activeLink.hasClass('active')) {
                    $('.navbar-nav .nav-link').removeClass('active');
                    activeLink.addClass('active');
                }
            }
            
        }, 5); // Reduzido de 10 para 5ms - resposta mais rápida
    });
    
    // Fechar navbar ao clicar em um link em telas pequenas - MELHORADO
    $('.navbar-nav .nav-link').on('click', function() {
        if ($(window).width() < 992) { // apenas em telas pequenas
            $('.navbar-collapse').collapse('hide');
        }
    });
    
    // Prevenir zoom em iOS - MELHORADO
    var lastTouchEnd = 0;
    document.addEventListener('touchstart', function(event) {
        if (event.touches.length > 1) {
            event.preventDefault();
        }
    }, { passive: false });
    
    document.addEventListener('touchend', function(event) {
        var now = (new Date()).getTime();
        if (now - lastTouchEnd <= 300) {
            event.preventDefault();
        }
        lastTouchEnd = now;
    }, false);
    
    // Ajustar altura da viewport em dispositivos móveis - MELHORADO
    function setVH() {
        let vh = window.innerHeight * 0.01;
        document.documentElement.style.setProperty('--vh', `${vh}px`);
    }
    
    // Executar imediatamente e nos eventos
    setVH();
    
    // Debounce para resize
    var resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(setVH, 100);
    });
    
    window.addEventListener('orientationchange', function() {
        setTimeout(setVH, 500); // delay maior para orientationchange
    });
    
    // Scroll suave para o botão "voltar ao topo" - OTIMIZADO
    $('.back-to-top').on('click', function(e) {
        e.preventDefault();
        $('html, body').animate({
            scrollTop: 0
        }, 500); // Mais rápido também
    });
});

// Popup script - MANTIDO IGUAL
$(document).ready(function() {
    // Variável para controlar se o popup já foi mostrado
    let popupShown = false;
    
    // Mostrar popup após 50 segundos
    setTimeout(function() {
        if (!popupShown) {
            $('#specialOfferPopup').addClass('show');
            popupShown = true;
            startCountdown();
        }
    }, 50000);
    
    // Detectar quando usuário tenta sair da página (apenas desktop)
    if (window.innerWidth > 768) {
        $(document).on('mouseleave', function(e) {
            if (e.clientY < 5 && !popupShown) {
                $('#specialOfferPopup').addClass('show');
                popupShown = true;
                startCountdown();
            }
        });
    }
    
    // Detectar tentativa de voltar na página
    let initialPageLoad = true;
    
    window.addEventListener('popstate', function(event) {
        if (!initialPageLoad && !popupShown) {
            history.pushState(null, document.title, window.location.href);
            $('#specialOfferPopup').addClass('show');
            popupShown = true;
            startCountdown();
        }
        initialPageLoad = false;
    });
    
    history.pushState(null, document.title, window.location.href);
    
    // Fechar popup ao clicar no X
    $('.special-offer-close').click(function() {
        $('#specialOfferPopup').removeClass('show');
    });
    
    // Processar formulário
    $('#specialOfferForm').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            type: 'POST',
            url: 'salvar_contato.php',
            data: $(this).serialize(),
            success: function(response) {
                $('.special-offer-content').html('<div style="text-align:center;padding:2rem 1rem;">' + 
                    '<i class="fas fa-check-circle" style="font-size:3rem;color:#4CAF50;margin-bottom:1rem;"></i>' + 
                    '<h4 style="font-size:1.5rem;margin-bottom:1rem;color:var(--primary-color);">Oferta Garantida!</h4>' + 
                    '<p style="font-size:1rem;margin-bottom:0.5rem;">Você garantiu sua vaga com sucesso!</p>' + 
                    '<p style="font-size:0.875rem;color:#666;">Enviaremos as instruções de acesso para seu WhatsApp em breve.</p>' + 
                    '</div>');
                
                setTimeout(function() {
                    $('#specialOfferPopup').removeClass('show');
                }, 5000);
            }
        });
    });
    
    // Fechar popup clicando fora da caixa
    $('#specialOfferPopup').click(function(e) {
        if (e.target === this) {
            $(this).removeClass('show');
        }
    });
    
    // Função para iniciar o cronômetro
    function startCountdown() {
        let minutes = 5;
        let seconds = 0;
        
        let countdownInterval = setInterval(function() {
            if (seconds == 0) {
                if (minutes == 0) {
                    clearInterval(countdownInterval);
                    return;
                } else {
                    minutes--;
                    seconds = 59;
                }
            } else {
                seconds--;
            }
            
            // Formatar o tempo (MM:SS)
            let formattedMinutes = minutes < 10 ? "0" + minutes : minutes;
            let formattedSeconds = seconds < 10 ? "0" + seconds : seconds;
            
            // Atualizar o cronômetro
            $('#countdownTimer').text(formattedMinutes + ":" + formattedSeconds);
            
            // Efeito visual quando o tempo está acabando
            if (minutes === 0 && seconds <= 30) {
                $('#countdownTimer').css('color', '#e74c3c');
                if (seconds <= 10) {
                    $('#countdownTimer').css('animation', 'blink 1s infinite');
                }
            }
        }, 1000);
    }
    
    // Efeito de piscar para o cronômetro
    $('<style>')
        .prop('type', 'text/css')
        .html(`
            @keyframes blink {
                0% { opacity: 1; }
                50% { opacity: 0.5; }
                100% { opacity: 1; }
            }
        `)
        .appendTo('head');
});
    </script>
</body>
</html>