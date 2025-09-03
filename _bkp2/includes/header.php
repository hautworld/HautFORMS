<?php
// Define o caminho base para links e arquivos
$base_url = "http://forms.haut.world"; // Substitua pelo seu subdomínio real

// Pega o nome do arquivo atual para destacar o link ativo na navegação
$pagina_atual = basename($_SERVER['PHP_SELF']);
$dir_atual = basename(dirname($_SERVER['PHP_SELF']));
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo_pagina; ?> - HautForms</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f5f5f5;
        }
        .navbar-item.is-active {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <nav class="navbar is-info" role="navigation" aria-label="main navigation">
        <div class="container">
            <div class="navbar-brand">
                <a class="navbar-item" href="<?php echo ($dir_atual === 'admin') ? 'index.php' : '../member'; ?>">
                    Forms Haut
                </a>
            </div>
            <div id="navbarBasicExample" class="navbar-menu">
                <div class="navbar-start">
                    <?php if ($dir_atual === 'admin') { ?>
                        <a class="navbar-item <?php echo ($pagina_atual === 'index.php') ? 'is-active' : ''; ?>" href="index.php">
                            Dashboard
                        </a>
                        <a class="navbar-item <?php echo ($pagina_atual === 'usuarios.php') ? 'is-active' : ''; ?>" href="usuarios.php">
                            Usuários
                        </a>
                        <a class="navbar-item <?php echo ($pagina_atual === 'departamentos.php') ? 'is-active' : ''; ?>" href="departamentos.php">
                            Departamentos
                        </a>
                        <a class="navbar-item <?php echo ($pagina_atual === 'cargos.php') ? 'is-active' : ''; ?>" href="cargos.php">
                            Cargos
                        </a>
                    <?php } else { ?>
                        <a class="navbar-item <?php echo ($pagina_atual === 'index.php') ? 'is-active' : ''; ?>" href="index.php">
                            Dashboard
                        </a>
                        <a class="navbar-item <?php echo ($pagina_atual === 'profile.php') ? 'is-active' : ''; ?>" href="profile.php">
                            Meu Perfil
                        </a>
                    <?php } ?>
                </div>
                <div class="navbar-end">
                    <div class="navbar-item">
                        <div class="buttons">
                            <a class="button is-light" href="<?php echo $base_url; ?>/logout.php">
                                Sair
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <section class="section">
        <div class="container">
