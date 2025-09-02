<?php
include('../includes/auth.php');
verifica_login();
verifica_acesso(1); // O ID do nível de acesso 'Admin' é 1

include('../includes/db.php');

$mensagem = "";

// Lógica para adicionar um novo usuário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'adicionar') {
    $nome = $_POST['nome'];
    $sobrenome = $_POST['sobrenome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $cargo_id = $_POST['cargo'];
    $departamento_id = $_POST['departamento'];
    $nivel_acesso_id = $_POST['nivel_acesso'];

    // Validar o e-mail para domínios específicos
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || (!str_ends_with($email, '@haut.com.br') && !str_ends_with($email, '@haut.email'))) {
        $mensagem = "<div class='notification is-danger'>E-mail inválido ou domínio não permitido.</div>";
    } else {
        $sql = "INSERT INTO usuarios (nome, sobrenome, email, senha, cargo_id, departamento_id, nivel_acesso_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssiiii", $nome, $sobrenome, $email, $senha, $cargo_id, $departamento_id, $nivel_acesso_id);
        if ($stmt->execute()) {
            $mensagem = "<div class='notification is-success'>Usuário adicionado com sucesso!</div>";
        } else {
            $mensagem = "<div class='notification is-danger'>Erro ao adicionar usuário: " . $stmt->error . "</div>";
        }
    }
}

// Lógica para excluir um usuário
if (isset($_GET['acao']) && $_GET['acao'] === 'excluir' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $mensagem = "<div class='notification is-success'>Usuário excluído com sucesso!</div>";
    } else {
        $mensagem = "<div class='notification is-danger'>Erro ao excluir usuário: " . $stmt->error . "</div>";
    }
}

// Lógica para editar um usuário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'editar') {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $sobrenome = $_POST['sobrenome'];
    $cargo_id = $_POST['cargo'];
    $departamento_id = $_POST['departamento'];
    $nivel_acesso_id = $_POST['nivel_acesso'];

    $sql = "UPDATE usuarios SET nome = ?, sobrenome = ?, cargo_id = ?, departamento_id = ?, nivel_acesso_id = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiiii", $nome, $sobrenome, $cargo_id, $departamento_id, $nivel_acesso_id, $id);
    if ($stmt->execute()) {
        $mensagem = "<div class='notification is-success'>Usuário editado com sucesso!</div>";
    } else {
        $mensagem = "<div class='notification is-danger'>Erro ao editar usuário: " . $stmt->error . "</div>";
    }
}


// Consultas para popular os dropdowns
$cargos = $conn->query("SELECT * FROM cargos ORDER BY nome");
$departamentos = $conn->query("SELECT * FROM departamentos ORDER BY nome");
$niveis_acesso = $conn->query("SELECT * FROM niveis_acesso ORDER BY nome");
$usuarios = $conn->query("SELECT u.id, u.nome, u.sobrenome, u.email, c.nome as cargo, d.nome as departamento, n.nome as nivel_acesso FROM usuarios u JOIN cargos c ON u.cargo_id = c.id JOIN departamentos d ON u.departamento_id = d.id JOIN niveis_acesso n ON u.nivel_acesso_id = n.id ORDER BY u.nome");
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Usuários - Forms Haut</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
</head>
<body>
    <nav class="navbar is-info">
        <div class="navbar-brand">
            <a class="navbar-item" href="index.php">Dashboard Admin</a>
        </div>
        <div class="navbar-menu">
            <div class="navbar-end">
                <a class="navbar-item" href="usuarios.php">Usuários</a>
                <a class="navbar-item" href="departamentos.php">Departamentos</a>
                <a class="navbar-item" href="cargos.php">Cargos</a>
                <a class="navbar-item" href="../logout.php">Sair</a>
            </div>
        </div>
    </nav>

    <section class="section">
        <div class="container">
            <h1 class="title">Gerenciar Usuários</h1>
            <?php echo $mensagem; ?>

            <!-- Formulário de Adicionar/Editar Usuário -->
            <div class="box">
                <h2 class="title is-4">Adicionar Novo Usuário</h2>
                <form action="usuarios.php" method="POST">
                    <input type="hidden" name="acao" value="adicionar">
                    <div class="field">
                        <label class="label">Nome</label>
                        <div class="control">
                            <input class="input" type="text" name="nome" required>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Sobrenome</label>
                        <div class="control">
                            <input class="input" type="text" name="sobrenome" required>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">E-mail Corporativo</label>
                        <div class="control">
                            <input class="input" type="email" name="email" placeholder="@haut.com.br ou @haut.email" required>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Senha</label>
                        <div class="control">
                            <input class="input" type="password" name="senha" required>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Cargo</label>
                        <div class="control">
                            <div class="select is-fullwidth">
                                <select name="cargo" required>
                                    <?php while ($cargo = $cargos->fetch_assoc()) { ?>
                                        <option value="<?php echo $cargo['id']; ?>"><?php echo htmlspecialchars($cargo['nome']); ?></option>
                                    <?php } $cargos->data_seek(0); ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Departamento</label>
                        <div class="control">
                            <div class="select is-fullwidth">
                                <select name="departamento" required>
                                    <?php while ($departamento = $departamentos->fetch_assoc()) { ?>
                                        <option value="<?php echo $departamento['id']; ?>"><?php echo htmlspecialchars($departamento['nome']); ?></option>
                                    <?php } $departamentos->data_seek(0); ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Nível de Acesso</label>
                        <div class="control">
                            <div class="select is-fullwidth">
                                <select name="nivel_acesso" required>
                                    <?php while ($nivel = $niveis_acesso->fetch_assoc()) { ?>
                                        <option value="<?php echo $nivel['id']; ?>"><?php echo htmlspecialchars($nivel['nome']); ?></option>
                                    <?php } $niveis_acesso->data_seek(0); ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="field">
                        <div class="control">
                            <button class="button is-primary" type="submit">Adicionar Usuário</button>
                        </div>
                    </div>
                </form>
            </div>

            <h2 class="title is-4 mt-6">Lista de Usuários</h2>
            <div class="table-container">
                <table class="table is-striped is-fullwidth is-hoverable">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Cargo</th>
                            <th>Departamento</th>
                            <th>Nível de Acesso</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($usuario = $usuarios->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($usuario['nome'] . " " . $usuario['sobrenome']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['cargo']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['departamento']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['nivel_acesso']); ?></td>
                                <td>
                                    <a href="?acao=editar&id=<?php echo $usuario['id']; ?>" class="button is-small is-warning">Editar</a>
                                    <a href="?acao=excluir&id=<?php echo $usuario['id']; ?>" class="button is-small is-danger" onclick="return confirm('Tem certeza que deseja excluir este usuário?');">Excluir</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</body>
</html>
