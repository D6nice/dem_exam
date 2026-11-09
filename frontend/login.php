<?php
declare(strict_types=1);

require_once __DIR__ . '/../backend/bootstrap.php';

$pageTitle = 'Вход';
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);

require __DIR__ . '/partials/header.php';
?>

<div class="auth-wrap">
    <div class="card auth-card">
        <div class="auth-head">
            <div class="auth-badge">ХЛ</div>
            <h1>Вход в кабинет</h1>
            <p>Введите логин и пароль, чтобы управлять записями</p>
        </div>

        <div class="auth-body">
            <form method="post" action="../backend/actions/login.php" class="form" novalidate>
                <div class="field<?= isset($errors['login']) ? ' has-error' : '' ?>">
                    <label for="login">Логин</label>
                    <input type="text" id="login" name="login" value="<?= e(old('login')) ?>" placeholder="Ваш логин">
                    <?php if (isset($errors['login'])): ?>
                        <div class="error-text"><?= e($errors['login']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="field<?= isset($errors['password']) ? ' has-error' : '' ?>">
                    <label for="password">Пароль</label>
                    <input type="password" id="password" name="password" placeholder="Ваш пароль">
                    <?php if (isset($errors['password'])): ?>
                        <div class="error-text"><?= e($errors['password']) ?></div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-block">Войти</button>
            </form>

            <p class="alt-link">
                Еще не зарегистрированы?
                <a href="../frontend/register.php">Регистрация</a>
            </p>
        </div>
    </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
