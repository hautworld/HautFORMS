<?php
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/auth.php';
require_login();
$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard • Haut Forms</title>
    <link rel="stylesheet" href="/assets/bulma.min.css">
</head>

<body>
    <section class="section">
        <div class="container">
            <nav class="level">
                <div class="level-left">
                    <div class="level-item">
                        <h1 class="title">Olá, <?= sanitize($user['display_name']); ?>!</h1>
                    </div>
                </div>
                <div class="level-right">
                    <?php if (is_admin()): ?>
                        <a class="button is-link mr-2" href="/admin/">Admin</a>
                    <?php endif; ?>
                    <a class="button mr-2" href="/member.php">Meu Perfil</a>
                    <a class="button is-light" href="/logout.php">Sair</a>
                </div>
            </nav>


            <div class="content">
                <h2 class="subtitle">Formulários da Empresa</h2>
                <ul>
                    <li><a href="/forms-demo/form-diretoria.php">Solicitação Estratégica (Diretoria)</a></li>
                    <!-- Adicione links para seus formulários reais aqui -->
                </ul>
            </div>
        </div>
    </section>
</body>

</html>