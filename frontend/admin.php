<?php
declare(strict_types=1);

require_once __DIR__ . '/../backend/bootstrap.php';

$pageTitle = 'Панель администратора';
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);

require __DIR__ . '/partials/header.php';
?>

<?php if (!auth()->isAdmin()): ?>
    <div class="auth-wrap">
        <div class="card auth-card">
            <div class="auth-head">
                <div class="auth-badge">АД</div>
                <h1>Служебный вход</h1>
                <p>Доступ только для администратора клиники</p>
            </div>

            <div class="auth-body">
                <form method="post" action="../backend/actions/admin_login.php" class="form" novalidate>
                    <div class="field<?= isset($errors['admin']) ? ' has-error' : '' ?>">
                        <label for="login">Логин</label>
                        <input type="text" id="login" name="login" value="<?= e(old('login')) ?>" placeholder="Admin">
                        <?php if (isset($errors['admin'])): ?>
                            <div class="error-text"><?= e($errors['admin']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="field">
                        <label for="password">Пароль</label>
                        <input type="password" id="password" name="password" placeholder="Пароль">
                    </div>

                    <button type="submit" class="btn btn-block">Войти</button>
                </form>
            </div>
        </div>
    </div>
<?php else: ?>
    <?php
    $filters = [
        'status' => trim($_GET['status'] ?? ''),
        'service' => trim($_GET['service'] ?? ''),
        'search' => trim($_GET['q'] ?? ''),
    ];

    $perPage = 5;
    $total = Application::countAll($filters);
    $pages = max(1, (int)ceil($total / $perPage));
    $page = min(max(1, (int)($_GET['page'] ?? 1)), $pages);
    $applications = Application::all($filters, $page, $perPage);
    $updatedId = (int)($_GET['updated'] ?? 0);

    $pageUrl = static function (int $target) use ($filters): string {
        $params = array_filter([
            'status' => $filters['status'],
            'service' => $filters['service'],
            'q' => $filters['search'],
            'page' => $target,
        ], static fn($v) => $v !== '' && $v !== null);

        return 'admin.php' . ($params ? '?' . http_build_query($params) : '');
    };
    ?>

    <div class="page-head">
        <div>
            <h1 class="page-title">Панель администратора</h1>
            <p class="page-subtitle">Все заявки клиентов и управление статусами</p>
        </div>
        <div class="page-actions">
            <a class="btn btn-small btn-outline" href="../backend/actions/logout.php">Выйти</a>
        </div>
    </div>

    <form method="get" action="admin.php" class="filter-form">
        <div class="filter-row">
            <select name="status" class="filter-input">
                <option value="">Все статусы</option>
                <?php foreach (array_keys(Application::TRANSITIONS) as $status): ?>
                    <option value="<?= e($status) ?>"<?= $filters['status'] === $status ? ' selected' : '' ?>>
                        <?= e($status) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="service" class="filter-input">
                <option value="">Все услуги</option>
                <?php foreach (Application::SERVICES as $service): ?>
                    <option value="<?= e($service) ?>"<?= $filters['service'] === $service ? ' selected' : '' ?>>
                        <?= e($service) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <input type="text" name="q" class="filter-input"
                   placeholder="Кличка или ФИО владельца"
                   value="<?= e($filters['search']) ?>">

            <button type="submit" class="btn">Найти</button>
            <a class="btn btn-outline" href="admin.php">Сброс</a>
        </div>
    </form>

    <p class="results-count">Найдено заявок: <strong><?= $total ?></strong></p>

    <?php if (!$applications): ?>
        <div class="card empty-state"><p>Заявок не найдено.</p></div>
    <?php else: ?>
        <div class="app-list">
            <?php foreach ($applications as $application): ?>
                <article class="card app-card status-<?= e(Application::statusClass($application['status'])) ?><?= $updatedId === (int)$application['id'] ? ' card-updated' : '' ?>">
                    <div class="app-card-head">
                        <h2><?= e($application['pet_name']) ?></h2>
                        <span class="status status-<?= e(Application::statusClass($application['status'])) ?>">
                            <?= e($application['status']) ?>
                        </span>
                    </div>

                    <dl class="app-details">
                        <div class="detail"><dt>Владелец</dt><dd><?= e($application['full_name']) ?> (<?= e($application['login']) ?>)</dd></div>
                        <div class="detail"><dt>Вид</dt><dd><?= e($application['species']) ?></dd></div>
                        <div class="detail"><dt>Услуга</dt><dd><?= e($application['service']) ?></dd></div>
                        <div class="detail"><dt>Дата и время</dt><dd><?= e($application['appointment_date']) ?> <?= e($application['appointment_time']) ?></dd></div>
                        <div class="detail"><dt>Оплата</dt><dd><?= e($application['payment_method']) ?></dd></div>
                    </dl>

                    <?php $nextStatuses = Application::TRANSITIONS[$application['status']] ?? []; ?>

                    <?php if ($nextStatuses): ?>
                        <div class="admin-actions">
                            <?php foreach ($nextStatuses as $nextStatus): ?>
                                <form method="post" action="../backend/actions/admin_status.php" class="inline-form">
                                    <input type="hidden" name="application_id" value="<?= (int)$application['id'] ?>">
                                    <input type="hidden" name="status" value="<?= e($nextStatus) ?>">
                                    <button type="submit"
                                            class="btn btn-small<?= $nextStatus === Application::STATUS_CANCELLED ? ' btn-danger' : '' ?>">
                                        <?= e($nextStatus) ?>
                                    </button>
                                </form>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="no-actions">Заявка закрыта</p>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>

        <?php if ($pages > 1): ?>
            <nav class="pagination">
                <?php if ($page > 1): ?>
                    <a class="page-link" href="<?= e($pageUrl($page - 1)) ?>">‹ Назад</a>
                <?php endif; ?>

                <span class="page-current">Стр. <?= $page ?> из <?= $pages ?></span>

                <?php if ($page < $pages): ?>
                    <a class="page-link" href="<?= e($pageUrl($page + 1)) ?>">Вперёд ›</a>
                <?php endif; ?>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
<?php endif; ?>

<?php require __DIR__ . '/partials/footer.php'; ?>
