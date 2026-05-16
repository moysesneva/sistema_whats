<?php
require_once __DIR__ . '/auth_guard.php';
session_start();

include 'funcoes.php';

if(!isset($_SESSION['login'])) {
VaiPara('login.php');
} 
#$_SESSION['tipo_menu'] = 1;
$login = $_SESSION['login'];

include 'conn.php';
include 'config_dados.php';
include 'estilo.php';
include 'css_de_icones.php';

if (isset($_GET['pagina_nome'])) {
$pagina_nome_recebe = $_GET['pagina_nome'];
}else{
$pagina_nome_recebe = 0;    
}

$stmt_busca_usuario = $conn->prepare("SELECT * FROM login WHERE login = ?");
$stmt_busca_usuario->bind_param("s", $login);
$stmt_busca_usuario->execute();
$query_busca_usuario = $stmt_busca_usuario->get_result();
$total_busca_usuario = $query_busca_usuario->num_rows;

while($rows_usuarios = $query_busca_usuario->fetch_array()) {
    $nome  = Priletra($rows_usuarios['nome']);
    $img_perfil  = $rows_usuarios['perfil_img'];
    $autorizado  = $rows_usuarios['autorizado'];
    $tipo  = $rows_usuarios['tipo'];
    $login  = $rows_usuarios['login'];

}
#####DEFINIMOS QUE  O TIPO DO MENU
## 1 É O ADM
## 2 É  O USUARIO
include 'menu.php';

if($total_busca_usuario != 1){
    VaiPara('login.php');
}
if($autorizado != 2){
 VaiPara('desbloquar.php');
}

if (isset($_POST['deletar'])) {
            $id = $_POST['id'];
            $sql_delete = "DELETE FROM datas_excluidas WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql_delete);
            mysqli_stmt_bind_param($stmt, 's', $id);
            mysqli_stmt_execute($stmt);
        }
        // Fechar a conexão

?>

