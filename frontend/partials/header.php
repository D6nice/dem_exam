<?php
$base = $base ?? '../';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle ?? 'Хвост и Лапы') ?></title>
    <link rel="stylesheet" href="<?= e($base) ?>frontend/assets/css/style.css">
    <script src="<?= e($base) ?>frontend/assets/js/app.js" defer></script>
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <a class="brand" href="<?= e($base) ?>index.php">
            <span class="brand-mark">ХЛ</span>
            <span class="brand-text">
                <span class="brand-name">Хвост и Лапы</span>
                <span class="brand-tag">ветеринарная клиника</span>
            </span>
        </a>

        <nav class="nav">
            <?php if (auth()->isAdmin()): ?>
                <a href="<?= e($base) ?>frontend/admin.php">Панель</a>
                <a href="<?= e($base) ?>backend/actions/logout.php">Выйти</a>
            <?php elseif (auth()->check()): ?>
                <a href="<?= e($base) ?>frontend/applications.php">Мои записи</a>
                <a href="<?= e($base) ?>frontend/application_create.php">Записаться</a>
                <a href="<?= e($base) ?>frontend/profile.php">Профиль</a>
                <a href="<?= e($base) ?>backend/actions/logout.php">Выйти</a>
            <?php else: ?>
                <a href="<?= e($base) ?>frontend/login.php">Вход</a>
                <a href="<?= e($base) ?>frontend/register.php">Регистрация</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<?php $flashes = get_flashes(); ?>
<?php if ($flashes): ?>
    <div id="toasts">
        <?php foreach ($flashes as $item): ?>
            <div class="toast toast-<?= e($item['type']) ?>">
                <?= e($item['message']) ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<main class="container">
