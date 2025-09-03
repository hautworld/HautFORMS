<?php
require_once __DIR__.'/../../app/config.php';
require_once __DIR__.'/../../app/auth.php';
require_admin();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin • Haut Forms</title>
<link rel="stylesheet" href="/assets/bulma.min.css">
</head>
<body>
<section class="section">
<div class="container">
<h1 class="title">Admin</h1>
<div class="buttons">
<a class="button is-link" href="/admin/users.php">Usuários</a>
<a class="button is-link" href="/admin/departments.php">Departamentos</a>
<a class="button is-link" href="/admin/roles.php">Cargos</a>
<a class="button is-link" href="/admin/access_levels.php">Níveis de Acesso</a>
<a class="button is-warning" href="/admin/snippets.php">Snippets de Acesso</a>
</div>
</div>
</section>
</body>
</html>