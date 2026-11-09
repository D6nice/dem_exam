<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

$login = trim($_POST['login'] ?? '');
$password = $_POST['password'] ?? '';
$fullName = trim($_POST['full_name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$email = trim($_POST['email'] ?? '');

$errors = [];

if ($error = Validator::login($login)) {
    $errors['login'] = $error;
}
if ($error = Validator::password($password)) {
    $errors['password'] = $error;
}
if ($error = Validator::fullName($fullName)) {
    $errors['full_name'] = $error;
}
if ($error = Validator::phone($phone)) {
    $errors['phone'] = $error;
}
if ($error = Validator::email($email)) {
    $errors['email'] = $error;
}
if (!$errors && User::loginExists($login)) {
    $errors['login'] = 'Пользователь с таким логином уже существует';
}

if ($errors) {
    $_SESSION['errors'] = $errors;
    keep_old(['login', 'full_name', 'phone', 'email']);
    redirect('../../frontend/register.php');
}

User::create([
    'login' => $login,
    'password_hash' => password_hash($password, PASSWORD_DEFAULT),
    'full_name' => $fullName,
    'phone' => Validator::normalizePhone($phone) ?? $phone,
    'email' => $email,
]);

unset($_SESSION['errors']);
clear_old();
flash('success', 'Пользователь создан. Теперь войдите в систему.');
redirect('../../frontend/login.php');