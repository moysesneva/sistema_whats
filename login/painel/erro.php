<?php
if (isset($_GET['erro']) && $_GET['erro'] == 'login') {
    // Se o erro for 'login', exibe o pop-up de erro dentro da div
    echo '
    <div class="popup-overlay" style="
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
    ">
        <div class="popup-content" style="
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        ">
            <h2 style="color: #f44336;">Erro!</h2>
            <p>Erro de login. Verifique suas credenciais e tente novamente.</p>
            <button onclick="document.querySelector(\'.popup-overlay\').style.display=\'none\'" style="
                background-color: #f44336;
                color: white;
                border: none;
                padding: 10px 20px;
                cursor: pointer;
                border-radius: 5px;
            ">Fechar</button>
        </div>
    </div>';
}
?>
<?php
if (isset($_GET['erro']) && $_GET['erro'] == 'login_duplicado') {
    // Se o erro for 'login', exibe o pop-up de erro dentro da div
    echo '
    <div class="popup-overlay" style="
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
    ">
        <div class="popup-content" style="
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        ">
            <h2 style="color: #f44336;">Erro!</h2>
            <p>Usuário duplicado. Este usuário ja existe.</p>
            <button onclick="document.querySelector(\'.popup-overlay\').style.display=\'none\'" style="
                background-color: #f44336;
                color: white;
                border: none;
                padding: 10px 20px;
                cursor: pointer;
                border-radius: 5px;
            ">Fechar</button>
        </div>
    </div>';
}
?>
<?php
if (isset($_GET['confirmacao']) && $_GET['confirmacao'] == 'cadastro_sucesso') {
    // Se a confirmação for 'cadastro_sucesso', exibe o pop-up de confirmação dentro da div
    echo '
    <div class="popup-overlay" style="
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
    ">
        <div class="popup-content" style="
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        ">
            <h2 style="color: #4CAF50;">Sucesso!</h2>
            <p>Cadastro realizado com sucesso! Faça o login para acessar sua conta.</p>
            <button onclick="document.querySelector(\'.popup-overlay\').style.display=\'none\'" style="
                background-color: #4CAF50;
                color: white;
                border: none;
                padding: 10px 20px;
                cursor: pointer;
                border-radius: 5px;
            ">Fechar</button>
        </div>
    </div>';
}
?>
<?php
if (isset($_GET['status']) && $_GET['status'] == 'sucesso') {
    // Se a confirmação for 'cadastro_sucesso', exibe o pop-up de confirmação dentro da div
    echo '
    <div class="popup-overlay" style="
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
    ">
        <div class="popup-content" style="
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        ">
            <h2 style="color: #4CAF50;">Sucesso!</h2>
            <p>Cadastro realizado com sucesso!</p>
            <button onclick="document.querySelector(\'.popup-overlay\').style.display=\'none\'" style="
                background-color: #4CAF50;
                color: white;
                border: none;
                padding: 10px 20px;
                cursor: pointer;
                border-radius: 5px;
            ">Fechar</button>
        </div>
    </div>';
}
?>
<?php
if (isset($_GET['erro']) && $_GET['erro'] == 'code') {
    // Se o erro for 'login', exibe o pop-up de erro dentro da div
    echo '
    <div class="popup-overlay" style="
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
    ">
        <div class="popup-content" style="
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        ">
            <h2 style="color: #f44336;">Erro!</h2>
            <p>ERRO NO CÓDIGO. por favor insira o código correto.</p>
            <button onclick="document.querySelector(\'.popup-overlay\').style.display=\'none\'" style="
                background-color: #f44336;
                color: white;
                border: none;
                padding: 10px 20px;
                cursor: pointer;
                border-radius: 5px;
            ">Fechar</button>
        </div>
    </div>';
}
?>
<?php
if (isset($_GET['confirmacao']) && $_GET['confirmacao'] == 'atualizado') {
    // Se a confirmação for 'cadastro_sucesso', exibe o pop-up de confirmação dentro da div
    echo '
    <div class="popup-overlay" style="
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
    ">
        <div class="popup-content" style="
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        ">
            <h2 style="color: #4CAF50;">Sucesso!</h2>
            <p>Cadastro atualizado com sucesso!</p>
            <button onclick="document.querySelector(\'.popup-overlay\').style.display=\'none\'" style="
                background-color: #4CAF50;
                color: white;
                border: none;
                padding: 10px 20px;
                cursor: pointer;
                border-radius: 5px;
            ">Fechar</button>
        </div>
    </div>';
}
?>
<?php
if (isset($_GET['erro']) && $_GET['erro'] == 'atualizado') {
    // Se o erro for 'login', exibe o pop-up de erro dentro da div
    echo '
    <div class="popup-overlay" style="
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
    ">
        <div class="popup-content" style="
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        ">
            <h2 style="color: #f44336;">Erro!</h2>
            <p>ERRO AO INSERIR DADOS. por favor tente novamente.</p>
            <button onclick="document.querySelector(\'.popup-overlay\').style.display=\'none\'" style="
                background-color: #f44336;
                color: white;
                border: none;
                padding: 10px 20px;
                cursor: pointer;
                border-radius: 5px;
            ">Fechar</button>
        </div>
    </div>';
}
?>

