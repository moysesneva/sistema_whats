<?php
// ===================================
// CONFIGURAÇÕES INICIAIS E SEGURANÇA
// ===================================
session_start();
include 'funcoes.php';

// Verificação de login
if(!isset($_SESSION['login'])) {
    VaiPara('login.php');
    exit();
} 

// Configurações de erro (descomente em produção)
// error_reporting(0);
// ini_set("display_errors", 0);

$login = $_SESSION['login'];

// Includes necessários
include 'conn.php';
include 'config_dados.php';
include 'estilo.php';
include 'css_de_icones.php';

// Recebe parâmetros GET
$pagina_nome_recebe = isset($_GET['pagina_nome']) ? $_GET['pagina_nome'] : 0;

// ===================================
// BUSCA DADOS DO USUÁRIO
// ===================================
$stmt_busca_usuario = $conn->prepare("SELECT * FROM login WHERE login = ?");
$stmt_busca_usuario->bind_param("s", $login);
$stmt_busca_usuario->execute();
$query_busca_usuario = $stmt_busca_usuario->get_result();
$total_busca_usuario = $query_busca_usuario->num_rows;

while($rows_usuarios = $query_busca_usuario->fetch_array()) {
    $nome = Priletra($rows_usuarios['nome']);
    $img_perfil = $rows_usuarios['perfil_img'];
    $autorizado = $rows_usuarios['autorizado'];
    $tipo = $rows_usuarios['tipo'];
}

// Validações de segurança
if($total_busca_usuario != 1){
    VaiPara('login.php');
    exit();
}

if($autorizado != 2){
    VaiPara('desbloquar.php');
    exit();
}

// Include do menu
include 'menu.php';

// ===================================
// BUSCA HORÁRIOS EXISTENTES
// ===================================
$stmt_horarios = $conn->prepare("SELECT hp.id as horario_id, hp.profissional_id, hp.dia_semana, hp.hora_entrada, hp.almoco_inicio, hp.almoco_fim, hp.hora_saida, hp.ativo, p.profissional_nome, p.profissional_cargo FROM horarios_profissional hp INNER JOIN profissional p ON hp.profissional_id = p.id WHERE p.login = ? ORDER BY p.profissional_nome, FIELD(hp.dia_semana, 'segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado', 'domingo')");
$stmt_horarios->bind_param("s", $login);
$stmt_horarios->execute();
$query_horarios_existentes = $stmt_horarios->get_result();
$stmt_horarios->close();
?>


<?php $css_extra = '    <style>
        /* ===================================
           ESTILOS CUSTOMIZADOS
           =================================== */
        
        /* Estilos para cards de dias da semana */
        .dia-semana-card {
            transition: all 0.3s ease;
            border: 2px solid #ddd;
            margin-bottom: 15px;
            border-radius: 10px;
            overflow: hidden;
        }

        .dia-semana-card:hover {
            border-color: #007bff;
            box-shadow: 0 4px 15px rgba(0,123,255,0.2);
            transform: translateY(-2px);
        }

        .dia-header {
            position: relative;
            transition: all 0.3s ease;
            padding: 18px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .dia-header:hover {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        }

        /* Checkbox customizado */
        .dia-checkbox:checked ~ label .dia-status {
            color: #28a745;
            font-weight: bold;
        }

        .dia-checkbox:not(:checked) ~ label .dia-status {
            color: #6c757d;
        }

        .dia-checkbox:checked ~ label {
            color: #007bff;
            font-weight: bold;
        }

        /* Animações */
        .servico-item, .intervalo-item {
            animation: slideIn 0.3s ease;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            background: #f8f9fa;
            margin-bottom: 10px;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Estilização das tabs */
        .nav-tabs .nav-link {
            border-radius: 10px 10px 0 0;
            font-weight: 500;
            padding: 12px 20px;
            margin-right: 5px;
        }

        .nav-tabs .nav-link.active {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            border-color: #007bff;
        }

        /* Cards principais */
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .card-header {
            border-radius: 15px 15px 0 0 !important;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-bottom: 2px solid #dee2e6;
        }

        /* Botões customizados */
        .btn-custom {
            border-radius: 25px;
            padding: 10px 25px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        /* Inputs customizados */
        .form-control {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
            transform: scale(1.02);
        }

        /* Modal */
        #modalServicos .modal-body {
            max-height: 70vh;
            overflow-y: auto;
        }

        /* Alertas flutuantes */
        #alert-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }

        /* Tabela melhorada */
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
            transform: scale(1.01);
            transition: all 0.2s ease;
        }

        /* Badge customizado */
        .badge {
            font-size: 0.8em;
            padding: 6px 12px;
            border-radius: 20px;
        }

        /* Header principal */
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
        }

        .page-header h3 {
            margin: 0;
            font-weight: 300;
            font-size: 2.5rem;
        }

        .page-header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
        }
    </style>'; ?>
