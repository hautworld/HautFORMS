<?php
require_once __DIR__.'/../../app/config.php';
require_once __DIR__.'/../../app/db.php';
require_once __DIR__.'/../../app/access.php';
require_min_level($pdo, 1); // garante que só diretoria processe o POST


// Aqui você processa o POST (validar/armazenar/enviar email/etc.)
// ...
header('Location: '.BASE_URL.'/dashboard.php');
exit;