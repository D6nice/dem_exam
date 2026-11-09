<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

if (!auth()->check() || auth()->isAdmin()) {
    flash('error', 'Отменить заявку может только её владелец.');
    redirect('../../frontend/login.php');
}

$applicationId = (int)($_POST['application_id'] ?? 0);

if (!Application::cancelByUser($applicationId, auth()->id())) {
    flash('error', 'Отменить можно только заявку со статусом «Новая».');
} else {
    flash('success', 'Заявка отменена.');
}

redirect('../../frontend/applications.php');