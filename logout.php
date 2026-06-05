<?php
require __DIR__ . '/config.php';

unset($_SESSION['manual_user']);
header('Location: login.php');
exit;
