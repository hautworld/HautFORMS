<?php
include('../includes/auth.php');
verifica_login();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Forms Haut</title>
    <link rel="stylesheet" href="[https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css](https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css)">
</head>
<body>
    <nav class="navbar is-info">
        <div class="navbar-brand">
            <a class="navbar-item" href="#">
                Olá, <?php echo $_SESSION['user_name']; ?>!
            </a>
            <div class="navbar-burger burger" data-target="navbarMenu">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
        <div id="navbarMenu" class="navbar-menu">
            <div class="navbar-end">
                <a class="navbar-item" href="profile.php">
                    Meu Perfil
                </a>
                <?php if ($_SESSION['nivel_acesso_id'] == 1) { // Supondo 1 para Admin ?>
                    <a class="navbar-item" href="../admin/index.php">
                        Área do Admin
                    </a>
                <?php } ?>
                <a class="navbar-item" href="../logout.php">
                    Sair
                </a>
            </div>
        </div>
    </nav>
    <section class="section">
        <div class="container">
            <h1 class="title">Dashboard</h1>
            <p class="subtitle">Bem-vindo(a) ao seu painel de controle. Aqui você encontra os links para os formulários e documentos.</p>
            <div class="columns is-multiline">
                <div class="column is-4">
                    <div class="card">
                        <div class="card-content">
                            <p class="title is-4">Formulário Financeiro</p>
                            <p class="subtitle">Acesse o formulário de solicitação financeira.</p>
                            <a href="../forms/financeiro.php" class="button is-link">Acessar</a>
                        </div>
                    </div>
                </div>
                <!-- Adicionar mais cards com links para outros formulários aqui -->
            </div>
        </div>
    </section>
</body>
</html>
