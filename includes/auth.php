<?php
require_once 'session.php';

function verifica_login() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: /index.php");
        exit();
    }
}

function verifica_acesso($nivel_requerido) {
    if ($_SESSION['nivel_acesso_id'] != $nivel_requerido) {
        header("Location: /member/index.php");
        exit();
    }
}
?>
