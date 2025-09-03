<?php
// app/config.php
session_set_cookie_params([
'lifetime' => 0,
'path' => '/',
'domain' => 'forms.haut.world',
'secure' => true,
'httponly' => true,
'samesite' => 'Lax',
]);
session_start();


define('DB_HOST', 'localhost');
define('DB_NAME', 'hautworl_forms');
define('DB_USER', 'hautworl_uForms');
define('DB_PASS', 'yEIMEZEvyszgxc4W');

define('ALLOWED_EMAIL_DOMAINS', ['haut.com.br', 'haut.email', 'hautmedical.com.br', 'hauttechnology.com.br']);


// URL base
define('BASE_URL', 'https://forms.haut.world');