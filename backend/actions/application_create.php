<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

if (!auth()->check() || auth()->isAdmin()) {
    flash('error', 'Записываться на приём может только владелец питомца.');
    redirect('../../frontend/login.php');
}

$petId = (int)($_POST['pet_id'] ?? 0);
$service = trim($_POST['service'] ?? '');
$date = trim($_POST['date'] ?? '');
$time = trim($_POST['time'] ?? '');
$payment = trim($_POST['payment_method'] ?? '');

$errors = [];

if (Pet::findForUser($petId, auth()->id()) === null) {
    $errors['pet_id'] = 'Выберите питомца из списка';
}
if (!in_array($service, Application::SERVICES, true)) {
    $errors['service'] = 'Выберите услугу из списка';
}
if ($error = Validator::date($date)) {
    $errors['date'] = $error;
}
if ($error = Validator::time($time)) {
    $errors['time'] = $error;
}
if (!in_array($payment, Application::PAYMENTS, true)) {
    $errors['payment_method'] = 'Выберите способ оплаты';
}

if ($errors) {
    $_SESSION['errors'] = $errors;
    keep_old(['pet_id', 'service', 'date', 'time', 'payment_method']);
    redirect('../../frontend/application_create.php');
}

Application::create([
    'user_id' => auth()->id(),
    'pet_id' => $petId,
    'service' => $service,
    'appointment_date' => $date,
    'appointment_time' => $time,
    'payment_method' => $payment,
]);

unset($_SESSION['errors']);
clear_old();
flash('success', 'Заявка отправлена. Ожидайте подтверждения администратора.');
redirect('../../frontend/applications.php');