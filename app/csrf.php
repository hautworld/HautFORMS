<?php // app/csrf.php
if (!isset($_SESSION['csrf_token'])) {
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
function csrf_token() { return $_SESSION['csrf_token'] ?? ''; }
function csrf_input() { echo '<input type="hidden" name="csrf" value="'.csrf_token().'">'; }
function csrf_check($token) { return hash_equals($_SESSION['csrf_token'] ?? '', $token ?? ''); }