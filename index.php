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

    $hero_background = !empty($rows_config['caminho_modelo']) ? 'login/painel/' . $rows_config['caminho_modelo'] : '';

    // Tema não é mais usado no design Enam (fixo dark navy)
    $tema = isset($rows_config['tema']) ? (int)$rows_config['tema'] : 4;
}

// Valores padrão
$default_values = array(
    'card1_icon' => 'assets/logos/icon1.png',
    'card1_title' => 'Reconhecimento Avançado',
    'card1_description' => 'Muito além do texto: uma IA capaz de compreender comandos por voz, escrita e imagens com máxima eficiência.',
    
    'card2_icon' => 'assets/logos/icon2.png',
    'card2_title' => 'Vendas no Piloto Automático',
    'card2_description' => 'Deixe nossa IA identificar as necessidades do cliente e fechar vendas personalizadas em poucos segundos.',
    
    'card3_icon' => 'assets/logos/icon3.png',
    'card3_title' => 'Disponibilidade 24/7',
    'card3_description' => 'Sua empresa sempre aberta e pronta para tirar dúvidas, atender clientes e fechar vendas a qualquer hora do dia ou da noite.',
    
    'feature_image' => 'assets/images/feature_1.png',
    'feature_title' => 'Assistente Inteligente para Vendas e Atendimento',
    'feature_description' => 'Elimine o trabalho manual e os gargalos de atendimento. Nossa IA multimodal organiza demandas, responde clientes na hora e mantém sua operação comercial rodando com máxima eficiência.',
    'feature_items' => array(
        'Atendimento personalizado com linguagem natural.',
        'Identificação de oportunidades de vendas.',
        'Disponível 24 horas por dia, 7 dias por semana.'
    )
);

// Consulta as configurações do banco de dados
$query = "SELECT * FROM config";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $config = mysqli_fetch_assoc($result);
    
    function getConfigValue($config, $field, $default) {
        return (!empty($config[$field])) ? $config[$field] : $default;
    }
    
    $card1_icon = getConfigValue($config, 'card1_icon', $default_values['card1_icon']);
    $card1_title = getConfigValue($config, 'card1_title', $default_values['card1_title']);
    $card1_description = getConfigValue($config, 'card1_description', $default_values['card1_description']);
    
    $card2_icon = getConfigValue($config, 'card2_icon', $default_values['card2_icon']);
    $card2_title = getConfigValue($config, 'card2_title', $default_values['card2_title']);
    $card2_description = getConfigValue($config, 'card2_description', $default_values['card2_description']);
    
    $card3_icon = getConfigValue($config, 'card3_icon', $default_values['card3_icon']);
    $card3_title = getConfigValue($config, 'card3_title', $default_values['card3_title']);
    $card3_description = getConfigValue($config, 'card3_description', $default_values['card3_description']);
    
    $feature_image = getConfigValue($config, 'feature_image', $default_values['feature_image']);
    $feature_title = getConfigValue($config, 'feature_title', $default_values['feature_title']);
    $feature_description = getConfigValue($config, 'feature_description', $default_values['feature_description']);
    
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
    $caminho_modelo = getConfigValue($config, 'caminho_modelo', '');
    $hero_title = getConfigValue($config, 'hero_title', '');
    $hero_subtitle = getConfigValue($config, 'hero_subtitle', '');
    $services_title = getConfigValue($config, 'services_title', '');
    $services_description = getConfigValue($config, 'services_description', '');
    $texto_vendas = getConfigValue($config, 'texto_vendas', '');
    $video_youtube = getConfigValue($config, 'video_youtube', '');
    
} else {
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
    $caminho_modelo = '';
    $hero_title = '';
    $hero_subtitle = '';
    $services_title = '';
    $services_description = '';
    $texto_vendas = '';
    $video_youtube = '';
}

// Telefone WhatsApp
$telefone_whatsapp = str_replace(['(', ')', ' ', '-'], '', $telefone);

// Inicializa o array de planos
$planos = [];
$sql_planos = "SELECT * FROM planos_online WHERE ativo = 1 ORDER BY preco ASC";
$result_planos = mysqli_query($conn, $sql_planos);

if ($result_planos && mysqli_num_rows($result_planos) > 0) {
    while ($plano = mysqli_fetch_assoc($result_planos)) {
        $id_plano = $plano['id'];
        $features = [];
        $sql_features = "SELECT feature FROM planos_features WHERE id_plano = $id_plano ORDER BY id";
        $result_features = mysqli_query($conn, $sql_features);
        if ($result_features && mysqli_num_rows($result_features) > 0) {
            while ($feature = mysqli_fetch_assoc($result_features)) {
                $features[] = $feature['feature'];
            }
        }
        $planos[] = [
            'titulo'   => $plano['titulo'],
            'preco'    => 'R$ ' . number_format($plano['preco'], 2, ',', '.'),
            'icone'    => '/login/painel/' . $small_logo,
            'features' => $features,
            'code_pag' => $plano['code_pag']
        ];
    }
}

