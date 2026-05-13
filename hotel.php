<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de Reserva de Hotel">
    <meta name="keywords" content="reserva, hotel, sistema">
    <title>Sistema de Reserva de Hotel</title>
    <!-- Favicon -->
    <link rel="icon" href="assets/logos/favicon.ico" type="image/png" sizes="16x16">
    <!-- Bootstrap -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="#">Hotel Exemplo</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link" href="#about">Sobre</a></li>
                    <li class="nav-item"><a class="nav-link" href="#rooms">Quartos</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Contato</a></li>
                    <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <main>
        <!-- Seção de Reserva -->
        <section id="reserva" class="py-5">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <h2 class="text-center">Faça sua Reserva</h2>
                        <p class="text-center">Escolha as datas, o quarto e faça sua reserva facilmente.</p>
                        <form action="confirmar_reserva.php" method="POST">
                            <div class="form-group">
                                <label for="nome">Nome</label>
                                <input type="text" class="form-control" id="nome" name="nome" required>
                            </div>
                            <div class="form-group">
                                <label for="email">E-mail</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="telefone">Telefone</label>
                                <input type="text" class="form-control" id="telefone" name="telefone" required>
                            </div>
                            <div class="form-group">
                                <label for="checkin">Data de Check-in</label>
                                <input type="date" class="form-control" id="checkin" name="checkin" required>
                            </div>
                            <div class="form-group">
                                <label for="checkout">Data de Check-out</label>
                                <input type="date" class="form-control" id="checkout" name="checkout" required>
                            </div>
                            <div class="form-group">
                                <label for="quarto">Escolha o tipo de quarto</label>
                                <select class="form-control" id="quarto" name="quarto" required>
                                    <option value="standard">Quarto Standard</option>
                                    <option value="deluxe">Quarto Deluxe</option>
                                    <option value="suite">Suíte Master</option>
                                </select>
                            </div>

                            <h3>Quartos Disponíveis</h3>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <h5>Quarto 101 - Standard</h5>
                                            <button type="button" class="btn btn-primary select-room" data-room="101">Selecionar</button>
                                            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#popupDetalhes" onclick="abrirPopup('Quarto 101 - Standard', 'https://barahotel.com.br/wp-content/uploads/2024/02/DSC_9180-HDR.jpg', 'Este é um quarto aconchegante com cama de casal, ar condicionado, TV e internet Wi-Fi gratuita.')">Ver Detalhes</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <h5>Quarto 202 - Deluxe</h5>
                                            <button type="button" class="btn btn-primary select-room" data-room="202">Selecionar</button>
                                            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#popupDetalhes" onclick="abrirPopup('Quarto 202 - Deluxe', 'https://barahotel.com.br/wp-content/uploads/2024/02/DSC_9180-HDR.jpg', 'Quarto espaçoso com cama king-size, minibar, varanda com vista para a cidade, ar condicionado e TV de tela plana.')">Ver Detalhes</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <h5>Suíte 303 - Master</h5>
                                            <button type="button" class="btn btn-primary select-room" data-room="303">Selecionar</button>
                                            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#popupDetalhes" onclick="abrirPopup('Suíte 303 - Master', 'https://barahotel.com.br/wp-content/uploads/2024/02/DSC_9180-HDR.jpg', 'Suíte de luxo com área de estar separada, banheira de hidromassagem, minibar, e vista panorâmica da cidade.')">Ver Detalhes</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" id="quarto_escolhido" name="quarto_escolhido">
                            <div class="mt-4 text-center">
                                <button type="submit" class="btn btn-success">Confirmar Reserva</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Popup para detalhes dos quartos -->
    <div class="modal fade" id="popupDetalhes" tabindex="-1" role="dialog" aria-labelledby="popupDetalhesLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="popupDetalhesLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <img id="popupImagem" src="" class="img-fluid" alt="Detalhes do Quarto">
                    <p id="popupDescricao"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Seção de Contato -->
    <section id="contact" class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center">Fale Conosco</h2>
            <p class="text-center">Estamos à disposição para atender você!</p>
            <div class="row justify-content-center">
                <div class="col-md-6 text-center">
                    <a href="https://wa.me/5531999999999" class="btn btn-success">
                        <i class="ion ion-social-whatsapp"></i> WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-4 bg-dark text-white text-center">
        <p>&copy; 2024 Hotel Exemplo. Todos os direitos reservados.</p>
    </footer>

    <!-- Scripts -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script para atualizar o campo de quarto escolhido
        document.querySelectorAll('.select-room').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('quarto_escolhido').value = this.getAttribute('data-room');
                alert('Quarto ' + this.getAttribute('data-room') + ' selecionado!');
            });
        });

        // Script para abrir popup com detalhes do quarto
        function abrirPopup(titulo, imagemUrl, descricao) {
            document.getElementById('popupDetalhesLabel').innerText = titulo;
            document.getElementById('popupImagem').src = imagemUrl;
            document.getElementById('popupDescricao').innerText = descricao;
        }
    </script>
</body>

</html>