<?php
// Verifica se o parâmetro 'aguarde' e 'tempo' estão presentes na URL
if (isset($_GET['aguarde']) && isset($_GET['tempo'])) {
    $pagina_destino = $_GET['aguarde'];
    $tempo_segundos = intval($_GET['tempo']) * 1000; // Converte o tempo para milissegundos
    $tempo_intervalo = $tempo_segundos / 100; // Calcula o intervalo para animar a barra

    echo '
    <div class="popup-overlay" style="
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    ">
        <div class="popup-content" style="
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        ">
            <h2>Aguarde, estamos redirecionando...</h2>

            <!-- Barra de progresso -->
            <div class="progress" style="height: 30px; margin-top: 20px;">
                <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                    style="width: 0%; background-color: #4caf50;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                    0%
                </div>
            </div>
        </div>
    </div>

    <!-- Script para animar a barra de progresso e redirecionar após o tempo especificado -->
    <script>
        let progressBar = document.getElementById("progressBar");
        let width = 0;
        let interval = setInterval(function () {
            if (width >= 100) {
                clearInterval(interval);
                window.location.href = "' . $pagina_destino . '"; // Redireciona para a página definida na URL
            } else {
                width++;
                progressBar.style.width = width + "%";
                progressBar.innerHTML = width + "%";
            }
        }, ' . $tempo_intervalo . '); // Intervalo de atualização da barra de progresso
    </script>

    <!-- CSS para personalizar a barra de progresso -->
    <style>
        .progress {
            background-color: #e9ecef;
            border-radius: 5px;
        }

        .progress-bar {
            font-size: 18px;
            line-height: 30px; /* Alinha o texto no centro vertical */
            color: white;
            text-align: center;
        }
    </style>
    ';
}
?>
<?php
if (isset($_GET['erro']) && $_GET['erro'] == 'duplicado') {
    // Se o erro for 'login', exibe o pop-up de erro dentro da div
    echo '
    <div class="popup-overlay" style="
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
    ">
        <div class="popup-content" style="
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        ">
            <h2 style="color: #f44336;">Erro!</h2>
            <p>Erro no agendameto, tente novamente.</p><br>
            <button onclick="document.querySelector(\'.popup-overlay\').style.display=\'none\'" style="
                background-color: #f44336;
                color: white;
                border: none;
                padding: 10px 20px;
                cursor: pointer;
                border-radius: 5px;
            ">Fechar</button>
        </div>
    </div>';
}
?>
<?php
if (isset($_GET['agenda']) && $_GET['agenda'] == 'atualizado') {
    // Se a confirmação for 'cadastro_sucesso', exibe o pop-up de confirmação dentro da div
    echo '
    <div class="popup-overlay" style="
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
    ">
        <div class="popup-content" style="
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        ">
            <h2 style="color: #4CAF50;">Sucesso!</h2>
            <p>Agendamento atualizado com sucesso!</p><br>
            <button onclick="document.querySelector(\'.popup-overlay\').style.display=\'none\'" style="
                background-color: #4CAF50;
                color: white;
                border: none;
                padding: 10px 20px;
                cursor: pointer;
                border-radius: 5px;
            ">Fechar</button>
        </div>
    </div>';
}
?>