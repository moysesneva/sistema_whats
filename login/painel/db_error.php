<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Serviço Indisponível — MoysesNet</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #001f3f;
            color: #fff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .logo-wrap {
            margin-bottom: 28px;
            text-align: center;
        }

        .logo-wrap img {
            max-height: 56px;
            max-width: 220px;
            object-fit: contain;
        }

        .logo-wrap .logo-fallback {
            font-size: 1.4rem;
            font-weight: 800;
            color: #FF5500;
            letter-spacing: 1px;
            display: none;
        }

        .card {
            background: #fff;
            color: #001f3f;
            border-radius: 12px;
            max-width: 520px;
            width: 100%;
            overflow: hidden;
            box-shadow: 0 8px 40px rgba(0,0,0,0.40);
        }

        .card-header {
            background: #FF5500;
            padding: 24px 32px;
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .card-header .icon {
            flex-shrink: 0;
            width: 42px;
            height: 42px;
            background: rgba(255,255,255,0.20);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card-header h1 {
            font-size: 1.15rem;
            font-weight: 700;
            color: #fff;
            line-height: 1.3;
        }

        .card-header p {
            font-size: 0.83rem;
            color: rgba(255,255,255,0.88);
            margin-top: 3px;
        }

        .card-body {
            padding: 28px 32px 24px;
        }

        .card-body > p {
            font-size: 0.95rem;
            line-height: 1.65;
            color: #333;
        }

        .steps {
            background: #f4f7fb;
            border-left: 4px solid #FF5500;
            border-radius: 0 8px 8px 0;
            padding: 14px 18px;
            margin-top: 20px;
        }

        .steps p {
            font-size: 0.88rem;
            font-weight: 700;
            color: #001f3f;
            margin-bottom: 10px;
        }

        .steps ul {
            list-style: none;
        }

        .steps ul li {
            font-size: 0.87rem;
            color: #444;
            padding: 3px 0;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }

        .steps ul li::before {
            content: '→';
            color: #FF5500;
            font-weight: 700;
            flex-shrink: 0;
        }

        .card-footer {
            border-top: 1px solid #e8edf3;
            padding: 18px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .brand {
            font-size: 0.78rem;
            color: #999;
        }

        .brand strong {
            color: #001f3f;
        }

        .btn-retry {
            background: #FF5500;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 10px 22px;
            font-size: 0.88rem;
            font-weight: 700;
            cursor: pointer;
            display: inline-block;
            transition: background 0.2s;
        }

        .btn-retry:hover {
            background: #e04a00;
        }
    </style>
</head>
<body>
    <div class="logo-wrap">
        <img
            id="db-error-logo"
            src="/login/files/assets/images/logo.png"
            alt="MoysesNet"
        >
        <span class="logo-fallback">MoysesNet</span>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="icon" aria-hidden="true">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 4v9M12 17.5v.5" stroke="#fff" stroke-width="2.2" stroke-linecap="round"/>
                    <circle cx="12" cy="12" r="10" stroke="#fff" stroke-width="2"/>
                </svg>
            </div>
            <div>
                <h1>Serviço Temporariamente Indisponível</h1>
                <p>Estamos trabalhando para restabelecer a conexão</p>
            </div>
        </div>
        <div class="card-body">
            <p>
                Não foi possível estabelecer a conexão com o banco de dados neste momento.
                O sistema estará de volta em breve.
            </p>
            <div class="steps">
                <p>O que você pode fazer agora:</p>
                <ul>
                    <li>Aguarde alguns instantes e tente recarregar a página</li>
                    <li>Se o problema persistir, entre em contato com o suporte</li>
                </ul>
            </div>
        </div>
        <div class="card-footer">
            <span class="brand"><strong>MoysesNet</strong> — Sistema de Agendamento</span>
            <button type="button" id="db-error-retry">Tentar novamente</button>
        </div>
    </div>
<script nonce="<?= htmlspecialchars($GLOBALS['csp_nonce'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
(function () {
    var img = document.getElementById('db-error-logo');
    if (img) {
        img.addEventListener('error', function () {
            img.style.display = 'none';
            var fallback = img.nextElementSibling;
            if (fallback) fallback.style.display = 'block';
        });
    }
    var btn = document.getElementById('db-error-retry');
    if (btn) {
        btn.addEventListener('click', function () { window.location.reload(); });
    }
}());
</script>
</body>
</html>
