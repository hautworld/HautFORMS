<?php
require_once __DIR__.'/../app/auth.php';
require_once __DIR__.'/../app/db.php';
require_once __DIR__.'/../app/helpers.php';
require_once __DIR__.'/../app/csrf.php';
require_login();
$user = $_SESSION['user'];


$departments = $pdo->query('SELECT id,name FROM departments ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
$roles = $pdo->query('SELECT r.id,r.name, al.name AS level_name FROM roles r JOIN access_levels al ON r.access_level_id=al.id ORDER BY al.rank, r.name')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Meu Perfil • Haut Forms</title>
<link rel="stylesheet" href="/assets/bulma.min.css">
</head>
<body>
<section class="section">
<div class="container">
<h1 class="title">Meu Perfil</h1>


<form class="box" method="post" action="/actions/profile_update.php">
<?php csrf_input(); ?>
<div class="field">
<label class="label">Nome de exibição</label>
<div class="control">
<input class="input" type="text" name="display_name" value="<?= sanitize($user['display_name']); ?>" maxlength="150">
</div>
</div>
<div class="field">
<label class="label">Departamento</label>
<div class="control">
<div class="select is-fullwidth">
<select name="department_id">
<option value="">Selecione...</option>
<?php foreach ($departments as $d): ?>
<option value="<?= (int)$d['id'] ?>" <?= ($user['department_id']==$d['id']?'selected':'') ?>><?= sanitize($d['name']) ?></option>
<?php endforeach; ?>
</select>
</div>
</div>
</div>
<div class="field">
<label class="label">Cargo</label>
<div class="control">
<div class="select is-fullwidth">
<select name="role_id">
<option value="">Selecione...</option>
<?php foreach ($roles as $r): ?>
<option value="<?= (int)$r['id'] ?>" <?= ($user['role_id']==$r['id']?'selected':'') ?>><?= sanitize($r['name']).' ('.sanitize($r['level_name']).')' ?></option>
<?php endforeach; ?>
</select>
</div>
</div>
</div>
<div class="field is-grouped">
<div class="control"><button class="button is-primary" type="submit">Salvar</button></div>
</div>
</form>


<form class="box" method="post" action="/actions/password_update.php">
<?php csrf_input(); ?>
<h2 class="subtitle">Mudar Senha</h2>
<div class="field"><label class="label">Senha atual</label><input class="input" type="password" name="old_password" required></div>
<div class="field"><label class="label">Nova senha</label><input class="input" type="password" name="new_password" minlength="8" required></div>
<div class="field"><label class="label">Confirmar nova senha</label><input class="input" type="password" name="confirm_password" minlength="8" required></div>
<div class="field"><button class="button is-link" type="submit">Alterar senha</button></div>
</form>
</div>
</section>
</body>
</html>