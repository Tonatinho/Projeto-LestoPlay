<?php
require_once __DIR__ . '/config.php';
require_once ROOT_PATH . '/includes/db.php';

session_start();

$titulo = 'Login Administrativo';
$mensagem = '';
$tipo_mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Credenciais fixas para o exemplo. Em um sistema real, buscar do banco de dados.
    $admin_username = 'admin';
    $admin_password = 'admin'; // Senha em texto puro, NÃO USAR EM PRODUÇÃO!

    if ($username === $admin_username && $password === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $mensagem = 'Usuário ou senha inválidos.';
        $tipo_mensagem = 'error';
    }
}

include ROOT_PATH . '/includes/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-user-lock"></i> Login Administrativo</h1>
    <p>Acesso restrito ao painel de gerenciamento</p>
</div>

<div class="form-container">
    <form method="POST" class="login-form">
        <div class="form-group">
            <label for="username">Usuário:</label>
            <input type="text" id="username" name="username" required placeholder="Nome de usuário">
        </div>
        <div class="form-group">
            <label for="password">Senha:</label>
            <input type="password" id="password" name="password" required placeholder="Sua senha">
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-large">
                <i class="fas fa-sign-in-alt"></i> Entrar
            </button>
        </div>
    </form>
</div>

<?php include ROOT_PATH . '/includes/footer.php'; ?>

