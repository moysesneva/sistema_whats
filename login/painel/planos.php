<?php
session_start();
include 'funcoes.php';

if(!isset($_SESSION['login'])) {
VaiPara('login.php');
} 
#error_reporting(0);
#ini_set("display_errors", 0 );
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




?>
<?php include 'header.php'; ?>




    
    
    
    <?php

// 1) Processa o POST de remover módulo
if (isset($_POST['remover_modulo']) && !empty($_POST['remover_modulo'])) {
    $modulo_id = (int) $_POST['remover_modulo'];
    $plano_nome = trim($_POST['plano']);

    $stmt_remove = mysqli_prepare($conn,
        "DELETE FROM planos_clientes WHERE id = ? AND nome_plano = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt_remove, "is", $modulo_id, $plano_nome);

    if (mysqli_stmt_execute($stmt_remove)) {
        echo '<div class="alert alert-success">Módulo removido com sucesso!</div>';
    } else {
        echo '<div class="alert alert-danger">Erro ao remover módulo: ' . mysqli_error($conn) . '</div>';
    }
}

// 2) Processa o POST de adicionar módulo
if (isset($_POST['adicionar_modulo']) && !empty($_POST['modulo']) && !empty($_POST['plano'])) {
    $modulo_id = (int) $_POST['modulo'];
    $nome_plano = trim($_POST['plano']);

    $stmt_busca = mysqli_prepare($conn, "SELECT nome_modulo, tipo FROM modulos_lista WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt_busca, "i", $modulo_id);
    mysqli_stmt_execute($stmt_busca);
    $res_busca = mysqli_stmt_get_result($stmt_busca);

    if ($res_busca && mysqli_num_rows($res_busca) > 0) {
        $row = mysqli_fetch_assoc($res_busca);
        $nome_modulo = $row['nome_modulo'];
        $tipo_modulo = $row['tipo'];

        $stmt_verifica = mysqli_prepare($conn,
            "SELECT id FROM planos_clientes WHERE nome_plano = ? AND nome_modulo = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt_verifica, "ss", $nome_plano, $nome_modulo);
        mysqli_stmt_execute($stmt_verifica);
        $res_verifica = mysqli_stmt_get_result($stmt_verifica);

        if ($res_verifica && mysqli_num_rows($res_verifica) > 0) {
            echo '<div class="alert alert-warning">Este módulo já está adicionado ao plano!</div>';
        } else {
            $stmt_insere = mysqli_prepare($conn,
                "INSERT INTO planos_clientes (nome_plano, nome_modulo, tipo, date) VALUES (?, ?, ?, NOW())");
            mysqli_stmt_bind_param($stmt_insere, "sss", $nome_plano, $nome_modulo, $tipo_modulo);
            if (mysqli_stmt_execute($stmt_insere)) {
                echo '<div class="alert alert-success">Módulo adicionado com sucesso! (Tipo: ' . htmlspecialchars($tipo_modulo, ENT_QUOTES) . ')</div>';
            } else {
                echo '<div class="alert alert-danger">Erro ao adicionar: ' . mysqli_error($conn) . '</div>';
            }
        }
    } else {
        echo '<div class="alert alert-warning">Módulo não encontrado.</div>';
    }
}

// 3) Busca todos os módulos disponíveis com tipo
$sql_mods = "
    SELECT id, nome_modulo, tipo
    FROM modulos_lista
    ORDER BY tipo ASC, nome_modulo ASC
";
$res_mods = mysqli_query($conn, $sql_mods);

// Inclui a conexão
include 'conn.php';
?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Configuração de Planos e Módulos</h2>
    
    <div class="row">
        <!-- PLANO 1 -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Plano 1</h5>
                </div>
                <div class="card-body">
                    <!-- Lista de módulos já adicionados ao plano -->
                    <div class="mb-3">
                        <label class="font-weight-bold">Módulos Ativos:</label>
                        <div class="modulos-container">
                            <?php
                            // Define o plano
                            $plano = 'plano1';

                            // Consulta estruturada ao banco incluindo tipo
                            $sql = "
                                SELECT 
                                    id,
                                    nome_modulo,
                                    tipo,
                                    date
                                FROM 
                                    planos_clientes 
                                WHERE 
                                    nome_plano = '{$plano}'
                                ORDER BY 
                                    tipo ASC, nome_modulo ASC
                            ";
                            $resultado = mysqli_query($conn, $sql);

                            // Exibe os módulos
                            if ($resultado && mysqli_num_rows($resultado) > 0) {
                                while ($modulo = mysqli_fetch_assoc($resultado)) {
                                    // Define a cor do badge baseado no tipo
                                    $badge_class = '';
                                 
                                    
                                    echo '<div class="modulo-item p-2 bg-light mb-2 rounded">';
                                        echo '<div class="d-flex justify-content-between align-items-center">';
                                            echo '<div>';
                                                echo '<span>' . htmlspecialchars($modulo['nome_modulo'], ENT_QUOTES, 'UTF-8') . '</span>';
                                                echo '<br><small class="badge ' . $badge_class . '">' . $tipo_nome . '</small>';
                                            echo '</div>';
                                            echo '<form method="post" class="d-inline">';
                                                echo '<input type="hidden" name="remover_modulo" value="' . $modulo['id'] . '">';
                                                echo '<input type="hidden" name="plano" value="' . $plano . '">';
                                                echo '<button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Tem certeza que deseja remover este módulo?\')">';
                                                    echo '<i class="fas fa-trash"></i>';
                                                echo '</button>';
                                            echo '</form>';
                                        echo '</div>';
                                    echo '</div>';
                                }
                            } else {
                                echo '<p class="text-muted">Nenhum módulo ativo.</p>';
                            }
                            ?>
                        </div>
                    </div>

                    <!-- Adicionar novo módulo -->
                    <form method="post">
                        <div class="form-group">
                            <label for="modulo_plano1"><strong>Adicionar Módulo:</strong></label>
                            <select class="form-control" id="modulo_plano1" name="modulo" required>
                                <option value="">Selecione um módulo</option>
                                <?php 
                                // Reset do ponteiro para reutilizar os resultados
                                mysqli_data_seek($res_mods, 0);
                                while ($mod = mysqli_fetch_assoc($res_mods)): 
                                    // Define o nome do tipo para exibição
                                   
                                ?>
                                    <option value="<?= $mod['id'] ?>">
                                        <?= htmlspecialchars($mod['nome_modulo'], ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endwhile ?>
                            </select>
                        </div>
                        
                        <input type="hidden" name="plano" value="plano1">
                        <button type="submit" name="adicionar_modulo" class="btn btn-success mt-2">
                            <i class="fas fa-plus"></i> Adicionar ao Plano
                        </button>
                    </form>
                </div>
                <div class="card-footer">
                    <small class="text-muted">Última atualização: <?php echo date('d/m/Y H:i'); ?></small>
                </div>
            </div>
        </div>

        <!-- PLANO 2 -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Plano 2</h5>
                </div>
                <div class="card-body">
                    <!-- Lista de módulos já adicionados ao plano -->
                    <div class="mb-3">
                        <label class="font-weight-bold">Módulos Ativos:</label>
                        <div class="modulos-container">
                            <?php
                            // Define o plano
                            $plano2 = 'plano2';

                            // Consulta estruturada ao banco incluindo tipo
                            $sql2 = "
                                SELECT 
                                    id,
                                    nome_modulo,
                                    tipo,
                                    date
                                FROM 
                                    planos_clientes 
                                WHERE 
                                    nome_plano = '{$plano2}'
                                ORDER BY 
                                    tipo ASC, nome_modulo ASC
                            ";
                            $resultado2 = mysqli_query($conn, $sql2);

                            // Exibe os módulos
                            if ($resultado2 && mysqli_num_rows($resultado2) > 0) {
                                while ($modulo2 = mysqli_fetch_assoc($resultado2)) {
                                    // Define a cor do badge baseado no tipo
                                    $badge_class = '';
                                    switch($modulo2['tipo']) {
                                        case '1':
                                            $badge_class = 'badge-success';
                                  
                                            break;
                                        case '2':
                                            $badge_class = 'badge-info';
                                          
                                            break;
                                        case '3':
                                            $badge_class = 'badge-warning';
                                            
                                            break;
                                        default:
                                            $badge_class = 'badge-secondary';
                                           $modulo2['tipo'];
                                    }
                                    
                                    echo '<div class="modulo-item p-2 bg-light mb-2 rounded">';
                                        echo '<div class="d-flex justify-content-between align-items-center">';
                                            echo '<div>';
                                                echo '<span>' . htmlspecialchars($modulo2['nome_modulo'], ENT_QUOTES, 'UTF-8') . '</span>';
                                                echo '<br><small class="badge ' . $badge_class . '">' . $tipo_nome . '</small>';
                                            echo '</div>';
                                            echo '<form method="post" class="d-inline">';
                                                echo '<input type="hidden" name="remover_modulo" value="' . $modulo2['id'] . '">';
                                                echo '<input type="hidden" name="plano" value="' . $plano2 . '">';
                                                echo '<button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Tem certeza que deseja remover este módulo?\')">';
                                                    echo '<i class="fas fa-trash"></i>';
                                                echo '</button>';
                                            echo '</form>';
                                        echo '</div>';
                                    echo '</div>';
                                }
                            } else {
                                echo '<p class="text-muted">Nenhum módulo ativo.</p>';
                            }
                            ?>
                        </div>
                    </div>
                    
                    <!-- Adicionar novo módulo -->
                    <form method="post">
                        <div class="form-group">
                            <label for="modulo_plano2"><strong>Adicionar Módulo:</strong></label>
                            <select class="form-control" id="modulo_plano2" name="modulo" required>
                                <option value="">Selecione um módulo</option>
                                <?php 
                                // Reset do ponteiro para reutilizar os resultados
                                mysqli_data_seek($res_mods, 0);
                                while ($mod2 = mysqli_fetch_assoc($res_mods)): 
                                    // Define o nome do tipo para exibição
                                  
                                ?>
                                    <option value="<?= $mod2['id'] ?>">
                                        <?= htmlspecialchars($mod2['nome_modulo'], ENT_QUOTES, 'UTF-8') ?> 
                                    </option>
                                <?php endwhile ?>
                            </select>
                        </div>
                        
                        <input type="hidden" name="plano" value="plano2">
                        <button type="submit" name="adicionar_modulo" class="btn btn-success mt-2">
                            <i class="fas fa-plus"></i> Adicionar ao Plano
                        </button>
                    </form>
                </div>
                <div class="card-footer">
                    <small class="text-muted">Última atualização: <?php echo date('d/m/Y H:i'); ?></small>
                </div>
            </div>
        </div>
        
        <!-- PLANO 3 -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Plano 3</h5>
                </div>
                <div class="card-body">
                    <!-- Lista de módulos já adicionados ao plano -->
                    <div class="mb-3">
                        <label class="font-weight-bold">Módulos Ativos:</label>
                        <div class="modulos-container">
                            <?php
                            // Define o plano
                            $plano3 = 'plano3';

                            // Consulta estruturada ao banco incluindo tipo
                            $sql3 = "
                                SELECT 
                                    id,
                                    nome_modulo,
                                    tipo,
                                    date
                                FROM 
                                    planos_clientes 
                                WHERE 
                                    nome_plano = '{$plano3}'
                                ORDER BY 
                                    tipo ASC, nome_modulo ASC
                            ";
                            $resultado3 = mysqli_query($conn, $sql3);

                            // Exibe os módulos
                            if ($resultado3 && mysqli_num_rows($resultado3) > 0) {
                                while ($modulo3 = mysqli_fetch_assoc($resultado3)) {
                                    // Define a cor do badge baseado no tipo
                                    $badge_class = '';
                                   
                                    
                                    echo '<div class="modulo-item p-2 bg-light mb-2 rounded">';
                                        echo '<div class="d-flex justify-content-between align-items-center">';
                                            echo '<div>';
                                                echo '<span>' . htmlspecialchars($modulo3['nome_modulo'], ENT_QUOTES, 'UTF-8') . '</span>';
                                                echo '<br><small class="badge ' . $badge_class . '">' . $tipo_nome . '</small>';
                                            echo '</div>';
                                            echo '<form method="post" class="d-inline">';
                                                echo '<input type="hidden" name="remover_modulo" value="' . $modulo3['id'] . '">';
                                                echo '<input type="hidden" name="plano" value="' . $plano3 . '">';
                                                echo '<button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Tem certeza que deseja remover este módulo?\')">';
                                                    echo '<i class="fas fa-trash"></i>';
                                                echo '</button>';
                                            echo '</form>';
                                        echo '</div>';
                                    echo '</div>';
                                }
                            } else {
                                echo '<p class="text-muted">Nenhum módulo ativo.</p>';
                            }
                            ?>
                        </div>
                    </div>
                    
                    <!-- Adicionar novo módulo -->
                    <form method="post">
                        <div class="form-group">
                            <label for="modulo_plano3"><strong>Adicionar Módulo:</strong></label>
                            <select class="form-control" id="modulo_plano3" name="modulo" required>
                                <option value="">Selecione um módulo</option>
                                <?php 
                                // Reset do ponteiro para reutilizar os resultados
                                mysqli_data_seek($res_mods, 0);
                                while ($mod3 = mysqli_fetch_assoc($res_mods)): 
                                    // Define o nome do tipo para exibição
                         
                                ?>
                                    <option value="<?= $mod3['id'] ?>">
                                        <?= htmlspecialchars($mod3['nome_modulo'], ENT_QUOTES, 'UTF-8') ?> 
                                    </option>
                                <?php endwhile ?>
                            </select>
                        </div>
                        
                        <input type="hidden" name="plano" value="plano3">
                        <button type="submit" name="adicionar_modulo" class="btn btn-success mt-2">
                            <i class="fas fa-plus"></i> Adicionar ao Plano
                        </button>
                    </form>
                </div>
                <div class="card-footer">
                    <small class="text-muted">Última atualização: <?php echo date('d/m/Y H:i'); ?></small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Resumo dos Planos -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                
            </div>
        </div>
    </div>
</div>

<!-- Estilo adicional para os cartões de planos -->
<style>
.modulos-container {
    max-height: 250px;
    overflow-y: auto;
    border: 1px solid #ced4da;
    border-radius: .25rem;
    padding: 10px;
    background-color: #f8f9fa;
}

.modulo-item {
    transition: all 0.2s ease;
}

.modulo-item:hover {
    background-color: #e9ecef !important;
}

.card {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.2);
}

.badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    margin: 2px;
}

.badge-success {
    background-color: #28a745;
}

.badge-info {
    background-color: #17a2b8;
}

.badge-warning {
    background-color: #ffc107;
    color: #212529;
}

.badge-secondary {
    background-color: #6c757d;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Adiciona confirmação para remoção de módulos
    document.querySelectorAll('button[type="submit"]').forEach(function(btn) {
        if (btn.innerHTML.includes('trash')) {
            btn.addEventListener('click', function(e) {
                if (!confirm('Tem certeza que deseja remover este módulo do plano?')) {
                    e.preventDefault();
                }
            });
        }
    });
});
</script>

<?php include 'footer.php'; ?>