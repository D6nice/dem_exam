<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

if (!auth()->check() || auth()->isAdmin()) {
    flash('error', 'Оставлять отзывы может только владелец питомца.');
    redirect('../../frontend/login.php');
}

$applicationId = (int)($_POST['application_id'] ?? 0);
$text = trim($_POST['text'] ?? '');

$application = Application::find($applicationId);
if ($application === null || (int)$application['user_id'] !== auth()->id()) {
    flash('error', 'Заявка не найдена.');
    redirect('../../frontend/applications.php');
}

if (Review::forApplication($applicationId) !== null) {
    flash('error', 'Отзыв по этой заявке уже оставлен.');
    redirect('../../frontend/applications.php');
}

if ($application['status'] !== Application::STATUS_COMPLETED) {
    flash('error', 'Отзыв доступен только после завершения приёма.');
    redirect('../../frontend/applications.php');
}

if ($error = Validator::required($text, 'Отзыв')) {
    $_SESSION['errors'] = ['text' => $error];
    keep_old(['text']);
    redirect('../../frontend/applications.php');
}

Review::create($applicationId, auth()->id(), $text);

unset($_SESSION['errors']);
clear_old();
flash('success', 'Спасибо за отзыв!');
redirect('../../frontend/applications.php');