<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title><?=$titulo;?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" href="<?=isset($favicon) ? $favicon : $icon;?>" type="image/x-icon">
    <link href="../files/assets/vendor/fonts/montserrat/montserrat.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../files/bower_components/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../files/assets/vendor/intl-tel-input/css/intlTelInput.css">
    <link href="../files/assets/vendor/aos/aos.css" rel="stylesheet">
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
