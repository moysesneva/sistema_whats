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

include 'estilo.php';

include 'css_de_icones.php';

if (isset($_GET['pagina_nome'])) {
$pagina_nome_recebe = $_GET['pagina_nome'];
}else{
$pagina_nome_recebe = 0;    
}

$stmt_user = mysqli_prepare($conn, "SELECT * FROM login WHERE login = ?");
mysqli_stmt_bind_param($stmt_user, "s", $login);
mysqli_stmt_execute($stmt_user);
$query_busca_usuario = mysqli_stmt_get_result($stmt_user);
$total_busca_usuario = mysqli_num_rows($query_busca_usuario);

while($rows_usuarios = $query_busca_usuario->fetch_array()) {
    $nome  = Priletra($rows_usuarios['nome']);
    $img_perfil  = $rows_usuarios['perfil_img'];
    $autorizado  = $rows_usuarios['autorizado'];
    $tipo  = $rows_usuarios['tipo'];

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

$sql_busca_config = "SELECT * FROM config";
$query_busca_config = mysqli_query($conn, $sql_busca_config);
$total_busca_config = mysqli_num_rows($query_busca_config);

while($rows_config = mysqli_fetch_array($query_busca_config)) {
    $chave  = $rows_config['chave'];
    $validade  = $rows_config['validade'];
    $link_pagamento =$rows_config['link_pagamento'];
    $webhook =$rows_config['webhook'];
}

?>
<?php include 'header.php'; ?>

                <?php
// Buscar os dados existentes do banco logo no início
$stmt_cfg = mysqli_prepare($conn, "SELECT * FROM config WHERE chave = ?");
mysqli_stmt_bind_param($stmt_cfg, "s", $chave);
mysqli_stmt_execute($stmt_cfg);
$result = mysqli_stmt_get_result($stmt_cfg);
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $link_plano1 = $row['link_plano1'];
    $link_plano2 = $row['link_plano2'];
    $link_plano3 = $row['link_plano3'];
    $link_creditos = $row['link_creditos'];
}

