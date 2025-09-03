# Haut Forms – PHP/Bulma Starter

Sistema básico em **PHP + MySQL + Bulma CSS** para autenticação, perfis e administração. Pensado para o subdomínio `forms.haut.world` com emails corporativos já pré‑cadastrados. Inclui **snippets de acesso** para validar permissões dentro de qualquer formulário/processador PHP futuro.

---

## Visão Geral
- **Login (sem cadastro público):** somente emails pré‑cadastrados (`@haut.com.br` ou `@haut.email`).
- **Dashboard:** links para páginas de formulários (sem gráficos).
- **Área do Membro:** editar nome de exibição, cargo, departamento, trocar senha.
- **Admin:** CRUD de usuários, departamentos, cargos e níveis de acesso. Regras simples de autorização.
- **Snippets de Acesso:** gere e cole nos seus formulários HTML/PHP para restringir uso por **nível** (Diretor, Gerente...) ou por **cargo** específico.
- **Segurança básica:** sessões seguras, `password_hash`, PDO + prepared statements, CSRF token, rate limit simples no login.

---

## Requisitos
- PHP 8.1+
- MySQL 8+
- Composer (opcional; aqui não usamos libs externas)
- Servidor com sessões habilitadas

---

## Estrutura de Pastas
```
/haut-forms
├─ /public
│  ├─ index.php                # Redireciona conforme sessão
│  ├─ login.php                # Tela de login (Bulma)
│  ├─ logout.php               # Encerra sessão
│  ├─ dashboard.php            # Área logada
│  ├─ member.php               # Área do Membro (perfil)
│  ├─ /admin
│  │  ├─ index.php             # Home Admin
│  │  ├─ users.php             # CRUD usuários
│  │  ├─ departments.php       # CRUD departamentos
│  │  ├─ roles.php             # CRUD cargos (associam nível)
│  │  ├─ access_levels.php     # CRUD níveis (Diretor, Gerente...)
│  │  └─ snippets.php          # Gera snippets PHP de acesso
│  ├─ /forms-demo              # (Opcional) exemplos
│  │  ├─ form-diretoria.php    # Exemplo restrito a Diretor
│  │  └─ process-diretoria.php # Handler do exemplo
│  └─ /assets
│     └─ bulma.min.css         # Bulma (CDN também é ok)
├─ /app
│  ├─ config.php               # Configurações globais e DB
│  ├─ db.php                   # Conexão PDO
│  ├─ auth.php                 # Login, logout, guardas
│  ├─ csrf.php                 # CSRF helpers
│  ├─ helpers.php              # Sanitização, respostas, validações
│  └─ access.php               # Funções de autorização + geração de snippet
├─ /actions
│  ├─ login.php                # POST login
│  ├─ profile_update.php       # POST atualizar perfil
│  ├─ password_update.php      # POST trocar senha
│  ├─ admin_user_save.php      # POST criar/editar usuário
│  ├─ admin_user_delete.php    # POST remover usuário
│  ├─ admin_department_save.php
│  ├─ admin_department_delete.php
│  ├─ admin_role_save.php
│  ├─ admin_role_delete.php
│  ├─ admin_level_save.php
│  └─ admin_level_delete.php
└─ /sql
   └─ schema.sql               # Tabelas + seeds
```

---

## Banco de Dados (schema.sql)
```sql
-- schema.sql
CREATE TABLE access_levels (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL UNIQUE, -- Diretor, Gerente, Supervisor, Analista, Assistente, Estagiario
  rank INT NOT NULL                 -- quanto menor o número, maior o poder (ex.: Diretor=1)
);

CREATE TABLE departments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE roles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE,
  access_level_id INT NOT NULL,
  FOREIGN KEY (access_level_id) REFERENCES access_levels(id)
);

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(100) NOT NULL,
  last_name VARCHAR(100) NOT NULL,
  email VARCHAR(200) NOT NULL UNIQUE,
  display_name VARCHAR(150) NULL,
  department_id INT NULL,
  role_id INT NULL,
  password_hash VARCHAR(255) NOT NULL,
  is_admin TINYINT(1) NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (department_id) REFERENCES departments(id),
  FOREIGN KEY (role_id) REFERENCES roles(id)
);

-- Seeds iniciais de níveis (ajuste como quiser)
INSERT INTO access_levels (name, rank) VALUES
 ('Diretor', 1),
 ('Gerente', 2),
 ('Supervisor', 3),
 ('Analista', 4),
 ('Assistente', 5),
 ('Estagiario', 6);

-- Departamentos e Cargos podem ser cadastrados pelo Admin UI

-- Usuário admin inicial (troque a hash por uma gerada no PHP)
-- senha: TrocarDepois!
INSERT INTO users (first_name, last_name, email, password_hash, is_admin)
VALUES ('Haut', 'Admin', 'admin@haut.email', '$2y$10$0HfV2g2B6v1aY4GQvF4mReYyJtC0wN9m3Jz8pQZ4l7yH3sTr3H4QK', 1);
```

