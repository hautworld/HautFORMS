<?php
// Configurações do banco de dados
$db_host = 'localhost';
$db_user = 'hautworl_uForms';
$db_pass = 'yEIMEZEvyszgxc4W';
$db_name = 'hautworl_forms';

// Cria a conexão
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Verifica a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Define o conjunto de caracteres para UTF-8
$conn->set_charset("utf8");
?>
