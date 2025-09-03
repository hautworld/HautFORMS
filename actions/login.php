<?php // actions/login.php
require_once __DIR__.'/../app/config.php';
require_once __DIR__.'/../app/db.php';
require_once __DIR__.'/../app/auth.php';
require_once __DIR__.'/../app/helpers.php';
require_once __DIR__.'/../app/csrf.php';


if (!can_attempt_login()) { json_redirect(BASE_URL.'/login.php?e=Tente novamente em alguns minutos'); }


if (!csrf_check($_POST['csrf'] ?? '')) { json_redirect(BASE_URL.'/login.php?e=CSRF inválido'); }


$email = trim($_POST['email'] ?? '');
$pass = (string)($_POST['password'] ?? '');


if (!valid_corporate_email($email)) { register_login_attempt(false); json_redirect(BASE_URL.'/login.php?e=Domínio não autorizado'); }


$st = $pdo->prepare("SELECT id,first_name,last_name,email,display_name,department_id,role_id,password_hash,is_admin,is_active FROM users WHERE email=? LIMIT 1");
$st->execute([$email]);
$user = $st->fetch(PDO::FETCH_ASSOC);


if (!$user || !$user['is_active'] || !password_verify($pass, $user['password_hash'])) {
register_login_attempt(false);
json_redirect(BASE_URL.'/login.php?e=Credenciais inválidas');
}


session_regenerate_id(true);
$_SESSION['user'] = [
'id' => (int)$user['id'],
'first_name' => $user['first_name'],
'last_name' => $user['last_name'],
'display_name' => $user['display_name'] ?: $user['first_name'],
'email' => $user['email'],
'department_id' => $user['department_id'],
'role_id' => $user['role_id'],
'is_admin' => (int)$user['is_admin'],
];
register_login_attempt(true);
json_redirect(BASE_URL.'/dashboard.php');