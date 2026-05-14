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


$sql_busca_usuario = "SELECT * FROM login WHERE login = '$login'";
$query_busca_usuario = mysqli_query($conn, $sql_busca_usuario);
$total_busca_usuario = mysqli_num_rows($query_busca_usuario);

while($rows_usuarios = mysqli_fetch_array($query_busca_usuario)) {
    $nome  = Priletra($rows_usuarios['nome']);
    $img_perfil  = $rows_usuarios['perfil_img'];
    $autorizado  = $rows_usuarios['autorizado'];
    $tipo  = $rows_usuarios['tipo'];
    $usuario_api = $rows_usuarios['usuario_api'];

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
<!DOCTYPE html>
<html lang="pt-br">


  

</style>
<head>
    <title><?=$titulo;?></title>
    <!-- HTML5 Shim and Respond.js IE10 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 10]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
      <![endif]-->
    <!-- Meta -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="#">
    <meta name="keywords" content="Admin , Responsive, Landing, Bootstrap, App, Template, Mobile, iOS, Android, apple, creative app">
    <meta name="author" content="#">
    <!-- Favicon icon -->
    <link rel="icon" href="<?=$icon;?>" type="image/x-icon">
    <!-- Google font-->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600" rel="stylesheet">
    <!-- Required Fremwork -->
    <link rel="stylesheet" type="text/css" href="..\files\bower_components\bootstrap\css\bootstrap.min.css">
    <!-- feather Awesome -->
    <link rel="stylesheet" type="text/css" href="..\files\assets\icon\feather\css\feather.css">
    <!-- Style.css -->
    <link rel="stylesheet" type="text/css" href="..\files\assets\css\style.css">
    <link rel="stylesheet" type="text/css" href="..\files\assets\css\jquery.mCustomScrollbar.css">
</head>

<body>
    <!-- Pre-loader start -->
    <div class="theme-loader">
        <div class="ball-scale">
            <div class='contain'>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Pre-loader end -->
    <div id="pcoded" class="pcoded">
        <div class="pcoded-overlay-box"></div>
        <div class="pcoded-container navbar-wrapper">

            <nav class="navbar header-navbar pcoded-header">
                <div class="navbar-wrapper">

                    <div class="navbar-logo">
                        <a class="mobile-menu" id="mobile-collapse" href="#!">
                            <i class="feather icon-menu"></i>
                        </a>
                        <a href="index.php">
                            <img class="img-fluid" src="<?=$logo;?>" alt="Theme-Logo" style="width: 150px; height: 30px;">
                        </a>
                        <a class="mobile-options">
                            <i class="feather icon-more-horizontal"></i>
                        </a>
                    </div>

                    <div class="navbar-container container-fluid">
                        <ul class="nav-left">
                            <li class="header-search">
                                <div class="main-search morphsearch-search">
                                    <div class="input-group">
                                        <span class="input-group-addon search-close"><i class="feather icon-x"></i></span>
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                            </li>
                            <li>
                                <a href="#!" onclick="javascript:toggleFullScreen()">
                                    <i class="feather icon-maximize full-screen"></i>
                                </a>
                            </li>
                        </ul>
                        <ul class="nav-right">
                          
                            </li>
                            <li class="user-profile header-notification">
                                <div class="dropdown-primary dropdown">
                                    <div class="dropdown-toggle" data-toggle="dropdown">
                                        <img src="<?=$img_perfil;?>" class="img-radius" alt="User-Profile-Image">
                                        <span><?=$nome?></span>
                                        <i class="feather icon-chevron-down"></i>
                                    </div>
                                    <ul class="show-notification profile-notification dropdown-menu" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                                        <li>
                                            <a href="config.php">
                                                <i class="feather icon-settings"></i> Configurações
                                            </a>
                                        </li>
                                        <li>
                                            <a href="perfil.php">
                                                <i class="feather icon-user"></i> Perfil
                                            </a>
                                        </li>
                                       
                                        
                                        <li>
                                            <a href="sair.php">
                                                <i class="feather icon-log-out"></i> Sair
                                            </a>
                                        </li>
                                    </ul>

                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Sidebar chat start -->
          

   
            <!-- Sidebar inner chat end-->
            <div class="pcoded-main-container">
                <div class="pcoded-wrapper">
                    <nav class="pcoded-navbar">
                        <div class="pcoded-inner-navbar main-menu">
                            <div class="pcoded-navigatio-lavel">Navegação</div>
                            <ul class="pcoded-item pcoded-left-item">



 <?php 