> **Observação:** gere sua própria hash com `password_hash('SuaSenhaForte', PASSWORD_DEFAULT)` e substitua no seed.

---

## Configuração (app/config.php)
```php
<?php
// app/config.php
session_set_cookie_params([
  'lifetime' => 0,
  'path' => '/',
  'domain' => 'forms.haut.world',
  'secure' => true,
  'httponly' => true,
  'samesite' => 'Lax',
]);
session_start();

define('DB_HOST', 'localhost');
define('DB_NAME', 'haut_forms');
define('DB_USER', 'root');
define('DB_PASS', 'secret');

define('ALLOWED_EMAIL_DOMAINS', ['haut.com.br', 'haut.email']);

// URL base
define('BASE_URL', 'https://forms.haut.world');
```

---

## Conexão PDO (app/db.php)
```php
<?php
// app/db.php
require_once __DIR__.'/config.php';

try {
  $pdo = new PDO(
    'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4',
    DB_USER,
    DB_PASS,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
  );
} catch (PDOException $e) {
  http_response_code(500);
  exit('Erro DB');
}
```

---

## Helpers e CSRF (app/helpers.php, app/csrf.php)
```php
<?php // app/helpers.php
function sanitize($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }

function json_redirect($url) {
  header('Location: '.$url);
  exit;
}

function valid_corporate_email(string $email): bool {
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return false;
  $domain = strtolower(substr(strrchr($email, '@'), 1));
  return in_array($domain, ALLOWED_EMAIL_DOMAINS, true);
}
```

```php
<?php // app/csrf.php
if (!isset($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
function csrf_token() { return $_SESSION['csrf_token'] ?? ''; }
function csrf_input() { echo '<input type="hidden" name="csrf" value="'.csrf_token().'">'; }
function csrf_check($token) { return hash_equals($_SESSION['csrf_token'] ?? '', $token ?? ''); }
```

---

## Autenticação e Autorização (app/auth.php, app/access.php)
```php
<?php // app/auth.php
require_once __DIR__.'/db.php';
require_once __DIR__.'/helpers.php';

function login_rate_key() { return 'login_attempts_'.($_SERVER['REMOTE_ADDR'] ?? 'ip'); }

function can_attempt_login(): bool {
  $key = login_rate_key();
  $attempts = $_SESSION[$key]['count'] ?? 0;
  $last = $_SESSION[$key]['time'] ?? 0;
  if ($attempts >= 5 && (time() - $last) < 300) return false; // 5 tentativas em 5min
  return true;
}

function register_login_attempt($success) {
  $key = login_rate_key();
  if ($success) { $_SESSION[$key] = ['count' => 0, 'time' => time()]; return; }
  $attempts = $_SESSION[$key]['count'] ?? 0;
  $_SESSION[$key] = ['count' => $attempts + 1, 'time' => time()];
}

function current_user() { return $_SESSION['user'] ?? null; }
function is_logged() { return !!current_user(); }
function is_admin() { return is_logged() && !empty($_SESSION['user']['is_admin']); }

function require_login() {
  if (!is_logged()) { json_redirect(BASE_URL.'/login.php'); }
}

function require_admin() {
  require_login();
  if (!is_admin()) { json_redirect(BASE_URL.'/dashboard.php'); }
}
```

