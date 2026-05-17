<?php
require_once __DIR__ . '/auth_guard.php';
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
<?php

// --- Ação: exportar CSV ---
if (isset($_GET['exportar']) && $_GET['exportar'] === 'csv') {
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="leads_' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8
    fputcsv($out, ['Nome', 'Email', 'WhatsApp', 'Data']);
    $res_exp = mysqli_query($conn, "SELECT nome, email, whats, data FROM leads ORDER BY data DESC");
    while ($r = mysqli_fetch_assoc($res_exp)) {
        fputcsv($out, [$r['nome'], $r['email'], $r['whats'], $r['data']]);
    }
    fclose($out);
    exit;
}

// --- Ação: deletar lead individual ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deletar_lead'])) {
    $lead_id = (int)$_POST['deletar_lead'];
    $stmt_del = $conn->prepare("DELETE FROM leads WHERE id = ?");
    $stmt_del->bind_param("i", $lead_id);
    $stmt_del->execute();
    $stmt_del->close();
    VaiPara('leads.php?msg=Lead+deletado');
}

// --- Ação: limpar todos os leads ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['limpar_todos'])) {
    mysqli_query($conn, "DELETE FROM leads");
    VaiPara('leads.php?msg=Leads+apagados');
}

include 'header.php';
?>

<?php
// Configuração da paginação
$registrosPorPagina = 20;
$paginaAtual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($paginaAtual - 1) * $registrosPorPagina;

// Conta o total de registros
$sqlCount = "SELECT COUNT(*) as total FROM leads";
$resultCount = mysqli_query($conn, $sqlCount);
$totalRegistros = mysqli_fetch_assoc($resultCount)['total'];
$totalPaginas = ceil($totalRegistros / $registrosPorPagina);
?>