if ($total_menu > 0) {
    // Itera sobre os resultados e gera o HTML dinâmico
    while ($row_menu = mysqli_fetch_array($query_menu)) {
        // Atribui os valores dos campos a variáveis com o sufixo _menu
        $id = $row_menu['id'];
        $menu_nome = $row_menu['menu'];
        $menu_pagina_menu = $row_menu['menu_pagina'];
        $tipo_menu = $row_menu['tipo'];
        $icone_menu = $row_menu['icone_menu'];

        // Gera a estrutura HTML para cada item do menu
   if ($id == $pagina_nome_recebe){     
echo   '<li class="pcoded-hasmenu active">';
}else{
  echo   '<li class="pcoded-hasmenu">';
  
}
        echo '
        
            <a href="' . $menu_pagina_menu . '?pagina_nome='.$id .' ">
                <span class="pcoded-micon"><i class="'. $icone_menu . '"></i></span>
                <span class="pcoded-mtext">' . $menu_nome . '</span>
            </a>
        </li>';
    }
}
?>


     </ul>                                                        
                       
</div>
</nav>
                    <div class="pcoded-content">
                        <div class="pcoded-inner-content">
                            <div class="main-body">
                                <div class="page-wrapper">


    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->














<?php
// Incluir conexão com banco de dados
require_once 'conn.php';
$login = $_SESSION['login'];

$sql_busca_usuario = "SELECT * FROM login WHERE login = '$login'";
$query_busca_usuario = mysqli_query($conn, $sql_busca_usuario);
$total_busca_usuario = mysqli_num_rows($query_busca_usuario);

while($rows_usuarios = mysqli_fetch_array($query_busca_usuario)) {
    $nome  = Priletra($rows_usuarios['nome']);
    $img_perfil  = $rows_usuarios['perfil_img'];
    $autorizado  = $rows_usuarios['autorizado'];
    $tipo  = $rows_usuarios['tipo'];
    $usuario_api = $rows_usuarios['usuario_api'];

}
// Buscar campanhas do banco de dados
$query = "SELECT 
    id,
    campaign_name,
    total_clientes,
    enviados,
    erros,
    status,
    created_at,
    media_type,
    login,
    usuario_api
FROM mensagens_massa
WHERE usuario_api = '$usuario_api'
ORDER BY created_at DESC";

$result = mysqli_query($conn, $query);


if (!$result) {
    die("Erro na consulta: " . mysqli_error($conn));
}