```php
<?php // app/access.php
require_once __DIR__.'/db.php';

// Retorna o rank numérico do nível do usuário logado (1 é o mais alto)
function user_level_rank(PDO $pdo, int $userId): ?int {
  $sql = "SELECT al.rank
          FROM users u
          LEFT JOIN roles r ON u.role_id = r.id
          LEFT JOIN access_levels al ON r.access_level_id = al.id
          WHERE u.id = ?";
  $st = $pdo->prepare($sql); $st->execute([$userId]);
  $rank = $st->fetchColumn();
  return $rank !== false ? (int)$rank : null;
}

// Checa se usuário tem nível <= requerido (ex.: requerido=3 => permite Diretor(1), Gerente(2), Supervisor(3))
function require_min_level(PDO $pdo, int $requiredRank) {
  if (!isset($_SESSION['user']['id'])) { header('Location: '.BASE_URL.'/login.php'); exit; }
  $rank = user_level_rank($pdo, (int)$_SESSION['user']['id']);
  if ($rank === null || $rank > $requiredRank) {
    header('Location: '.BASE_URL.'/dashboard.php');
    exit;
  }
}

// Checa por cargo específico
function require_role(PDO $pdo, string $roleName) {
  if (!isset($_SESSION['user']['id'])) { header('Location: '.BASE_URL.'/login.php'); exit; }
  $sql = "SELECT r.name FROM users u JOIN roles r ON u.role_id=r.id WHERE u.id=?";
  $st = $pdo->prepare($sql); $st->execute([$_SESSION['user']['id']]);
  $name = $st->fetchColumn();
  if (!$name || strcasecmp($name, $roleName) !== 0) {
    header('Location: '.BASE_URL.'/dashboard.php');
    exit;
  }
}

// Gera snippet para colar no topo de um formulário/processador
function generate_access_snippet_for_level(int $requiredRank): string {
  return <<<PHP
<?php
require_once __DIR__.'/../app/config.php';
require_once __DIR__.'/../app/db.php';
require_once __DIR__.'/../app/access.php';
require_min_level(\$pdo, {$requiredRank}); // exige nível mínimo (número menor = mais poder)
?>
PHP;
}

function generate_access_snippet_for_role(string $roleName): string {
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
```

---

## Login (public/login.php) e Ação (actions/login.php)
```php
<?php // public/login.php
require_once __DIR__.'/../app/config.php';
require_once __DIR__.'/../app/csrf.php';
if (isset($_SESSION['user'])) { header('Location: '.BASE_URL.'/dashboard.php'); exit; }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login • Haut Forms</title>
  <link rel="stylesheet" href="/assets/bulma.min.css">
</head>
<body>
<section class="section">
  <div class="container">
    <div class="column is-half is-offset-one-quarter">
      <h1 class="title">Login</h1>
      <form method="post" action="/actions/login.php">
        <?php csrf_input(); ?>
        <div class="field">
          <label class="label">Email corporativo</label>
          <div class="control has-icons-left">
            <input class="input" type="email" name="email" required>
            <span class="icon is-small is-left"><i class="fas fa-user"></i></span>
          </div>
        </div>
        <div class="field">
          <label class="label">Senha</label>
          <div class="control has-icons-left">
            <input class="input" type="password" name="password" required>
            <span class="icon is-small is-left"><i class="fas fa-lock"></i></span>
          </div>
        </div>
        <div class="field">
          <button class="button is-primary" type="submit">Entrar</button>
        </div>
        <?php if (!empty($_GET['e'])): ?>
          <p class="has-text-danger"><?= sanitize($_GET['e']) ?></p>
        <?php endif; ?>
      </form>
    </div>
  </div>
</section>
</body>
</html>
```

