<?php
// Exibe erros em dev
ini_set('display_errors',1);
error_reporting(E_ALL);

// ===== TRATAMENTO DE FORMULÁRIOS =====
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // -- ATIVAR/DESATIVAR PLANO --
    if (isset($_POST['toggle_plan'])) {
        $id = intval($_POST['toggle_plan']);
        $row = mysqli_fetch_assoc(
            mysqli_query($conn, "SELECT ativo FROM planos_online WHERE id = $id")
        );
        $novo = $row['ativo'] ? 0 : 1;
        mysqli_query($conn, "UPDATE planos_online SET ativo = $novo WHERE id = $id");
    }

    // -- ATUALIZAR PLANO (nome, preço, link) --
    if (isset($_POST['update_plan'])) {
        $id             = intval($_POST['update_plan']);
        $titulo         = trim($_POST['titulo']);
        $preco          = floatval(str_replace(',', '.', preg_replace('/[^\d,]/','', $_POST['preco'])));
        #$link_pagamento = trim($_POST['link_pagamento']);

        $stmt = mysqli_prepare($conn,
            "UPDATE planos_online
                SET titulo = ?, preco = ?
              WHERE id = ?"
        );
        mysqli_stmt_bind_param($stmt, "sdi",
            $titulo, $preco, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    // -- ADICIONAR FEATURE --
    if (isset($_POST['add_feature'])) {
        $plan_id = intval($_POST['plan_id']);
        $feat    = trim($_POST['feature']);
        $stmt = mysqli_prepare($conn,
            "INSERT INTO planos_features (id_plano, feature) VALUES (?, ?)"
        );
        mysqli_stmt_bind_param($stmt, "is", $plan_id, $feat);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    // -- ATUALIZAR FEATURE --
    if (isset($_POST['update_feature'])) {
        $fid  = intval($_POST['update_feature']);
        $text = trim($_POST['feature']);
        $stmt = mysqli_prepare($conn,
            "UPDATE planos_features SET feature = ? WHERE id = ?"
        );
        mysqli_stmt_bind_param($stmt, "si", $text, $fid);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    // -- EXCLUIR FEATURE --
    if (isset($_POST['delete_feature'])) {
        $fid = intval($_POST['delete_feature']);
        mysqli_query($conn, "DELETE FROM planos_features WHERE id = $fid");
    }
}

// ===== BUSCAR PLANOS =====
$res_plans = mysqli_query($conn, "SELECT * FROM planos_online ORDER BY id");
?>

<!-- ... dentro do <body> ... -->
<h3 class="mb-4">Benefícios (Features) dos Planos</h3>

<table class="table table-bordered">
  <thead class="thead-light">
    <tr>
      <th>Plano</th>
      <th>Preço</th>
      <th>Status</th>
      <th width="160">Ações</th>
    </tr>
  </thead>
  <tbody>
    <?php 
    $contador = 1;
    while ($plan = mysqli_fetch_assoc($res_plans)): 
    ?>
      <!-- linha de edição do plano -->
      <tr>
        <form action="" method="post">
          <td>
            <input type="text" name="titulo" class="form-control"
                   value="Plano <?= $contador ?>" required>
          </td>
          <td>
            <input type="text" name="preco" class="form-control"
                   value="<?= number_format($plan['preco'],2,',','.'); ?>" required>
          </td>
          <td class="text-center">
            <?= $plan['ativo']
               ? '<span class="badge badge-success">Ativo</span>'
               : '<span class="badge badge-secondary">Inativo</span>'; ?>
          </td>
          <td>
            <button type="submit" name="update_plan" value="<?= $plan['id']; ?>" class="btn btn-sm btn-primary">
              Salvar
            </button>
            <button type="submit" name="toggle_plan" value="<?= $plan['id']; ?>" class="btn btn-sm btn-warning">
              <?= $plan['ativo'] ? 'Desativar' : 'Ativar'; ?>
            </button>
          </td>
        </form>
      </tr>

      <!-- linha de features vinculadas -->
      <tr>
        <td colspan="4" class="p-0 bg-light">
          <?php
            $res_feat = mysqli_query($conn,
              "SELECT * FROM planos_features WHERE id_plano = {$plan['id']} ORDER BY id"
            );
          ?>
          <div class="p-3">
            <h6>Recursos de "Plano <?= $contador ?>"</h6>
            <table class="table table-sm mb-3">
              <thead>
                <tr>
                  <th>Funcionalidade</th>
                  <th width="140">Ações</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($feat = mysqli_fetch_assoc($res_feat)): ?>
                  <tr>
                    <form action="" method="post">
                      <input type="hidden" name="feature_id" value="<?= $feat['id']; ?>">
                      <td>
                        <input type="text" name="feature" class="form-control"
                               value="<?= htmlspecialchars($feat['feature']); ?>" required>
                      </td>
                      <td>
                        <button type="submit" name="update_feature" value="<?= $feat['id']; ?>" class="btn btn-sm btn-primary">
                          Salvar
                        </button>
                        <button type="submit" name="delete_feature" value="<?= $feat['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Excluir esta funcionalidade?')">
                          Excluir
                        </button>
                      </td>
                    </form>
                  </tr>
                <?php endwhile; ?>

                <!-- adicionar nova feature -->
                <tr>
                  <form action="" method="post">
                    <input type="hidden" name="plan_id" value="<?= $plan['id']; ?>">
                    <td>
                      <input type="text" name="feature" class="form-control" placeholder="Nova funcionalidade..." required>
                    </td>
                    <td>
                      <button type="submit" name="add_feature" class="btn btn-sm btn-success">
                        Adicionar
                      </button>
                    </td>
                  </form>
                </tr>

              </tbody>
            </table>
          </div>
        </td>
      </tr>
    <?php 
    $contador++;
    endwhile; 
    ?>
  </tbody>
</table>