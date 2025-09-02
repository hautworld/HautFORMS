<?php
// Inclui o arquivo de configuração
require_once __DIR__ . '/../config.php';

// Habilita exceções para erros do MySQLi
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Cria a conexão
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verifica a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Define o conjunto de caracteres para UTF-8
$conn->set_charset("utf8");
?>