// Verifica se os links de pagamento foram enviados
// Quando o formulário para salvar todos os links de uma vez é enviado
if (isset($_POST['chave']) && isset($_POST['salvar_links'])) {

    $chave = trim($_POST['chave']);
    $link_plano1 = !empty($_POST['link_plano1']) ? trim($_POST['link_plano1']) : null;
    $link_plano2 = !empty($_POST['link_plano2']) ? trim($_POST['link_plano2']) : null;
    $link_plano3 = !empty($_POST['link_plano3']) ? trim($_POST['link_plano3']) : null;
    $link_creditos = !empty($_POST['link_creditos']) ? trim($_POST['link_creditos']) : null;

    $stmt_upd = mysqli_prepare($conn,
        "UPDATE config SET link_plano1 = ?, link_plano2 = ?, link_plano3 = ?, link_creditos = ? WHERE chave = ?");
    mysqli_stmt_bind_param($stmt_upd, "sssss", $link_plano1, $link_plano2, $link_plano3, $link_creditos, $chave);

    if (mysqli_stmt_execute($stmt_upd)) {
        echo "<div class='alert alert-success'>Links de pagamento atualizados com sucesso!</div>";

        if (!empty($link_plano1)) {
            $stmt_p1 = mysqli_prepare($conn, "UPDATE planos_online SET link_pagamento = ? WHERE id = 1");
            mysqli_stmt_bind_param($stmt_p1, "s", $link_plano1);
            if (mysqli_stmt_execute($stmt_p1)) {
                echo "<div class='alert alert-info'>Link do plano Popular atualizado na tabela de planos!</div>";
            }
        }

        if (!empty($link_plano2)) {
            $stmt_p2 = mysqli_prepare($conn, "UPDATE planos_online SET link_pagamento = ? WHERE id = 2");
            mysqli_stmt_bind_param($stmt_p2, "s", $link_plano2);
            if (mysqli_stmt_execute($stmt_p2)) {
                echo "<div class='alert alert-info'>Link do plano Premium atualizado na tabela de planos!</div>";
            }
        }

        if (!empty($link_plano3)) {
            $stmt_p3 = mysqli_prepare($conn, "UPDATE planos_online SET link_pagamento = ? WHERE id = 3");
            mysqli_stmt_bind_param($stmt_p3, "s", $link_plano3);
            if (mysqli_stmt_execute($stmt_p3)) {
                echo "<div class='alert alert-info'>Link do plano Enterprise atualizado na tabela de planos!</div>";
            }
        }
    } else {
        echo "<div class='alert alert-danger'>Erro ao atualizar os links de pagamento. Tente novamente.</div>";
    }

    $stmt_sel = mysqli_prepare($conn, "SELECT * FROM config WHERE chave = ?");
    mysqli_stmt_bind_param($stmt_sel, "s", $chave);
    mysqli_stmt_execute($stmt_sel);
    $result = mysqli_stmt_get_result($stmt_sel);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $link_plano1 = $row['link_plano1'];
        $link_plano2 = $row['link_plano2'];
        $link_plano3 = $row['link_plano3'];
        $link_creditos = $row['link_creditos'];
    }
} 
// Quando o formulário para adicionar um plano individual é enviado
elseif (isset($_POST['adicionar_plano'])) {
    // Adicionar um plano específico
    $chave = trim($_POST['chave']);
    $plano = trim($_POST['plano']);
    $novo_link = !empty($_POST['novo_link']) ? trim($_POST['novo_link']) : null;

    $coluna = '';
    $plano_id = 0;
    $plano_nome = '';

    switch ($plano) {
        case 'plano1':
            $coluna = 'link_plano1';
            $plano_id = 1;
            $plano_nome = 'Popular';
            break;
        case 'plano2':
            $coluna = 'link_plano2';
            $plano_id = 2;
            $plano_nome = 'Premium';
            break;
        case 'plano3':
            $coluna = 'link_plano3';
            $plano_id = 3;
            $plano_nome = 'Enterprise';
            break;
        case 'creditos':
            $coluna = 'link_creditos';
            $plano_id = 0;
            $plano_nome = 'Créditos';
            break;
    }

    if (!empty($coluna)) {
        $stmt_ai = mysqli_prepare($conn, "UPDATE config SET $coluna = ? WHERE chave = ?");
        mysqli_stmt_bind_param($stmt_ai, "ss", $novo_link, $chave);

        if (mysqli_stmt_execute($stmt_ai)) {
            echo "<div class='alert alert-success'>Link de pagamento do plano $plano_nome adicionado com sucesso!</div>";

            if ($plano_id > 0 && !empty($novo_link)) {
                $stmt_po = mysqli_prepare($conn, "UPDATE planos_online SET link_pagamento = ? WHERE id = ?");
                mysqli_stmt_bind_param($stmt_po, "si", $novo_link, $plano_id);
                if (mysqli_stmt_execute($stmt_po)) {
                    echo "<div class='alert alert-info'>Link do plano $plano_nome atualizado na tabela de planos!</div>";
                } else {
                    echo "<div class='alert alert-warning'>Não foi possível atualizar a tabela de planos. Erro: " . mysqli_error($conn) . "</div>";
                }
            }
        } else {
            echo "<div class='alert alert-danger'>Erro ao adicionar o link de pagamento. Tente novamente.</div>";
        }

        $stmt_ri = mysqli_prepare($conn, "SELECT * FROM config WHERE chave = ?");
        mysqli_stmt_bind_param($stmt_ri, "s", $chave);
        mysqli_stmt_execute($stmt_ri);
        $result = mysqli_stmt_get_result($stmt_ri);
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $link_plano1 = $row['link_plano1'];
            $link_plano2 = $row['link_plano2'];
            $link_plano3 = $row['link_plano3'];
            $link_creditos = $row['link_creditos'];
        }
    }
}
// Quando o botão para apagar um plano é clicado
elseif (isset($_POST['apagar_plano'])) {
    $chave = trim($_POST['chave']);
    $plano = trim($_POST['plano']);

    $coluna = '';
    $plano_id = 0;
    $plano_nome = '';

    switch ($plano) {
        case 'plano1':
            $coluna = 'link_plano1';
            $plano_id = 1;
            $plano_nome = 'Popular';
            break;
        case 'plano2':
            $coluna = 'link_plano2';
            $plano_id = 2;
            $plano_nome = 'Premium';
            break;
        case 'plano3':
            $coluna = 'link_plano3';
            $plano_id = 3;
            $plano_nome = 'Enterprise';
            break;
        case 'creditos':
            $coluna = 'link_creditos';
            $plano_id = 0;
            $plano_nome = 'Créditos';
            break;
    }

    if (!empty($coluna)) {
        $stmt_ap = mysqli_prepare($conn, "UPDATE config SET $coluna = NULL WHERE chave = ?");
        mysqli_stmt_bind_param($stmt_ap, "s", $chave);

        if (mysqli_stmt_execute($stmt_ap)) {
            echo "<div class='alert alert-success'>Link de pagamento do plano $plano_nome removido com sucesso!</div>";

            if ($plano_id > 0) {
                $link_padrao = "https://seusite.com/pagar?plano=premium";
                $stmt_pp = mysqli_prepare($conn, "UPDATE planos_online SET link_pagamento = ? WHERE id = ?");
                mysqli_stmt_bind_param($stmt_pp, "si", $link_padrao, $plano_id);
                if (mysqli_stmt_execute($stmt_pp)) {
                    echo "<div class='alert alert-info'>Link do plano $plano_nome redefinido na tabela de planos!</div>";
                } else {
                    echo "<div class='alert alert-warning'>Não foi possível atualizar a tabela de planos. Erro: " . mysqli_error($conn) . "</div>";
                }
            }
        } else {
            echo "<div class='alert alert-danger'>Erro ao remover o link de pagamento. Tente novamente.</div>";
        }

        $stmt_ra = mysqli_prepare($conn, "SELECT * FROM config WHERE chave = ?");
        mysqli_stmt_bind_param($stmt_ra, "s", $chave);
        mysqli_stmt_execute($stmt_ra);
        $result = mysqli_stmt_get_result($stmt_ra);
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $link_plano1 = $row['link_plano1'];
            $link_plano2 = $row['link_plano2'];
            $link_plano3 = $row['link_plano3'];
            $link_creditos = $row['link_creditos'];
        }
    }

} elseif (isset($_POST['adicionar_plano'])) {
    $chave = trim($_POST['chave']);
    $plano = trim($_POST['plano']);
    $novo_link = !empty($_POST['novo_link']) ? trim($_POST['novo_link']) : null;

    $coluna = '';
    switch ($plano) {
        case 'plano1': $coluna = 'link_plano1'; break;
        case 'plano2': $coluna = 'link_plano2'; break;
        case 'plano3': $coluna = 'link_plano3'; break;
        case 'creditos': $coluna = 'link_creditos'; break;
    }

    if (!empty($coluna)) {
        $stmt_ai2 = mysqli_prepare($conn, "UPDATE config SET $coluna = ? WHERE chave = ?");
        mysqli_stmt_bind_param($stmt_ai2, "ss", $novo_link, $chave);

        if (mysqli_stmt_execute($stmt_ai2)) {
            echo "<div class='alert alert-success'>Link de pagamento adicionado com sucesso!</div>";
        } else {
            echo "<div class='alert alert-danger'>Erro ao adicionar o link de pagamento. Tente novamente.</div>";
        }

        $stmt_ri2 = mysqli_prepare($conn, "SELECT * FROM config WHERE chave = ?");
        mysqli_stmt_bind_param($stmt_ri2, "s", $chave);
        mysqli_stmt_execute($stmt_ri2);
        $result = mysqli_stmt_get_result($stmt_ri2);
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $link_plano1 = $row['link_plano1'];
            $link_plano2 = $row['link_plano2'];
            $link_plano3 = $row['link_plano3'];
            $link_creditos = $row['link_creditos'];
        }
    }
} elseif (isset($_POST['apagar_plano'])) {
    $chave = trim($_POST['chave']);
    $plano = trim($_POST['plano']);

    $coluna = '';
    switch ($plano) {
        case 'plano1': $coluna = 'link_plano1'; break;
        case 'plano2': $coluna = 'link_plano2'; break;
        case 'plano3': $coluna = 'link_plano3'; break;
        case 'creditos': $coluna = 'link_creditos'; break;
    }

    if (!empty($coluna)) {
        $stmt_ap2 = mysqli_prepare($conn, "UPDATE config SET $coluna = NULL WHERE chave = ?");
        mysqli_stmt_bind_param($stmt_ap2, "s", $chave);

        if (mysqli_stmt_execute($stmt_ap2)) {
            echo "<div class='alert alert-success'>Link de pagamento removido com sucesso!</div>";
        } else {
            echo "<div class='alert alert-danger'>Erro ao remover o link de pagamento. Tente novamente.</div>";
        }

        $stmt_ra2 = mysqli_prepare($conn, "SELECT * FROM config WHERE chave = ?");
        mysqli_stmt_bind_param($stmt_ra2, "s", $chave);
        mysqli_stmt_execute($stmt_ra2);
        $result = mysqli_stmt_get_result($stmt_ra2);
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $link_plano1 = $row['link_plano1'];
            $link_plano2 = $row['link_plano2'];
            $link_plano3 = $row['link_plano3'];
            $link_creditos = $row['link_creditos'];
        }
    }
}

