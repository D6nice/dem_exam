<?php
declare(strict_types=1);

require_once __DIR__ . '/../backend/bootstrap.php';

if (!auth()->check() || auth()->isAdmin()) {
    flash('error', 'Войдите как владелец питомца.');
    redirect('../frontend/login.php');
}

$pageTitle = 'Запись на приём';
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);

$pets = Pet::forUser(auth()->id());

require __DIR__ . '/partials/header.php';
?>

<div class="page-head">
    <div>
        <h1 class="page-title">Запись на приём</h1>
        <p class="page-subtitle">Оставьте заявку — администратор подтвердит её</p>
    </div>
    <div class="page-actions">
        <a class="btn btn-small btn-outline" href="../frontend/applications.php">Мои записи</a>
    </div>
</div>

<div class="card form-card">
    <?php if (!$pets): ?>
        <p class="empty-text">Сначала добавьте питомца в профиле.</p>
        <a class="btn" href="../frontend/profile.php">Перейти в профиль</a>
    <?php else: ?>
        <form method="post" action="../backend/actions/application_create.php"
              class="form" data-application-form novalidate>
            <div class="field<?= isset($errors['pet_id']) ? ' has-error' : '' ?>">
                <label for="pet_id">Питомец</label>
                <select id="pet_id" name="pet_id">
                    <option value="">Выберите питомца</option>
                    <?php foreach ($pets as $pet): ?>
                        <option value="<?= (int)$pet['id'] ?>"<?= (string)old('pet_id') === (string)$pet['id'] ? ' selected' : '' ?>>
                            <?= e($pet['name']) ?> — <?= e($pet['species']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['pet_id'])): ?>
                    <div class="error-text"><?= e($errors['pet_id']) ?></div>
                <?php endif; ?>
            </div>

            <div class="field<?= isset($errors['service']) ? ' has-error' : '' ?>">
                <label for="service">Услуга</label>
                <select id="service" name="service">
                    <option value="">Выберите услугу</option>
                    <?php foreach (Application::SERVICES as $service): ?>
                        <option value="<?= e($service) ?>"<?= old('service') === $service ? ' selected' : '' ?>>
                            <?= e($service) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['service'])): ?>
                    <div class="error-text"><?= e($errors['service']) ?></div>
                <?php endif; ?>
            </div>

            <div class="field-row">
                <div class="field<?= isset($errors['date']) ? ' has-error' : '' ?>">
                    <label for="date">Дата приёма</label>
                    <input type="text" id="date" name="date"
                           value="<?= e(old('date')) ?>" placeholder="ДД.ММ.ГГГГ">
                    <?php if (isset($errors['date'])): ?>
                        <div class="error-text"><?= e($errors['date']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="field<?= isset($errors['time']) ? ' has-error' : '' ?>">
                    <label for="time">Время приёма</label>
                    <input type="text" id="time" name="time"
                           value="<?= e(old('time')) ?>" placeholder="ЧЧ:ММ">
                    <?php if (isset($errors['time'])): ?>
                        <div class="error-text"><?= e($errors['time']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="form-note">Клиника работает с 09:00 до 20:00</div>

            <div class="field<?= isset($errors['payment_method']) ? ' has-error' : '' ?>">
                <label for="payment_method">Способ оплаты</label>
                <select id="payment_method" name="payment_method">
                    <option value="">Выберите способ оплаты</option>
                    <?php foreach (Application::PAYMENTS as $payment): ?>
                        <option value="<?= e($payment) ?>"<?= old('payment_method') === $payment ? ' selected' : '' ?>>
                            <?= e($payment) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['payment_method'])): ?>
                    <div class="error-text"><?= e($errors['payment_method']) ?></div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-block">Записаться</button>
        </form>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