<?php include 'header.php'; ?>

   <!-- Formulário para solicitar confirmação de agendamento -->

    <?php
    // Função para conectar ao banco de dados
    function conectarDB() {
       include 'conn.php';
        return $conn;
    }
    ?>

    <div class="page-container">
        <!-- Page Header -->
        <div class="page-header">
            <h3><i class="fas fa-calendar-star"></i> Datas Especiais</h3>
            <p class="page-subtitle">Gerencie datas especiais e indisponibilidades dos profissionais</p>
        </div>

        <!-- Div para exibir os resultados da pesquisa -->
        <div id="resultadoBusca" class="resultado-busca"></div>

        <!-- Card de Cadastro de Data Especial -->
        <div class="modern-card">
            <div class="card-header-modern">
                <h5><i class="fas fa-plus-circle"></i> Cadastrar Nova Data Especial</h5>
            </div>
            <div class="card-body-modern">
                <!-- Formulário para Agendamento -->
                <form action="datas_especiais_confirma.php" method="post">
                    <div class="row">
                        <!-- Seleção do Profissional -->
                        <div class="col-md-6">
                            <div class="form-group-modern">
                                <label class="form-label-modern" for="profissional">
                                    <i class="fas fa-user-md"></i> Selecione o Profissional
                                </label>
                                <select class="form-control form-control-modern" id="profissional" name="profissional" required data-change-fn="carregarDiasSemana">
                                    <option value="">Escolha um profissional</option>
                                    <?php
                                    // Conexão com o banco de dados
                                    $conn = conectarDB();

                                    // Consulta para obter os profissionais
                                    $stmt_de1 = $conn->prepare("SELECT * FROM profissional WHERE login = ?");
                                    $stmt_de1->bind_param("s", $login);
                                    $stmt_de1->execute();
                                    $result = $stmt_de1->get_result();
                                    $stmt_de1->close();

                                    while ($row = $result->fetch_assoc()) {
                                        echo '<option value="' . (int)$row['id'] . '">' . htmlspecialchars($row['profissional_nome'], ENT_QUOTES, 'UTF-8') . ' - ' . htmlspecialchars($row['profissional_cargo'], ENT_QUOTES, 'UTF-8') . '</option>';
                                    }

                                    // Fechar a conexão
                                    mysqli_close($conn);
                                    ?>
                                </select>
                            </div>
                        </div>

                        <!-- Inserção de Data -->
                        <div class="col-md-6">
                            <div class="form-group-modern">
                                <label class="form-label-modern" for="data">
                                    <i class="fas fa-calendar-alt"></i> Data Especial
                                </label>
                                <input type="date" class="form-control form-control-modern" id="data" name="data" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Campo de Descrição -->
                        <div class="col-12">
                            <div class="form-group-modern">
                                <label class="form-label-modern" for="texto">
                                    <i class="fas fa-edit"></i> Motivo (Opcional)
                                </label>
                                <input type="text" class="form-control form-control-modern" id="texto" name="texto" placeholder="Digite a descrição da data especial">
                            </div>
                        </div>
                    </div>

                    <!-- Botão para Agendar -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-modern btn-success-modern">
                            <i class="fas fa-save"></i> Cadastrar Data Especial
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Card de Filtro e Listagem -->
        <div class="modern-card">
            <div class="card-header-modern">
                <h5><i class="fas fa-list"></i> Datas Especiais Cadastradas</h5>
            </div>
            <div class="card-body-modern">
                <!-- Formulário para filtrar por Data -->
                <div class="filter-form">
                    <form action="" method="get" class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label-modern" for="data_filtro">
                                <i class="fas fa-search"></i> Buscar por Data:
                            </label>
                            <input type="date" class="form-control form-control-modern" id="data_filtro" name="data_filtro" 
                                   value="<?= isset($_GET['data_filtro']) ? $_GET['data_filtro'] : '' ?>">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-modern btn-primary-modern me-2">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                            <a href="?" class="btn btn-modern btn-outline-secondary">
                                <i class="fas fa-times"></i> Limpar
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Tabela de Resultados -->
                <div class="table-responsive">
                    <?php
                    // Conexão com o banco de dados
                    $conn = conectarDB();

                    // Verificar se o login foi definido
                    $stmt_de2 = $conn->prepare("SELECT * FROM profissional WHERE login = ?");
                    $stmt_de2->bind_param("s", $login);
                    $stmt_de2->execute();
                    $query_busca_profissional = $stmt_de2->get_result();
                    $stmt_de2->close();

                    $ID_ARRAY = [];

                    while ($rows_profissional = $query_busca_profissional->fetch_array()) {
                        // Adicionar cada ID ao array
                        $ID_ARRAY[] = $rows_profissional['id'];
                    }

                    // Verificar se o array não está vazio
                    if (!empty($ID_ARRAY)) {
                        // Gerar placeholders para o IN (...)
                        $id_placeholders = implode(',', array_fill(0, count($ID_ARRAY), '?'));

                        // Base da consulta SQL
                        $sql = "SELECT * FROM datas_excluidas WHERE id_profissional IN ($id_placeholders)";
                        
                        // Verificar se há um filtro de data
                        if (isset($_GET['data_filtro']) && !empty($_GET['data_filtro'])) {
                            $data_filtro = $_GET['data_filtro'];
                            $sql .= " AND data_excluida = ?";
                        }

                        // Preparar a consulta SQL
                        $stmt = mysqli_prepare($conn, $sql);

                        // Associar parâmetros
                        if (isset($data_filtro)) {
                            $bind_args = array_merge($ID_ARRAY, [$data_filtro]);
                            $bind_types = str_repeat('i', count($ID_ARRAY)) . 's';
                        } else {
                            $bind_args = $ID_ARRAY;
                            $bind_types = str_repeat('i', count($ID_ARRAY));
                        }
                        mysqli_stmt_bind_param($stmt, $bind_types, ...$bind_args);

                        // Executar a consulta
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);

                        // Verificar se há resultados
                        if (mysqli_num_rows($result) > 0) {
                            echo '<div class="table-modern">';
                            echo '<table class="table table-hover mb-0">';
                            echo '<thead>';
                            echo '<tr>';
                            echo '<th><i class="fas fa-calendar-day"></i> Data Excluída</th>';
                            echo '<th><i class="fas fa-user-md"></i> Profissional</th>';
                            echo '<th><i class="fas fa-comment"></i> Motivo</th>';
                            echo '<th><i class="fas fa-cogs"></i> Ação</th>';
                            echo '</tr>';
                            echo '</thead>';
                            echo '<tbody>';

                            while ($row = mysqli_fetch_assoc($result)) {
                                // Converter data_excluida para o formato brasileiro
                                $data_excluida = !empty($row['data_excluida']) ? date('d/m/Y', strtotime($row['data_excluida'])) : '';

                                echo '<tr>';
                                echo '<td><strong>' . htmlspecialchars($data_excluida, ENT_QUOTES, 'UTF-8') . '</strong></td>';
                                echo '<td>' . htmlspecialchars($row['profissional'], ENT_QUOTES, 'UTF-8') . '</td>';
                                echo '<td>' . htmlspecialchars($row['motivo'], ENT_QUOTES, 'UTF-8') . '</td>';
                                echo '<td>';
                                echo '<form method="post" action="" style="display:inline;">';
                                echo '<input type="hidden" name="id" value="' . htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') . '">';
                                echo '<button type="submit" name="deletar" class="btn btn-modern btn-danger-modern" data-fn="__confirm" data-args=\'["Tem certeza que deseja excluir esta data especial?"]\'>';
                                echo '<i class="fas fa-trash"></i> Excluir';
                                echo '</button>';
                                echo '</form>';
                                echo '</td>';
                                echo '</tr>';
                            }

                            echo '</tbody>';
                            echo '</table>';
                            echo '</div>';
                        } else {
                            echo '<div class="alert alert-modern alert-info-modern">';
                            echo '<i class="fas fa-info-circle"></i> ';
                            echo '<strong>Nenhuma data especial encontrada.</strong><br>';
                            echo 'Não há datas especiais cadastradas' . (isset($_GET['data_filtro']) && !empty($_GET['data_filtro']) ? ' para a data selecionada' : '') . '.';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="alert alert-modern alert-info-modern">';
                        echo '<i class="fas fa-user-times"></i> ';
                        echo '<strong>Nenhum profissional encontrado.</strong><br>';
                        echo 'É necessário ter profissionais cadastrados para gerenciar datas especiais.';
                        echo '</div>';
                    }

                    // Fechar conexão com o banco de dados
                    mysqli_close($conn);
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Action Button -->
    <div class="floating-action" data-fn="__el_focus" data-args='["profissional"]' title="Adicionar Data Especial">
        <i class="fas fa-plus"></i>
    </div>

    <!-- Scripts -->
    <script src="../files/bower_components/jquery/js/jquery.min.js"></script>
    <script nonce="<?= htmlspecialchars($GLOBALS['csp_nonce'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        // Função para buscar clientes em tempo real
        function buscarCliente() {
            var nome = document.getElementById('nome').value;
            var telefone = document.getElementById('telefone').value;

            // Criação do objeto XMLHttpRequest para fazer a requisição AJAX
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // Atualiza o conteúdo da div com os resultados da busca
                    document.getElementById('resultadoBusca').innerHTML = xhr.responseText;
                }
            };

            // Envia a requisição para o script PHP com os valores de nome e telefone
            xhr.open("GET", "buscar_cliente.php?nome=" + nome + "&telefone=" + telefone, true);
            xhr.send();
        }

        // Função para preencher os campos de nome e telefone ao clicar em um resultado da busca
        function preencherCampos(nome, telefone) {
            document.getElementById('nome').value = nome;
            document.getElementById('telefone').value = telefone;

            // Limpa os resultados da busca depois de selecionar um cliente
            document.getElementById('resultadoBusca').innerHTML = '';
        }

        // Scripts AJAX para carregar agendamentos disponíveis dinamicamente
        // Carregar os dias da semana disponíveis ao selecionar um profissional
        function carregarDiasSemana() {
            var profissionalId = $('#profissional').val();
            if (profissionalId !== '') {
                $.ajax({
                    url: 'buscar_dias_semana.php',
                    type: 'POST',
                    data: { profissional_id: profissionalId },
                    success: function(response) {
                        $('#dia_semana').html(response);
                        $('#agendamento').html('<option value="">Escolha uma data e horário disponível</option>');
                    }
                });
            } else {
                $('#dia_semana').html('<option value="">Escolha um dia da semana</option>');
                $('#agendamento').html('<option value="">Escolha uma data e horário disponível</option>');
            }
        }

        // Carregar as datas e horários disponíveis ao selecionar um dia da semana
        function carregarAgendamentosDisponiveis() {
            var profissionalId = $('#profissional').val();
            var diaSemana = $('#dia_semana').val();
            if (profissionalId !== '' && diaSemana !== '') {
                $.ajax({
                    url: 'buscar_agendamentos_disponiveis.php',
                    type: 'POST',
                    data: { profissional_id: profissionalId, dia_semana: diaSemana },
                    success: function(response) {
                        $('#agendamento').html(response);
                    }
                });
            } else {
                $('#agendamento').html('<option value="">Escolha uma data e horário disponível</option>');
            }
        }

        // Smooth scrolling para melhor UX
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Animação suave para cards
        const cards = document.querySelectorAll('.modern-card');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        });

        cards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(card);
        });
    </script>

<?php include 'footer.php'; ?>