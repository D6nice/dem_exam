<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

if (!auth()->isAdmin()) {
    flash('error', 'Недостаточно прав.');
    redirect('../../frontend/admin.php');
}

$applicationId = (int)($_POST['application_id'] ?? 0);
$status = trim($_POST['status'] ?? '');

if (!Application::updateStatus($applicationId, $status)) {
    flash('error', 'Недопустимый переход статуса.');
    redirect('../../frontend/admin.php');
}

flash('success', 'Статус изменён на «' . $status . '».');
redirect('../../frontend/admin.php?updated=' . $applicationId);