```php
<?php // actions/login.php
require_once __DIR__.'/../app/config.php';
require_once __DIR__.'/../app/db.php';
require_once __DIR__.'/../app/auth.php';
require_once __DIR__.'/../app/helpers.php';
require_once __DIR__.'/../app/csrf.php';

if (!can_attempt_login()) { json_redirect(BASE_URL.'/login.php?e=Tente novamente em alguns minutos'); }

if (!csrf_check($_POST['csrf'] ?? '')) { json_redirect(BASE_URL.'/login.php?e=CSRF inválido'); }

$email = trim($_POST['email'] ?? '');
$pass  = (string)($_POST['password'] ?? '');

if (!valid_corporate_email($email)) { register_login_attempt(false); json_redirect(BASE_URL.'/login.php?e=Domínio não autorizado'); }

$st = $pdo->prepare("SELECT id,first_name,last_name,email,display_name,department_id,role_id,password_hash,is_admin,is_active FROM users WHERE email=? LIMIT 1");
$st->execute([$email]);
$user = $st->fetch(PDO::FETCH_ASSOC);

if (!$user || !$user['is_active'] || !password_verify($pass, $user['password_hash'])) {
  register_login_attempt(false);
  json_redirect(BASE_URL.'/login.php?e=Credenciais inválidas');
}

session_regenerate_id(true);
$_SESSION['user'] = [
  'id' => (int)$user['id'],
  'first_name' => $user['first_name'],
  'last_name' => $user['last_name'],
  'display_name' => $user['display_name'] ?: $user['first_name'],
  'email' => $user['email'],
  'department_id' => $user['department_id'],
  'role_id' => $user['role_id'],
  'is_admin' => (int)$user['is_admin'],
];
register_login_attempt(true);
json_redirect(BASE_URL.'/dashboard.php');
```

---

## Dashboard (public/dashboard.php)
```php
<?php
require_once __DIR__.'/../app/config.php';
require_once __DIR__.'/../app/auth.php';
require_login();
$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard • Haut Forms</title>
  <link rel="stylesheet" href="/assets/bulma.min.css">
</head>
<body>
<section class="section">
  <div class="container">
    <nav class="level">
      <div class="level-left">
        <div class="level-item"><h1 class="title">Olá, <?= sanitize($user['display_name']); ?>!</h1></div>
      </div>
      <div class="level-right">
        <?php if (is_admin()): ?>
          <a class="button is-link mr-2" href="/admin/">Admin</a>
        <?php endif; ?>
        <a class="button mr-2" href="/member.php">Meu Perfil</a>
        <a class="button is-light" href="/logout.php">Sair</a>
      </div>
    </nav>

    <div class="content">
      <h2 class="subtitle">Formulários da Empresa</h2>
      <ul>
        <li><a href="/forms-demo/form-diretoria.php">Solicitação Estratégica (Diretoria)</a></li>
        <!-- Adicione links para seus formulários reais aqui -->
      </ul>
    </div>
  </div>
</section>
</body>
</html>
```

---

## Área do Membro (public/member.php) + ações
```php
<?php
require_once __DIR__.'/../app/config.php';
require_once __DIR__.'/../app/auth.php';
require_once __DIR__.'/../app/db.php';
require_once __DIR__.'/../app/helpers.php';
require_once __DIR__.'/../app/csrf.php';
require_login();
$user = $_SESSION['user'];

$departments = $pdo->query('SELECT id,name FROM departments ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
$roles = $pdo->query('SELECT r.id,r.name, al.name AS level_name FROM roles r JOIN access_levels al ON r.access_level_id=al.id ORDER BY al.rank, r.name')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Meu Perfil • Haut Forms</title>
  <link rel="stylesheet" href="/assets/bulma.min.css">
</head>
<body>
<section class="section">
  <div class="container">
    <h1 class="title">Meu Perfil</h1>

    <form class="box" method="post" action="/actions/profile_update.php">
      <?php csrf_input(); ?>
      <div class="field">
        <label class="label">Nome de exibição</label>
        <div class="control">
          <input class="input" type="text" name="display_name" value="<?= sanitize($user['display_name']); ?>" maxlength="150">
        </div>
      </div>
      <div class="field">
        <label class="label">Departamento</label>
        <div class="control">
          <div class="select is-fullwidth">
            <select name="department_id">
              <option value="">Selecione...</option>
              <?php foreach ($departments as $d): ?>
                <option value="<?= (int)$d['id'] ?>" <?= ($user['department_id']==$d['id']?'selected':'') ?>><?= sanitize($d['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
      </div>
      <div class="field">
        <label class="label">Cargo</label>
        <div class="control">
          <div class="select is-fullwidth">
            <select name="role_id">
              <option value="">Selecione...</option>
              <?php foreach ($roles as $r): ?>
                <option value="<?= (int)$r['id'] ?>" <?= ($user['role_id']==$r['id']?'selected':'') ?>><?= sanitize($r['name']).' ('.sanitize($r['level_name']).')' ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
      </div>
      <div class="field is-grouped">
        <div class="control"><button class="button is-primary" type="submit">Salvar</button></div>
      </div>
    </form>

    <form class="box" method="post" action="/actions/password_update.php">
      <?php csrf_input(); ?>
      <h2 class="subtitle">Mudar Senha</h2>
      <div class="field"><label class="label">Senha atual</label><input class="input" type="password" name="old_password" required></div>
      <div class="field"><label class="label">Nova senha</label><input class="input" type="password" name="new_password" minlength="8" required></div>
      <div class="field"><label class="label">Confirmar nova senha</label><input class="input" type="password" name="confirm_password" minlength="8" required></div>
      <div class="field"><button class="button is-link" type="submit">Alterar senha</button></div>
    </form>
  </div>
</section>
</body>
</html>
```

