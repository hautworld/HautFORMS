<?php
include('../includes/auth.php');
verifica_login();
verifica_acesso(1); // Supondo que 1 é o ID do nível de acesso para Admin
include('../includes/db.php');

$niveis = $conn->query("SELECT * FROM niveis_acesso");

$snippet = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nivel_acesso'])) {
    $nivel_id = $_POST['nivel_acesso'];
    $snippet = "<?php\n";
    $snippet .= "include('../includes/auth.php');\n";
    $snippet .= "verifica_login();\n";
    $snippet .= "verifica_acesso(" . $nivel_id . ");\n";
    $snippet .= "?>";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerar Snippet - HautForms</title>
    <link rel="stylesheet" href="[https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css](https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css)">
</head>
<body>
    <!-- Navbar e seções de admin aqui -->
    <section class="section">
        <div class="container">
            <h1 class="title">Gerar Snippet de Acesso</h1>
            <form action="snippet.php" method="POST">
                <div class="field">
                    <label class="label">Selecione o Nível de Acesso</label>
                    <div class="control">
                        <div class="select">
                            <select name="nivel_acesso" required>
                                <?php while ($nivel = $niveis->fetch_assoc()) { ?>
                                    <option value="<?php echo $nivel['id']; ?>">
                                        <?php echo htmlspecialchars($nivel['nome']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="field">
                    <div class="control">
                        <button class="button is-primary" type="submit">Gerar Snippet</button>
                    </div>
                </div>
            </form>
            <?php if (!empty($snippet)) { ?>
                <div class="box mt-4">
                    <p class="subtitle">Código a ser inserido no formulário:</p>
                    <pre><code><?php echo htmlspecialchars($snippet); ?></code></pre>
                </div>
            <?php } ?>
        </div>
    </section>
</body>
</html>
