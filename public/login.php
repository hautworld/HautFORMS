<?php // public/login.php
require_once __DIR__.'/../app/config.php';
require_once __DIR__.'/../app/csrf.php';
if (isset($_SESSION['user'])) { header('Location: '.BASE_URL.'/dashboard.php'); exit; }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login • Haut Forms</title>
<link rel="stylesheet" href="/assets/bulma.min.css">
</head>
<body>
<section class="section">
<div class="container">
<div class="column is-half is-offset-one-quarter">
<h1 class="title">Login</h1>
<form method="post" action="/actions/login.php">
<?php csrf_input(); ?>
<div class="field">
<label class="label">Email corporativo</label>
<div class="control has-icons-left">
<input class="input" type="email" name="email" required>
<span class="icon is-small is-left"><i class="fas fa-user"></i></span>
</div>
</div>
<div class="field">
<label class="label">Senha</label>
<div class="control has-icons-left">
<input class="input" type="password" name="password" required>
<span class="icon is-small is-left"><i class="fas fa-lock"></i></span>
</div>
</div>
<div class="field">
<button class="button is-primary" type="submit">Entrar</button>
</div>
<?php if (!empty($_GET['e'])): ?>
<p class="has-text-danger"><?= sanitize($_GET['e']) ?></p>
<?php endif; ?>
</form>
</div>
</div>
</section>
</body>
</html>