> As ações `/actions/profile_update.php` e `/actions/password_update.php` só atualizam o próprio usuário (conferem `csrf` e `is_logged`).

---

## Administração – exemplos (public/admin/*.php)
> Todas as páginas admin começam com `require_admin();`.

**public/admin/index.php**
```php
<?php
require_once __DIR__.'/../../app/config.php';
require_once __DIR__.'/../../app/auth.php';
require_admin();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin • Haut Forms</title>
  <link rel="stylesheet" href="/assets/bulma.min.css">
</head>
<body>
<section class="section">
  <div class="container">
    <h1 class="title">Admin</h1>
    <div class="buttons">
      <a class="button is-link" href="/admin/users.php">Usuários</a>
      <a class="button is-link" href="/admin/departments.php">Departamentos</a>
      <a class="button is-link" href="/admin/roles.php">Cargos</a>
      <a class="button is-link" href="/admin/access_levels.php">Níveis de Acesso</a>
      <a class="button is-warning" href="/admin/snippets.php">Snippets de Acesso</a>
    </div>
  </div>
</section>
</body>
</html>
```

**Validação de domínio corporativo no cadastro/edição de usuário** é feita no servidor usando `valid_corporate_email()`.

---

## Snippets de Acesso (public/admin/snippets.php)
Página que lista os níveis existentes com o **rank** e exibe o **código pronto** para colar no topo de qualquer `form.php` / `process.php` que você criar.

```php
<?php
require_once __DIR__.'/../../app/config.php';
require_once __DIR__.'/../../app/auth.php';
require_once __DIR__.'/../../app/db.php';
require_once __DIR__.'/../../app/access.php';
require_admin();

$levels = $pdo->query('SELECT id,name,rank FROM access_levels ORDER BY rank')->fetchAll(PDO::FETCH_ASSOC);
$roles  = $pdo->query('SELECT id,name FROM roles ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Snippets • Haut Forms</title>
  <link rel="stylesheet" href="/assets/bulma.min.css">
</head>
<body>
<section class="section">
  <div class="container content">
    <h1 class="title">Snippets de Acesso</h1>

    <h2 class="subtitle">Por Nível (mínimo)</h2>
    <table class="table is-fullwidth">
      <thead><tr><th>Nível</th><th>Rank</th><th>Snippet</th></tr></thead>
      <tbody>
      <?php foreach ($levels as $lv): ?>
        <tr>
          <td><?= sanitize($lv['name']) ?></td>
          <td><?= (int)$lv['rank'] ?></td>
          <td><pre><code><?= sanitize(generate_access_snippet_for_level((int)$lv['rank'])) ?></code></pre></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>

    <h2 class="subtitle">Por Cargo específico</h2>
    <table class="table is-fullwidth">
      <thead><tr><th>Cargo</th><th>Snippet</th></tr></thead>
      <tbody>
      <?php foreach ($roles as $r): ?>
        <tr>
          <td><?= sanitize($r['name']) ?></td>
          <td><pre><code><?= sanitize(generate_access_snippet_for_role($r['name'])) ?></code></pre></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>
</body>
</html>
```

---