// Seção de contato
$contact_title = 'Precisa de ajuda? Fale conosco no WhatsApp!';
$contact_description = 'Estamos disponíveis para responder suas dúvidas ou fornecer mais informações. Clique no botão abaixo para falar diretamente conosco via WhatsApp.';
$contact_button_text = 'Fale pelo WhatsApp';

// Rodapé
$footer_copyright = 'Copyright &copy; ' . date('Y') . '. Todos os direitos reservados.';

// Menu itens
$menu_items = [
    ['href' => '#main',         'label' => 'Início'],
    ['href' => '#services',     'label' => 'Recursos'],
    ['href' => '#features',     'label' => 'Benefícios'],
    ['href' => '#video-section','label' => 'Demonstração'],
    ['href' => '#pricing',      'label' => 'Planos'],
    ['href' => '#contact',      'label' => 'Contato']
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title><?php echo $titulo; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Robô de Atendimento Inteligente com IA">
    <meta name="keywords" content="atendimento, inteligência artificial, robô, automação, vendas, chatbot, whatsapp">
    <meta name="author" content="<?php echo $titulo; ?>">

    <link rel="icon" href="<?php echo $icon; ?>" type="image/x-icon">
    <link rel="shortcut icon" href="<?php echo $icon; ?>" type="image/x-icon">
    <link rel="apple-touch-icon" href="<?php echo $icon; ?>">

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">

    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --bg-deep:    #001228;
            --bg-dark:    #001f3f;
            --bg-mid:     #00172e;
            --accent:     #FF5500;
            --accent-dim: rgba(255,85,0,0.12);
            --accent-glow:rgba(255,85,0,0.35);
            --white:      #ffffff;
            --white-60:   rgba(255,255,255,0.60);
            --white-40:   rgba(255,255,255,0.40);
            --white-08:   rgba(255,255,255,0.08);
            --white-04:   rgba(255,255,255,0.04);
            --border:     rgba(255,255,255,0.07);
            --transition: all 0.35s cubic-bezier(0.165,0.84,0.44,1);
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Montserrat', sans-serif;
            background: var(--bg-dark);
            color: var(--white);
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* ── NAVBAR ── */
        .enam-nav {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 1000;
            padding: 1.1rem 0;
            background: rgba(0,18,40,0.6);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border-bottom: 1px solid var(--border);
            transition: var(--transition);
        }
        .enam-nav.scrolled {
            padding: 0.65rem 0;
            background: rgba(0,18,40,0.92);
        }
        .nav-inner {
            max-width: 1180px;
            margin: 0 auto;
            padding: 0 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .nav-logo img { height: 40px; width: auto; }
        .nav-links {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            list-style: none;
        }
        .nav-links a {
            color: var(--white-60);
            font-size: 0.875rem;
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            text-decoration: none;
            transition: var(--transition);
        }
        .nav-links a:hover, .nav-links a.active {
            color: var(--white);
            background: var(--white-08);
        }
        .nav-cta {
            background: var(--accent) !important;
            color: var(--white) !important;
            padding: 0.55rem 1.25rem !important;
            border-radius: 10px !important;
            box-shadow: 0 4px 16px var(--accent-glow);
        }
        .nav-cta:hover { filter: brightness(1.1); transform: translateY(-1px); }
        .nav-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--white);
            font-size: 1.4rem;
            cursor: pointer;
            padding: 0.4rem;
        }
        @media(max-width:900px){
            .nav-toggle { display: block; }
            .nav-links {
                display: none;
                flex-direction: column;
                position: absolute;
                top: 100%; left: 0; right: 0;
                background: rgba(0,18,40,0.97);
                padding: 1rem 1.5rem 1.5rem;
                gap: 0.15rem;
                border-bottom: 1px solid var(--border);
            }
            .nav-links.open { display: flex; }
            .nav-links a { width: 100%; }
        }

        /* ── HERO ── */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            padding-top: 80px;
        }
        .hero-bg {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, #001228 0%, #002855 60%, #001228 100%);
        }
        .hero-blob-1 {
            position: absolute;
            top: 20%; right: -80px;
            width: 600px; height: 600px;
            border-radius: 50%;
            background: radial-gradient(circle, #FF5500 0%, transparent 70%);
            opacity: 0.10;
            pointer-events: none;
        }
        .hero-blob-2 {
            position: absolute;
            bottom: 0; left: 20%;
            width: 400px; height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, #0066cc 0%, transparent 70%);
            opacity: 0.08;
            pointer-events: none;
        }
        .hero-inner {
            position: relative;
            z-index: 2;
            max-width: 1180px;
            margin: 0 auto;
            padding: 5rem 1.5rem 5rem;
        }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 1rem;
            border-radius: 50px;
            border: 1px solid rgba(255,85,0,0.35);
            background: rgba(255,85,0,0.08);
            color: #FF8855;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            margin-bottom: 2rem;
        }
        .hero-badge-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: var(--accent);
            box-shadow: 0 0 8px var(--accent);
            flex-shrink: 0;
        }
        .hero-title {
            font-size: clamp(2.4rem, 6vw, 5rem);
            font-weight: 900;
            line-height: 1.05;
            letter-spacing: -0.02em;
            margin-bottom: 1.5rem;
            color: var(--white);
        }
        .hero-title .hl { color: var(--accent); }
        .hero-subtitle {
            font-size: clamp(1rem, 2vw, 1.15rem);
            color: var(--white-60);
            max-width: 560px;
            line-height: 1.75;
            margin-bottom: 2.5rem;
            font-weight: 500;
        }
        .hero-btns {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 3.5rem;
        }
        .btn-orange {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.9rem 2rem;
            border-radius: 12px;
            background: var(--accent);
            color: var(--white);
            font-weight: 700;
            font-size: 0.95rem;
            text-decoration: none;
            box-shadow: 0 8px 24px var(--accent-glow);
            transition: var(--transition);
        }
        .btn-orange:hover { filter: brightness(1.1); transform: translateY(-2px); color: var(--white); text-decoration: none; }
        .btn-ghost {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.9rem 2rem;
            border-radius: 12px;
            border: 1.5px solid rgba(255,255,255,0.2);
            background: rgba(255,255,255,0.04);
            color: var(--white-60);
            font-weight: 700;
            font-size: 0.95rem;
            text-decoration: none;
            transition: var(--transition);
        }
        .btn-ghost:hover { color: var(--white); background: rgba(255,255,255,0.08); text-decoration: none; }
        .hero-stats {
            display: flex;
            gap: 2.5rem;
            flex-wrap: wrap;
            padding-top: 2.5rem;
            border-top: 1px solid var(--border);
        }
        .stat-num {
            font-size: 1.9rem;
            font-weight: 900;
            color: var(--accent);
            line-height: 1;
        }
        .stat-label {
            font-size: 0.78rem;
            color: var(--white-40);
            font-weight: 600;
            margin-top: 0.3rem;
        }

        /* ── SECTION BASE ── */
        .enam-section {
            padding: 5.5rem 1.5rem;
        }
        .section-inner {
            max-width: 1180px;
            margin: 0 auto;
        }
        .section-label {
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.14em;
            color: var(--accent);
            text-transform: uppercase;
            margin-bottom: 0.75rem;
        }
        .section-heading {
            font-size: clamp(1.75rem, 4vw, 2.75rem);
            font-weight: 900;
            line-height: 1.15;
            letter-spacing: -0.015em;
            color: var(--white);
            margin-bottom: 3.5rem;
        }
        .section-heading .hl { color: var(--accent); }
        .section-heading-center { text-align: center; }

        /* ── SERVICES (CARDS) ── */
        .bg-mid { background: var(--bg-mid); }
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.25rem;
        }
        .enam-card {
            padding: 1.75rem;
            border-radius: 18px;
            background: var(--white-04);
            border: 1px solid var(--border);
            transition: var(--transition);
        }
        .enam-card:hover { transform: translateY(-4px); border-color: rgba(255,85,0,0.2); }
        .card-icon-wrap {
            width: 50px; height: 50px;
            border-radius: 12px;
            background: var(--accent-dim);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.1rem;
        }
        .card-icon-wrap img {
            width: 28px; height: 28px;
            object-fit: contain;
            filter: invert(1) sepia(1) saturate(5) hue-rotate(330deg) brightness(1.1);
        }
        .card-icon-wrap i {
            font-size: 1.25rem;
            color: var(--accent);
        }
        .enam-card h3 {
            font-size: 1rem;
            font-weight: 700;
            color: var(--white);
            margin-bottom: 0.6rem;
        }
        .enam-card p {
            font-size: 0.875rem;
            color: var(--white-60);
            line-height: 1.7;
        }

        /* ── FEATURES ── */
        .bg-deep { background: var(--bg-deep); }
        .features-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }
        @media(max-width:768px){ .features-grid { grid-template-columns: 1fr; gap: 2rem; } }
        .feature-img {
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid var(--border);
        }
        .feature-img img { width: 100%; display: block; }
        .feature-text h2 {
            font-size: clamp(1.5rem, 3vw, 2.25rem);
            font-weight: 900;
            line-height: 1.2;
            color: var(--white);
            margin-bottom: 1rem;
        }
        .feature-text p {
            font-size: 0.95rem;
            color: var(--white-60);
            line-height: 1.8;
            margin-bottom: 1.5rem;
        }
        .feature-list {
            list-style: none;
            margin-bottom: 2rem;
        }
        .feature-list li {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            font-size: 0.9rem;
            color: var(--white-60);
            margin-bottom: 0.8rem;
        }
        .feature-list li i {
            color: var(--accent);
            margin-top: 0.2rem;
            flex-shrink: 0;
        }

        /* ── VIDEO ── */
        .video-wrap {
            border-radius: 18px;
            overflow: hidden;
            border: 1px solid var(--border);
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            margin-top: 1.5rem;
        }
        .video-wrap iframe {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            border: none;
        }
        .video-text-center { text-align: center; max-width: 640px; margin: 0 auto 2rem; }
        .video-text-center p {
            font-size: 0.95rem;
            color: var(--white-60);
            line-height: 1.8;
            margin-bottom: 1.5rem;
        }

        /* ── PRICING ── */
        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(270px, 1fr));
            gap: 1.5rem;
            align-items: center;
        }
        .pricing-card {
            padding: 2rem;
            border-radius: 20px;
            background: var(--white-04);
            border: 1px solid var(--border);
            position: relative;
            transition: var(--transition);
        }
        .pricing-card:hover { transform: translateY(-4px); }
        .pricing-card.featured {
            background: linear-gradient(135deg, #FF5500, #cc3300);
            border: none;
            box-shadow: 0 20px 60px rgba(255,85,0,0.30);
            transform: scale(1.04);
        }
        .pricing-card.featured:hover { transform: scale(1.04) translateY(-4px); }
        .pricing-badge {
            position: absolute;
            top: -14px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--bg-deep);
            color: var(--white);
            font-size: 0.7rem;
            font-weight: 800;
            letter-spacing: 0.1em;
            padding: 0.3rem 1rem;
            border-radius: 50px;
        }
        .pricing-plan-name {
            font-size: 0.8rem;
            font-weight: 700;
            color: rgba(255,255,255,0.55);
            margin-bottom: 0.4rem;
        }
        .pricing-price {
            font-size: 2.25rem;
            font-weight: 900;
            color: var(--white);
            line-height: 1;
            margin-bottom: 0.3rem;
        }
        .pricing-period {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.45);
            margin-bottom: 1.5rem;
        }
        .pricing-features {
            list-style: none;
            margin-bottom: 1.75rem;
        }
        .pricing-features li {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-size: 0.875rem;
            color: rgba(255,255,255,0.7);
            margin-bottom: 0.65rem;
        }
        .pricing-features li i { color: var(--accent); flex-shrink: 0; }
        .pricing-card.featured .pricing-features li i { color: rgba(255,255,255,0.85); }
        .pricing-card.featured .pricing-features li { color: rgba(255,255,255,0.9); }
        .btn-plan {
            display: block;
            width: 100%;
            padding: 0.8rem;
            border-radius: 12px;
            background: var(--accent);
            color: var(--white);
            font-weight: 700;
            font-size: 0.9rem;
            text-align: center;
            text-decoration: none;
            transition: var(--transition);
        }
        .btn-plan:hover { filter: brightness(1.12); color: var(--white); text-decoration: none; }
        .pricing-card.featured .btn-plan {
            background: rgba(255,255,255,0.18);
        }
        .pricing-card.featured .btn-plan:hover { background: rgba(255,255,255,0.28); }

        /* ── CONTACT ── */
        .contact-box {
            text-align: center;
            max-width: 660px;
            margin: 0 auto;
        }
        .contact-box h2 {
            font-size: clamp(1.5rem, 3.5vw, 2.5rem);
            font-weight: 900;
            color: var(--white);
            margin-bottom: 1rem;
        }
        .contact-box h2 .hl { color: var(--accent); }
        .contact-box p {
            font-size: 0.95rem;
            color: var(--white-60);
            line-height: 1.8;
            margin-bottom: 2rem;
        }
        .contact-phone {
            margin-top: 1.25rem;
            font-size: 0.85rem;
            color: var(--white-40);
        }
        .btn-whatsapp {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            padding: 1rem 2.25rem;
            border-radius: 12px;
            background: #25D366;
            color: var(--white);
            font-weight: 700;
            font-size: 1rem;
            text-decoration: none;
            box-shadow: 0 8px 24px rgba(37,211,102,0.3);
            transition: var(--transition);
        }
        .btn-whatsapp:hover { filter: brightness(1.1); transform: translateY(-2px); color: var(--white); text-decoration: none; }

        /* ── FOOTER ── */
        .enam-footer {
            background: #000d1a;
            border-top: 1px solid var(--border);
            padding: 2.5rem 1.5rem;
            text-align: center;
        }
        .footer-inner {
            max-width: 1180px;
            margin: 0 auto;
        }
        .enam-footer img { height: 36px; width: auto; margin-bottom: 1rem; }
        .enam-footer p {
            font-size: 0.8rem;
            color: var(--white-40);
        }

        /* ── BACK TO TOP ── */
        .back-to-top {
            position: fixed;
            bottom: 1.5rem;
            right: 1.5rem;
            width: 44px; height: 44px;
            border-radius: 50%;
            background: var(--accent);
            color: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 1rem;
            box-shadow: 0 4px 16px var(--accent-glow);
            opacity: 0;
            pointer-events: none;
            transition: var(--transition);
            z-index: 900;
        }
        .back-to-top.active { opacity: 1; pointer-events: auto; }
        .back-to-top:hover { filter: brightness(1.1); transform: translateY(-2px); color: var(--white); }

        /* ── POPUP ── */
        .special-offer-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.85);
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            padding: 1rem;
        }
        .special-offer-overlay.show { opacity: 1; visibility: visible; }
        .special-offer-container {
            width: 100%;
            max-width: 440px;
            background: #001f3f;
            border: 1px solid rgba(255,85,0,0.25);
            border-radius: 16px;
            position: relative;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
            transform: translateY(20px);
            transition: transform 0.4s ease;
            overflow: hidden;
            max-height: 90vh;
            overflow-y: auto;
        }
        .special-offer-overlay.show .special-offer-container { transform: translateY(0); }
        .special-offer-tag {
            position: absolute;
            top: 15px; left: -35px;
            background: var(--accent);
            color: white;
            padding: 5px 40px;
            font-size: 0.7rem;
            font-weight: 800;
            transform: rotate(-45deg);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .special-offer-close {
            position: absolute;
            top: 10px; right: 10px;
            width: 30px; height: 30px;
            background: rgba(255,255,255,0.1);
            border: none;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: white;
            transition: all 0.3s ease;
            z-index: 2;
        }
        .special-offer-close:hover { background: rgba(255,255,255,0.2); }
        .special-offer-content {
            padding: 2rem 1.5rem 1.5rem;
            text-align: center;
        }
        .special-offer-urgency {
            display: flex;
            justify-content: space-around;
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px dashed rgba(255,255,255,0.12);
        }
        .urgency-counter { display: flex; flex-direction: column; align-items: center; }
        .counter-number { font-size: 2rem; font-weight: 900; color: var(--accent); line-height: 1; }
        .counter-text { font-size: 0.72rem; color: var(--white-60); text-align: center; line-height: 1.3; margin-top: 0.25rem; }
        .countdown-timer { display: flex; flex-direction: column; align-items: center; }
        .countdown-value {
            font-size: 1.6rem;
            font-weight: 700;
            color: #ff4444;
            background: rgba(255,68,68,0.1);
            padding: 0.2rem 0.9rem;
            border-radius: 6px;
            border: 1px solid rgba(255,68,68,0.25);
        }
        .countdown-label { font-size: 0.72rem; color: var(--white-60); margin-top: 0.25rem; }
        .special-offer-content h3 { color: var(--accent); font-size: 1.3rem; font-weight: 700; margin-bottom: 0.75rem; }
        .special-offer-content p { color: var(--white-60); margin-bottom: 1.25rem; font-size: 0.9rem; line-height: 1.6; }
        .special-offer-content p strong { color: var(--accent); font-weight: 700; }
        #specialOfferForm input {
            width: 100%;
            padding: 0.7rem 1rem;
            margin-bottom: 0.65rem;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 10px;
            font-size: 0.9rem;
            color: white;
            transition: all 0.3s ease;
        }
        #specialOfferForm input::placeholder { color: rgba(255,255,255,0.35); }
        #specialOfferForm input:focus { border-color: var(--accent); outline: none; background: rgba(255,255,255,0.07); }
        .btn-special-offer {
            width: 100%;
            padding: 0.875rem;
            background: var(--accent);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 0.875rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 5px 15px var(--accent-glow);
            letter-spacing: 0.04em;
            text-transform: uppercase;
            transition: var(--transition);
        }
        .btn-special-offer:hover { filter: brightness(1.1); }
        .security-badge { display: inline-block; margin-top: 1rem; font-size: 0.72rem; color: var(--white-40); }
        .security-badge i { color: #66BB6A; margin-right: 0.25rem; }

        /* ── RESPONSIVE ── */
        @media(max-width:600px){
            .hero-stats { gap: 1.25rem; }
            .pricing-card.featured { transform: scale(1); }
            .pricing-card.featured:hover { transform: translateY(-4px); }
        }
    </style>
</head>

<body>

<!-- ── POPUP OFERTA ESPECIAL ── -->
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
                <button type="submit" class="btn-special-offer">Garantir Minha Oferta Agora</button>
            </form>
            <div class="security-badge">
                <i class="fas fa-lock"></i> Seus dados estão seguros
            </div>
        </div>
    </div>
</div>

<!-- ── NAVBAR ── -->
<nav class="enam-nav" id="enamNav">
    <div class="nav-inner">
        <a class="nav-logo" href="#main">
            <img src="<?php echo $logo; ?>" alt="<?php echo $titulo; ?>">
        </a>
        <button class="nav-toggle" id="navToggle" aria-label="Menu"><i class="fas fa-bars"></i></button>
        <ul class="nav-links" id="navLinks">
            <?php foreach($menu_items as $item): ?>
            <li><a href="<?php echo $item['href']; ?>" class="page-scroll"><?php echo $item['label']; ?></a></li>
            <?php endforeach; ?>
            <li><a href="/login/painel/login.php" class="nav-cta">Acessar</a></li>
        </ul>
    </div>
</nav>

<!-- ── HERO ── -->
<section class="hero" id="main">
    <div class="hero-bg"></div>
    <div class="hero-blob-1"></div>
    <div class="hero-blob-2"></div>
    <div class="hero-inner" data-aos="fade-up" data-aos-duration="900">
        <div class="hero-badge">
            <span class="hero-badge-dot"></span>
            ATENDIMENTO INTELIGENTE &middot; WHATSAPP
        </div>
        <h1 class="hero-title">
            <?php
            $ht = !empty($hero_title) ? $hero_title : 'Agende com Inteligência. Atenda com Excelência.';
            // Wrap first word in orange if it's the default
            if (empty($hero_title)) {
                echo '<span class="hl">Agende</span> com Inteligência.<br><span class="hl">Atenda</span> com <span class="hl">Excelência.</span>';
            } else {
                echo htmlspecialchars($ht);
            }
            ?>
        </h1>
        <p class="hero-subtitle">
            <?php echo !empty($hero_subtitle) ? htmlspecialchars($hero_subtitle) : 'Automatize agendamentos, tire dúvidas e fidelize clientes — tudo pelo WhatsApp, com IA avançada trabalhando 24h por dia.'; ?>
        </p>
        <div class="hero-btns">
            <a href="/login/painel/cadastro_conta.php" class="btn-orange">
                Começar Grátis <i class="fas fa-arrow-right"></i>
            </a>
            <a href="/login/painel/login.php" class="btn-ghost">
                Fazer Login <i class="fas fa-chevron-right"></i>
            </a>
        </div>
        <div class="hero-stats">
            <div>
                <div class="stat-num">+5.000</div>
                <div class="stat-label">Clientes Atendidos</div>
            </div>
            <div>
                <div class="stat-num">98%</div>
                <div class="stat-label">Taxa de Satisfação</div>
            </div>
            <div>
                <div class="stat-num">24/7</div>
                <div class="stat-label">Disponibilidade</div>
            </div>
        </div>
    </div>
</section>

<!-- ── SERVIÇOS (CARDS) ── -->
<section class="enam-section bg-mid" id="services">
    <div class="section-inner">
        <div class="section-heading-center" data-aos="fade-up">
            <p class="section-label">RECURSOS</p>
            <h2 class="section-heading">
                <?php echo !empty($services_title) ? htmlspecialchars($services_title) : 'Tudo que você precisa'; ?>
                <br><span class="hl">em um só lugar</span>
            </h2>
        </div>
        <?php if(!empty($services_description)): ?>
        <p style="text-align:center;color:var(--white-60);font-size:0.95rem;line-height:1.8;max-width:640px;margin:-1.5rem auto 2.5rem;" data-aos="fade-up" data-aos-delay="100">
            <?php echo htmlspecialchars($services_description); ?>
        </p>
        <?php endif; ?>
        <div class="cards-grid">
            <div class="enam-card" data-aos="fade-up" data-aos-delay="100">
                <div class="card-icon-wrap">
                    <img src="<?php echo $card1_icon; ?>" alt="<?php echo $card1_title; ?>">
                </div>
                <h3><?php echo htmlspecialchars($card1_title); ?></h3>
                <p><?php echo htmlspecialchars($card1_description); ?></p>
            </div>
            <div class="enam-card" data-aos="fade-up" data-aos-delay="200">
                <div class="card-icon-wrap">
                    <img src="<?php echo $card2_icon; ?>" alt="<?php echo $card2_title; ?>">
                </div>
                <h3><?php echo htmlspecialchars($card2_title); ?></h3>
                <p><?php echo htmlspecialchars($card2_description); ?></p>
            </div>
            <div class="enam-card" data-aos="fade-up" data-aos-delay="300">
                <div class="card-icon-wrap">
                    <img src="<?php echo $card3_icon; ?>" alt="<?php echo $card3_title; ?>">
                </div>
                <h3><?php echo htmlspecialchars($card3_title); ?></h3>
                <p><?php echo htmlspecialchars($card3_description); ?></p>
            </div>
            <div class="enam-card" data-aos="fade-up" data-aos-delay="400">
                <div class="card-icon-wrap"><i class="fas fa-shield-alt"></i></div>
                <h3>Lista Negra</h3>
                <p>Controle quem pode interagir com o sistema e bloqueie contatos indesejados automaticamente.</p>
            </div>
            <div class="enam-card" data-aos="fade-up" data-aos-delay="500">
                <div class="card-icon-wrap"><i class="fas fa-history"></i></div>
                <h3>Histórico Completo</h3>
                <p>Acompanhe cada conversa, agendamento e status em um painel centralizado e intuitivo.</p>
            </div>
            <div class="enam-card" data-aos="fade-up" data-aos-delay="600">
                <div class="card-icon-wrap"><i class="fas fa-users"></i></div>
                <h3>Multi-Atendente</h3>
                <p>Vários profissionais, uma só central. Cada um gerencia sua própria agenda com total autonomia.</p>
            </div>
        </div>
    </div>
</section>

<!-- ── BENEFÍCIOS ── -->
<section class="enam-section bg-deep" id="features">
    <div class="section-inner">
        <div class="features-grid">
            <div class="feature-img" data-aos="fade-right" data-aos-duration="1000">
                <img src="<?php echo $feature_image; ?>" alt="<?php echo htmlspecialchars($feature_title); ?>">
            </div>
            <div class="feature-text" data-aos="fade-left" data-aos-duration="1000">
                <p class="section-label">BENEFÍCIOS</p>
                <h2><?php echo !empty($feature_title) ? htmlspecialchars($feature_title) : 'Assistente Inteligente para Vendas e Atendimento'; ?></h2>
                <p><?php echo !empty($feature_description) ? htmlspecialchars($feature_description) : 'Elimine o trabalho manual e os gargalos de atendimento. Nossa IA multimodal organiza demandas, responde clientes na hora e mantém sua operação comercial rodando com máxima eficiência.'; ?></p>
                <ul class="feature-list">
                    <?php foreach($feature_items as $item): ?>
                    <li><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($item); ?></li>
                    <?php endforeach; ?>
                </ul>
                <a href="/login/painel/cadastro_conta.php" class="btn-orange">Cadastrar Agora <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </div>
</section>

<!-- ── VÍDEO ── -->
<?php if(!empty($video_youtube)): ?>
<?php
$video_id = null;
if (preg_match('/(?:youtu\.be\/|v=)([A-Za-z0-9_-]{11})/', $video_youtube, $m)) {
    $video_id = $m[1];
}
if($video_id):
$embed_url = "https://www.youtube.com/embed/{$video_id}";
?>
<section class="enam-section bg-mid" id="video-section">
    <div class="section-inner">
        <div class="video-text-center" data-aos="fade-up">
            <p class="section-label">DEMONSTRAÇÃO</p>
            <h2 class="section-heading">Veja o sistema<br><span class="hl">em ação</span></h2>
            <?php if(!empty($texto_vendas)): ?>
            <p><?php echo htmlspecialchars($texto_vendas); ?></p>
            <?php endif; ?>
        </div>
        <div class="video-wrap" data-aos="zoom-in" data-aos-delay="200">
            <iframe src="<?php echo htmlspecialchars($embed_url); ?>"
                title="YouTube video player"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                allowfullscreen>
            </iframe>
        </div>
        <div style="text-align:center;margin-top:2rem;" data-aos="fade-up" data-aos-delay="300">
            <a href="/login/painel/cadastro_conta.php" class="btn-orange">Experimente Agora <i class="fas fa-arrow-right"></i></a>
        </div>
    </div>
</section>
<?php endif; ?>
<?php endif; ?>

<!-- ── PLANOS ── -->
<?php if(!empty($planos)): ?>
<section class="enam-section bg-deep" id="pricing">
    <div class="section-inner">
        <div class="section-heading-center" data-aos="fade-up">
            <p class="section-label">PLANOS</p>
            <h2 class="section-heading">Escolha o plano<br><span class="hl">ideal para você</span></h2>
        </div>
        <div class="pricing-grid">
            <?php
            $pi = 0;
            foreach($planos as $plano):
                $featured = ($pi == 1) ? 'featured' : '';
                $delay = 100 + ($pi * 150);
            ?>
            <div class="pricing-card <?php echo $featured; ?>" data-aos="zoom-in" data-aos-delay="<?php echo $delay; ?>">
                <?php if($featured): ?>
                <div class="pricing-badge">MAIS POPULAR</div>
                <?php endif; ?>
                <div class="pricing-plan-name"><?php echo htmlspecialchars($plano['titulo']); ?></div>
                <div class="pricing-price"><?php echo htmlspecialchars($plano['preco']); ?></div>
                <div class="pricing-period">/mês</div>
                <ul class="pricing-features">
                    <?php foreach($plano['features'] as $feat): ?>
                    <li><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($feat); ?></li>
                    <?php endforeach; ?>
                </ul>
                <a href="/login/painel/cadastro_conta.php?code_pag=<?php echo urlencode($plano['code_pag']); ?>" class="btn-plan">
                    Assinar Agora
                </a>
            </div>
            <?php $pi++; endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ── CONTATO ── -->
<section class="enam-section bg-dark" id="contact">
    <div class="section-inner">
        <div class="contact-box" data-aos="fade-up">
            <h2>Pronto para <span class="hl">transformar</span><br>seu atendimento?</h2>
            <p><?php echo htmlspecialchars($contact_description); ?></p>
            <a href="https://wa.me/<?php echo $telefone_whatsapp; ?>" target="_blank" class="btn-whatsapp">
                <i class="fab fa-whatsapp"></i> <?php echo htmlspecialchars($contact_button_text); ?>
            </a>
            <?php if(!empty($telefone)): ?>
            <p class="contact-phone">Número de contato: <strong><?php echo htmlspecialchars($telefone); ?></strong></p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ── FOOTER ── -->
<footer class="enam-footer">
    <div class="footer-inner">
        <img src="<?php echo $logo; ?>" alt="<?php echo $titulo; ?>">
        <p><?php echo $footer_copyright; ?></p>
    </div>
</footer>

<!-- ── VOLTAR AO TOPO ── -->
<a href="#main" class="back-to-top page-scroll" id="backToTop">
    <i class="fas fa-arrow-up"></i>
</a>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>

<script>
$(document).ready(function() {

    // Inicializar AOS
    AOS.init({ once: true, offset: 60, duration: 750, easing: 'ease-in-out' });

    // Navbar mobile toggle
    $('#navToggle').on('click', function() {
        $('#navLinks').toggleClass('open');
        var icon = $(this).find('i');
        icon.toggleClass('fa-bars fa-times');
    });

    // Fechar menu mobile ao clicar em link
    $('#navLinks a').on('click', function() {
        if($(window).width() < 900) {
            $('#navLinks').removeClass('open');
            $('#navToggle i').addClass('fa-bars').removeClass('fa-times');
        }
    });

    // Rolagem suave
    $('a.page-scroll, a[href^="#"]').on('click', function(e) {
        var target = $(this.getAttribute('href'));
        if (target.length) {
            e.preventDefault();
            var navH = $('#enamNav').outerHeight(true) || 70;
            var sectionId = target.attr('id');
            var offset = (sectionId === 'main') ? 0 : target.offset().top - navH - 20;
            $('html, body').animate({ scrollTop: Math.max(0, offset) }, 600);
        }
    });

    // Navbar scroll + back-to-top
    $(window).on('scroll', function() {
        var sp = $(this).scrollTop();
        if (sp > 50) { $('#enamNav').addClass('scrolled'); } 
        else { $('#enamNav').removeClass('scrolled'); }

        if (sp > 300) { $('#backToTop').addClass('active'); }
        else { $('#backToTop').removeClass('active'); }

        // Menu ativo
        var current = '';
        var navH = $('#enamNav').outerHeight(true) || 70;
        ['main','services','features','video-section','pricing','contact'].forEach(function(id) {
            var el = $('#' + id);
            if (el.length) {
                var top = (id === 'main') ? 0 : el.offset().top - navH - 40;
                if (sp >= top) current = id;
            }
        });
        if (current) {
            $('#navLinks a').removeClass('active');
            $('#navLinks a[href="#' + current + '"]').addClass('active');
        }
    });

    // Popup oferta especial
    var popupShown = false;
    setTimeout(function() {
        if (!popupShown) { $('#specialOfferPopup').addClass('show'); popupShown = true; startCountdown(); }
    }, 50000);

    if (window.innerWidth > 768) {
        $(document).on('mouseleave', function(e) {
            if (e.clientY < 5 && !popupShown) {
                $('#specialOfferPopup').addClass('show'); popupShown = true; startCountdown();
            }
        });
    }

    $('.special-offer-close').click(function() { $('#specialOfferPopup').removeClass('show'); });

    $('#specialOfferPopup').click(function(e) {
        if (e.target === this) $(this).removeClass('show');
    });

    $('#specialOfferForm').submit(function(e) {
        e.preventDefault();
        $.ajax({
            type: 'POST', url: 'salvar_contato.php', data: $(this).serialize(),
            success: function() {
                $('.special-offer-content').html(
                    '<div style="padding:2rem 1rem;text-align:center;">' +
                    '<i class="fas fa-check-circle" style="font-size:3rem;color:#25D366;margin-bottom:1rem;display:block;"></i>' +
                    '<h4 style="color:#FF5500;margin-bottom:0.75rem;">Oferta Garantida!</h4>' +
                    '<p style="color:rgba(255,255,255,0.6);font-size:0.9rem;">Enviaremos as instruções via WhatsApp em breve.</p>' +
                    '</div>'
                );
                setTimeout(function() { $('#specialOfferPopup').removeClass('show'); }, 5000);
            }
        });
    });

    function startCountdown() {
        var m = 5, s = 0;
        var t = setInterval(function() {
            if (s === 0) { if (m === 0) { clearInterval(t); return; } m--; s = 59; } else { s--; }
            var fm = m < 10 ? '0'+m : m;
            var fs = s < 10 ? '0'+s : s;
            $('#countdownTimer').text(fm + ':' + fs);
            if (m === 0 && s <= 30) $('#countdownTimer').css('color','#ff4444');
        }, 1000);
    }

    // Ajuste de viewport em mobile
    function setVH() { document.documentElement.style.setProperty('--vh', (window.innerHeight * 0.01)+'px'); }
    setVH();
    window.addEventListener('resize', function() { setTimeout(setVH, 100); });
    window.addEventListener('orientationchange', function() { setTimeout(setVH, 500); });
});
</script>
</body>
</html>
