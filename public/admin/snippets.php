<?php
require_once __DIR__.'/../../app/config.php';
require_once __DIR__.'/../../app/auth.php';
require_once __DIR__.'/../../app/db.php';
require_once __DIR__.'/../../app/access.php';
require_admin();


$levels = $pdo->query('SELECT id,name,rank FROM access_levels ORDER BY rank')->fetchAll(PDO::FETCH_ASSOC);
$roles = $pdo->query('SELECT id,name FROM roles ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Snippets • Haut Forms</title>
<link rel="stylesheet" href="/assets/bulma.min.css">
</head>
<body>
<section class="section">
<div class="container content">
<h1 class="title">Snippets de Acesso</h1>


<h2 class="subtitle">Por Nível (mínimo)</h2>
<table class="table is-fullwidth">
<thead><tr><th>Nível</th><th>Rank</th><th>Snippet</th></tr></thead>
<tbody>
<?php foreach ($levels as $lv): ?>
<tr>
<td><?= sanitize($lv['name']) ?></td>
<td><?= (int)$lv['rank'] ?></td>
<td><pre><code><?= sanitize(generate_access_snippet_for_level((int)$lv['rank'])) ?></code></pre></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>


<h2 class="subtitle">Por Cargo específico</h2>
<table class="table is-fullwidth">
<thead><tr><th>Cargo</th><th>Snippet</th></tr></thead>
<tbody>
<?php foreach ($roles as $r): ?>
<tr>
<td><?= sanitize($r['name']) ?></td>
<td><pre><code><?= sanitize(generate_access_snippet_for_role($r['name'])) ?></code></pre></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
</section>
</body>
</html>