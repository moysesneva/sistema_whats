
<body>
    <div class="container">
        <h2>Atualizar Configurações do Site</h2>
        <form action="upload_logo.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="logo_site">Logo do Site:</label>
                <input type="file" class="form-control" id="logo_site" name="logo_site" required>
            </div>
            <button type="submit" class="btn btn-primary">Enviar Logo</button>
        </form>
        <hr>

        <form action="upload_emblema.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="emblema_site">Emblema do Site:</label>
                <input type="file" class="form-control" id="emblema_site" name="emblema_site" required>
            </div>
            <button type="submit" class="btn btn-primary">Enviar Emblema</button>
        </form>
        <hr>

        <form action="upload_fundo.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="fundo_login">Fundo do Login:</label>
                <input type="file" class="form-control" id="fundo_login" name="fundo_login" required>
            </div>
            <button type="submit" class="btn btn-primary">Enviar Fundo</button>
        </form>
        <hr>

        <form action="upload_icon.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="icon_site">Ícone do Site:</label>
                <input type="file" class="form-control" id="icon_site" name="icon_site" required>
            </div>
            <button type="submit" class="btn btn-primary">Enviar Ícone</button>
        </form>
    </div>
