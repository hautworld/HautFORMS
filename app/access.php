<?php // app/access.php
function user_level_rank(PDO $pdo, int $userId): ?int
{
    $sql = "SELECT al.rank
FROM users u
LEFT JOIN roles r ON u.role_id = r.id
LEFT JOIN access_levels al ON r.access_level_id = al.id
WHERE u.id = ?";
    $st = $pdo->prepare($sql);
    $st->execute([$userId]);
    $rank = $st->fetchColumn();
    return $rank !== false ? (int)$rank : null;
}


// Checa se usuário tem nível <= requerido (ex.: requerido=3 => permite Diretor(1), Gerente(2), Supervisor(3))
function require_min_level(PDO $pdo, int $requiredRank)
{
    if (!isset($_SESSION['user']['id'])) {
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
    $rank = user_level_rank($pdo, (int)$_SESSION['user']['id']);
    if ($rank === null || $rank > $requiredRank) {
        header('Location: ' . BASE_URL . '/dashboard.php');
        exit;
    }
}


// Checa por cargo específico
function require_role(PDO $pdo, string $roleName)
{
    if (!isset($_SESSION['user']['id'])) {
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
    $sql = "SELECT r.name FROM users u JOIN roles r ON u.role_id=r.id WHERE u.id=?";
    $st = $pdo->prepare($sql);
    $st->execute([$_SESSION['user']['id']]);
    $name = $st->fetchColumn();
    if (!$name || strcasecmp($name, $roleName) !== 0) {
        header('Location: ' . BASE_URL . '/dashboard.php');
        exit;
    }
}


// Gera snippet para colar no topo de um formulário/processador
function generate_access_snippet_for_level(int $requiredRank): string
{
    return <<<PHP
<?php
require_once __DIR__.'/../app/config.php';
require_once __DIR__.'/../app/db.php';
require_once __DIR__.'/../app/access.php';
require_min_level(\$pdo, {$requiredRank}); // exige nível mínimo (número menor = mais poder)
?>
PHP;
}


function generate_access_snippet_for_role(string $roleName): string
{
    $safe = addslashes($roleName);
    return <<<PHP
<?php
require_once __DIR__.'/../app/config.php';
require_once __DIR__.'/../app/db.php';
require_once __DIR__.'/../app/access.php';
require_role(\$pdo, '{$safe}'); // exige cargo específico
?>
PHP;
}