$campanhas = [];
while ($row = mysqli_fetch_assoc($result)) {
    $campanhas[] = $row;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campanhas WhatsApp - Dashboard</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --danger-gradient: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            --success-gradient: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%);
            --warning-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --whatsapp-green: #25D366;
            --card-shadow: 0 10px 30px rgba(0,0,0,0.1);
            --border-radius: 20px;
            --transition: all 0.3s ease;
        }


        .page-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .page-title {
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .page-subtitle {
            color: #6b7280;
            font-size: 1.1rem;
        }

        .campaign-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            border: none;
            transition: var(--transition);
            overflow: hidden;
            margin-bottom: 2rem;
            max-width: 600px;
            margin: 2rem auto;
        }

        .campaign-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .card-header-simple {
            background: var(--primary-gradient);
            color: white;
            padding: 1.5rem 2rem;
            border-bottom: none;
        }

        .card-header-simple.status-concluida {
            background: var(--success-gradient);
        }

        .card-header-simple.status-pausada {
            background: var(--warning-gradient);
        }

        .card-header-simple.status-erro {
            background: var(--danger-gradient);
        }

        .campaign-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .campaign-date {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-top: 0.5rem;
        }

        .campaign-status {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            margin-top: 0.5rem;
            background: rgba(255,255,255,0.2);
        }

        .card-body-simple {
            padding: 2rem;
        }

        .progress-section {
            margin-bottom: 2rem;
        }

        .progress-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .progress-title {
            font-weight: 600;
            color: #2d3748;
            font-size: 1.1rem;
        }

        .progress-numbers {
            font-size: 1rem;
            font-weight: 600;
            color: var(--whatsapp-green);
        }

        .progress-bar-container {
            background: #e2e8f0;
            border-radius: 10px;
            height: 15px;
            overflow: hidden;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
        }

        .progress-bar-custom {
            height: 100%;
            background: linear-gradient(90deg, var(--whatsapp-green) 0%, #20c65a 100%);
            border-radius: 10px;
            transition: width 1s ease;
            position: relative;
            overflow: hidden;
        }

        .progress-bar-custom::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.3) 50%, transparent 100%);
            animation: shine 2s infinite;
        }

        @keyframes shine {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .progress-details {
            display: flex;
            justify-content: space-between;
            margin-top: 0.5rem;
            font-size: 0.85rem;
            color: #6b7280;
        }

        .progress-percentage {
            font-weight: 600;
            color: var(--whatsapp-green);
        }

        .btn-delete {
            background: var(--danger-gradient);
            border: none;
            border-radius: 12px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            color: white;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            font-size: 1rem;
        }

        .btn-delete:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 107, 107, 0.4);
            color: white;
        }

        .no-campaigns {
            text-align: center;
            padding: 4rem 2rem;
            color: #6b7280;
        }

        .no-campaigns i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        /* Modal Styles */
        .modal-content {
            border-radius: var(--border-radius);
            border: none;
            box-shadow: var(--card-shadow);
        }

        .modal-header {
            background: var(--danger-gradient);
            color: white;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
            border-bottom: none;
        }

        .modal-body {
            padding: 2rem;
            text-align: center;
        }

        .warning-icon {
            font-size: 3rem;
            color: #ef4444;
            margin-bottom: 1rem;
        }

        .btn-modern {
            border-radius: 12px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border: none;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-outline-modern {
            background: white;
            border: 2px solid #e2e8f0;
            color: #4a5568;
        }

        .btn-outline-modern:hover {
            background: #f8fafc;
            border-color: #667eea;
            color: #667eea;
        }

        .btn-danger-modern {
            background: var(--danger-gradient);
            color: white;
        }

        .btn-danger-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 107, 107, 0.4);
            color: white;
        }

        @media (max-width: 768px) {
            .campaign-card {
                margin: 1rem;
                max-width: none;
            }

            .card-body-simple {
                padding: 1.5rem;
            }

            .page-title {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header da Página -->
        <div class="page-header">
            <h1 class="page-title">
                <i class="fab fa-whatsapp"></i>
                Campanhas WhatsApp
            </h1>
            <p class="page-subtitle">
                Gerencie suas campanhas de marketing em massa
            </p>
        </div>

        <?php if (empty($campanhas)): ?>
            <!-- Mensagem quando não há campanhas -->
            <div class="no-campaigns">
                <i class="fas fa-inbox"></i>
                <h3>Nenhuma campanha encontrada</h3>
                <p>Você ainda não criou nenhuma campanha de marketing.</p>
            </div>
        <?php else: ?>
            <!-- Loop das Campanhas -->
            <?php foreach ($campanhas as $campanha): ?>
                <?php
                // Calcular progresso
                $total = (int)$campanha['total_clientes'];
                $enviados = (int)$campanha['enviados'];
                $faltam = $total - $enviados;
                $porcentagem = $total > 0 ? round(($enviados / $total) * 100, 1) : 0;
                
                // Formatação da data
                $data_criacao = new DateTime($campanha['created_at']);
                $data_formatada = $data_criacao->format('d/m/Y \à\s H:i');
                
                // Classe do status
                $status_class = '';
                switch ($campanha['status']) {
                    case 'concluida':
                        $status_class = 'status-concluida';
                        break;
                    case 'pausada':
                        $status_class = 'status-pausada';
                        break;
                    case 'erro':
                        $status_class = 'status-erro';
                        break;
                    default:
                        $status_class = '';
                }
                
                // Ícone do tipo de mídia
                $media_icon = 'fas fa-comment';
                switch ($campanha['media_type']) {
                    case 'image':
                        $media_icon = 'fas fa-image';
                        break;
                    case 'video':
                        $media_icon = 'fas fa-video';
                        break;
                    case 'audio':
                        $media_icon = 'fas fa-microphone';
                        break;
                    case 'document':
                        $media_icon = 'fas fa-file';
                        break;
                }
                ?>
                
                <div class="campaign-card">
                    <!-- Header -->
                    <div class="card-header-simple <?php echo $status_class; ?>">
                        <h2 class="campaign-title">
                            <i class="<?php echo $media_icon; ?>"></i>
                            <?php echo htmlspecialchars($campanha['campaign_name'] ?: 'Campanha sem nome'); ?>
                        </h2>
                        <div class="campaign-date">
                            <i class="fas fa-calendar-alt"></i>
                            Criada em <?php echo $data_formatada; ?>
                        </div>
                        <span class="campaign-status">
                            <?php echo ucfirst($campanha['status']); ?>
                        </span>
                    </div>

                    <!-- Body -->
                    <div class="card-body-simple">
                        <!-- Progresso -->
                        <div class="progress-section">
                            <div class="progress-header">
                                <div class="progress-title">
                                    <i class="fas fa-paper-plane"></i>
                                    Progresso do Envio
                                </div>
                                <div class="progress-numbers">
                                    <?php echo number_format($enviados, 0, ',', '.'); ?> / <?php echo number_format($total, 0, ',', '.'); ?>
                                </div>
                            </div>
                            
                            <div class="progress-bar-container">
                                <div class="progress-bar-custom" style="width: <?php echo $porcentagem; ?>%"></div>
                            </div>
                            
                            <div class="progress-details">
                                <span>Enviadas: <strong><?php echo number_format($enviados, 0, ',', '.'); ?></strong></span>
                                <span>Faltam: <strong><?php echo number_format($faltam, 0, ',', '.'); ?></strong></span>
                                <span class="progress-percentage"><?php echo $porcentagem; ?>%</span>
                            </div>
                            
                            <?php if ($campanha['erros'] > 0): ?>
                                <div class="progress-details mt-2">
                                    <span style="color: #ef4444;">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        Erros: <strong><?php echo number_format($campanha['erros'], 0, ',', '.'); ?></strong>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Botão Apagar -->
                        <button type="button" class="btn btn-delete" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="setCampaignToDelete(<?php echo $campanha['id']; ?>, '<?php echo htmlspecialchars($campanha['campaign_name'], ENT_QUOTES); ?>')">
                            <i class="fas fa-trash"></i>
                            Apagar Campanha
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Modal de Confirmação -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">
                        <i class="fas fa-exclamation-triangle"></i>
                        Confirmar Exclusão
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="warning-icon">
                        <i class="fas fa-trash-alt"></i>
                    </div>
                    <h4 class="mb-3">Tem certeza que deseja apagar?</h4>
                    <p class="text-muted mb-3">
                        Esta ação não pode ser desfeita. A campanha "<span id="campaignNameToDelete"></span>" será permanentemente excluída.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-modern btn-outline-modern" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </button>
                    <button type="button" class="btn btn-modern btn-danger-modern" onclick="deleteCampaign()">
                        <i class="fas fa-trash"></i>
                        Sim, Apagar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let campaignIdToDelete = null;

        // Função para definir qual campanha será deletada
        function setCampaignToDelete(id, name) {
            campaignIdToDelete = id;
            document.getElementById('campaignNameToDelete').textContent = name;
        }

        // Função para apagar campanha
        function deleteCampaign() {
            if (!campaignIdToDelete) {
                showNotification('Erro: ID da campanha não encontrado', 'error');
                return;
            }

            // Mostrar loading
            const deleteBtn = document.querySelector('#deleteModal .btn-danger-modern');
            const originalText = deleteBtn.innerHTML;
            deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Apagando...';
            deleteBtn.disabled = true;
            
            // Fazer requisição AJAX para apagar
            fetch('delete_campaign.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    campaign_id: campaignIdToDelete
                })
            })
            .then(response => response.json())
            .then(data => {
                // Restaurar botão
                deleteBtn.innerHTML = originalText;
                deleteBtn.disabled = false;
                
                if (data.success) {
                    // Fechar modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
                    modal.hide();
                    
                    // Mostrar notificação de sucesso
                    showNotification('Campanha apagada com sucesso!', 'success');
                    
                    // Recarregar página após 2 segundos
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    showNotification(data.message || 'Erro ao apagar campanha', 'error');
                }
            })
            .catch(error => {
                // Restaurar botão
                deleteBtn.innerHTML = originalText;
                deleteBtn.disabled = false;
                
                console.error('Erro:', error);
                showNotification('Erro na requisição', 'error');
            });
        }
        
        // Função para mostrar notificações
        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const iconClass = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
            
            notification.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `
                <i class="${iconClass}"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(notification);
            
            // Remover após 5 segundos
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 5000);
        }

        // Auto-refresh da página a cada 30 segundos (opcional)
        setInterval(() => {
            location.reload();
        }, 300000);
    </script>
</body>
</html>

































    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->
    <!-- DADOS PARA INSERIR AQUI -->




   </div>
</div>
</div>
</div>


    <!-- Warning Section Ends -->
    <!-- Required Jquery -->
    <script data-cfasync="false" src="..\..\..\cdn-cgi\scripts\5c5dd728\cloudflare-static\email-decode.min.js"></script><script type="text/javascript" src="..\files\bower_components\jquery\js\jquery.min.js"></script>
    <script type="text/javascript" src="..\files\bower_components\jquery-ui\js\jquery-ui.min.js"></script>
    <script type="text/javascript" src="..\files\bower_components\bootstrap\js\bootstrap.bundle.min.js"></script>
    <!-- jquery slimscroll js -->
    <script type="text/javascript" src="..\files\bower_components\jquery-slimscroll\js\jquery.slimscroll.js"></script>
    <!-- modernizr js -->
    <script type="text/javascript" src="..\files\bower_components\modernizr\js\modernizr.js"></script>
    <!-- Chart js -->
    <script type="text/javascript" src="..\files\bower_components\chart.js\js\Chart.js"></script>
    <!-- amchart js -->
    <script src="..\files\assets\pages\widget\amchart\amcharts.js"></script>
    <script src="..\files\assets\pages\widget\amchart\serial.js"></script>
    <script src="..\files\assets\pages\widget\amchart\light.js"></script>
    <script src="..\files\assets\js\jquery.mCustomScrollbar.concat.min.js"></script>
    <script type="text/javascript" src="..\files\assets\js\SmoothScroll.js"></script>
    <!--   LEMBRAR DESSA PARTE  <script src="..\files\assets\js\pcoded.min.js"></script> -->

    <!-- custom js -->
    <script src="..\files\assets\js\vartical-layout.min.js"></script>
    <script type="text/javascript" src="..\files\assets\pages\dashboard\custom-dashboard.js"></script>
    <script type="text/javascript" src="..\files\assets\js\script.min.js"></script>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async="" src="https://www.googletagmanager.com/gtag/js?id=UA-23581568-13"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-23581568-13');
</script>
</body>

</html>
<script type="text/javascript">
    // Redireciona para uma nova URL após 3 segundos
 /   setTimeout(function() {
        window.location.href = "http://localhost/codigos/template/adminty-dashboard-master/default/edita.php";
    }, 2000); // 3000 milissegundos = 3 segundos
</script>


<?php

include 'pcoded.php';
include'erro.php';

?>