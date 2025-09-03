<?php
// Inclui o cabeçalho, que tem a navegação e o início do HTML
$titulo_pagina = "Dashboard Admin";
include('../includes/header.php');

// Apenas usuários com nível de acesso 1 (Admin) podem acessar esta página
verifica_login();
verifica_acesso(1);
?>

<h1 class="title is-2">Bem-vindo(a) ao Painel do Administrador!</h1>
<p class="subtitle is-5">Aqui você pode gerenciar todos os aspectos do seu sistema.</p>

<div class="columns is-multiline mt-5">
    <!-- Card para Gerenciar Usuários -->
    <div class="column is-one-third">
        <div class="card has-background-info-light">
            <div class="card-content">
                <p class="title is-4 has-text-info">Gerenciar Usuários</p>
                <p class="subtitle is-6">Adicione, edite e remova usuários do sistema.</p>
                <a href="usuarios.php" class="button is-info is-light mt-3">Acessar</a>
            </div>
        </div>
    </div>
    
    <!-- Card para Gerenciar Departamentos -->
    <div class="column is-one-third">
        <div class="card has-background-link-light">
            <div class="card-content">
                <p class="title is-4 has-text-link">Gerenciar Departamentos</p>
                <p class="subtitle is-6">Cadastre os departamentos e áreas da empresa.</p>
                <a href="departamentos.php" class="button is-link is-light mt-3">Acessar</a>
            </div>
        </div>
    </div>

    <!-- Card para Gerenciar Cargos -->
    <div class="column is-one-third">
        <div class="card has-background-primary-light">
            <div class="card-content">
                <p class="title is-4 has-text-primary">Gerenciar Cargos</p>
                <p class="subtitle is-6">Defina os cargos para cada funcionário.</p>
                <a href="cargos.php" class="button is-primary is-light mt-3">Acessar</a>
            </div>
        </div>
    </div>

    <!-- Card para Gerar Snippet de Código -->
    <div class="column is-one-third">
        <div class="card has-background-warning-light">
            <div class="card-content">
                <p class="title is-4 has-text-warning">Gerar Snippet de Acesso</p>
                <p class="subtitle is-6">Crie o código de segurança para seus formulários.</p>
                <a href="snippet.php" class="button is-warning is-light mt-3">Acessar</a>
            </div>
        </div>
    </div>
</div>

<?php
// Inclui o rodapé que tem o final do HTML
include('../includes/footer.php');
?>
