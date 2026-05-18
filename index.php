<?php
include 'login/painel/conn.php';
include 'login/painel/estilo.php';
include 'login/painel/css_de_icones.php';
include 'login/painel/config_dados.php';
include 'login/painel/funcoes.php';
include 'login/painel/menu.php';

$icon = 'login/painel/'.$icon;
$logo = 'login/painel/'.$logo;

$sql_busca_config = "SELECT * FROM config";
$query_busca_config = mysqli_query($conn, $sql_busca_config);
$total_busca_config = mysqli_num_rows($query_busca_config);

while($rows_config = mysqli_fetch_array($query_busca_config)) {
    $chave            = $rows_config['chave'];
    $validade         = $rows_config['validade'];
    $link_pagamento   = $rows_config['link_pagamento'];
    $preco            = $rows_config['preco'];
    $telefone         = $rows_config['telefone'];
    $caminho_modelo   = $rows_config['caminho_modelo'];
    $hero_title       = $rows_config['hero_title'];
    $hero_subtitle    = $rows_config['hero_subtitle'];
    $services_title   = $rows_config['services_title'];
    $services_description = $rows_config['services_description'];
    $tipo_vendas      = $rows_config['tipo_vendas'];
    $texto_vendas     = $rows_config['texto_vendas'];
    $video_youtube    = $rows_config['video_youtube'];
    $tema             = (int)$rows_config['tema'];
    $telefone_whatsapp = str_replace(['(', ')', ' ', '-'], '', $telefone);
}

$default_values = [
    'card1_icon'        => 'assets/logos/icon1.png',
    'card1_title'       => 'Chatbot com IA',
    'card1_description' => 'Responde automaticamente perguntas frequentes, horários e confirmações sem intervenção humana.',
    'card2_icon'        => 'assets/logos/icon2.png',
    'card2_title'       => 'Agendamento Inteligente',
    'card2_description' => 'Clientes agendam pelo WhatsApp em segundos. Sistema verifica disponibilidade em tempo real.',
    'card3_icon'        => 'assets/logos/icon3.png',
    'card3_title'       => 'Disparos em Massa',
    'card3_description' => 'Envie lembretes, promoções e confirmações para toda a sua base de clientes com um clique.',
    'feature_image'       => 'assets/images/feature_1.png',
    'feature_title'       => 'Assistente Inteligente para Vendas e Atendimento',
    'feature_description' => 'Elimine o trabalho manual e os gargalos de atendimento. Nossa IA multimodal organiza demandas, responde clientes na hora e mantém sua operação comercial rodando com máxima eficiência.',
    'feature_items'       => ['Atendimento personalizado com linguagem natural.', 'Identificação de oportunidades de vendas.', 'Disponível 24 horas por dia, 7 dias por semana.'],
];

