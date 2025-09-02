<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';

// Redireciona se não estiver logado
if (!$_SESSION['logado']) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$mensagem = "";
$dados_usuario = null;

// Busca os dados do usuário logado
$sql_user = "SELECT id, nome, sobrenome, email, cargo_id, departamento_id, nivel_acesso_id FROM usuarios WHERE id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
if ($result_user->num_rows > 0) {
    $dados_usuario = $result_user->fetch_assoc();
}
$stmt_user->close();

// Lógica para atualizar dados do perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'atualizar_perfil') {
    $nome = trim($_POST['nome']);
    $sobrenome = trim($_POST['sobrenome']);
    $cargo_id = (int)$_POST['cargo_id'];
    $departamento_id = (int)$_POST['departamento_id'];

    $sql = "UPDATE usuarios SET nome=?, sobrenome=?, cargo_id=?, departamento_id=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiii", $nome, $sobrenome, $cargo_id, $departamento_id, $user_id);

    if ($stmt->execute()) {
        $_SESSION['user_nome'] = $nome;
        $mensagem = "<div class='notification is-success'>Perfil atualizado com sucesso!</div>";
    } else {
        $mensagem = "<div class='notification is-danger'>Erro ao atualizar perfil.</div>";
    }
    $stmt->close();
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
        $stmt_senha->close();
    } else {
        $mensagem = "<div class='notification is-warning'>O campo de senha não pode estar vazio.</div>";
    }
}

// Busca os dados de cargos e departamentos para os selects do formulário
$cargos = [];
$sql_cargos = "SELECT id, nome FROM cargos ORDER BY nome";
$result_cargos = $conn->query($sql_cargos);
while ($row = $result_cargos->fetch_assoc()) {
    $cargos[] = $row;
}

$departamentos = [];
$sql_deptos = "SELECT id, nome FROM departamentos ORDER BY nome";
$result_deptos = $conn->query($sql_deptos);
while ($row = $result_deptos->fetch_assoc()) {
    $departamentos[] = $row;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
</head>
<body>
<nav class="navbar is-info">
    <div class="navbar-brand">
        <a class="navbar-item" href="../dashboard.php">
            <h1 class="title is-4 has-text-white">Dashboard</h1>
        </a>
    </div>
    <div class="navbar-menu is-active">
        <div class="navbar-start">
            <a class="navbar-item" href="profile.php">Meu Perfil</a>
        </div>
        <div class="navbar-end">
            <div class="navbar-item">
                <div class="buttons">
                    <a class="button is-light" href="../logout.php">Sair</a>
                </div>
            </div>
        </div>
    </div>
</nav>

<section class="section">
    <div class="container">
        <h1 class="title">Meu Perfil</h1>
        <?php if ($mensagem): ?>
            <div class="notification is-success">
                <?php echo $mensagem; ?>
            </div>
        <?php endif; ?>

        <div class="columns">
            <div class="column is-half">
                <h2 class="title is-4">Atualizar Dados do Perfil</h2>
                <form action="profile.php" method="POST">
                    <input type="hidden" name="acao" value="atualizar_perfil">
                    <div class="field">
                        <label class="label">Nome</label>
                        <div class="control">
                            <input class="input" type="text" name="nome" value="<?php echo htmlspecialchars($dados_usuario['nome']); ?>" required>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Sobrenome</label>
                        <div class="control">
                            <input class="input" type="text" name="sobrenome" value="<?php echo htmlspecialchars($dados_usuario['sobrenome']); ?>" required>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Email Corporativo</label>
                        <div class="control">
                            <input class="input" type="email" value="<?php echo htmlspecialchars($dados_usuario['email']); ?>" disabled>
                        </div>
                        <p class="help">O e-mail não pode ser alterado.</p>
                    </div>
                    <div class="field">
                        <label class="label">Cargo</label>
                        <div class="control">
                            <div class="select is-fullwidth">
                                <select name="cargo_id" required>
                                    <?php foreach ($cargos as $cargo): ?>
                                        <option value="<?php echo $cargo['id']; ?>" <?php echo ($cargo['id'] == $dados_usuario['cargo_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cargo['nome']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Departamento</label>
                        <div class="control">
                            <div class="select is-fullwidth">
                                <select name="departamento_id" required>
                                    <?php foreach ($departamentos as $departamento): ?>
                                        <option value="<?php echo $departamento['id']; ?>" <?php echo ($departamento['id'] == $dados_usuario['departamento_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($departamento['nome']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="field">
                        <div class="control">
                            <button class="button is-primary" type="submit">Salvar Alterações</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="column is-half">
                <h2 class="title is-4">Mudar Senha</h2>
                <form action="profile.php" method="POST">
                    <input type="hidden" name="acao" value="mudar_senha">
                    <div class="field">
                        <label class="label">Nova Senha</label>
                        <div class="control">
                            <input class="input" type="password" name="nova_senha" placeholder="Insira a nova senha" required>
                        </div>
                    </div>
                    <div class="field">
                        <div class="control">
                            <button class="button is-info" type="submit">Mudar Senha</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
</body>
</html>
