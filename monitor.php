<?php

$assetPrefix = '';

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

require_admin();

/**
 * @return array{name: string, status: string, message: string}
 */
function monitor_check_service(string $name, callable $test): array
{
    try {
        $result = $test();

        return [
            'name' => $name,
            'status' => 'online',
            'message' => is_string($result) ? $result : 'Operational',
        ];
    } catch (Throwable $e) {
        return [
            'name' => $name,
            'status' => 'offline',
            'message' => $e->getMessage(),
        ];
    }
}

function monitor_http_reachable(string $url, int $timeoutSeconds = 5): string
{
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'timeout' => $timeoutSeconds,
            'ignore_errors' => true,
        ],
        'ssl' => [
            'verify_peer' => true,
            'verify_peer_name' => true,
        ],
    ]);

    $response = @file_get_contents($url, false, $context);

    if ($response === false) {
        throw new RuntimeException('Unable to reach ' . $url);
    }

    return 'Reachable';
}

function monitor_file_exists(string $relativePath): string
{
    $path = __DIR__ . '/' . ltrim($relativePath, '/');

    if (!is_file($path)) {
        throw new RuntimeException('Missing file: ' . $relativePath);
    }

    return basename($relativePath) . ' found';
}

function monitor_collect_services(): array
{
    $services = [];

    $services[] = monitor_check_service('PHP Runtime', static function (): string {
        return 'PHP ' . PHP_VERSION;
    });

    $services[] = monitor_check_service('Session Support', static function (): string {
        if (session_status() === PHP_SESSION_NONE) {
            throw new RuntimeException('Session is not active');
        }

        return 'Sessions enabled';
    });

    $services[] = monitor_check_service('MySQL Database', static function (): string {
        try {
            db()->query('SELECT 1');
            return 'Connected to ' . DB_NAME . ' on ' . DB_HOST;
        } catch (Throwable $e) {
            throw new RuntimeException('Unable to connect to the database');
        }
    });

    $services[] = monitor_check_service('Restaurant Data', static function (): string {
        if (!database_is_ready()) {
            throw new RuntimeException('Expected at least 20 restaurants in the database');
        }

        $count = (int) db()->query('SELECT COUNT(*) FROM restaurants')->fetchColumn();

        return $count . ' restaurants loaded';
    });

    $services[] = monitor_check_service('Menu Items', static function (): string {
        $count = (int) db()->query('SELECT COUNT(*) FROM menu_items')->fetchColumn();

        if ($count < 20) {
            throw new RuntimeException('Expected at least 20 menu items');
        }

        return $count . ' menu items available';
    });

    $services[] = monitor_check_service('User Authentication', static function (): string {
        $count = (int) db()->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();

        if ($count < 1) {
            throw new RuntimeException('No admin account found');
        }

        return $count . ' admin account(s), sessions ready';
    });

    $services[] = monitor_check_service('Restaurant Search', static function (): string {
        $stmt = db()->prepare(
            'SELECT id FROM restaurants
             WHERE name LIKE :term1 OR cuisine LIKE :term2 OR description LIKE :term3'
        );
        try {
            $stmt->execute(['term1' => '%viet%', 'term2' => '%viet%', 'term3' => '%viet%']);
        } catch (Throwable $e) {
            throw new RuntimeException('Unable to execute search query: ' . $e->getMessage());
        }
        $match = $stmt->fetch();

        if (!$match) {
            throw new RuntimeException('Search query returned no results');
        }

        return 'Keyword search responding';
    });

    $services[] = monitor_check_service('Reservations', static function (): string {
        $count = (int) db()->query('SELECT COUNT(*) FROM reservations')->fetchColumn();

        return $count . ' reservation record(s)';
    });

    $services[] = monitor_check_service('Reviews', static function (): string {
        $count = (int) db()->query('SELECT COUNT(*) FROM reviews')->fetchColumn();

        return $count . ' review record(s)';
    });

    $services[] = monitor_check_service('Favourites', static function (): string {
        $count = (int) db()->query('SELECT COUNT(*) FROM favourites')->fetchColumn();

        return $count . ' favourite record(s)';
    });

    $services[] = monitor_check_service('Theme System', static function (): string {
        $theme = get_site_theme();
        $stylesheet = __DIR__ . '/assets/css/theme-' . $theme . '.css';

        if (!is_file($stylesheet)) {
            throw new RuntimeException('Theme stylesheet missing for ' . $theme);
        }

        return theme_label($theme) . ' theme active';
    });

    $services[] = monitor_check_service('Site Settings', static function (): string {
        $version = get_site_setting('site_version');

        if ($version === null || $version === '') {
            throw new RuntimeException('site_version setting is missing');
        }

        return 'Version ' . $version;
    });

    $services[] = monitor_check_service('Map Coordinates', static function (): string {
        $count = (int) db()->query(
            'SELECT COUNT(*) FROM restaurants WHERE latitude IS NOT NULL AND longitude IS NOT NULL'
        )->fetchColumn();

        if ($count < 1) {
            throw new RuntimeException('No restaurants have map coordinates');
        }

        return $count . ' restaurants ready for Leaflet maps';
    });

    $assetChecks = [
        'Core Stylesheet' => 'assets/css/style.css',
        'Main JavaScript' => 'assets/js/main.js',
        'Site Logo' => 'assets/images/DineSpot-logo.jpg',
        'Restaurant Images' => 'assets/images/restaurants/french.jpg',
        'Leaflet Stylesheet' => 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css',
        'Chart.js CDN' => 'https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js',
        'OpenStreetMap Tiles' => 'https://tile.openstreetmap.org/0/0/0.png',
    ];

    foreach ($assetChecks as $name => $target) {
        $services[] = monitor_check_service($name, function () use ($target): string {
            if (str_starts_with($target, 'http://') || str_starts_with($target, 'https://')) {
                return monitor_http_reachable($target);
            }

            return monitor_file_exists($target);
        });
    }

    return $services;
}