$query = "SELECT * FROM config";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $config = mysqli_fetch_assoc($result);
    function getConfigValue($config, $field, $default) {
        return (!empty($config[$field])) ? $config[$field] : $default;
    }
    $card1_title        = getConfigValue($config, 'card1_title',        $default_values['card1_title']);
    $card1_description  = getConfigValue($config, 'card1_description',  $default_values['card1_description']);
    $card2_title        = getConfigValue($config, 'card2_title',        $default_values['card2_title']);
    $card2_description  = getConfigValue($config, 'card2_description',  $default_values['card2_description']);
    $card3_title        = getConfigValue($config, 'card3_title',        $default_values['card3_title']);
    $card3_description  = getConfigValue($config, 'card3_description',  $default_values['card3_description']);
    $feature_title      = getConfigValue($config, 'feature_title',      $default_values['feature_title']);
    $feature_description= getConfigValue($config, 'feature_description',$default_values['feature_description']);
    $feature_items_json = $config['feature_items'] ?? '';
    $feature_items      = !empty($feature_items_json) ? (json_decode($feature_items_json, true) ?: $default_values['feature_items']) : $default_values['feature_items'];
    $telefone           = getConfigValue($config, 'telefone',           '');
    $hero_title         = getConfigValue($config, 'hero_title',         '');
    $hero_subtitle      = getConfigValue($config, 'hero_subtitle',      '');
    $services_title     = getConfigValue($config, 'services_title',     '');
    $services_description = getConfigValue($config, 'services_description', '');
    $texto_vendas       = getConfigValue($config, 'texto_vendas',       '');
    $video_youtube      = getConfigValue($config, 'video_youtube',      '');
} else {
    $card1_title = $default_values['card1_title']; $card1_description = $default_values['card1_description'];
    $card2_title = $default_values['card2_title']; $card2_description = $default_values['card2_description'];
    $card3_title = $default_values['card3_title']; $card3_description = $default_values['card3_description'];
    $feature_title = $default_values['feature_title']; $feature_description = $default_values['feature_description'];
    $feature_items = $default_values['feature_items'];
    $telefone = ''; $hero_title = ''; $hero_subtitle = ''; $services_title = ''; $services_description = ''; $texto_vendas = ''; $video_youtube = '';
}

$telefone_whatsapp = str_replace(['(', ')', ' ', '-'], '', $telefone);

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
            while ($feature = mysqli_fetch_assoc($result_features)) { $features[] = $feature['feature']; }
        }
        $planos[] = [
            'titulo'   => $plano['titulo'],
            'preco'    => 'R$ ' . number_format($plano['preco'], 2, ',', '.'),
            'features' => $features,
            'code_pag' => $plano['code_pag'],
        ];
    }
}

$footer_copyright = 'Copyright &copy; ' . date('Y') . ' ' . htmlspecialchars($titulo) . '. Todos os direitos reservados.';
$contact_description = 'Estamos disponíveis para responder suas dúvidas ou fornecer mais informações. Clique no botão abaixo para falar diretamente conosco via WhatsApp.';
$contact_button_text = 'Falar pelo WhatsApp';

$recursos = [
    ['icon' => 'fa-robot',         'title' => $card1_title, 'desc' => $card1_description],
    ['icon' => 'fa-calendar-check','title' => $card2_title, 'desc' => $card2_description],
    ['icon' => 'fa-paper-plane',   'title' => $card3_title, 'desc' => $card3_description],
    ['icon' => 'fa-ban',      'title' => 'Lista Negra',        'desc' => 'Controle quem pode interagir com o sistema e bloqueie contatos indesejados automaticamente.'],
    ['icon' => 'fa-history',  'title' => 'Histórico Completo', 'desc' => 'Acompanhe cada conversa, agendamento e status em um painel centralizado e intuitivo.'],
    ['icon' => 'fa-users',    'title' => 'Multi-Atendente',    'desc' => 'Vários profissionais, uma só central. Cada um gerencia sua própria agenda com total autonomia.'],
];