<?php include 'header.php'; ?>



                                    <!-- ===================================
                                         CONTAINER DE HORÁRIOS
                                         =================================== -->
                                    <div class="container-fluid mt-4">
                                        <div class="row">
                                            <div class="col-md-12">
                                                
                                                <!-- Header da Página -->
                                                <div class="page-header">
                                                    <h3>
                                                        <i class="fa fa-clock-o"></i>
                                                        Gestão de Horários Profissionais
                                                    </h3>
                                                    <p>Configure horários de trabalho e associe serviços aos seus profissionais</p>
                                                </div>
                                                
                                                <!-- ===================================
                                                     ABAS DE NAVEGAÇÃO
                                                     =================================== -->
                                                <ul class="nav nav-tabs mb-4" id="tipoCadastro" role="tablist">
                                                    <li class="nav-item">
                                                        <a class="nav-link active" id="servico-tab" data-toggle="tab" href="#cadastro-servico" role="tab">
                                                            <i class="fa fa-briefcase"></i> Configurar Horários e Serviços
                                                        </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" id="gerenciar-tab" data-toggle="tab" href="#gerenciar-horarios" role="tab">
                                                            <i class="fa fa-cogs"></i> Gerenciar Horários Cadastrados
                                                        </a>
                                                    </li>
                                                </ul>

                                                <!-- ===================================
                                                     CONTEÚDO DAS ABAS
                                                     =================================== -->
                                                <div class="tab-content" id="tipoCadastroContent">
                                                    
                                                    <!-- ===================================
                                                         TAB 1: CADASTRO POR SERVIÇO
                                                         =================================== -->
                                                    <div class="tab-pane fade show active" id="cadastro-servico" role="tabpanel">
                                                        <div class="card">
                                                            <div class="card-header">
                                                                <h5 class="card-title mb-0">
                                                                    <i class="fa fa-user-plus"></i> Configuração Completa do Profissional
                                                                </h5>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="alert alert-info">
                                                                    <i class="fa fa-info-circle"></i>
                                                                    <strong>Como usar:</strong> Configure os horários de trabalho completos e associe serviços ao profissional selecionado. 
                                                                    Você pode definir horários diferentes para cada dia da semana, incluir intervalos e escolher quais serviços o profissional oferece.
                                                                </div>
                                                                
                                                                <form action="cadastrar_horarios_servico.php" method="post" id="formPrincipal">
                                                                    
                                                                    <!-- ===================================
                                                                         SELEÇÃO DO PROFISSIONAL
                                                                         =================================== -->
                                                                    <div class="card mb-4">
                                                                        <div class="card-header bg-primary text-white">
                                                                            <h6 class="mb-0">
                                                                                <i class="fa fa-user"></i> Selecionar Profissional
                                                                            </h6>
                                                                        </div>
                                                                        <div class="card-body">
                                                                            <div class="form-group">
                                                                                <label for="profissional_servico">
                                                                                    <i class="fa fa-users"></i> Profissional *
                                                                                </label>
                                                                                <select class="form-control form-control-lg" id="profissional_servico" name="profissional_servico" required>
                                                                                    <option value="">🔍 Selecione um profissional...</option>
                                                                                    <?php              
                                                                                    $stmt_ch1 = $conn->prepare("SELECT * FROM profissional WHERE login = ? ORDER BY profissional_nome");
                                                                                    $stmt_ch1->bind_param("s", $login);
                                                                                    $stmt_ch1->execute();
                                                                                    $query_busca_profissional = $stmt_ch1->get_result();
                                                                                    $stmt_ch1->close();
                                                                                    while($rows_profissional = $query_busca_profissional->fetch_array()) {
                                                                                        echo '<option value="'.$rows_profissional['id'].'">👤 '.$rows_profissional['profissional_nome'].' - '.$rows_profissional['profissional_cargo'].'</option>';
                                                                                    }            
                                                                                    ?>                  
                                                                                </select>
                                                                                <small class="form-text text-muted">
                                                                                    Escolha o profissional para configurar horários e serviços
                                                                                </small>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <!-- ===================================
                                                                         HORÁRIOS DE TRABALHO
                                                                         =================================== -->
                                                                    <div class="card mb-4">
                                                                        <div class="card-header bg-success text-white">
                                                                            <h6 class="mb-0">
                                                                                <i class="fa fa-calendar"></i> Configurar Horários de Trabalho
                                                                            </h6>
                                                                        </div>
                                                                        <div class="card-body">
                                                                            <div class="alert alert-warning">
                                                                                <i class="fa fa-lightbulb-o"></i>
                                                                                <strong>Dica:</strong> Clique no checkbox do dia da semana para ativar e configurar os horários daquele dia.
                                                                            </div>
                                                                            
                                                                            <?php
                                                                            $dias_semana = [
                                                                                'segunda' => ['nome' => 'Segunda-feira', 'icon' => 'fa-briefcase', 'color' => 'primary'],
                                                                                'terca' => ['nome' => 'Terça-feira', 'icon' => 'fa-briefcase', 'color' => 'info'],
                                                                                'quarta' => ['nome' => 'Quarta-feira', 'icon' => 'fa-briefcase', 'color' => 'success'],
                                                                                'quinta' => ['nome' => 'Quinta-feira', 'icon' => 'fa-briefcase', 'color' => 'warning'],
                                                                                'sexta' => ['nome' => 'Sexta-feira', 'icon' => 'fa-briefcase', 'color' => 'danger'],
                                                                                'sabado' => ['nome' => 'Sábado', 'icon' => 'fa-sun-o', 'color' => 'secondary'],
                                                                                'domingo' => ['nome' => 'Domingo', 'icon' => 'fa-sun-o', 'color' => 'dark']
                                                                            ];
                                                                            
                                                                            foreach($dias_semana as $dia_key => $dia_info):
                                                                            ?>
                                                                            <div class="dia-semana-card">
                                                                                <div class="dia-header" style="cursor: pointer;">
                                                                                    <div class="custom-control custom-checkbox">
                                                                                        <input type="checkbox" class="custom-control-input dia-checkbox" 
                                                                                               id="ativo_<?=$dia_key?>" name="dias_ativos[]" value="<?=$dia_key?>">
                                                                                        <label class="custom-control-label" for="ativo_<?=$dia_key?>" style="cursor: pointer; width: 100%;">
                                                                                            <i class="fa <?=$dia_info['icon']?> text-<?=$dia_info['color']?>"></i> 
                                                                                            <strong><?=$dia_info['nome']?></strong>
                                                                                            <span class="float-right dia-status">
                                                                                                <i class="fa fa-toggle-off text-muted"></i> 
                                                                                                <small>Clique para ativar</small>
                                                                                            </span>
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="card-body" id="config_<?=$dia_key?>" style="display: none; background: linear-gradient(135deg, #f0f8ff, #e6f3ff);">
                                                                                    <div class="row">
                                                                                        <div class="col-md-3">
                                                                                            <label>
                                                                                                <i class="fa fa-sign-in text-success"></i> 
                                                                                                <strong>Horário de Entrada</strong>
                                                                                            </label>
                                                                                            <input type="time" class="form-control" name="entrada_<?=$dia_key?>" placeholder="08:00">
                                                                                            <small class="text-muted">Início do expediente</small>
                                                                                        </div>
                                                                                        <div class="col-md-3">
                                                                                            <label>
                                                                                                <i class="fa fa-cutlery text-warning"></i> 
                                                                                                <strong>Almoço - Início</strong>
                                                                                            </label>
                                                                                            <input type="time" class="form-control" name="almoco_inicio_<?=$dia_key?>" placeholder="12:00">
                                                                                            <small class="text-muted">Opcional</small>
                                                                                        </div>
                                                                                        <div class="col-md-3">
                                                                                            <label>
                                                                                                <i class="fa fa-cutlery text-warning"></i> 
                                                                                                <strong>Almoço - Fim</strong>
                                                                                            </label>
                                                                                            <input type="time" class="form-control" name="almoco_fim_<?=$dia_key?>" placeholder="13:00">
                                                                                            <small class="text-muted">Opcional</small>
                                                                                        </div>
                                                                                        <div class="col-md-3">
                                                                                            <label>
                                                                                                <i class="fa fa-sign-out text-danger"></i> 
                                                                                                <strong>Horário de Saída</strong>
                                                                                            </label>
                                                                                            <input type="time" class="form-control" name="saida_<?=$dia_key?>" placeholder="18:00">
                                                                                            <small class="text-muted">Fim do expediente</small>
                                                                                        </div>
                                                                                    </div>
                                                                                    
                                                                                    <!-- Intervalos Adicionais -->
                                                                                    <div class="mt-4">
                                                                                        <label>
                                                                                            <i class="fa fa-coffee text-info"></i> 
                                                                                            <strong>Intervalos Adicionais</strong>
                                                                                        </label>
                                                                                        <div id="intervalos_<?=$dia_key?>">
                                                                                            <button type="button" class="btn btn-sm btn-outline-info btn-custom" 
                                                                                                    onclick="adicionarIntervalo('<?=$dia_key?>')">
                                                                                                <i class="fa fa-plus"></i> Adicionar Intervalo
                                                                                            </button>
                                                                                        </div>
                                                                                        <small class="text-muted">Ex: pausas para café, intervalos personalizados</small>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <?php endforeach; ?>
                                                                        </div>
                                                                    </div>

                                                                    <!-- ===================================
                                                                         SERVIÇOS
                                                                         =================================== -->
                                                                    <div class="card mb-4">
                                                                        <div class="card-header bg-info text-white">
                                                                            <h6 class="mb-0 d-flex justify-content-between align-items-center">
                                                                                <span>
                                                                                    <i class="fa fa-wrench"></i> Associar Serviços ao Profissional
                                                                                </span>
                                                                                <button type="button" class="btn btn-light btn-sm" data-toggle="modal" data-target="#modalServicos">
                                                                                    <i class="fa fa-cog"></i> Gerenciar Serviços
                                                                                </button>
                                                                            </h6>
                                                                        </div>
                                                                        <div class="card-body">
                                                                            <div class="alert alert-info">
                                                                                <i class="fa fa-info-circle"></i>
                                                                                <strong>Importante:</strong> Selecione quais serviços este profissional pode executar. 
                                                                                Os valores e tempos são carregados automaticamente dos serviços cadastrados.
                                                                            </div>
                                                                            
                                                                            <div id="servicos-container">
                                                                                <div class="servico-item">
                                                                                    <div class="row">
                                                                                        <div class="col-md-4">
                                                                                            <label>
                                                                                                <i class="fa fa-tags"></i> 
                                                                                                <strong>Serviço</strong>
                                                                                            </label>
                                                                                            <select class="form-control servico-select" name="servico_id[]" required>
                                                                                                <option value="">🔍 Selecione um serviço...</option>
                                                                                                <?php
                                                                                                $stmt_ch2 = $conn->prepare("SELECT * FROM servicos WHERE login = ? AND ativo = 1 ORDER BY nome");
                                                                                                $stmt_ch2->bind_param("s", $login);
                                                                                                $stmt_ch2->execute();
                                                                                                $query_servicos = $stmt_ch2->get_result();
                                                                                                $stmt_ch2->close();
                                                                                                while($servico = $query_servicos->fetch_array()) {
                                                                                                    echo '<option value="'.$servico['id'].'" 
                                                                                                          data-duracao="'.$servico['duracao_minutos'].'" 
                                                                                                          data-valor="'.$servico['valor'].'">🛠️ '.$servico['nome'].'</option>';
                                                                                                }
                                                                                                ?>
                                                                                            </select>
                                                                                        </div>
                                                                                        <div class="col-md-3">
                                                                                            <label>
                                                                                                <i class="fa fa-clock-o"></i> 
                                                                                                <strong>Tempo (minutos)</strong>
                                                                                            </label>
                                                                                            <input type="number" class="form-control tempo-servico" name="tempo_servico[]" 
                                                                                                   min="15" step="15" readonly style="background-color: #f8f9fa;">
                                                                                            <small class="text-muted">Carregado automaticamente</small>
                                                                                        </div>
                                                                                        <div class="col-md-3">
                                                                                            <label>
                                                                                                <i class="fa fa-money"></i> 
                                                                                                <strong>Valor R$</strong>
                                                                                            </label>
                                                                                            <input type="number" class="form-control valor-servico" name="valor_servico[]" 
                                                                                                   step="0.01" min="0" readonly style="background-color: #f8f9fa;">
                                                                                            <small class="text-muted">Carregado automaticamente</small>
                                                                                        </div>
                                                                                        <div class="col-md-2">
                                                                                            <label>&nbsp;</label>
                                                                                            <button type="button" class="btn btn-danger btn-block btn-custom" onclick="removerServico(this)">
                                                                                                <i class="fa fa-trash"></i> Remover
                                                                                            </button>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            
                                                                            <div class="text-center mt-3">
                                                                                <button type="button" class="btn btn-outline-info btn-custom" onclick="adicionarServico()">
                                                                                    <i class="fa fa-plus"></i> Adicionar Mais Serviços
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <!-- ===================================
                                                                         BOTÃO SALVAR
                                                                         =================================== -->
                                                                    <div class="text-center">
                                                                        <button type="submit" class="btn btn-success btn-lg btn-custom">
                                                                            <i class="fa fa-save"></i> Salvar Configurações Completas
                                                                        </button>
                                                                        <br>
                                                                        <small class="text-muted mt-2">
                                                                            Certifique-se de preencher todos os campos obrigatórios antes de salvar
                                                                        </small>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- ===================================
                                                         TAB 2: GERENCIAR HORÁRIOS
                                                         =================================== -->
                                                    <div class="tab-pane fade" id="gerenciar-horarios" role="tabpanel">
                                                        <div class="card">
                                                            <div class="card-header">
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <h5 class="card-title mb-0">
                                                                        <i class="fa fa-calendar-check-o"></i> Horários Cadastrados
                                                                    </h5>
                                                                    <div class="card-tools d-flex align-items-center">
                                                                        <select class="form-control mr-2" id="filtro-profissional" onchange="filtrarPorProfissional()" 
                                                                                style="width: 250px;">
                                                                            <option value="">👥 Todos os Profissionais</option>
                                                                            <?php
                                                                            $stmt_ch3 = $conn->prepare("SELECT DISTINCT id, profissional_nome FROM profissional WHERE login = ? ORDER BY profissional_nome");
                                                                            $stmt_ch3->bind_param("s", $login);
                                                                            $stmt_ch3->execute();
                                                                            $query_profs = $stmt_ch3->get_result();
                                                                            $stmt_ch3->close();
                                                                            while($prof = $query_profs->fetch_array()) {
                                                                                echo '<option value="'.$prof['id'].'">👤 '.$prof['profissional_nome'].'</option>';
                                                                            }
                                                                            ?>
                                                                        </select>
                                                                        <button class="btn btn-info btn-sm btn-custom" onclick="recarregarHorarios()">
                                                                            <i class="fa fa-refresh"></i> Atualizar
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="card-body">
                                                                <?php if(mysqli_num_rows($query_horarios_existentes) > 0): ?>
                                                                <div class="table-responsive">
                                                                    <table class="table table-striped table-hover" id="tabelaHorarios">
                                                                        <thead class="thead-dark">
                                                                            <tr>
                                                                                <th><i class="fa fa-user"></i> Profissional</th>
                                                                                <th><i class="fa fa-calendar"></i> Dia da Semana</th>
                                                                                <th><i class="fa fa-clock-o"></i> Entrada</th>
                                                                                <th><i class="fa fa-cutlery"></i> Almoço</th>
                                                                                <th><i class="fa fa-clock-o"></i> Saída</th>
                                                                                <th><i class="fa fa-toggle-on"></i> Status</th>
                                                                                <th><i class="fa fa-cog"></i> Ações</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <?php
                                                                            $dias_map = [
                                                                                'segunda' => 'Segunda-feira',
                                                                                'terca' => 'Terça-feira', 
                                                                                'quarta' => 'Quarta-feira',
                                                                                'quinta' => 'Quinta-feira',
                                                                                'sexta' => 'Sexta-feira',
                                                                                'sabado' => 'Sábado',
                                                                                'domingo' => 'Domingo'
                                                                            ];
                                                                            
                                                                            mysqli_data_seek($query_horarios_existentes, 0);
                                                                            while($horario = mysqli_fetch_array($query_horarios_existentes)):
                                                                                $almoco_texto = '';
                                                                                if($horario['almoco_inicio'] && $horario['almoco_fim']) {
                                                                                    $almoco_texto = '<span class="text-success"><i class="fa fa-check"></i> '.$horario['almoco_inicio'] . ' - ' . $horario['almoco_fim'].'</span>';
                                                                                } else {
                                                                                    $almoco_texto = '<span class="text-muted"><i class="fa fa-times"></i> Sem intervalo</span>';
                                                                                }
                                                                                
                                                                                $status_badge = $horario['ativo'] == 1 ? 
                                                                                    '<span class="badge badge-success"><i class="fa fa-check"></i> Ativo</span>' : 
                                                                                    '<span class="badge badge-danger"><i class="fa fa-times"></i> Inativo</span>';
                                                                            ?>
                                                                            <tr data-profissional-id="<?=$horario['profissional_id']?>" data-horario-id="<?=$horario['horario_id']?>">
                                                                                <td>
                                                                                    <div>
                                                                                        <strong><i class="fa fa-user-circle"></i> <?=$horario['profissional_nome']?></strong>
                                                                                        <br><small class="text-muted"><i class="fa fa-briefcase"></i> <?=$horario['profissional_cargo']?></small>
                                                                                    </div>
                                                                                </td>
                                                                                <td>
                                                                                    <span class="badge badge-info badge-lg">
                                                                                        <i class="fa fa-calendar-o"></i> <?=$dias_map[$horario['dia_semana']]?>
                                                                                    </span>
                                                                                </td>
                                                                                <td>
                                                                                    <span class="text-success">
                                                                                        <i class="fa fa-sign-in"></i> <?=$horario['hora_entrada']?>
                                                                                    </span>
                                                                                </td>
                                                                                <td><?=$almoco_texto?></td>
                                                                                <td>
                                                                                    <span class="text-danger">
                                                                                        <i class="fa fa-sign-out"></i> <?=$horario['hora_saida']?>
                                                                                    </span>
                                                                                </td>
                                                                                <td><?=$status_badge?></td>
                                                                                <td>
                                                                                    <div class="btn-group" role="group">
                                                                                        <button class="btn btn-warning btn-sm" onclick="editarHorario(<?=$horario['horario_id']?>)" title="Editar">
                                                                                            <i class="fa fa-edit"></i>
                                                                                        </button>
                                                                                        <button class="btn btn-info btn-sm" onclick="verDetalhesHorario(<?=$horario['horario_id']?>)" title="Ver Detalhes">
                                                                                            <i class="fa fa-eye"></i>
                                                                                        </button>
                                                                                        <button class="btn btn-<?=$horario['ativo'] == 1 ? 'secondary' : 'success'?> btn-sm" 
                                                                                                onclick="toggleStatusHorario(<?=$horario['horario_id']?>, <?=$horario['ativo']?>)" 
                                                                                                title="<?=$horario['ativo'] == 1 ? 'Desativar' : 'Ativar'?>">
                                                                                            <i class="fa fa-power-off"></i>
                                                                                        </button>
                                                                                        <button class="btn btn-danger btn-sm" 
                                                                                                onclick="excluirHorario(<?=$horario['horario_id']?>, '<?=$horario['profissional_nome']?>', '<?=$dias_map[$horario['dia_semana']]?>')" 
                                                                                                title="Excluir">
                                                                                            <i class="fa fa-trash"></i>
                                                                                        </button>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                            <?php endwhile; ?>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                                <?php else: ?>
                                                                <div class="alert alert-info text-center">
                                                                    <i class="fa fa-info-circle fa-3x mb-3 text-info"></i>
                                                                    <h4>Nenhum horário cadastrado ainda</h4>
                                                                    <p>Utilize a aba "Configurar Horários e Serviços" para cadastrar horários para seus profissionais.</p>
                                                                    <button class="btn btn-primary btn-custom" onclick="$('#servico-tab').tab('show')">
                                                                        <i class="fa fa-plus"></i> Cadastrar Primeiro Horário
                                                                    </button>
                                                                </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===================================
         MODAIS
         =================================== -->
    
    <!-- Modal para Gerenciar Serviços -->
    <div class="modal fade" id="modalServicos" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fa fa-wrench"></i> Gerenciar Serviços Disponíveis
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Formulário para adicionar novo serviço -->
                    <div class="card mb-3">
                        <div class="card-header bg-success text-white">
                            <i class="fa fa-plus"></i> Adicionar Novo Serviço
                        </div>
                        <div class="card-body">
                            <form id="formNovoServico">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label>Nome do Serviço *</label>
                                        <input type="text" class="form-control" id="novo_servico_nome" placeholder="Ex: Corte de Cabelo" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Duração (min) *</label>
                                        <input type="number" class="form-control" id="novo_servico_duracao" placeholder="60" min="15" step="15" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Valor R$ *</label>
                                        <input type="number" class="form-control" id="novo_servico_valor" placeholder="50.00" step="0.01" min="0" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label>&nbsp;</label>
                                        <button type="button" class="btn btn-success btn-block btn-custom" onclick="salvarNovoServico()">
                                            <i class="fa fa-save"></i> Salvar
                                        </button>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-12">
                                        <label>Descrição (opcional)</label>
                                        <textarea class="form-control" id="novo_servico_descricao" placeholder="Descrição detalhada do serviço..." rows="2"></textarea>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Lista de serviços existentes -->
                    <div class="card">
                        <div class="card-header">
                            <i class="fa fa-list"></i> Serviços Cadastrados
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th><i class="fa fa-tag"></i> Serviço</th>
                                            <th><i class="fa fa-clock-o"></i> Duração</th>
                                            <th><i class="fa fa-money"></i> Valor</th>
                                            <th><i class="fa fa-toggle-on"></i> Status</th>
                                            <th><i class="fa fa-cog"></i> Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody id="listaServicos">
                                        <?php
                                        $stmt_ch4 = $conn->prepare("SELECT * FROM servicos WHERE login = ? ORDER BY nome");
                                        $stmt_ch4->bind_param("s", $login);
                                        $stmt_ch4->execute();
                                        $query_lista_servicos = $stmt_ch4->get_result();
                                        $stmt_ch4->close();
                                        while($servico = $query_lista_servicos->fetch_array()) {
                                            $status_badge = $servico['ativo'] == 1 ? 
                                                '<span class="badge badge-success"><i class="fa fa-check"></i> Ativo</span>' : 
                                                '<span class="badge badge-danger"><i class="fa fa-times"></i> Inativo</span>';
                                            echo '<tr data-servico-id="'.$servico['id'].'">
                                                    <td><strong>'.$servico['nome'].'</strong></td>
                                                    <td><span class="badge badge-info">'.$servico['duracao_minutos'].' min</span></td>
                                                    <td><span class="text-success"><strong>R$ '.number_format($servico['valor'], 2, ',', '.').'</strong></span></td>
                                                    <td>'.$status_badge.'</td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <button class="btn btn-sm btn-warning" onclick="editarServico('.$servico['id'].')" title="Editar">
                                                                <i class="fa fa-edit"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-'.($servico['ativo'] == 1 ? 'secondary' : 'success').'" onclick="toggleServico('.$servico['id'].')" title="'.($servico['ativo'] == 1 ? 'Desativar' : 'Ativar').'">
                                                                <i class="fa fa-power-off"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                  </tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fa fa-times"></i> Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Editar Horário -->
    <div class="modal fade" id="modalEditarHorario" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title">
                        <i class="fa fa-edit"></i> Editar Horário de Trabalho
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="formEditarHorario" action="editar_horario.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" id="edit_horario_id" name="horario_id">
                        
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i>
                            <strong id="edit_profissional_info"></strong>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><i class="fa fa-sign-in text-success"></i> <strong>Entrada</strong></label>
                                    <input type="time" class="form-control" id="edit_entrada" name="entrada" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><i class="fa fa-cutlery text-warning"></i> <strong>Almoço - Início</strong></label>
                                    <input type="time" class="form-control" id="edit_almoco_inicio" name="almoco_inicio">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><i class="fa fa-cutlery text-warning"></i> <strong>Almoço - Fim</strong></label>
                                    <input type="time" class="form-control" id="edit_almoco_fim" name="almoco_fim">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><i class="fa fa-sign-out text-danger"></i> <strong>Saída</strong></label>
                                    <input type="time" class="form-control" id="edit_saida" name="saida" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="edit_ativo" name="ativo" value="1">
                                        <label class="custom-control-label" for="edit_ativo">
                                            <i class="fa fa-toggle-on"></i> <strong>Horário Ativo</strong>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Seção de Intervalos -->
                        <div class="card mt-3">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0 d-flex justify-content-between align-items-center">
                                    <span><i class="fa fa-coffee"></i> Intervalos Adicionais</span>
                                    <button type="button" class="btn btn-light btn-sm" onclick="adicionarIntervaloEdicao()">
                                        <i class="fa fa-plus"></i> Adicionar
                                    </button>
                                </h6>
                            </div>
                            <div class="card-body">
                                <div id="intervalos-edicao">
                                    <!-- Intervalos serão carregados aqui via JavaScript -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fa fa-times"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary btn-custom">
                            <i class="fa fa-save"></i> Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Ver Detalhes -->
    <div class="modal fade" id="modalDetalhesHorario" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">
                        <i class="fa fa-eye"></i> Detalhes Completos do Horário
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="detalhesHorarioConteudo">
                    <!-- Conteúdo será carregado via JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fa fa-times"></i> Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Container para Alertas -->
    <div id="alert-container"></div>

    <!-- ===================================
         SCRIPTS
         =================================== -->
    
    <!-- jQuery -->

<?php include 'footer.php'; ?>