## Exemplo de Formulário restrito à Diretoria (public/forms-demo/form-diretoria.php)
```php
<?php
// Cole o snippet abaixo no topo para exigir "Diretor" (rank 1) ou nível mínimo desejado.
// (Você também pode usar snippet por cargo específico.)
require_once __DIR__.'/../../app/config.php';
require_once __DIR__.'/../../app/db.php';
require_once __DIR__.'/../../app/access.php';
require_min_level($pdo, 1); // 1 = Diretor
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Solicitação Estratégica (Diretoria)</title>
  <link rel="stylesheet" href="/assets/bulma.min.css">
</head>
<body>
<section class="section">
  <div class="container">
    <h1 class="title">Solicitação Estratégica</h1>
    <form method="post" action="/forms-demo/process-diretoria.php" class="box">
      <div class="field"><label class="label">Assunto</label><input class="input" name="assunto" required></div>
      <div class="field"><label class="label">Descrição</label><textarea class="textarea" name="descricao" required></textarea></div>
      <button class="button is-primary" type="submit">Enviar</button>
    </form>
  </div>
</section>
</body>
</html>
```

**Handler** (public/forms-demo/process-diretoria.php)
```php
<?php
require_once __DIR__.'/../../app/config.php';
require_once __DIR__.'/../../app/db.php';
require_once __DIR__.'/../../app/access.php';
require_min_level($pdo, 1); // garante que só diretoria processe o POST

// Aqui você processa o POST (validar/armazenar/enviar email/etc.)
// ...
header('Location: '.BASE_URL.'/dashboard.php');
exit;
```

---

## Boas Práticas de Segurança (já contempladas ou sugeridas)
- `password_hash` / `password_verify`.
- Regeneração de sessão após login.
- Rate limiting simples no login.
- Tokens CSRF em formulários sensíveis.
- Filtro estrito de domínio para emails.
- Prepared statements em **todos** os acessos ao DB.
- `SameSite=Lax`, `HttpOnly` e `Secure` nos cookies de sessão.

> **Sugerido (próxima iteração):**
> - Log de auditoria (logins, CRUDs admin, acessos a formulários sensíveis).
> - 2FA/OTP por email para perfis administrativos.
> - Lista de permissões por formulário (matriz `form_permissions`), além do nível mínimo.
> - Webhooks/Email para aprovações hierárquicas.
> - Página de “Meus formulários” com base em permissões por departamento/cargo.
> - Backup/Export de usuários em CSV.

---

## Como subir
1. Crie o banco `haut_forms` e rode `/sql/schema.sql`.
2. Ajuste credenciais em `app/config.php`.
3. Suba o conteúdo para o docroot do subdomínio `forms.haut.world` (recomendado apontar o docroot para `/public`).
4. Faça login com o admin seed e troque a senha.
5. Cadastre departamentos, níveis e cargos; depois usuários.
6. Gere snippets em **Admin → Snippets** e cole no topo dos seus formulários/handlers.

---

## Perguntas Frequentes
**Q:** Posso exigir cargo específico em vez de nível?  
**A:** Sim. Use `require_role($pdo, 'Nome do Cargo')` ou gere o snippet correspondente na tela *Snippets*.

**Q:** Onde adiciono novos formulários?  
**A:** Crie a página do formulário em `/public/...` e cole o snippet gerado no topo. Depois, aponte o `action` para um handler que também deve começar com o mesmo snippet.

**Q:** Como defino que “Gerente” também acessa formulários de “Supervisor”?  
**A:** O modelo por **nível mínimo** já faz isso: se exigir `rank=3`, então `rank=1` (Diretor) e `rank=2` (Gerente) também passam.

---

## Ideias de melhoria que podem elevar o projeto
- **Matriz de Permissões por Formulário:** tabela `form_permissions(form_slug, min_rank, allowed_role_id NULLABLE)`. Isso permite configurar tudo no Admin sem editar arquivo.
- **Logs e Trilhas de Auditoria:** compliance/ISO (quem acessou, quando e de onde).
- **SSO Futuro:** se a empresa adotar IdP (ex.: Google Workspace SSO apenas para domínios autorizados).
- **Anexos Seguros:** upload para um storage privado com URLs temporárias.
- **Templates de Formulário:** gerador básico no Admin para criar formulários HTML padrão com Bulma + CSRF já embutido e o snippet correto.
- **Recuperação de Senha:** fluxo opcional de reset via email interno (limitado a domínios corporativos).

---

### Nota Final
Este starter foca em **clareza** e **controle** interno. A partir dele, você poderá ir adicionando formularização, workflows e permissões finas sem reescrever autenticação ou a base de autorização.

