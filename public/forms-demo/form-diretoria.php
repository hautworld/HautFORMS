<?php
// Cole o snippet abaixo no topo para exigir "Diretor" (rank 1) ou nível mínimo desejado.
// (Você também pode usar snippet por cargo específico.)
require_once __DIR__.'/../../app/config.php';
require_once __DIR__.'/../../app/db.php';
require_once __DIR__.'/../../app/access.php';
require_min_level($pdo, 1); // 1 = Diretor
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Solicitação Estratégica (Diretoria)</title>
<link rel="stylesheet" href="/assets/bulma.min.css">
</head>
<body>
<section class="section">
<div class="container">
<h1 class="title">Solicitação Estratégica</h1>
<form method="post" action="/forms-demo/process-diretoria.php" class="box">
<div class="field"><label class="label">Assunto</label><input class="input" name="assunto" required></div>
<div class="field"><label class="label">Descrição</label><textarea class="textarea" name="descricao" required></textarea></div>
<button class="button is-primary" type="submit">Enviar</button>
</form>
</div>
</section>
</body>
</html>