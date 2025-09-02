<?php
require_once 'includes/session.php';
require_once 'includes/db.php';

$mensagem = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email']) && isset($_POST['senha'])) {
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    try {
        $sql = "SELECT id, nome, senha, nivel_acesso_id FROM usuarios WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $usuario = $result->fetch_assoc();
            
            // Verifica se a senha digitada corresponde ao hash no banco de dados
            if (password_verify($senha, $usuario['senha'])) {
                $_SESSION['user_id'] = $usuario['id'];
                $_SESSION['user_nome'] = $usuario['nome'];
                $_SESSION['nivel_acesso_id'] = $usuario['nivel_acesso_id'];

                // Redireciona para o dashboard correto com base no nível de acesso
                if ($_SESSION['nivel_acesso_id'] == 1) {
                    header("Location: admin/index.php");
                } else {
                    header("Location: member/index.php");
                }
                exit();
            } else {
                $mensagem = "E-mail ou senha incorretos.";
            }
        } else {
            $mensagem = "E-mail ou senha incorretos.";
        }
    } catch (mysqli_sql_exception $e) {
        // Em um ambiente de produção, você deveria logar o erro em vez de exibi-lo.
        // error_log($e->getMessage());
        $mensagem = "Ocorreu um erro no sistema. Por favor, tente novamente mais tarde.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Forms Haut 2</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
</head>
<body>
<section class="hero is-fullheight">
    <div class="hero-body">
        <div class="container has-text-centered">
            <div class="column is-4 is-offset-4">
                <h3 class="title has-text-grey">Forms Haut</h3>
                <p class="subtitle has-text-grey">Faça login para continuar.</p>
                <div class="box">
                    <figure class="avatar">
                        <img src="https://placehold.co/128x128/eeeeee/ffffff?text=User" alt="Login Avatar">
                    </figure>
                    <form action="index.php" method="POST">
                        <div class="field">
                            <div class="control">
                                <input class="input is-large" type="email" name="email" placeholder="Seu e-mail corporativo" autofocus="" required>
                            </div>
                        </div>
                        <div class="field">
                            <div class="control">
                                <input class="input is-large" type="password" name="senha" placeholder="Sua senha" required>
                            </div>
                        </div>
                        <button class="button is-block is-info is-large is-fullwidth" type="submit">Entrar</button>
                    </form>
                </div>
                <?php if ($mensagem): ?>
                <p class="has-text-grey"><?php echo $mensagem; ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
</body>
</html>