// Embed YouTube
$embed_url = '';
if (!empty($video_youtube)) {
    preg_match('/(?:v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $video_youtube, $m);
    if (!empty($m[1])) $embed_url = 'https://www.youtube.com/embed/' . $m[1] . '?rel=0&modestbranding=1';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($titulo); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Automatize agendamentos e atendimento com IA pelo WhatsApp.">
    <link rel="icon" href="<?php echo $icon; ?>" type="image/x-icon">
    <link rel="shortcut icon" href="<?php echo $icon; ?>" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        /* ── RESET & BASE ── */
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --navy:        #001228;
            --navy-mid:    #00172e;
            --navy-deep:   #001f3f;
            --orange:      #FF5500;
            --orange-dim:  rgba(255,85,0,0.12);
            --orange-glow: rgba(255,85,0,0.35);
            --white:       #ffffff;
            --w60:         rgba(255,255,255,0.60);
            --w50:         rgba(255,255,255,0.50);
            --w40:         rgba(255,255,255,0.40);
            --w08:         rgba(255,255,255,0.08);
            --w06:         rgba(255,255,255,0.06);
            --w04:         rgba(255,255,255,0.04);
            --border:      rgba(255,255,255,0.07);
            --ease:        cubic-bezier(0.165,0.84,0.44,1);
        }
        html { scroll-behavior: smooth; }
        body {
            font-family: 'Montserrat', sans-serif;
            background: var(--navy-deep);
            color: var(--white);
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }
        a  { color: inherit !important; text-decoration: none !important; }
        h1,h2,h3,h4,h5,h6 { color: var(--white) !important; }
        p  { color: var(--w60) !important; }
        img { max-width: 100%; }

        /* ── NAVBAR ── */
        .ln-nav {
            position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
            padding: 1.1rem 1.5rem;
            display: flex; align-items: center; justify-content: space-between;
            background: rgba(0,18,38,0.80);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border);
            transition: padding .3s var(--ease), background .3s var(--ease);
        }
        .ln-nav.scrolled { padding-top: .65rem; padding-bottom: .65rem; background: rgba(0,18,38,0.96); }
        .ln-nav-logo { display: flex; align-items: center; gap: .75rem; }
        .ln-nav-logo img { height: 38px; width: auto; }
        .ln-nav-logo .ln-logo-text {
            font-size: 1.1rem; font-weight: 800; letter-spacing: -.02em; color: var(--white) !important;
        }
        .ln-nav-logo .ln-logo-text span { color: var(--orange) !important; }
        .ln-nav-links {
            display: flex; align-items: center; gap: .25rem; list-style: none;
        }
        .ln-nav-links a {
            color: var(--w60) !important;
            font-size: .875rem; font-weight: 600;
            padding: .5rem 1rem; border-radius: 8px;
            transition: color .2s, background .2s;
        }
        .ln-nav-links a:hover, .ln-nav-links a.active {
            color: var(--white) !important; background: var(--w08);
        }
        .ln-nav-cta {
            padding: .6rem 1.4rem; border-radius: 10px;
            background: var(--orange) !important; color: var(--white) !important;
            font-size: .875rem; font-weight: 700;
            box-shadow: 0 4px 14px var(--orange-glow);
            transition: filter .2s, transform .2s;
        }
        .ln-nav-cta:hover { filter: brightness(1.1); transform: translateY(-1px); }
        .ln-toggle {
            display: none; background: none; border: none;
            color: var(--white); font-size: 1.4rem; cursor: pointer; padding: .4rem;
        }
        @media (max-width:900px) {
            .ln-toggle { display: block; }
            .ln-nav-links {
                display: none; flex-direction: column; position: absolute;
                top: 100%; left: 0; right: 0;
                background: rgba(0,18,38,0.97);
                padding: 1rem 1.5rem 1.5rem; gap: .1rem;
                border-bottom: 1px solid var(--border);
            }
            .ln-nav-links.open { display: flex; }
            .ln-nav-links a { width: 100%; }
        }

        /* ── HERO ── */
        .ln-hero {
            min-height: 100vh; display: flex; align-items: center;
            position: relative; overflow: hidden; padding-top: 80px;
        }
        .ln-hero-bg {
            position: absolute; inset: 0;
            background: linear-gradient(135deg, #001228 0%, #002855 60%, #001228 100%);
        }
        .ln-blob-1 {
            position: absolute; top: 20%; right: -80px;
            width: 600px; height: 600px; border-radius: 50%;
            background: radial-gradient(circle, #FF5500 0%, transparent 70%);
            opacity: .10; pointer-events: none;
        }
        .ln-blob-2 {
            position: absolute; bottom: 0; left: 20%;
            width: 400px; height: 400px; border-radius: 50%;
            background: radial-gradient(circle, #0066cc 0%, transparent 70%);
            opacity: .08; pointer-events: none;
        }
        .ln-hero-inner {
            position: relative; z-index: 2;
            max-width: 1180px; margin: 0 auto;
            padding: 5rem 1.5rem;
        }
        .ln-badge {
            display: inline-flex; align-items: center; gap: .5rem;
            padding: .4rem 1rem; border-radius: 50px; margin-bottom: 2rem;
            border: 1px solid rgba(255,85,0,.35); background: rgba(255,85,0,.08);
            color: #FF8855 !important; font-size: .78rem; font-weight: 700; letter-spacing: .08em;
        }
        .ln-badge-dot {
            width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0;
            background: var(--orange); box-shadow: 0 0 8px var(--orange);
        }
        .ln-hero-title {
            font-size: clamp(2.6rem, 7vw, 5.2rem);
            font-weight: 900; line-height: 1.03; letter-spacing: -.03em;
            margin-bottom: 1.5rem; color: var(--white) !important;
        }
        .ln-hero-title .hl { color: var(--orange) !important; }
        .ln-hero-sub {
            font-size: clamp(1rem, 2.5vw, 1.2rem); font-weight: 500;
            color: var(--w60) !important; max-width: 540px;
            line-height: 1.7; margin-bottom: 2.5rem;
        }
        .ln-hero-btns { display: flex; flex-wrap: wrap; gap: 1rem; margin-bottom: 3.5rem; }
        .ln-btn-primary {
            display: inline-flex; align-items: center; gap: .5rem;
            padding: 1rem 2.2rem; border-radius: 12px; border: none; cursor: pointer;
            background: var(--orange); color: var(--white) !important;
            font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 1rem;
            box-shadow: 0 8px 24px var(--orange-glow);
            transition: filter .25s, transform .25s;
        }
        .ln-btn-primary:hover { filter: brightness(1.1); transform: translateY(-2px); }
        .ln-btn-ghost {
            display: inline-flex; align-items: center; gap: .5rem;
            padding: 1rem 2.2rem; border-radius: 12px; cursor: pointer;
            border: 1.5px solid rgba(255,255,255,.2); background: rgba(255,255,255,.04);
            color: rgba(255,255,255,.75) !important;
            font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 1rem;
            transition: color .25s, background .25s;
        }
        .ln-btn-ghost:hover { color: var(--white) !important; background: rgba(255,255,255,.08); }
        .ln-hero-stats {
            display: flex; gap: 3rem; flex-wrap: wrap;
            padding-top: 2.5rem; border-top: 1px solid rgba(255,255,255,.08);
        }
        .ln-stat-num { font-size: 2rem; font-weight: 900; color: var(--orange) !important; }
        .ln-stat-label { font-size: .8rem; font-weight: 600; color: var(--w50) !important; margin-top: .2rem; }

        /* ── SECTION BASE ── */
        .ln-section { padding: 6rem 1.5rem; }
        .ln-inner { max-width: 1180px; margin: 0 auto; }
        .ln-section-label {
            font-size: .75rem; font-weight: 700; letter-spacing: .12em;
            color: var(--orange) !important; text-align: center; margin-bottom: .75rem;
        }
        .ln-section-title {
            font-size: clamp(1.8rem, 4vw, 3rem); font-weight: 900;
            letter-spacing: -.02em; text-align: center; line-height: 1.15;
            margin-bottom: 3.5rem; color: var(--white) !important;
        }
        .ln-section-title .hl { color: var(--orange) !important; }

        /* ── RECURSOS ── */
        .ln-recursos-bg { background: var(--navy-mid); }
        .ln-cards-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 1.5rem; }
        @media (max-width:900px) { .ln-cards-grid { grid-template-columns: repeat(2,1fr); } }
        @media (max-width:600px) { .ln-cards-grid { grid-template-columns: 1fr; } }
        .ln-card {
            padding: 1.8rem 1.6rem; border-radius: 16px;
            background: var(--w04); border: 1px solid var(--w06);
            transition: transform .3s var(--ease), border-color .3s;
        }
        .ln-card:hover { transform: translateY(-4px); border-color: rgba(255,85,0,.25); }
        .ln-card-icon {
            width: 48px; height: 48px; border-radius: 12px; margin-bottom: 1.25rem;
            display: flex; align-items: center; justify-content: center;
            background: var(--orange-dim); color: var(--orange) !important; font-size: 1.2rem;
        }
        .ln-card h3 { font-size: 1.05rem; font-weight: 700; margin-bottom: .6rem; color: var(--white) !important; }
        .ln-card p  { font-size: .875rem; line-height: 1.65; color: var(--w50) !important; }

        /* ── PRICING ── */
        .ln-pricing-bg { background: var(--navy); }
        .ln-plans-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 1.5rem; align-items: center; }
        @media (max-width:900px) { .ln-plans-grid { grid-template-columns: 1fr; max-width: 420px; margin: 0 auto; } }
        .ln-plan {
            padding: 2.2rem 1.8rem; border-radius: 20px;
            background: var(--w04); border: 1px solid var(--w06);
            transition: transform .3s;
        }
        .ln-plan:hover { transform: translateY(-4px); }
        .ln-plan.featured {
            background: linear-gradient(135deg, #FF5500, #cc3300);
            border: none; transform: scale(1.05);
            box-shadow: 0 20px 60px rgba(255,85,0,.3);
            position: relative;
        }
        @media (max-width:900px) { .ln-plan.featured { transform: none; } }
        .ln-plan-badge {
            position: absolute; top: -14px; left: 50%; transform: translateX(-50%);
            padding: .25rem 1.1rem; border-radius: 50px;
            background: var(--navy); color: var(--white) !important;
            font-size: .7rem; font-weight: 700; letter-spacing: .08em; white-space: nowrap;
        }
        .ln-plan-name { font-size: .85rem; font-weight: 700; color: var(--w60) !important; margin-bottom: .25rem; }
        .ln-plan.featured .ln-plan-name { color: rgba(255,255,255,.8) !important; }
        .ln-plan-price { font-size: 2.6rem; font-weight: 900; color: var(--white) !important; line-height: 1; margin-bottom: .15rem; }
        .ln-plan-period { font-size: .85rem; color: var(--w50) !important; margin-bottom: 1.75rem; }
        .ln-plan.featured .ln-plan-period { color: rgba(255,255,255,.65) !important; }
        .ln-plan-features { list-style: none; margin-bottom: 1.75rem; display: flex; flex-direction: column; gap: .75rem; }
        .ln-plan-features li { display: flex; align-items: center; gap: .6rem; font-size: .875rem; color: var(--w60) !important; }
        .ln-plan.featured .ln-plan-features li { color: rgba(255,255,255,.9) !important; }
        .ln-plan-features li i { color: var(--orange) !important; font-size: .9rem; flex-shrink: 0; }
        .ln-plan.featured .ln-plan-features li i { color: rgba(255,255,255,.85) !important; }
        .ln-plan-btn {
            display: block; width: 100%; padding: .85rem 1rem; border-radius: 12px; border: none; cursor: pointer;
            font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: .9rem; text-align: center;
            background: var(--orange); color: var(--white) !important;
            transition: filter .25s, transform .25s;
        }
        .ln-plan.featured .ln-plan-btn { background: rgba(255,255,255,.18); color: var(--white) !important; }
        .ln-plan-btn:hover { filter: brightness(1.1); transform: translateY(-1px); }

        /* ── VIDEO ── */
        .ln-video-bg { background: var(--navy-mid); }
        .ln-video-wrap { position: relative; max-width: 800px; margin: 0 auto; border-radius: 16px; overflow: hidden; box-shadow: 0 24px 60px rgba(0,0,0,.5); }
        .ln-video-wrap iframe { width: 100%; aspect-ratio: 16/9; border: none; display: block; }

        /* ── CTA FOOTER ── */
        .ln-cta-bg { background: var(--navy-deep); }
        .ln-cta-box { max-width: 720px; margin: 0 auto; text-align: center; }
        .ln-cta-box h2 { font-size: clamp(1.8rem,4vw,3rem); font-weight: 900; line-height: 1.15; margin-bottom: 1rem; color: var(--white) !important; }
        .ln-cta-box h2 .hl { color: var(--orange) !important; }
        .ln-cta-box p { font-size: 1.05rem; color: var(--w50) !important; margin-bottom: 2rem; }
        .ln-cta-phone { margin-top: 1.25rem; font-size: .9rem; color: var(--w40) !important; }
        .ln-cta-phone strong { color: var(--w60) !important; }
        .ln-btn-whatsapp {
            display: inline-flex; align-items: center; gap: .6rem;
            padding: 1rem 2.4rem; border-radius: 12px; border: none; cursor: pointer;
            background: #25D366; color: var(--white) !important;
            font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 1rem;
            box-shadow: 0 8px 24px rgba(37,211,102,.3);
            transition: filter .25s, transform .25s;
        }
        .ln-btn-whatsapp:hover { filter: brightness(1.08); transform: translateY(-2px); }

        /* ── FOOTER ── */
        .ln-footer {
            padding: 2rem 1.5rem;
            border-top: 1px solid var(--border);
            background: var(--navy);
            text-align: center;
        }
        .ln-footer img { height: 34px; width: auto; margin-bottom: .75rem; }
        .ln-footer p { font-size: .8rem; color: var(--w40) !important; }

        /* ── BACK TO TOP ── */
        .ln-top {
            position: fixed; bottom: 1.75rem; right: 1.75rem; z-index: 999;
            width: 44px; height: 44px; border-radius: 50%;
            background: var(--orange); color: var(--white) !important;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem; box-shadow: 0 4px 16px var(--orange-glow);
            opacity: 0; pointer-events: none;
            transition: opacity .3s, transform .3s;
        }
        .ln-top.active { opacity: 1; pointer-events: auto; }
        .ln-top:hover { transform: translateY(-2px); }
    </style>
</head>
<body>

<!-- ── NAVBAR ── -->
<nav class="ln-nav" id="lnNav">
    <div class="ln-nav-logo">
        <?php if(!empty($logo)): ?>
        <img src="<?php echo htmlspecialchars($logo); ?>" alt="<?php echo htmlspecialchars($titulo); ?>">
        <?php else: ?>
        <span class="ln-logo-text"><?php echo htmlspecialchars($titulo); ?></span>
        <?php endif; ?>
    </div>

    <button class="ln-toggle" id="lnToggle" aria-label="Menu">
        <i class="fas fa-bars"></i>
    </button>

    <ul class="ln-nav-links" id="lnLinks">
        <li><a href="#main"     class="page-scroll">Início</a></li>
        <li><a href="#recursos" class="page-scroll">Recursos</a></li>
        <li><a href="#pricing"  class="page-scroll">Planos</a></li>
        <li><a href="#contact"  class="page-scroll">Contato</a></li>
        <li><a href="/login/painel/login.php" class="ln-nav-cta">Acessar</a></li>
    </ul>
</nav>

<!-- ── HERO ── -->
<section class="ln-hero" id="main">
    <div class="ln-hero-bg"></div>
    <div class="ln-blob-1"></div>
    <div class="ln-blob-2"></div>

    <div class="ln-hero-inner">
        <div class="ln-badge" data-aos="fade-down">
            <span class="ln-badge-dot"></span>
            ATENDIMENTO INTELIGENTE · WHATSAPP
        </div>

        <h1 class="ln-hero-title" data-aos="fade-up" data-aos-delay="80">
            <?php if(!empty($hero_title)): ?>
                <?php echo htmlspecialchars($hero_title); ?>
            <?php else: ?>
                <span class="hl">Agende</span> com Inteligência.<br>
                <span class="hl">Atenda</span> com <span class="hl">Excelência.</span>
            <?php endif; ?>
        </h1>

        <p class="ln-hero-sub" data-aos="fade-up" data-aos-delay="160">
            <?php echo !empty($hero_subtitle) ? htmlspecialchars($hero_subtitle) : 'Automatize agendamentos, tire dúvidas e fidelize clientes — tudo pelo WhatsApp, com IA avançada trabalhando 24h por dia.'; ?>
        </p>

        <div class="ln-hero-btns" data-aos="fade-up" data-aos-delay="240">
            <a href="/login/painel/cadastro_conta.php" class="ln-btn-primary">
                Começar Grátis <i class="fas fa-arrow-right"></i>
            </a>
            <a href="/login/painel/login.php" class="ln-btn-ghost">
                Fazer Login <i class="fas fa-chevron-right"></i>
            </a>
        </div>

        <div class="ln-hero-stats" data-aos="fade-up" data-aos-delay="320">
            <div>
                <div class="ln-stat-num">+5.000</div>
                <div class="ln-stat-label">Clientes Atendidos</div>
            </div>
            <div>
                <div class="ln-stat-num">98%</div>
                <div class="ln-stat-label">Taxa de Satisfação</div>
            </div>
            <div>
                <div class="ln-stat-num">24/7</div>
                <div class="ln-stat-label">Disponibilidade</div>
            </div>
        </div>
    </div>
</section>

<!-- ── RECURSOS ── -->
<section class="ln-section ln-recursos-bg" id="recursos">
    <div class="ln-inner">
        <p class="ln-section-label" data-aos="fade-up">RECURSOS</p>
        <h2 class="ln-section-title" data-aos="fade-up" data-aos-delay="80">
            <?php echo !empty($services_title) ? htmlspecialchars($services_title) : 'Tudo que você precisa'; ?><br>
            <span class="hl">em um só lugar</span>
        </h2>

        <div class="ln-cards-grid">
            <?php $ri = 0; foreach($recursos as $r): $ri++; ?>
            <div class="ln-card" data-aos="fade-up" data-aos-delay="<?php echo 80 + ($ri % 3) * 80; ?>">
                <div class="ln-card-icon">
                    <i class="fas <?php echo htmlspecialchars($r['icon']); ?>"></i>
                </div>
                <h3><?php echo htmlspecialchars($r['title']); ?></h3>
                <p><?php echo htmlspecialchars($r['desc']); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ── VÍDEO (condicional) ── -->
<?php if(!empty($embed_url)): ?>
<section class="ln-section ln-video-bg" id="video-section">
    <div class="ln-inner">
        <p class="ln-section-label" data-aos="fade-up">DEMONSTRAÇÃO</p>
        <h2 class="ln-section-title" data-aos="fade-up" data-aos-delay="80">
            <?php echo !empty($texto_vendas) ? htmlspecialchars($texto_vendas) : 'Veja como funciona <span class="hl">na prática</span>'; ?>
        </h2>
        <div class="ln-video-wrap" data-aos="zoom-in" data-aos-delay="160">
            <iframe src="<?php echo htmlspecialchars($embed_url); ?>"
                title="Demonstração" frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen></iframe>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ── PLANOS ── -->
<?php if(!empty($planos)): ?>
<section class="ln-section ln-pricing-bg" id="pricing">
    <div class="ln-inner">
        <p class="ln-section-label" data-aos="fade-up">PLANOS</p>
        <h2 class="ln-section-title" data-aos="fade-up" data-aos-delay="80">
            Escolha o plano<br><span class="hl">ideal para você</span>
        </h2>
        <div class="ln-plans-grid">
            <?php $pi = 0; foreach($planos as $plano): $feat = ($pi == 1) ? 'featured' : ''; $pi++; ?>
            <div class="ln-plan <?php echo $feat; ?>" data-aos="zoom-in" data-aos-delay="<?php echo 80 + ($pi-1)*120; ?>">
                <?php if($feat): ?><div class="ln-plan-badge">MAIS POPULAR</div><?php endif; ?>
                <div class="ln-plan-name"><?php echo htmlspecialchars($plano['titulo']); ?></div>
                <div class="ln-plan-price"><?php echo htmlspecialchars($plano['preco']); ?></div>
                <div class="ln-plan-period">/mês</div>
                <ul class="ln-plan-features">
                    <?php foreach($plano['features'] as $feat_item): ?>
                    <li><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($feat_item); ?></li>
                    <?php endforeach; ?>
                </ul>
                <a href="/login/painel/cadastro_conta.php?code_pag=<?php echo urlencode($plano['code_pag']); ?>" class="ln-plan-btn">
                    Assinar Agora
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ── CTA CONTATO ── -->
<section class="ln-section ln-cta-bg" id="contact">
    <div class="ln-inner">
        <div class="ln-cta-box" data-aos="fade-up">
            <h2>Pronto para <span class="hl">transformar</span><br>seu atendimento?</h2>
            <p><?php echo htmlspecialchars($contact_description); ?></p>
            <a href="https://wa.me/<?php echo htmlspecialchars($telefone_whatsapp); ?>" target="_blank" class="ln-btn-whatsapp">
                <i class="fab fa-whatsapp"></i>
                <?php echo htmlspecialchars($contact_button_text); ?>
            </a>
            <?php if(!empty($telefone)): ?>
            <p class="ln-cta-phone">Número de contato: <strong><?php echo htmlspecialchars($telefone); ?></strong></p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ── FOOTER ── -->
<footer class="ln-footer">
    <?php if(!empty($logo)): ?>
    <img src="<?php echo htmlspecialchars($logo); ?>" alt="<?php echo htmlspecialchars($titulo); ?>">
    <?php endif; ?>
    <p><?php echo $footer_copyright; ?></p>
</footer>

<!-- ── BACK TO TOP ── -->
<a href="#main" class="ln-top page-scroll" id="lnTop"><i class="fas fa-arrow-up"></i></a>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>
$(function() {
    AOS.init({ once: true, offset: 60, duration: 700, easing: 'ease-in-out' });

    // Mobile toggle
    $('#lnToggle').on('click', function() {
        $('#lnLinks').toggleClass('open');
        $(this).find('i').toggleClass('fa-bars fa-times');
    });
    $('#lnLinks a').on('click', function() {
        if ($(window).width() < 900) {
            $('#lnLinks').removeClass('open');
            $('#lnToggle i').addClass('fa-bars').removeClass('fa-times');
        }
    });

    // Scroll suave
    $('a.page-scroll, a[href^="#"]').on('click', function(e) {
        var target = $(this.attr('href'));
        if (target.length) {
            e.preventDefault();
            var off = (this.getAttribute('href') === '#main') ? 0 : target.offset().top - 75;
            $('html,body').animate({ scrollTop: Math.max(0, off) }, 600);
        }
    });

    // Navbar + back-to-top
    $(window).on('scroll', function() {
        var sp = $(this).scrollTop();
        $('#lnNav').toggleClass('scrolled', sp > 50);
        $('#lnTop').toggleClass('active', sp > 300);

        // Link ativo
        var current = 'main';
        ['recursos','video-section','pricing','contact'].forEach(function(id) {
            var el = $('#' + id);
            if (el.length && sp >= el.offset().top - 100) current = id;
        });
        $('#lnLinks a').removeClass('active');
        $('#lnLinks a[href="#' + current + '"]').addClass('active');
    });
});
</script>
</body>
</html>
