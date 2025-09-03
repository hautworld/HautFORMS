<?php // app/helpers.php
function sanitize($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }


function json_redirect($url) {
header('Location: '.$url);
exit;
}


function valid_corporate_email(string $email): bool {
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return false;
$domain = strtolower(substr(strrchr($email, '@'), 1));
return in_array($domain, ALLOWED_EMAIL_DOMAINS, true);
}