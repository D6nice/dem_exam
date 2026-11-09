<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

$login = trim($_POST['login'] ?? '');
$password = $_POST['password'] ?? '';

if (!auth()->attemptAdmin($login, $password)) {
    $_SESSION['errors'] = ['admin' => 'Неверный логин или пароль администратора'];
    keep_old(['login']);
    redirect('../../frontend/admin.php');
}

unset($_SESSION['errors']);
clear_old();
flash('success', 'Вы вошли как администратор.');
redirect('../../frontend/admin.php');