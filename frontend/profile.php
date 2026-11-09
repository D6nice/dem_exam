<?php
declare(strict_types=1);

require_once __DIR__ . '/../backend/bootstrap.php';

if (!auth()->check() || auth()->isAdmin()) {
    flash('error', 'Войдите как владелец питомца.');
    redirect('../frontend/login.php');
}

$pageTitle = 'Мой профиль';
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);

$user = auth()->user();
$pets = Pet::forUser(auth()->id());

require __DIR__ . '/partials/header.php';
?>

<div class="page-head">
    <div>
        <h1 class="page-title">Мой профиль</h1>
        <p class="page-subtitle">Данные аккаунта и питомцы</p>
    </div>
    <div class="page-actions">
        <a class="btn btn-small" href="../frontend/application_create.php">Записаться</a>
    </div>
</div>

<div class="profile-grid">
    <div class="card">
        <h2>Данные аккаунта</h2>
        <dl class="app-details">
            <div class="detail"><dt>Логин</dt><dd><?= e($user['login']) ?></dd></div>
            <div class="detail"><dt>ФИО</dt><dd><?= e($user['full_name']) ?></dd></div>
            <div class="detail"><dt>Телефон</dt><dd><?= e($user['phone']) ?></dd></div>
            <div class="detail"><dt>E-mail</dt><dd><?= e($user['email']) ?></dd></div>
        </dl>
    </div>

    <div class="card">
        <h2>Мои питомцы</h2>
        <?php if (!$pets): ?>
            <p class="empty-text">Питомцев пока нет.</p>
        <?php else: ?>
            <ul class="pet-list">
                <?php foreach ($pets as $pet): ?>
                    <li class="pet-item">
                        <span><?= e($pet['name']) ?></span>
                        <span class="pet-species"><?= e($pet['species']) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <div class="card">
        <h2>Добавить питомца</h2>

        <form method="post" action="../backend/actions/pet_create.php" class="form" novalidate>
            <div class="field-row">
                <div class="field<?= isset($errors['name']) ? ' has-error' : '' ?>">
                    <label for="pet_name">Кличка</label>
                    <input type="text" id="pet_name" name="name" value="<?= e(old('name')) ?>" placeholder="Например, Барсик">
                    <?php if (isset($errors['name'])): ?>
                        <div class="error-text"><?= e($errors['name']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="field<?= isset($errors['species']) ? ' has-error' : '' ?>">
                    <label for="pet_species">Вид животного</label>
                    <select id="pet_species" name="species">
                        <option value="">Выберите вид</option>
                        <?php foreach (Pet::SPECIES as $species): ?>
                            <option value="<?= e($species) ?>"<?= old('species') === $species ? ' selected' : '' ?>>
                                <?= e($species) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['species'])): ?>
                        <div class="error-text"><?= e($errors['species']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <button type="submit" class="btn btn-block">Добавить питомца</button>
        </form>
    </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
