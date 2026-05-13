   
<?php
// Variáveis PHP para definir as cores de fundo
$cor_fundo_body = "#F3F3F3";  // Cor de fundo para o corpo da página
$cor_fundo_hero = "linear-gradient(to right, rgba(0, 0, 0, 0.9), rgba(0, 0, 0, 0.1)), url('../images/hero3.jpg')"; // Cor de fundo para a seção hero
$cor_fundo_features = "#F3F3F3";  // Cor de fundo para a seção de funcionalidades
$cor_fundo_pricing = "#F3F3F3";  // Cor de fundo para a seção de preços
$cor_fundo_footer = "#F3F3F3";  // Cor de fundo para o rodapé
?>


   <style>
        /* Cor de fundo do corpo da página */
        body {
            background-color: <?php echo $cor_fundo_body; ?>;
            font-family: "Open Sans", sans-serif;
        }

        /* Seção Hero (destaque principal) */
        .hero-section {
            background-image: <?php echo $cor_fundo_hero; ?>;
            background-position: 50% 50%;
            background-repeat: no-repeat;
            background-size: cover;
            height: 100%;
            width: 100%;
        }

        /* Seção de funcionalidades */
        .flex-features {
            background-color: <?php echo $cor_fundo_features; ?>;
            padding-top: 50px;
            padding-bottom: 50px;
        }

        /* Seção de preços */
        .pricing-section {
            background-color: <?php echo $cor_fundo_pricing; ?>;
            padding-top: 100px;
            padding-bottom: 100px;
        }

        /* Rodapé da página */
        .footer {
            background-color: <?php echo $cor_fundo_footer; ?>;
            padding-top: 50px;
            padding-bottom: 50px;
            text-align: center;
        }
    </style>