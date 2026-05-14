<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title><?=$titulo;?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" href="<?=isset($favicon) ? $favicon : $icon;?>" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../files/bower_components/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
    <?php if (isset($css_extra)) echo $css_extra; ?>
</head>
<body class="fix-menu">
    <!-- Pre-loader -->
    <div class="theme-loader">
        <div class="ball-scale">
            <div class='contain'>
                <div class="ring"></div>
            </div>
        </div>
    </div>

    <!-- Elementos decorativos de fundo -->
    <div class="bg-hex bg-hex-1"></div>
    <div class="bg-hex bg-hex-2"></div>
    <div class="bg-hex bg-hex-3"></div>
    <div class="bg-line"></div>
