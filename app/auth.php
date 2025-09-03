<?php // app/auth.php
require_once __DIR__.'/db.php';
require_once __DIR__.'/helpers.php';


function login_rate_key() { return 'login_attempts_'.($_SERVER['REMOTE_ADDR'] ?? 'ip'); }


function can_attempt_login(): bool {
$key = login_rate_key();
$attempts = $_SESSION[$key]['count'] ?? 0;
$last = $_SESSION[$key]['time'] ?? 0;
if ($attempts >= 5 && (time() - $last) < 300) return false; // 5 tentativas em 5min
return true;
}


function register_login_attempt($success) {
$key = login_rate_key();
if ($success) { $_SESSION[$key] = ['count' => 0, 'time' => time()]; return; }
$attempts = $_SESSION[$key]['count'] ?? 0;
$_SESSION[$key] = ['count' => $attempts + 1, 'time' => time()];
}


function current_user() { return $_SESSION['user'] ?? null; }
function is_logged() { return !!current_user(); }
function is_admin() { return is_logged() && !empty($_SESSION['user']['is_admin']); }


function require_login() {
if (!is_logged()) { json_redirect(BASE_URL.'/login.php'); }
}


function require_admin() {
require_login();
if (!is_admin()) { json_redirect(BASE_URL.'/dashboard.php'); }
}