date_default_timezone_set('America/Toronto');

$services = monitor_collect_services();
$offlineCount = count(array_filter($services, static fn (array $service): bool => $service['status'] === 'offline'));
$onlineCount = count($services) - $offlineCount;
$checkedAt = date('M j, Y g:i A');

$pageTitle = 'System Monitoring';
$pageDescription = 'Live status of DineSpot services and feature dependencies.';
$bodyClass = 'page-client page-admin page-monitor';

require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <h1>System Monitoring</h1>
        <p class="lead">
            Service health for DineSpot. Last checked <?= e($checkedAt) ?>.
        </p>
    </div>
</section>

<section class="page-content">
    <div class="container">
        <div class="admin-stats-row" aria-label="Monitoring summary">
            <div class="stat-box admin-stat-highlight">
                <strong><?= $onlineCount ?></strong>
                <span class="admin-stat-label">Services online</span>
            </div>
            <div class="stat-box">
                <strong><?= $offlineCount ?></strong>
                <span class="admin-stat-label">Services offline</span>
            </div>
            <div class="stat-box">
                <strong><?= count($services) ?></strong>
                <span class="admin-stat-label">Total checks</span>
            </div>
        </div>

        <div class="content-card admin-users-card">
            <div class="admin-table-toolbar">
                <div>
                    <h2>Service Status</h2>
                    <p class="admin-table-meta">
                        <?php if ($offlineCount === 0): ?>
                            All monitored services are online.
                        <?php else: ?>
                            <?= $offlineCount ?> service<?= $offlineCount === 1 ? '' : 's' ?> require attention.
                        <?php endif; ?>
                    </p>
                </div>
                <a class="btn btn-secondary" href="<?= e(admin_path('dashboard.php')) ?>">Back to Dashboard</a>
            </div>

            <div class="admin-table-scroll" tabindex="0" aria-label="Service monitoring table">
                <table class="admin-data-table">
                    <thead>
                        <tr>
                            <th scope="col">Service</th>
                            <th scope="col">Status</th>
                            <th scope="col">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($services as $service): ?>
                            <?php $isOnline = $service['status'] === 'online'; ?>
                            <tr>
                                <td><strong><?= e($service['name']) ?></strong></td>
                                <td>
                                    <span class="status-badge <?= $isOnline ? 'status-active' : 'status-suspended' ?>">
                                        <span class="status-dot" aria-hidden="true"></span>
                                        <?= $isOnline ? 'Online' : 'Offline' ?>
                                    </span>
                                </td>
                                <td><?= e($service['message']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
