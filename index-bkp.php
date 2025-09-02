<?php
session_start();
include('includes/db.php');

// Lógica de autenticação
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
        if (password_verify($senha, $usuario['senha'])) {
            $_SESSION['user_id'] = $usuario['id'];
            $_SESSION['user_name'] = $usuario['nome'];
            $_SESSION['nivel_acesso_id'] = $usuario['nivel_acesso_id'];
            header("Location: member/");
            exit();
        } else {
            $error = "Senha incorreta.";
        }
    } else {
        $error = "E-mail não encontrado.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - HautForms</title>
    <link rel="stylesheet" href="[https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css](https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css)">
</head>
<body>
    <section class="hero is-fullheight">
        <div class="hero-body">
            <div class="container has-text-centered">
                <div class="column is-4 is-offset-4">
                    <h3 class="title has-text-black">Login</h3>
                    <hr class="login-hr">
                    <p class="subtitle has-text-black">Por favor, faça login para continuar.</p>
                    <div class="box">
                        <?php if (isset($error)) { ?>
                            <div class="notification is-danger"><?php echo $error; ?></div>
                        <?php } ?>
                        <form action="index.php" method="POST">
                            <div class="field">
                                <div class="control">
                                    <input class="input is-large" type="email" name="email" placeholder="Seu E-mail Corporativo" autofocus="" required>
                                </div>
                            </div>
                            <div class="field">
                                <div class="control">
                                    <input class="input is-large" type="password" name="senha" placeholder="Sua Senha" required>
                                </div>
                            </div>
                            <button class="button is-block is-info is-large is-fullwidth" type="submit">Entrar <i class="fa fa-sign-in" aria-hidden="true"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>
