<?php
include 'conn.php';
include 'estilo.php';
include 'funcoes.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — <?php echo $titulo; ?></title>
    <link rel="icon" href="<?php echo $favicon; ?>" type="image/x-icon">
    <link rel="stylesheet" href="../files/assets/vendor/fonts/montserrat/montserrat.css">
    <link rel="stylesheet" href="../files/assets/vendor/aos/aos.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Montserrat', sans-serif;
            background: #000d1a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: linear-gradient(135deg, #001f3f 0%, #000d1a 50%, #001229 100%);
            z-index: 0;
        }

        .bg-hex { position: fixed; z-index: 0; opacity: 0.06; }
        .bg-hex-1 {
            width: 500px; height: 500px; top: -200px; left: -200px;
            background: #FF5500;
            clip-path: polygon(50% 0%, 93% 25%, 93% 75%, 50% 100%, 7% 75%, 7% 25%);
        }
        .bg-hex-2 {
            width: 350px; height: 350px; bottom: -120px; right: -100px;
            background: #FF5500;
            clip-path: polygon(50% 0%, 93% 25%, 93% 75%, 50% 100%, 7% 75%, 7% 25%);
        }
        .bg-line {
            position: fixed; z-index: 0; width: 100%; height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,85,0,0.15), transparent);
            top: 50%;
        }

        .theme-loader {
            position: fixed; inset: 0; background: #000d1a;
            z-index: 9999; display: flex; align-items: center; justify-content: center;
        }
        .theme-loader .ring {
            width: 50px; height: 50px; border-radius: 50%;
            border: 3px solid #FF5500; border-top-color: transparent;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        .login-wrapper {
            position: relative; z-index: 10;
            width: 100%; max-width: 440px; padding: 20px;
        }
        .login-logo { text-align: center; margin-bottom: 32px; }
        .login-logo img {
            height: 48px; width: auto;
            filter: drop-shadow(0 0 20px rgba(255,85,0,0.4));
        }

        .auth-card {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 20px; padding: 40px;
            backdrop-filter: blur(20px);
            box-shadow: 0 25px 60px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,255,255,0.05);
        }
        .auth-card::before {
            content: ''; display: block; width: 60px; height: 3px;
            background: #FF5500; border-radius: 2px; margin: 0 auto 28px;
        }
        .auth-card h3 {
            font-size: 22px; font-weight: 700; color: #ffffff;
            text-align: center; margin-bottom: 6px; letter-spacing: 0.5px;
        }
        .auth-card h3 span { color: #FF5500; }
        .auth-card .subtitle {
            text-align: center; font-size: 12px;
            color: rgba(255,255,255,0.35); margin-bottom: 32px;
            text-transform: uppercase; letter-spacing: 1.5px; font-weight: 600;
        }

        .form-label {
            display: block; font-size: 12px; font-weight: 600;
            color: rgba(255,255,255,0.45); text-transform: uppercase;
            letter-spacing: 1px; margin-bottom: 8px;
        }
        .form-group { margin-bottom: 22px; }

        .form-control {
            width: 100%; height: 50px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 10px; color: #ffffff;
            font-family: 'Montserrat', sans-serif; font-size: 14px;
            padding: 0 16px; transition: all 0.3s ease; outline: none;
        }
        .form-control::placeholder { color: rgba(255,255,255,0.25); }
        .form-control:focus {
            border-color: #FF5500; background: rgba(255,85,0,0.06);
            box-shadow: 0 0 0 3px rgba(255,85,0,0.12);
        }

        .admin-badge {
            display: inline-flex; align-items: center; gap: 6px;
            background: rgba(255,85,0,0.12); border: 1px solid rgba(255,85,0,0.3);
            border-radius: 20px; padding: 4px 12px;
            font-size: 11px; font-weight: 700; color: #FF5500;
            text-transform: uppercase; letter-spacing: 1.5px;
            margin: 0 auto 28px; display: flex; width: fit-content;
        }
        .admin-badge::before {
            content: ''; width: 6px; height: 6px;
            background: #FF5500; border-radius: 50%;
            box-shadow: 0 0 6px #FF5500;
        }

        .btn-enam {
            display: block; width: 100%; height: 52px;
            background: linear-gradient(135deg, #FF5500, #e64a00);
            border: none; border-radius: 10px; color: #ffffff;
            font-family: 'Montserrat', sans-serif; font-size: 15px;
            font-weight: 700; letter-spacing: 1px; text-transform: uppercase;
            cursor: pointer; transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(255,85,0,0.35);
        }
        .btn-enam:hover {
            background: linear-gradient(135deg, #ff6620, #FF5500);
            transform: translateY(-2px); box-shadow: 0 12px 35px rgba(255,85,0,0.5);
        }
        .btn-enam:active { transform: translateY(0); }

        .divider {
            display: flex; align-items: center; gap: 12px; margin: 24px 0 20px;
        }
        .divider::before, .divider::after {
            content: ''; flex: 1; height: 1px; background: rgba(255,255,255,0.08);
        }
        .divider span {
            font-size: 11px; color: rgba(255,255,255,0.25);
            font-weight: 500; white-space: nowrap;
        }
        .back-link {
            display: block; text-align: center; font-size: 13px;
            color: rgba(255,255,255,0.4); text-decoration: none; transition: color 0.2s;
        }
        .back-link span { font-weight: 700; color: #FF5500; }
        .back-link:hover { color: rgba(255,255,255,0.7); text-decoration: none; }

        .error-msg {
            background: rgba(220,53,69,0.15); border: 1px solid rgba(220,53,69,0.4);
            border-radius: 10px; padding: 12px 16px; margin-bottom: 20px;
            color: #ff6b7a; font-size: 13px; font-weight: 500; text-align: center;
        }

        @media (max-width: 480px) {
            .auth-card { padding: 28px 22px; }
            .login-logo img { height: 40px; }
        }
    </style>
</head>
<body>
    <div class="theme-loader" id="loader">
        <div class="ring"></div>
    </div>

    <div class="bg-hex bg-hex-1"></div>
    <div class="bg-hex bg-hex-2"></div>
    <div class="bg-line"></div>

    <div class="login-wrapper" data-aos="fade-up" data-aos-duration="800">
        <div class="login-logo">
            <img src="<?php echo $logo; ?>" alt="<?php echo $titulo; ?>">
        </div>

        <form action="validar_adm.php" method="post" id="admin-form">
            <input type="hidden" name="titulo" value="LOGIN">

            <div class="auth-card">
                <div class="admin-badge">Acesso Administrativo</div>

                <h3>Bem-vindo, <span>Admin</span></h3>
                <p class="subtitle">Painel de controle</p>

                <?php if(isset($_GET['erro']) && $_GET['erro'] == 'login'): ?>
                <div class="error-msg">Usuário ou senha incorretos. Tente novamente.</div>
                <?php endif; ?>

                <?php if(isset($_GET['expirado']) && $_GET['expirado'] == '1'): ?>
                <div class="error-msg">Sua sessão expirou por inatividade. Faça login novamente.</div>
                <?php endif; ?>

                <div class="form-group">
                    <label class="form-label">Usuário</label>
                    <input type="text" name="telefone" class="form-control"
                           required autocomplete="username"
                           placeholder="Digite seu usuário ou telefone">
                </div>

                <div class="form-group">
                    <label class="form-label">Senha</label>
                    <input type="password" name="password" class="form-control"
                           required autocomplete="current-password"
                           placeholder="••••••••">
                </div>

                <button type="submit" class="btn-enam">Entrar no Painel</button>

                <div class="divider"><span>ou</span></div>

                <a href="login.php" class="back-link">Voltar para o <span>login de usuários</span></a>
            </div>
        </form>
    </div>

    <script src="../files/assets/vendor/aos/aos.js"></script>
    <script nonce="<?= htmlspecialchars($GLOBALS['csp_nonce'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        AOS.init({ once: true, duration: 700 });
        window.addEventListener('load', function() {
            document.getElementById('loader').style.display = 'none';
        });
    </script>
</body>
</html>
