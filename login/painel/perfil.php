<?php
require_once __DIR__ . '/auth_guard.php';
session_start();
include 'funcoes.php';

if(!isset($_SESSION['login'])) {
    VaiPara('login.php');
} 
$login = $_SESSION['login'];

include 'conn.php';
include 'config_dados.php';
include 'estilo.php';
include 'css_de_icones.php';

if (isset($_GET['pagina_nome'])) {
    $pagina_nome_recebe = $_GET['pagina_nome'];
} else {
    $pagina_nome_recebe = 0;    
}

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
    $email = $rows_usuarios['email'];
    $pagamento_cliente = $rows_usuarios['pagamento_cliente'];
    $vencimento = $rows_usuarios['vencimento'];
    $id_assinatura = $rows_usuarios['id_assinatura'];
    $situacao = $rows_usuarios['situacao'];
}

$sql_busca_config = "SELECT * FROM config";
$query_busca_config = mysqli_query($conn, $sql_busca_config);
$total_busca_config = mysqli_num_rows($query_busca_config);

while($rows_config = mysqli_fetch_array($query_busca_config)) {
    $link_pagamento = $rows_config['link_pagamento'];
    $preco = $rows_config['preco'];
    $telefone_adm = $rows_config['telefone'];
}

include 'menu.php';
if($tipo == '1'){
    VaiPara('config_adm.php?pagina_nome=1');    
}

if($total_busca_usuario != 1){
    VaiPara('login.php');
}
if($autorizado != 2){
    VaiPara('desbloquar.php');
}

if($tipo == '5'){
    
VaiPara('senha.php');
    
}

?>

