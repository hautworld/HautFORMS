<?php

include('../includes/auth.php');
verifica_login();
verifica_acesso(1); // O ID do nível de acesso 'Admin' é 1

include('../includes/db.php');

$mensagem = "";
$cargo_a_editar = null;

// Lógica para adicionar um novo cargo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'adicionar') {
    $nome = trim($_POST['nome']);
    if (!empty($nome)) {
        $sql = "INSERT INTO cargos (nome) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $nome);
        if ($stmt->execute()) {
            $mensagem = "<div class='notification is-success'>Cargo adicionado com sucesso!</div>";
        } else {
            $mensagem = "<div class='notification is-danger'>Erro ao adicionar cargo: " . $stmt->error . "</div>";
        }
    }
}

// Lógica para excluir um cargo
if (isset($_GET['acao']) && $_GET['acao'] === 'excluir' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM cargos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $mensagem = "<div class='notification is-success'>Cargo excluído com sucesso!</div>";
    } else {
        $mensagem = "<div class='notification is-danger'>Erro ao excluir cargo: " . $stmt->error . "</div>";
    }
}

// Lógica para carregar cargo para edição
if (isset($_GET['acao']) && $_GET['acao'] === 'editar' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM cargos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $cargo_a_editar = $result->fetch_assoc();
    }
}

// Lógica para atualizar um cargo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'atualizar') {
    $id = $_POST['id'];
    $nome = trim($_POST['nome']);
    if (!empty($nome)) {
        $sql = "UPDATE cargos SET nome = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $nome, $id);
        if ($stmt->execute()) {
            $mensagem = "<div class='notification is-success'>Cargo atualizado com sucesso!</div>";
        } else {
            $mensagem = "<div class='notification is-danger'>Erro ao atualizar cargo: " . $stmt->error . "</div>";
        }
    }
}


// Consultar todos os cargos para exibir na tabela
$cargos = $conn->query("SELECT * FROM cargos ORDER BY nome");

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Cargos - Forms Haut</title>
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
            <h1 class="title">Gerenciar Cargos</h1>
            <?php echo $mensagem; ?>

            <div class="box">
                <h2 class="title is-4"><?php echo $cargo_a_editar ? 'Editar Cargo' : 'Adicionar Novo Cargo'; ?></h2>
                <form action="cargos.php" method="POST">
                    <input type="hidden" name="acao" value="<?php echo $cargo_a_editar ? 'atualizar' : 'adicionar'; ?>">
                    <?php if ($cargo_a_editar) { ?>
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($cargo_a_editar['id']); ?>">
                    <?php } ?>
                    <div class="field">
                        <label class="label">Nome do Cargo</label>
                        <div class="control">
                            <input class="input" type="text" name="nome" value="<?php echo $cargo_a_editar ? htmlspecialchars($cargo_a_editar['nome']) : ''; ?>" required>
                        </div>
                    </div>
                    <div class="field">
                        <div class="control">
                            <button class="button is-primary" type="submit"><?php echo $cargo_a_editar ? 'Atualizar' : 'Adicionar'; ?></button>
                        </div>
                    </div>
                </form>
            </div>

            <h2 class="title is-4 mt-6">Lista de Cargos</h2>
            <div class="table-container">
                <table class="table is-striped is-fullwidth is-hoverable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($cargo = $cargos->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($cargo['id']); ?></td>
                                <td><?php echo htmlspecialchars($cargo['nome']); ?></td>
                                <td>
                                    <a href="?acao=editar&id=<?php echo $cargo['id']; ?>" class="button is-small is-warning">Editar</a>
                                    <a href="?acao=excluir&id=<?php echo $cargo['id']; ?>" class="button is-small is-danger" onclick="return confirm('Tem certeza que deseja excluir este cargo?');">Excluir</a>
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