<style>
  .leads-wrapper {
    max-width: 1000px;
    margin: 30px auto;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    border: 1px solid #e8e8e8;
  }

  .leads-header,
  .lead-row {
    display: grid;
    grid-template-columns: 2fr 3fr 2fr 2fr;
    align-items: center;
    padding: 16px 20px;
    gap: 12px;
  }

  .leads-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-weight: 600;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: none;
  }

  .lead-row {
    border-bottom: 1px solid #f1f1f1;
    transition: all 0.3s ease;
    color: #2d3748;
    font-size: 14px;
  }

  .lead-row:hover {
    background: linear-gradient(135deg, #f8faff 0%, #f0f4ff 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.15);
  }

  .lead-row:nth-child(even) {
    background-color: #fafbfc;
  }

  .lead-row:nth-child(even):hover {
    background: linear-gradient(135deg, #f8faff 0%, #f0f4ff 100%);
  }

  .lead-row:last-child {
    border-bottom: none;
  }

  .leads-header div,
  .lead-row div {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    padding: 4px 0;
  }

  .lead-row div:first-child {
    font-weight: 500;
    color: #1a202c;
  }

  .lead-row div:nth-child(2) {
    color: #4a5568;
    font-family: 'Courier New', monospace;
    font-size: 13px;
  }

  .whatsapp-link {
    color: #25d366;
    font-weight: 600;
    text-decoration: none;
    padding: 8px 12px;
    border-radius: 8px;
    background: #f0fff4;
    border: 1px solid #25d366;
    transition: all 0.3s ease;
    display: inline-block;
    text-align: center;
  }

  .whatsapp-link:hover {
    background: #25d366;
    color: white;
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(37, 211, 102, 0.3);
  }

  .lead-row div:nth-child(4) {
    color: #718096;
    font-size: 12px;
    font-weight: 500;
  }

  /* Paginação */
  .pagination-wrapper {
    padding: 20px;
    text-align: center;
    background: #f8f9fa;
    border-top: 1px solid #e8e8e8;
  }

  .pagination {
    display: inline-flex;
    gap: 8px;
    align-items: center;
  }

  .pagination a,
  .pagination span {
    padding: 10px 15px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    border: 1px solid #e2e8f0;
    background: white;
    color: #4a5568;
  }

  .pagination a:hover {
    background: #667eea;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
  }

  .pagination .current {
    background: #667eea;
    color: white;
    border-color: #667eea;
  }

  .pagination .disabled {
    color: #a0aec0;
    cursor: not-allowed;
    background: #f7fafc;
  }

  .info-pagination {
    margin-top: 15px;
    color: #718096;
    font-size: 14px;
  }

  /* Responsividade */
  @media (max-width: 768px) {
    .leads-wrapper {
      margin: 20px 10px;
      border-radius: 12px;
    }
    
    .leads-header,
    .lead-row {
      grid-template-columns: 1.5fr 2fr 1.5fr 1fr;
      padding: 12px 16px;
      gap: 8px;
      font-size: 13px;
    }
    
    .leads-header {
      font-size: 12px;
    }
    
    .whatsapp-link {
      padding: 6px 8px;
      font-size: 12px;
    }
    
    .pagination a,
    .pagination span {
      padding: 8px 12px;
      font-size: 14px;
    }
  }

  @media (max-width: 480px) {
    .leads-header,
    .lead-row {
      grid-template-columns: 1fr 1.5fr 1fr;
      font-size: 12px;
    }
    
    .lead-row div:nth-child(4) {
      display: none;
    }
    
    .leads-header div:nth-child(4) {
      display: none;
    }
    
    .pagination {
      flex-wrap: wrap;
      gap: 4px;
    }
    
    .pagination a,
    .pagination span {
      padding: 6px 10px;
      font-size: 12px;
    }
  }
</style>

<div class="leads-wrapper">
  <div style="padding: 16px 20px; display: flex; justify-content: space-between; align-items: center; background: #f8f9fa; border-bottom: 1px solid #e8e8e8;">
    <h5 style="margin:0; color:#001f3f; font-weight:600;"><i class="feather icon-users"></i> Leads Capturados</h5>
    <div style="display:flex; gap:10px;">
      <a href="?exportar=csv" class="btn btn-sm btn-success"><i class="feather icon-download"></i> Exportar CSV</a>
      <form method="POST" style="margin:0;" id="formLimparLeads">
        <input type="hidden" name="limpar_todos" value="1">
        <button type="submit" class="btn btn-sm btn-danger" data-fn="__confirm" data-args="Apagar todos os leads? Esta ação não pode ser desfeita."><i class="feather icon-trash-2"></i> Limpar Tudo</button>
      </form>
    </div>
  </div>
  <div class="leads-header">
    <div>Nome</div>
    <div>Email</div>
    <div>WhatsApp</div>
    <div>Data / Ações</div>
  </div>
  <?php
  // Busca os leads com paginação
  $stmt_leads = $conn->prepare("SELECT id, nome, email, whats, data FROM leads ORDER BY data DESC LIMIT ? OFFSET ?");
  $stmt_leads->bind_param("ii", $registrosPorPagina, $offset);
  $stmt_leads->execute();
  $result = $stmt_leads->get_result();
  $stmt_leads->close();
  
  if ($result->num_rows > 0):
    while ($row = $result->fetch_assoc()):
        // Formata para o padrão brasileiro
        $dataBR = date('d/m/Y H:i:s', strtotime($row['data']));
        
        // Limpa o número do WhatsApp (remove caracteres especiais)
        $whatsClean = preg_replace('/[^0-9]/', '', $row['whats']);
        
        // Se não começar com 55, adiciona o código do Brasil
        if (!str_starts_with($whatsClean, '55')) {
            $whatsClean = '55' . $whatsClean;
        }
  ?>
    <div class="lead-row">
      <div><?= htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8') ?></div>
      <div><?= htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8') ?></div>
      <div>
        <a href="https://wa.me/<?= $whatsClean ?>" target="_blank" class="whatsapp-link">
          <?= htmlspecialchars($row['whats'], ENT_QUOTES, 'UTF-8') ?>
        </a>
      </div>
      <div>
        <?= $dataBR ?>
        <form method="POST" style="display:inline; margin-left:6px;">
          <input type="hidden" name="deletar_lead" value="<?= (int)$row['id'] ?>">
          <button type="submit" class="btn btn-xs btn-danger" title="Deletar" data-fn="__confirm" data-args="Deletar este lead?"><i class="feather icon-trash-2"></i></button>
        </form>
      </div>
    </div>
  <?php 
    endwhile;
  else:
  ?>
    <div class="lead-row">
      <div colspan="4" style="text-align: center; color: #718096; font-style: italic;">
        Nenhum lead encontrado
      </div>
    </div>
  <?php endif; ?>
  
  <!-- Paginação -->
  <div class="pagination-wrapper">
    <div class="pagination">
      <?php if ($paginaAtual > 1): ?>
        <a href="?pagina=1">&laquo; Primeira</a>
        <a href="?pagina=<?= $paginaAtual - 1 ?>">&lsaquo; Anterior</a>
      <?php else: ?>
        <span class="disabled">&laquo; Primeira</span>
        <span class="disabled">&lsaquo; Anterior</span>
      <?php endif; ?>
      
      <?php
      // Mostra páginas próximas
      $inicio = max(1, $paginaAtual - 2);
      $fim = min($totalPaginas, $paginaAtual + 2);
      
      for ($i = $inicio; $i <= $fim; $i++):
        if ($i == $paginaAtual):
      ?>
        <span class="current"><?= $i ?></span>
      <?php else: ?>
        <a href="?pagina=<?= $i ?>"><?= $i ?></a>
      <?php 
        endif;
      endfor;
      ?>
      
      <?php if ($paginaAtual < $totalPaginas): ?>
        <a href="?pagina=<?= $paginaAtual + 1 ?>">Próxima &rsaquo;</a>
        <a href="?pagina=<?= $totalPaginas ?>">Última &raquo;</a>
      <?php else: ?>
        <span class="disabled">Próxima &rsaquo;</span>
        <span class="disabled">Última &raquo;</span>
      <?php endif; ?>
    </div>
    
    <div class="info-pagination">
      Mostrando <?= ($offset + 1) ?> a <?= min($offset + $registrosPorPagina, $totalRegistros) ?> de <?= $totalRegistros ?> leads
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>