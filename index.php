<?php
declare(strict_types=1);

require_once __DIR__ . '/backend/bootstrap.php';

$base = '';
$pageTitle = 'Хвост и Лапы — запись к ветеринару';

require __DIR__ . '/frontend/partials/header.php';
?>

<section class="home">
    <div class="home-intro">
        <span class="eyebrow">Забота рядом</span>
        <h1>Здоровье питомца начинается с простой записи</h1>
        <p>Выберите услугу, удобную дату и время — администратор клиники подтвердит заявку. Всё онлайн, без звонков.</p>

        <div class="home-actions">
            <?php if (auth()->isAdmin()): ?>
                <a class="btn" href="frontend/admin.php">Панель администратора</a>
            <?php elseif (auth()->check()): ?>
                <a class="btn" href="frontend/application_create.php">Записаться на приём</a>
                <a class="btn btn-outline" href="frontend/applications.php">Мои записи</a>
            <?php else: ?>
                <a class="btn" href="frontend/register.php">Создать аккаунт</a>
                <a class="btn btn-outline" href="frontend/login.php">Войти</a>
            <?php endif; ?>
        </div>
    </div>

    <section class="slider" id="slider">
        <div class="slide active">
            <img src="frontend/assets/img/slide1.jpg.webp" alt="Врач на приёме">
        </div>
        <div class="slide">
            <img src="frontend/assets/img/slide2.jpg.webp" alt="Довольный питомец">
        </div>
        <div class="slide">
            <img src="frontend/assets/img/slide3.jpg.webp" alt="Кабинет клиники">
        </div>
        <div class="slide">
            <img src="frontend/assets/img/slide4.jpg.webp" alt="Вакцинация">
        </div>

        <button type="button" class="slider-btn slider-prev" aria-label="Назад">‹</button>
        <button type="button" class="slider-btn slider-next" aria-label="Вперёд">›</button>
        <div class="slider-dots"></div>
    </section>
</section>

<section class="features">
    <article class="feature">
        <div class="feature-icon">01</div>
        <h2>Быстрая запись</h2>
        <p>Заявка создаётся за пару минут и сразу попадает администратору клиники.</p>
    </article>
    <article class="feature">
        <div class="feature-icon">02</div>
        <h2>Мои записи</h2>
        <p>Все приёмы и их статусы всегда под рукой — в личном кабинете.</p>
    </article>
    <article class="feature">
        <div class="feature-icon">03</div>
        <h2>Обратная связь</h2>
        <p>После завершения приёма можно оставить отзыв о работе клиники.</p>
    </article>
</section>

<?php require __DIR__ . '/frontend/partials/footer.php'; ?>
