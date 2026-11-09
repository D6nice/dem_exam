<?php
declare(strict_types=1);

require_once __DIR__ . '/../backend/bootstrap.php';

if (!auth()->check() || auth()->isAdmin()) {
    flash('error', 'Войдите как владелец питомца.');
    redirect('../frontend/login.php');
}

$pageTitle = 'Мои записи';
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);

$applications = Application::forUser(auth()->id());

require __DIR__ . '/partials/header.php';
?>

<div class="page-head">
    <div>
        <h1 class="page-title">Мои записи</h1>
        <p class="page-subtitle">История приёмов и статусы заявок</p>
    </div>
    <div class="page-actions">
        <a class="btn btn-small" href="../frontend/application_create.php">Новая запись</a>
    </div>
</div>

<?php if (isset($errors['text'])): ?>
    <div class="flash flash-error"><?= e($errors['text']) ?></div>
<?php endif; ?>

<?php if (!$applications): ?>
    <div class="card empty-state">
        <p>У вас пока нет записей. Создайте первую заявку на приём.</p>
        <a class="btn" href="../frontend/application_create.php">Записаться на приём</a>
    </div>
<?php else: ?>
    <div class="app-list">
        <?php foreach ($applications as $application): ?>
            <?php $review = Review::forApplication((int)$application['id']); ?>
            <article class="card app-card status-<?= e(Application::statusClass($application['status'])) ?>">
                <div class="app-card-head">
                    <h2><?= e($application['pet_name']) ?></h2>
                    <span class="status status-<?= e(Application::statusClass($application['status'])) ?>">
                        <?= e($application['status']) ?>
                    </span>
                </div>

                <dl class="app-details">
                    <div class="detail"><dt>Вид</dt><dd><?= e($application['species']) ?></dd></div>
                    <div class="detail"><dt>Услуга</dt><dd><?= e($application['service']) ?></dd></div>
                    <div class="detail"><dt>Дата</dt><dd><?= e($application['appointment_date']) ?></dd></div>
                    <div class="detail"><dt>Время</dt><dd><?= e($application['appointment_time']) ?></dd></div>
                    <div class="detail"><dt>Оплата</dt><dd><?= e($application['payment_method']) ?></dd></div>
                </dl>

                <?php if ($application['status'] === Application::STATUS_NEW): ?>
                    <form method="post" action="../backend/actions/application_cancel.php" class="cancel-form">
                        <input type="hidden" name="application_id" value="<?= (int)$application['id'] ?>">
                        <button type="submit" class="btn btn-small btn-cancel">Отменить заявку</button>
                    </form>
                <?php endif; ?>

                <?php if ($review): ?>
                    <div class="review">
                        <div class="review-label">Ваш отзыв</div>
                        <p><?= e($review['text']) ?></p>
                    </div>
                <?php elseif ($application['status'] === Application::STATUS_COMPLETED): ?>
                    <form method="post" action="../backend/actions/review_create.php" class="form review-form">
                        <input type="hidden" name="application_id" value="<?= (int)$application['id'] ?>">
                        <div class="field">
                            <label for="review_<?= (int)$application['id'] ?>">Отзыв о работе клиники</label>
                            <textarea id="review_<?= (int)$application['id'] ?>" name="text" rows="3" placeholder="Расскажите, как прошёл приём"></textarea>
                        </div>
                        <button type="submit" class="btn btn-small">Оставить отзыв</button>
                    </form>
                <?php else: ?>
                    <p class="review-wait">Отзыв можно оставить после завершения приёма.</p>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/partials/footer.php'; ?>
