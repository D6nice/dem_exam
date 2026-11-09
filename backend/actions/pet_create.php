<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

if (!auth()->check() || auth()->isAdmin()) {
    flash('error', 'Добавлять питомцев может только владелец.');
    redirect('../../frontend/login.php');
}

$name = trim($_POST['name'] ?? '');
$species = trim($_POST['species'] ?? '');

$errors = [];

if ($error = Validator::required($name, 'Кличка')) {
    $errors['name'] = $error;
}
if (!in_array($species, Pet::SPECIES, true)) {
    $errors['species'] = 'Выберите вид животного из списка';
}

if ($errors) {
    $_SESSION['errors'] = $errors;
    keep_old(['name', 'species']);
    redirect('../../frontend/profile.php');
}

Pet::create(auth()->id(), $name, $species);

unset($_SESSION['errors']);
clear_old();
flash('success', 'Питомец добавлен в профиль.');
redirect('../../frontend/profile.php');