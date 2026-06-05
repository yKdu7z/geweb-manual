<?php
require __DIR__ . '/config.php';

unset($_SESSION['usuario_manual']);
header('Location: ' . url_app('login.php'));
exit;


