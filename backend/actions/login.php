<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

$login = trim($_POST['login'] ?? '');
$password = $_POST['password'] ?? '';

$errors = [];

if ($error = Validator::required($login, 'Логин')) {
    $errors['login'] = $error;
}
if ($error = Validator::required($password, 'Пароль')) {
    $errors['password'] = $error;
}

if (!$errors && !auth()->attempt($login, $password)) {
    $errors['login'] = 'Неверный логин или пароль';
}

if ($errors) {
    $_SESSION['errors'] = $errors;
    keep_old(['login']);
    redirect('../../frontend/login.php');
}

unset($_SESSION['errors']);
clear_old();

if (auth()->isAdmin()) {
    flash('success', 'Вы вошли как администратор.');
    redirect('../../frontend/admin.php');
}

flash('success', 'Вы вошли в систему.');
redirect('../../frontend/applications.php');