<?php $css_extra = '    <link rel="stylesheet" href="../files/assets/vendor/font-awesome-6/css/all.min.css">
    <style>
        /* Estilos para o ribbon "Mais Popular" */
        .ribbon-wrapper {
            position: absolute;
            top: -5px;
            right: -5px;
            z-index: 1;
            overflow: hidden;
            width: 150px;
            height: 150px;
        }
        
        .ribbon {
            position: absolute;
            top: 35px;
            right: -50px;
            transform: rotate(45deg);
            width: 200px;
            padding: 10px 0;
            background-color: #ffc107;
            color: #000;
            text-align: center;
            font-size: 13px;
            font-weight: bold;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        
        /* Estilos para os cards de planos */
        .pricing-card {
            transition: all 0.3s ease;
            border-radius: 15px;
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .pricing-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
        }
        
        .pricing-card .card-body {
            padding: 1.5rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        
        .pricing-price {
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 0.5rem;
        }
        
        .pricing-period {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .pricing-features {
            margin-bottom: 1.5rem;
            flex-grow: 1;
        }
        
        .pricing-features li {
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(0,0,0,.05);
        }
        
        .pricing-features li:last-child {
            border-bottom: none;
        }
        
        .pricing-features i {
            color: #28a745;
        }
        
        .btn-select-plan {
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        
        .btn-select-plan:hover {
            transform: translateY(-3px);
        }
        
        /* Efeito de pulsação para o botão do plano popular */
        @keyframes pulse-button {
            0% {
                box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.5);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(0, 123, 255, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(0, 123, 255, 0);
            }
        }
        
        .pulse-animation {
            animation: pulse-button 2s infinite;
        }
    </style>'; ?>
<?php include 'header.php'; ?>

                                <?php
                                if(empty($email)) {
                                ?>
                                    <!-- Formulário de E-mail com Verificação Dupla -->
                                    <div class="card">
                                        <div class="card-header bg-primary text-white">
                                            <h3 class="mb-0"><i class="feather icon-mail mr-2"></i>Configuração de E-mail</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-8 mx-auto">
                                                    <h4 class="mb-4">Insira e Verifique seu E-mail</h4>
                                                    
                                                    <form action="verificar_email.php" method="post" onsubmit="return verificarEmails()">
                                                        <div class="form-group">
                                                            <label for="emailPerfil">E-mail</label>
                                                            <input type="email" class="form-control" id="emailPerfil" name="emailPerfil" placeholder="Digite seu e-mail" required onchange="mostrarAlerta()">
                                                            <small class="form-text text-info font-weight-bold">
                                                                <i class="fas fa-exclamation-circle"></i> Este e-mail será utilizado como forma de identificação de pagamento. Por favor, insira um e-mail válido e confirme-o com cuidado.
                                                            </small>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="emailPerfilConfirmar">Confirme seu E-mail</label>
                                                            <input type="email" class="form-control" id="emailPerfilConfirmar" name="emailPerfilConfirmar" placeholder="Digite seu e-mail novamente" required>
                                                            <small id="emailErro" class="form-text text-danger" style="display: none;">Os e-mails não coincidem. Por favor, tente novamente.</small>
                                                        </div>

                                                        <button type="submit" class="btn btn-success mt-3">Enviar</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <script>
                                        function verificarEmails() {
                                            var email = document.getElementById('emailPerfil').value;
                                            var emailConfirmar = document.getElementById('emailPerfilConfirmar').value;
                                            var emailErro = document.getElementById('emailErro');

                                            if (email !== emailConfirmar) {
                                                emailErro.style.display = 'block';
                                                return false;
                                            } else {
                                                emailErro.style.display = 'none';
                                                return true;
                                            }
                                        }

                                        function mostrarAlerta() {
                                            alert("Este e-mail será utilizado para identificação no pagamento. Verifique se está correto antes de prosseguir.");
                                        }
                                    </script>
                                <?php
                                } else {
                                    // Usuário já tem email
                                    $data_atual = date("Y-m-d");
                                    if ($vencimento >= $data_atual && $email) {
                                        $nome_usuario = $nome;
                                        $data_vencimento_formatada = date("d/m/Y", strtotime($vencimento));
                                ?>
                                    <!-- Detalhes da Fatura -->
                                    <div class="card shadow">
                                        <div class="card-header bg-primary text-white">
                                            <div class="d-flex align-items-center">
                                                <i class="feather icon-calendar mr-2"></i>
                                                <h4 class="mb-0">Detalhes da Fatura</h4>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="text-center mb-4">
                                                <div class="avatar mb-3">
                                                    <img src="<?php echo htmlspecialchars($img_perfil ?? '', ENT_QUOTES, 'UTF-8'); ?>" alt="Perfil" class="img-radius" style="width: 80px; height: 80px;">
                                                </div>
                                                <h5 class="card-title">Olá, <span class="text-primary"><?php echo htmlspecialchars($nome_usuario, ENT_QUOTES, 'UTF-8'); ?></span>!</h5>
                                            </div>
                                            
                                            <div class="alert alert-light border text-center mb-4">
                                                <div class="text-muted mb-2">Sua próxima fatura tem vencimento em:</div>
                                                <div class="d-flex justify-content-center align-items-center">
                                                    <i class="feather icon-clock text-danger mr-2"></i>
                                                    <h3 class="text-danger mb-0"><?php echo $data_vencimento_formatada; ?></h3>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-center mt-5">
    <div class="col-md-6">
        <div class="border rounded p-4 mb-4 text-center shadow-sm" style="background-color: #f9f9f9;">
            <div class="text-muted small mb-2">Status</div>
            <div class="<?= ($situacao != 'ativado') ? 'text-danger' : 'text-success'; ?>" style="font-size: 1.5rem; font-weight: bold;">
                <i class="feather icon-check-circle mr-1"></i> 
                <?= ($situacao != 'ativado') ? 'Bloqueado' : 'Ativo'; ?>
            </div>
        </div>
    </div>
</div>

                                            <?php if ($situacao != 'ativado'): ?>
                                            <!-- Seção de Ativação do Bot -->
                                           
                                              <!-- SEÇÃO DE PLANOS CORRIGIDA - MELHOR CONTRASTE E BOTÕES -->
                                    <div class="card">
                                        <div class="card-header bg-primary text-white">
                                            <h3 class="mb-0"><i class="feather icon-package mr-2"></i> Nossos Planos</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="text-center mb-4">
                                                <h4>Escolha o plano ideal para o seu negócio</h4>
                                            </div>
                                            
                                            <div class="row justify-content-center">
                                                <?php 
                                                // Consulta os planos ativos
                                                $sql_planos = "SELECT * FROM planos_online WHERE ativo = 1 ORDER BY preco ASC";
                                                $result_planos = mysqli_query($conn, $sql_planos);
                                                
                                                $i = 0;
                                                if ($result_planos && mysqli_num_rows($result_planos) > 0) {
                                                    $total_planos = mysqli_num_rows($result_planos);
                                                    
                                                    while ($plano = mysqli_fetch_assoc($result_planos)) {
                                                        $id_plano = $plano['id'];
                                                        $titulo_plano = $plano['titulo'];
                                                        $preco_plano = $plano['preco'];
                                                        $link_pagamento_plano = $plano['link_pagamento'];
                                                        
                                                        // O plano do meio é destacado
                                                        $isMiddlePlan = ($i == 1 || ($total_planos == 2 && $i == 0));
                                                        
                                                        // Consulta as features do plano
                                                        $features = [];
                                                        $sql_features = "SELECT feature FROM planos_features WHERE id_plano = $id_plano ORDER BY id";
                                                        $result_features = mysqli_query($conn, $sql_features);
                                                        
                                                        if ($result_features && mysqli_num_rows($result_features) > 0) {
                                                            while ($feature = mysqli_fetch_assoc($result_features)) {
                                                                $features[] = $feature['feature'];
                                                            }
                                                        }
                                                ?>
                                                <div class="col-lg-4 col-md-6 mb-4">
                                                    <div class="card pricing-card h-100 <?php echo $isMiddlePlan ? 'shadow border-primary' : ''; ?>">
                                                        <?php if($isMiddlePlan): ?>
                                                        <div class="ribbon-wrapper">
                                                            <div class="ribbon">Mais Popular</div>
                                                        </div>
                                                        <?php endif; ?>
                                                        
                                                        <div class="card-header text-center py-4 <?php echo $isMiddlePlan ? 'bg-primary text-white' : ''; ?>">
                                                            <h3 class="font-weight-bold mb-0"><?php echo htmlspecialchars($titulo_plano, ENT_QUOTES, 'UTF-8'); ?></h3>
                                                        </div>
                                                        
                                                        <div class="card-body d-flex flex-column">
                                                            <div class="text-center mb-4">
                                                                <div class="pricing-price">
                                                                    R$ <?php echo number_format(floatval($preco_plano), 2, ',', '.'); ?>
                                                                </div>
                                                                <div class="pricing-period">/ mês</div>
                                                            </div>
                                                            
                                                            <ul class="pricing-features list-unstyled">
                                                                <?php foreach($features as $feature): ?>
                                                                <li>
                                                                    <i class="feather icon-check-circle mr-2"></i>
                                                                    <?php echo htmlspecialchars($feature, ENT_QUOTES, 'UTF-8'); ?>
                                                                    <li>
                                                                    <i class="feather icon-check-circle mr-2"></i>
                                                                    <?php echo htmlspecialchars($feature, ENT_QUOTES, 'UTF-8'); ?>
                                                                </li>
                                                                <?php endforeach; ?>
                                                            </ul>
                                                            
                                                           <div class="mt-auto">
    <!-- Modificação aqui para concatenar o email ao link do plano -->
    <a href="<?php echo $link_pagamento_plano . (strpos($link_pagamento_plano, '?') !== false ? '&' : '?') . 'email=' . urlencode($email); ?>" target="_blank" 
       class="btn btn-block btn-lg <?php echo $isMiddlePlan ? 'btn-primary pulse-animation' : 'btn-outline-primary'; ?> btn-select-plan">
        Selecionar Plano
    </a>
</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                                        $i++;
                                                    }
                                                } else {
                                                ?>
                                                <div class="col-12">
                                                    <div class="alert alert-info text-center">
                                                        <i class="feather icon-info-circle mr-2"></i>
                                                        Nenhum plano disponível no momento. Entre em contato com o administrador.
                                                    </div>
                                                </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-light text-center py-4">
                                            <div class="d-flex justify-content-center align-items-center">
                                                <i class="feather icon-shield text-success mr-2" style="font-size: 24px;"></i>
                                                <div>
                                                    <strong>Satisfação garantida ou seu dinheiro de volta</strong><br>
                                                    <small class="text-muted">Teste qualquer plano por 7 dias e cancele se não estiver satisfeito.</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                           
                                            <?php else: ?>
                                            <div class="alert alert-success text-center">
                                                <i class="feather icon-check-circle mr-2"></i>
                                                <span>Sua assinatura está ativa até a data de vencimento</span>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="card-footer bg-light">
                                            <div class="d-flex justify-content-between align-items-center small">
                                                <span><i class="feather icon-help-circle mr-1"></i> Precisa de ajuda?</span>
                                                <a href="https://wa.me/<?=$telefone_adm;?>?text=Preciso%20de%20ajuda" target="_blank" class="text-primary">Fale conosco</a>
                                            </div>
                                        </div>
                                    </div>
                                <?php
                                    } else {
                                ?>
                                    <!-- SEÇÃO DE PLANOS CORRIGIDA - MELHOR CONTRASTE E BOTÕES -->
                                    <div class="card">
                                        <div class="card-header bg-primary text-white">
                                            <h3 class="mb-0"><i class="feather icon-package mr-2"></i> Nossos Planos</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="text-center mb-4">
                                                <h4>Escolha o plano ideal para o seu negócio</h4>
                                            </div>
                                            
                                            <div class="row justify-content-center">
                                                <?php 
                                                // Consulta os planos ativos
                                                $sql_planos = "SELECT * FROM planos_online WHERE ativo = 1 ORDER BY preco ASC";
                                                $result_planos = mysqli_query($conn, $sql_planos);
                                                
                                                $i = 0;
                                                if ($result_planos && mysqli_num_rows($result_planos) > 0) {
                                                    $total_planos = mysqli_num_rows($result_planos);
                                                    
                                                    while ($plano = mysqli_fetch_assoc($result_planos)) {
                                                        $id_plano = $plano['id'];
                                                        $titulo_plano = $plano['titulo'];
                                                        $preco_plano = $plano['preco'];
                                                        $link_pagamento_plano = $plano['link_pagamento'];
                                                        
                                                        // O plano do meio é destacado
                                                        $isMiddlePlan = ($i == 1 || ($total_planos == 2 && $i == 0));
                                                        
                                                        // Consulta as features do plano
                                                        $features = [];
                                                        $sql_features = "SELECT feature FROM planos_features WHERE id_plano = $id_plano ORDER BY id";
                                                        $result_features = mysqli_query($conn, $sql_features);
                                                        
                                                        if ($result_features && mysqli_num_rows($result_features) > 0) {
                                                            while ($feature = mysqli_fetch_assoc($result_features)) {
                                                                $features[] = $feature['feature'];
                                                            }
                                                        }
                                                ?>
                                                <div class="col-lg-4 col-md-6 mb-4">
                                                    <div class="card pricing-card h-100 <?php echo $isMiddlePlan ? 'shadow border-primary' : ''; ?>">
                                                        <?php if($isMiddlePlan): ?>
                                                        <div class="ribbon-wrapper">
                                                            <div class="ribbon">Mais Popular</div>
                                                        </div>
                                                        <?php endif; ?>
                                                        
                                                        <div class="card-header text-center py-4 <?php echo $isMiddlePlan ? 'bg-primary text-white' : ''; ?>">
                                                            <h3 class="font-weight-bold mb-0"><?php echo htmlspecialchars($titulo_plano, ENT_QUOTES, 'UTF-8'); ?></h3>
                                                        </div>
                                                        
                                                        <div class="card-body d-flex flex-column">
                                                            <div class="text-center mb-4">
                                                                <div class="pricing-price">
                                                                    R$ <?php echo number_format(floatval($preco_plano), 2, ',', '.'); ?>
                                                                </div>
                                                                <div class="pricing-period">/ mês</div>
                                                            </div>
                                                            
                                                            <ul class="pricing-features list-unstyled">
                                                                <?php foreach($features as $feature): ?>
                                                                <li>
                                                                    <i class="feather icon-check-circle mr-2"></i>
                                                                    <?php echo htmlspecialchars($feature, ENT_QUOTES, 'UTF-8'); ?>
                                                                    <li>
                                                                    <i class="feather icon-check-circle mr-2"></i>
                                                                    <?php echo htmlspecialchars($feature, ENT_QUOTES, 'UTF-8'); ?>
                                                                </li>
                                                                <?php endforeach; ?>
                                                            </ul>
                                                            
                                                           <div class="mt-auto">
    <!-- Modificação aqui para concatenar o email ao link do plano -->
    <a href="<?php echo $link_pagamento_plano . (strpos($link_pagamento_plano, '?') !== false ? '&' : '?') . 'email=' . urlencode($email); ?>" target="_blank" 
       class="btn btn-block btn-lg <?php echo $isMiddlePlan ? 'btn-primary pulse-animation' : 'btn-outline-primary'; ?> btn-select-plan">
        Selecionar Plano
    </a>
</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                                        $i++;
                                                    }
                                                } else {
                                                ?>
                                                <div class="col-12">
                                                    <div class="alert alert-info text-center">
                                                        <i class="feather icon-info-circle mr-2"></i>
                                                        Nenhum plano disponível no momento. Entre em contato com o administrador.
                                                    </div>
                                                </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-light text-center py-4">
                                            <div class="d-flex justify-content-center align-items-center">
                                                <i class="feather icon-shield text-success mr-2" style="font-size: 24px;"></i>
                                                <div>
                                                    <strong>Satisfação garantida ou seu dinheiro de volta</strong><br>
                                                    <small class="text-muted">Teste qualquer plano por 7 dias e cancele se não estiver satisfeito.</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php
                                    }
                                }
                                ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->

<?php include 'footer.php'; ?>