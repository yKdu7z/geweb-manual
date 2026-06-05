<?php
require __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = trim((string) ($_POST['password'] ?? ''));

    if ($username !== '' && $password !== '') {
        $_SESSION['manual_user'] = $username;
        session_write_close();
        header('Location: index.php');
        exit;
    }

    flash('Informe usuario e senha.', 'error');
}

$message = flash();
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Manual Geweb</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body class="login-page">
    <main class="login-card">
        <img src="Geweb-sistemas.png" alt="Geweb Sistemas">
        <h1>Acessar manual</h1>
        <p>Entre com qualquer usuario e senha. Usuarios iniciados por <strong>geweb</strong> liberam cadastro de manuais.</p>

        <?php if ($message): ?>
            <div class="flash <?= e($message['type']) ?>"><?= e($message['message']) ?></div>
        <?php endif; ?>

        <form method="post">
            <label>
                Usuario
                <input name="username" placeholder="Geweb.carlos.eduardo" required autofocus>
            </label>
            <label>
                Senha
                <input type="password" name="password" placeholder="Senha" required>
            </label>
            <button class="login-submit" type="submit">Entrar</button>
        </form>
    </main>
</body>
</html>
