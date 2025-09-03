<?php
// Este é o snippet de verificação de acesso gerado na área do admin.
// Suponha que este formulário é apenas para Diretores (nível de acesso 2).
include('../includes/auth.php');
verifica_login();
verifica_acesso(2);

// Lógica para processar o formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Aqui você deve validar e sanitizar os dados recebidos.
    // Exemplo de como obter os dados:
    $solicitante = $_POST['solicitante'];
    $valor = $_POST['valor'];
    $descricao = $_POST['descricao'];
    $data_solicitacao = date('Y-m-d H:i:s');

    // Aqui você insere a lógica para salvar os dados no banco de dados.
    // Lembre-se de usar prepared statements para evitar injeção SQL.
    // Exemplo:
    // $sql = "INSERT INTO solicitacoes_financeiras (solicitante, valor, descricao, data_solicitacao) VALUES (?, ?, ?, ?)";
    // $stmt = $conn->prepare($sql);
    // $stmt->bind_param("sds", $solicitante, $valor, $descricao);
    // $stmt->execute();

    // Mensagem de sucesso
    $mensagem = "Solicitação enviada com sucesso!";
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulário Financeiro - Forms Haut</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
</head>
<body>
    <nav class="navbar is-info">
        <div class="navbar-brand">
            <a class="navbar-item" href="../member/index.php">Voltar para o Dashboard</a>
        </div>
    </nav>
    <section class="section">
        <div class="container">
            <h1 class="title">Formulário Financeiro</h1>
            <p class="subtitle">Preencha os dados da solicitação financeira.</p>

            <?php if (isset($mensagem)) { ?>
                <div class="notification is-success"><?php echo $mensagem; ?></div>
            <?php } ?>
            
            <form action="financeiro.php" method="POST">
                <!-- Campos do formulário financeiro -->
                <div class="field">
                    <label class="label">Nome do Solicitante</label>
                    <div class="control">
                        <input class="input" type="text" name="solicitante" placeholder="Seu nome completo" required>
                    </div>
                </div>

                <div class="field">
                    <label class="label">Valor da Solicitação</label>
                    <div class="control">
                        <input class="input" type="number" name="valor" placeholder="Ex: 500.50" step="0.01" required>
                    </div>
                </div>

                <div class="field">
                    <label class="label">Descrição</label>
                    <div class="control">
                        <textarea class="textarea" name="descricao" placeholder="Descreva o motivo da solicitação" required></textarea>
                    </div>
                </div>

                <div class="field is-grouped">
                    <div class="control">
                        <button class="button is-link" type="submit">Enviar Solicitação</button>
                    </div>
                    <div class="control">
                        <button class="button is-link is-light" type="reset">Limpar</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
</body>
</html>