// Função para truncar URLs longas
function truncarURL($url, $maxLength = 40) {
    if (strlen($url) <= $maxLength) {
        return $url;
    }
    
    $baseUrl = parse_url($url, PHP_URL_SCHEME) . '://' . parse_url($url, PHP_URL_HOST);
    $truncated = substr($baseUrl, 0, $maxLength - 3) . '...';
    
    return $truncated;
}
?><div class="container mt-5">
    <h2 class="text-center mb-4">Chave Edita Código</h2>
    <!-- Exibição das chaves e validade -->
    <div class="card p-4 shadow-sm">
        <div class="form-group">
            <h4><label>Sua Chave: <b class="text-primary"><?= $chave; ?></b></label></h4>
        </div>
        <?php 
        // Verifica se tem pelo menos um link configurado
        $temLinkConfigurado = !empty($link_plano1) || !empty($link_plano2) || !empty($link_plano3) || !empty($link_creditos);
        
        if (!$temLinkConfigurado) { ?>
            <!-- Formulário para adicionar links de pagamento -->
            <form action="" method="post">
                <input type="hidden" name="chave" value="<?= $chave; ?>">
                <input type="hidden" name="salvar_links" value="1">
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Você pode configurar apenas os planos que desejar. Deixe em branco os planos que não quiser usar.
                </div>
                
                <div class="form-group">
                    <label for="link_plano1"><strong>Plano 1:</strong></label>
                    <input type="url" class="form-control" id="link_plano1" name="link_plano1" 
                           placeholder="Digite o link de pagamento para o Plano 1">
                    <small class="text-muted">Opcional: Deixe em branco se não utilizar este plano</small>
                </div>
                
                <div class="form-group mt-3">
                    <label for="link_plano2"><strong>Plano 2:</strong></label>
                    <input type="url" class="form-control" id="link_plano2" name="link_plano2" 
                           placeholder="Digite o link de pagamento para o Plano 2">
                    <small class="text-muted">Opcional: Deixe em branco se não utilizar este plano</small>
                </div>
                
                <div class="form-group mt-3">
                    <label for="link_plano3"><strong>Plano 3:</strong></label>
                    <input type="url" class="form-control" id="link_plano3" name="link_plano3" 
                           placeholder="Digite o link de pagamento para o Plano 3">
                    <small class="text-muted">Opcional: Deixe em branco se não utilizar este plano</small>
                </div>
                
                <div class="form-group mt-3">
                    <label for="link_creditos"><strong>Créditos Extras de IA:</strong></label>
                    <input type="url" class="form-control" id="link_creditos" name="link_creditos" 
                           placeholder="Digite o link de pagamento para Créditos Extras">
                    <small class="text-muted">Opcional: Deixe em branco se não utilizar esta opção</small>
                </div>
                
                <button type="submit" class="btn btn-success mt-4">Salvar Links de Pagamento</button>
            </form>
        <?php } else { ?>
            <!-- Exibição dos links de pagamento e ações -->
            <div id="viewMode">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Links de Pagamento Configurados</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($link_plano1)) { ?>
                        <div class="form-group">
                            <label class="font-weight-bold">Plano 1:</label>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control bg-light" id="link_plano1_display" value="<?= truncarURL($link_plano1); ?>" readonly>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" onclick="copyLink('link_plano1_full')">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                                <input type="hidden" id="link_plano1_full" value="<?= $link_plano1; ?>">
                            </div>
                            <div class="mt-2">
                                <form method="post" class="d-inline mr-1 mb-1">
                                    <input type="hidden" name="chave" value="<?= $chave; ?>">
                                    <input type="hidden" name="plano" value="plano1">
                                    <input type="hidden" name="apagar_plano" value="1">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja remover este link?')">
                                        <i class="fas fa-trash"></i> Remover
                                    </button>
                                </form>
                                <button type="button" class="btn btn-sm btn-secondary" onclick="copyWebhook('plano1')">
                                    <i class="fas fa-link"></i> Copiar Webhook
                                </button>
                            </div>
                        </div>
                        <?php } ?>
                        
                        <?php if (!empty($link_plano2)) { ?>
                        <div class="form-group mt-4">
                            <label class="font-weight-bold">Plano 2:</label>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control bg-light" id="link_plano2_display" value="<?= truncarURL($link_plano2); ?>" readonly>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" onclick="copyLink('link_plano2_full')">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                                <input type="hidden" id="link_plano2_full" value="<?= $link_plano2; ?>">
                            </div>
                            <div class="mt-2">
                                <form method="post" class="d-inline mr-1 mb-1">
                                    <input type="hidden" name="chave" value="<?= $chave; ?>">
                                    <input type="hidden" name="plano" value="plano2">
                                    <input type="hidden" name="apagar_plano" value="1">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja remover este link?')">
                                        <i class="fas fa-trash"></i> Remover
                                    </button>
                                </form>
                                <button type="button" class="btn btn-sm btn-secondary" onclick="copyWebhook('plano2')">
                                    <i class="fas fa-link"></i> Copiar Webhook
                                </button>
                            </div>
                        </div>
                        <?php } ?>
                        
                        <?php if (!empty($link_plano3)) { ?>
                        <div class="form-group mt-4">
                            <label class="font-weight-bold">Plano 3:</label>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control bg-light" id="link_plano3_display" value="<?= truncarURL($link_plano3); ?>" readonly>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" onclick="copyLink('link_plano3_full')">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                                <input type="hidden" id="link_plano3_full" value="<?= $link_plano3; ?>">
                            </div>
                            <div class="mt-2">
                                <form method="post" class="d-inline mr-1 mb-1">
                                    <input type="hidden" name="chave" value="<?= $chave; ?>">
                                    <input type="hidden" name="plano" value="plano3">
                                    <input type="hidden" name="apagar_plano" value="1">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja remover este link?')">
                                        <i class="fas fa-trash"></i> Remover
                                    </button>
                                </form>
                                <button type="button" class="btn btn-sm btn-secondary" onclick="copyWebhook('plano3')">
                                    <i class="fas fa-link"></i> Copiar Webhook
                                </button>
                            </div>
                        </div>
                        <?php } ?>
                        
                        <?php if (!empty($link_creditos)) { ?>
                        <div class="form-group mt-4">
                            <label class="font-weight-bold">Créditos Extras de IA:</label>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control bg-light" id="link_creditos_display" value="<?= truncarURL($link_creditos); ?>" readonly>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" onclick="copyLink('link_creditos_full')">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                                <input type="hidden" id="link_creditos_full" value="<?= $link_creditos; ?>">
                            </div>
                            <div class="mt-2">
                                <form method="post" class="d-inline mr-1 mb-1">
                                    <input type="hidden" name="chave" value="<?= $chave; ?>">
                                    <input type="hidden" name="plano" value="creditos">
                                    <input type="hidden" name="apagar_plano" value="1">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja remover este link?')">
                                        <i class="fas fa-trash"></i> Remover
                                    </button>
                                </form>
                                <button type="button" class="btn btn-sm btn-secondary" onclick="copyWebhook('creditos')">
                                    <i class="fas fa-link"></i> Copiar Webhook
                                </button>
                            </div>
                        </div>
                        <?php } ?>
                        
                        <!-- Seção para adicionar novos links -->
                        <?php if (empty($link_plano1) || empty($link_plano2) || empty($link_plano3) || empty($link_creditos)) { ?>
                        <div class="alert alert-info mt-4">
                            <div class="text-center mb-2">
                                <i class="fas fa-info-circle"></i> Adicionar novos links:
                            </div>
                            <div class="d-flex flex-wrap justify-content-center">
                                <?php if (empty($link_plano1)) { ?>
                                <button type="button" class="btn btn-sm btn-dark m-1" onclick="adicionarNovoLink('plano1', '<?= $chave; ?>')">
                                    <i class="fas fa-plus"></i> Plano 1
                                </button>
                                <?php } ?>
                                
                                <?php if (empty($link_plano2)) { ?>
                                <button type="button" class="btn btn-sm btn-dark m-1" onclick="adicionarNovoLink('plano2', '<?= $chave; ?>')">
                                    <i class="fas fa-plus"></i> Plano 2
                                </button>
                                <?php } ?>
                                
                                <?php if (empty($link_plano3)) { ?>
                                <button type="button" class="btn btn-sm btn-dark m-1" onclick="adicionarNovoLink('plano3', '<?= $chave; ?>')">
                                    <i class="fas fa-plus"></i> Plano 3
                                </button>
                                <?php } ?>
                                
                                <?php if (empty($link_creditos)) { ?>
                                <button type="button" class="btn btn-sm btn-dark m-1" onclick="adicionarNovoLink('creditos', '<?= $chave; ?>')">
                                    <i class="fas fa-plus"></i> Créditos Extras de IA
                                </button>
                                <?php } ?>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            
            <!-- Formulário para adicionar um novo link (inicialmente oculto) -->
            <div id="adicionarNovoLink" style="display: none;" class="mt-4">
                <h5 class="mb-3" id="tituloAdicionarPlano"></h5>
                <form action="" method="post">
                    <input type="hidden" name="chave" value="<?= $chave; ?>">
                    <input type="hidden" name="plano" id="planoAdicionar" value="">
                    <input type="hidden" name="adicionar_plano" value="1">
                    
                    <div class="form-group">
                        <label for="novo_link_adicionar"><strong>Link de Pagamento:</strong></label>
                        <input type="url" class="form-control" id="novo_link_adicionar" name="novo_link" 
                               placeholder="Digite o link de pagamento completo">
                    </div>
                    
                    <div class="mt-3">
                        <button type="submit" class="btn btn-success">Adicionar Link</button>
                        <button type="button" class="btn btn-secondary ml-2" onclick="document.getElementById('adicionarNovoLink').style.display = 'none'; document.getElementById('viewMode').style.display = 'block';">Cancelar</button>
                    </div>
                </form>
            </div>
            
            <!-- Scripts -->
            <script>
                // Função para copiar link para área de transferência
                function copyLink(elementId) {
                    var copyText = document.getElementById(elementId);
                    
                    // Cria um elemento de texto temporário
                    var tempInput = document.createElement("input");
                    tempInput.value = copyText.value;
                    document.body.appendChild(tempInput);
                    
                    // Seleciona e copia o conteúdo
                    tempInput.select();
                    document.execCommand("copy");
                    
                    // Remove o elemento temporário
                    document.body.removeChild(tempInput);
                    
                    // Feedback visual no botão
                    var btn = event.currentTarget;
                    var originalHTML = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-check"></i>';
                    
                    setTimeout(function() {
                        btn.innerHTML = originalHTML;
                    }, 1500);
                }
                
                // Função para copiar webhook para área de transferência
                function copyWebhook(planoTipo) {
                    // Usa diretamente o valor PHP para webhook e chave
                    var webhook = "<?= $webhook; ?>";
                    var chave = "<?= $chave; ?>";
                    
                    // Concatena webhook + plano + chave
                    var webhookUrl = webhook + planoTipo + '.php?chave=' + chave;
                    
                    // Cria um elemento de texto temporário
                    var tempInput = document.createElement("input");
                    tempInput.value = webhookUrl;
                    document.body.appendChild(tempInput);
                    
                    // Seleciona e copia o conteúdo
                    tempInput.select();
                    document.execCommand("copy");
                    
                    // Remove o elemento temporário
                    document.body.removeChild(tempInput);
                    
                    // Feedback visual no botão
                    var btn = event.currentTarget;
                    var originalHTML = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-check"></i> Copiado!';
                    
                    setTimeout(function() {
                        btn.innerHTML = originalHTML;
                    }, 1500);
                }
                
                // Função para adicionar um novo link
                function adicionarNovoLink(plano, chave) {
                    document.getElementById('viewMode').style.display = 'none';
                    document.getElementById('adicionarNovoLink').style.display = 'block';
                    
                    document.getElementById('planoAdicionar').value = plano;
                    document.getElementById('novo_link_adicionar').value = '';
                    
                    // Define o título de acordo com o plano
                    var titulo = '';
                    switch(plano) {
                        case 'plano1':
                            titulo = 'Adicionar Link - Plano 1';
                            break;
                        case 'plano2':
                            titulo = 'Adicionar Link - Plano 2';
                            break;
                        case 'plano3':
                            titulo = 'Adicionar Link - Plano 3';
                            break;
                        case 'creditos':
                            titulo = 'Adicionar Link - Créditos Extras de IA';
                            break;
                    }
                    document.getElementById('tituloAdicionarPlano').innerText = titulo;
                }
            </script>
        <?php } ?>
    </div>
</div>

<!-- Custom CSS -->
<style>
    .container {
        max-width: 600px;
        margin: auto;
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    h2 {
        color: #1e3a8a;
        font-weight: bold;
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .btn-primary {
        background-color: #4a5568;
        border-color: #4a5568;
    }
</style>

<?php include 'footer.php'; ?>