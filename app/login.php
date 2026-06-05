<?php
require __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim((string) ($_POST['usuario'] ?? ''));
    $senha = trim((string) ($_POST['senha'] ?? ''));

    if ($usuario !== '' && $senha !== '') {
        $_SESSION['usuario_manual'] = $usuario;
        session_write_close();
        header('Location: ' . url_app('index.php'));
        exit;
    }

    mensagem_temporaria('Informe usuario e senha.', 'erro');
}

$mensagem = mensagem_temporaria();
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Manual Geweb</title>
    <link rel="stylesheet" href="<?= escapar(url('assets/css/styles.css')) ?>">
</head>
<body class="pagina-login">
    <main class="cartao-login">
        <img src="<?= escapar(url('assets/img/Geweb-sistemas.png')) ?>" alt="Geweb Sistemas">
        <h1>Acessar manual</h1>
        <p>Entre com qualquer usuario e senha. Usuarios iniciados por <strong>geweb</strong> liberam cadastro de manuais.</p>

        <?php if ($mensagem): ?>
            <div class="mensagem <?= escapar($mensagem['tipo']) ?>"><?= escapar($mensagem['mensagem']) ?></div>
        <?php endif; ?>

        <form method="post">
            <label>
                Usuario
                <input name="usuario" placeholder="Geweb.carlos.eduardo" required autofocus>
            </label>
            <label>
                Senha
                <input type="password" name="senha" placeholder="Senha" required>
            </label>
            <button class="botao-login" type="submit">Entrar</button>
        </form>
    </main>
</body>
</html>


