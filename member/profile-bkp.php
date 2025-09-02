<?php
$titulo_pagina = "Meu Perfil";
include('../includes/auth.php');
include('../includes/db.php');
include('../includes/header.php');

verifica_login();

$mensagem = "";

// Carrega os dados do usuário logado
$user_id = $_SESSION['user_id'];
$sql = "SELECT id, nome, sobrenome, email, cargo, departamento FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

// Lógica para atualizar o perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'atualizar_perfil') {
    $nome = trim($_POST['nome']);
    $sobrenome = trim($_POST['sobrenome']);
    $cargo = trim($_POST['cargo']);
    $departamento = trim($_POST['departamento']);
    
    $sql_update = "UPDATE usuarios SET nome = ?, sobrenome = ?, cargo = ?, departamento = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ssssi", $nome, $sobrenome, $cargo, $departamento, $user_id);
    if ($stmt_update->execute()) {
        $mensagem = "<div class='notification is-success'>Perfil atualizado com sucesso!</div>";
        // Recarrega os dados do usuário para exibir as informações atualizadas
        $usuario['nome'] = $nome;
        $usuario['sobrenome'] = $sobrenome;
        $usuario['cargo'] = $cargo;
        $usuario['departamento'] = $departamento;
    } else {
        $mensagem = "<div class='notification is-danger'>Erro ao atualizar perfil.</div>";
    }
}

// Lógica para mudar a senha
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'mudar_senha') {
    $nova_senha = $_POST['nova_senha'];
    if (!empty($nova_senha)) {
        // Gera o hash da nova senha
        $nova_senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
        
        $sql_senha = "UPDATE usuarios SET senha = ? WHERE id = ?";
        $stmt_senha = $conn->prepare($sql_senha);
        $stmt_senha->bind_param("si", $nova_senha_hash, $user_id);
        if ($stmt_senha->execute()) {
            $mensagem = "<div class='notification is-success'>Senha alterada com sucesso!</div>";
        } else {
            $mensagem = "<div class='notification is-danger'>Erro ao mudar a senha.</div>";
        }
    } else {
        $mensagem = "<div class='notification is-warning'>O campo de senha não pode estar vazio.</div>";
    }
}
?>

<h1 class="title is-2">Meu Perfil</h1>
<p class="subtitle is-5">Edite suas informações e mude sua senha.</p>

<?php echo $mensagem; ?>

<div class="columns">
    <div class="column is-half">
        <div class="box">
            <h2 class="title is-4">Informações do Perfil</h2>
            <form action="profile.php" method="POST">
                <input type="hidden" name="acao" value="atualizar_perfil">
                
                <div class="field">
                    <label class="label">Nome</label>
                    <div class="control">
                        <input class="input" type="text" name="nome" value="<?php echo htmlspecialchars($usuario['nome']); ?>" required>
                    </div>
                </div>

                <div class="field">
                    <label class="label">Sobrenome</label>
                    <div class="control">
                        <input class="input" type="text" name="sobrenome" value="<?php echo htmlspecialchars($usuario['sobrenome']); ?>" required>
                    </div>
                </div>

                <div class="field">
                    <label class="label">E-mail</label>
                    <div class="control">
                        <input class="input" type="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" disabled>
                    </div>
                </div>

                <div class="field">
                    <label class="label">Cargo</label>
                    <div class="control">
                        <input class="input" type="text" name="cargo" value="<?php echo htmlspecialchars($usuario['cargo']); ?>" required>
                    </div>
                </div>

                <div class="field">
                    <label class="label">Departamento</label>
                    <div class="control">
                        <input class="input" type="text" name="departamento" value="<?php echo htmlspecialchars($usuario['departamento']); ?>" required>
                    </div>
                </div>

                <div class="control">
                    <button class="button is-primary" type="submit">Atualizar Perfil</button>
                </div>
            </form>
        </div>
    </div>

    <div class="column is-half">
        <div class="box">
            <h2 class="title is-4">Mudar Senha</h2>
            <form action="profile.php" method="POST">
                <input type="hidden" name="acao" value="mudar_senha">
                
                <div class="field">
                    <label class="label">Nova Senha</label>
                    <div class="control">
                        <input class="input" type="password" name="nova_senha" required>
                    </div>
                </div>

                <div class="control">
                    <button class="button is-warning" type="submit">Mudar Senha</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
include('../includes/footer.php');
?>
