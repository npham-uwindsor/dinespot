<?php

/*
    Author: Tuong Nguyen Pham
    Student ID: 110192780
    COMP 3340 - Web Development
    Couse Project
    HTML5, CSS, JS, PHP, MySQL
*/
function asset_prefix(): string
{
    return $GLOBALS['assetPrefix'] ?? '';
}


// Path functions for the client and admin
function client_path(string $path): string
{
    $path = ltrim(str_replace('\\', '/', $path), '/');

    return asset_prefix() . 'client/' . $path;
}

function admin_path(string $path): string
{
    $path = ltrim(str_replace('\\', '/', $path), '/');

    return asset_prefix() . 'admin/' . $path;
}

// Path functions for the help page
function help_path(string $path = 'index.php'): string
{
    $path = ltrim(str_replace('\\', '/', $path), '/');

    return asset_prefix() . 'help/' . $path;
}

function get_help_pages(): array
{
    return [
        'index.php' => 'Help Home',
        'browsing.php' => 'Browsing Restaurants',
        'reservations.php' => 'Reservations',
        'account.php' => 'Your Account',
        'updating-content.php' => 'Updating Content (Admin Only)',
    ];
}

function get_context_help_page(): string
{
    $script = current_script_path();

    if (str_contains($script, '/restaurants/')) {
        return 'browsing.php';
    }

    if (str_contains($script, '/client/reservation') || str_contains($script, '/client/cancel_reservation')) {
        return 'reservations.php';
    }

    if (str_contains($script, '/client/')) {
        return 'account.php';
    }

    if (str_contains($script, '/admin/restaurants/') || str_contains($script, '/admin/theme/')) {
        return 'updating-content.php';
    }

    if (str_contains($script, '/help/')) {
        $page = basename($script);

        return $page !== '' ? $page : 'index.php';
    }

    return 'index.php';
}

function context_help_path(): string
{
    return help_path(get_context_help_page());
}

function context_help_label(): string
{
    $pages = get_help_pages();
    $page = get_context_help_page();

    return $pages[$page] ?? 'Help';
}

// Function to determine if the client path should be used
function should_use_client_path(): bool
{
    if (!function_exists('is_logged_in')) {
        require_once __DIR__ . '/auth.php';
    }

    return !is_logged_in() || is_client();
}

function account_path(string $path): string
{
    if (!function_exists('is_logged_in')) {
        require_once __DIR__ . '/auth.php';
    }

    if (is_logged_in() && is_admin()) {
        return admin_path($path);
    }

    return client_path($path);
}

// Escape HTML characters to avoid XSS attacks
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function current_script_path(): string
{
    return str_replace('\\', '/', $_SERVER['PHP_SELF'] ?? '');
}


// check if the current page is the active page, so we can add style to the active page
function is_active_page(string $page): bool
{
    $script = current_script_path();
    $page = str_replace('\\', '/', $page);

    if ($page === 'restaurants') {
        return str_contains($script, '/restaurants/');
    }

    if ($page === 'charts') {
        return str_contains($script, '/charts/');
    }

    if ($page === 'help') {
        return str_contains($script, '/help/');
    }

    if ($page === 'index.php') {
        return str_ends_with($script, '/index.php') && !str_contains($script, '/restaurants/');
    }

    if (str_contains($page, '/')) {
        return str_contains($script, $page);
    }

    return basename($script) === $page;
}

function get_available_themes(): array
{
    return [
        'classic' => [
            'label' => 'Classic',
            'description' => 'Burgundy and gold palette with a warm, upscale feel.',
            'primary' => '#8b2942',
            'accent' => '#c9a962',
            'background' => '#faf8f5',
        ],
        'refresh' => [
            'label' => 'Refresh',
            'description' => 'Teal and coral palette with a fresh coastal mood.',
            'primary' => '#0d5c63',
            'accent' => '#f4a261',
            'background' => '#f0f7f7',
        ],
        'forest' => [
            'label' => 'Forest',
            'description' => 'Emerald and amber palette with a natural, earthy feel.',
            'primary' => '#2d6a4f',
            'accent' => '#e9c46a',
            'background' => '#f4f7f4',
        ],
    ];
}

function theme_label(string $theme): string
{
    $themes = get_available_themes();

    return $themes[$theme]['label'] ?? ucfirst($theme);
}


// get the site setting from the database
function get_site_setting(string $key, ?string $default = null): ?string
{
    static $cache = [];

    // check if the setting is already in the cache
    if (array_key_exists($key, $cache)) {
        return $cache[$key];
    }

    // if not, get the setting from the database
    try {
        require_once __DIR__ . '/db.php';
        $stmt = db()->prepare('SELECT setting_value FROM site_settings WHERE setting_key = :key');
        $stmt->execute(['key' => $key]);
        $value = $stmt->fetchColumn();
        $cache[$key] = $value !== false ? (string) $value : $default;

        return $cache[$key];
    } catch (Throwable $e) {
        return $default;
    }
}


// set the site setting in the database
function set_site_setting(string $key, string $value): void
{
    require_once __DIR__ . '/db.php';

    $stmt = db()->prepare(
        'INSERT INTO site_settings (setting_key, setting_value)
         VALUES (:insert_key, :insert_value)
         ON DUPLICATE KEY UPDATE setting_value = :update_value'
    );
    $stmt->execute([
        'insert_key' => $key,
        'insert_value' => $value,
        'update_value' => $value,
    ]);
}


// get the site theme from the database
function get_site_theme(): string
{
    $themes = array_keys(get_available_themes());
    $siteTheme = get_site_setting('active_theme', 'classic');

    if (in_array($siteTheme, $themes, true)) {
        return $siteTheme;
    }

    return 'classic';
}

// set the site theme in the database
function set_site_theme(string $theme): bool
{
    $themes = array_keys(get_available_themes());

    if (!in_array($theme, $themes, true)) {
        return false;
    }

    set_site_setting('active_theme', $theme);

    return true;
}

// get the active theme from the database or cookie
function get_active_theme(): string
{
    $themes = array_keys(get_available_themes());

    if (isset($_COOKIE['dinespot_theme']) && in_array($_COOKIE['dinespot_theme'], $themes, true)) { // this appears to admin only
        return $_COOKIE['dinespot_theme'];
    }

    return get_site_theme();
}

// set the theme cookie
function set_theme_cookie(string $theme, bool $isPreview = false): void
{
    $themes = array_keys(get_available_themes());

    if (!in_array($theme, $themes, true)) {
        return;
    }

    if ($isPreview) {
        setcookie('dinespot_theme', $theme, [
            'expires' => time() + 60 * 5, // 5 minutes
            'path' => '/',
            'samesite' => 'Lax',
        ]);
        $_COOKIE['dinespot_theme'] = $theme;
        return;
    }
    setcookie('dinespot_theme', $theme, [
        'expires' => time() + 60 * 60 * 24 * 365,
        'path' => '/',
        'samesite' => 'Lax',
    ]);
    $_COOKIE['dinespot_theme'] = $theme;
    return;
}

function theme_stylesheet(): string
{
    return 'theme-' . get_active_theme() . '.css';
}

function theme_switch_url(string $theme): string
{
    $redirect = $_SERVER['REQUEST_URI'] ?? (asset_prefix() . 'index.php');

    $redirect = preg_replace('/\?.*$/', '', $redirect);

    return admin_path('theme/switch.php') . '?theme=' . urlencode($theme) . '&redirect=' . urlencode($redirect);
}

function price_range_label(int $range): string
{
    return str_repeat('$', max(1, min(4, $range)));
}

function restaurant_image_url(array $restaurant, string $prefix = ''): string
{
    return $prefix . ($restaurant['image_path'] ?? 'assets/images/DineSpot-logo.jpg');
}

// functions to get, update, delete, etc. the restaurants, menus,... data from the database
function get_featured_restaurants(int $limit = 6): array
{
    require_once __DIR__ . '/db.php';

    $stmt = db()->prepare(
        'SELECT r.*, COALESCE(AVG(rv.rating), 0) AS avg_rating, COUNT(rv.id) AS review_count
         FROM restaurants r
         LEFT JOIN reviews rv ON rv.restaurant_id = r.id
         WHERE r.is_active = 1
         GROUP BY r.id
         ORDER BY avg_rating DESC, r.name ASC
         LIMIT :limit'
    );
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

function get_all_restaurants(): array
{
    require_once __DIR__ . '/db.php';

    $sql = 'SELECT r.*, COALESCE(AVG(rv.rating), 0) AS avg_rating, COUNT(rv.id) AS review_count
            FROM restaurants r
            LEFT JOIN reviews rv ON rv.restaurant_id = r.id
            WHERE r.is_active = 1
            GROUP BY r.id
            ORDER BY r.name ASC';

    return db()->query($sql)->fetchAll();
}

function search_restaurants(string $query = '', string $cuisine = '', string $city = ''): array
{
    require_once __DIR__ . '/db.php';

    $conditions = ['r.is_active = 1'];
    $params = [];

    if ($query !== '') {
        $conditions[] = 'MATCH(r.name, r.cuisine, r.description) AGAINST(:query IN NATURAL LANGUAGE MODE)';
        $params['query'] = $query;
    }

    if ($cuisine !== '') {
        $conditions[] = 'r.cuisine = :cuisine';
        $params['cuisine'] = $cuisine;
    }

    if ($city !== '') {
        $conditions[] = 'r.city = :city';
        $params['city'] = $city;
    }

    $sql = 'SELECT r.*, COALESCE(AVG(rv.rating), 0) AS avg_rating, COUNT(rv.id) AS review_count
            FROM restaurants r
            LEFT JOIN reviews rv ON rv.restaurant_id = r.id
            WHERE ' . implode(' AND ', $conditions) . '
            GROUP BY r.id
            ORDER BY r.name ASC';

    $stmt = db()->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll();
}

function get_restaurant_by_id(int $id): ?array
{
    require_once __DIR__ . '/db.php';

    $stmt = db()->prepare(
        'SELECT r.*, COALESCE(AVG(rv.rating), 0) AS avg_rating, COUNT(rv.id) AS review_count
         FROM restaurants r
         LEFT JOIN reviews rv ON rv.restaurant_id = r.id
         WHERE r.id = :id AND r.is_active = 1
         GROUP BY r.id'
    );
    $stmt->execute(['id' => $id]);
    $restaurant = $stmt->fetch();

    return $restaurant ?: null;
}

function get_menu_items_grouped(int $restaurantId): array
{
    require_once __DIR__ . '/db.php';

    $stmt = db()->prepare(
        'SELECT category, name, description, price
         FROM menu_items
         WHERE restaurant_id = :restaurant_id
         ORDER BY category ASC, name ASC'
    );
    $stmt->execute(['restaurant_id' => $restaurantId]);

    $grouped = [];
    foreach ($stmt->fetchAll() as $item) {
        $grouped[$item['category']][] = $item;
    }

    return $grouped;
}

function get_reviews_for_restaurant(int $restaurantId): array
{
    require_once __DIR__ . '/db.php';

    $stmt = db()->prepare(
        'SELECT rv.rating, rv.comment, rv.created_at, u.full_name
         FROM reviews rv
         INNER JOIN users u ON u.id = rv.user_id
         WHERE rv.restaurant_id = :restaurant_id
         ORDER BY rv.created_at DESC'
    );
    $stmt->execute(['restaurant_id' => $restaurantId]);

    return $stmt->fetchAll();
}

function get_filter_options(): array
{
    require_once __DIR__ . '/db.php';

    $cuisines = db()->query(
        'SELECT DISTINCT cuisine FROM restaurants WHERE is_active = 1 ORDER BY cuisine ASC'
    )->fetchAll(PDO::FETCH_COLUMN);

    $cities = db()->query(
        'SELECT DISTINCT city FROM restaurants WHERE is_active = 1 ORDER BY city ASC'
    )->fetchAll(PDO::FETCH_COLUMN);

    return ['cuisines' => $cuisines, 'cities' => $cities];
}

function youtube_embed_url(string $url): string
{
    if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([A-Za-z0-9_-]+)/', $url, $matches)) {
        return 'https://www.youtube.com/embed/' . $matches[1];
    }

    return $url;
}

function format_rating(float $rating): string
{
    return number_format($rating, 1);
}

function is_restaurant_favourited(int $userId, int $restaurantId): bool
{
    require_once __DIR__ . '/db.php';

    $stmt = db()->prepare(
        'SELECT 1 FROM favourites WHERE user_id = :user_id AND restaurant_id = :restaurant_id LIMIT 1'
    );
    $stmt->execute([
        'user_id' => $userId,
        'restaurant_id' => $restaurantId,
    ]);

    return (bool) $stmt->fetchColumn();
}
function is_restaurant_reserved(int $userId, int $restaurantId): bool
{
    require_once __DIR__ . '/db.php';
    $stmt = db()->prepare(
        'SELECT 1 FROM reservations
         WHERE user_id = :user_id
           AND restaurant_id = :restaurant_id
           AND status IN (\'pending\', \'approved\')
         LIMIT 1'
    );
    $stmt->execute([
        'user_id' => $userId,
        'restaurant_id' => $restaurantId,
    ]);

    return (bool) $stmt->fetchColumn();
}

function is_restaurant_approved_reservation(int $userId, int $restaurantId): bool
{
    require_once __DIR__ . '/db.php';
    $stmt = db()->prepare(
        'SELECT 1 FROM reservations
         WHERE user_id = :user_id
           AND restaurant_id = :restaurant_id
           AND status = \'approved\'
         LIMIT 1'
    );
    $stmt->execute([
        'user_id' => $userId,
        'restaurant_id' => $restaurantId,
    ]);
    return (bool) $stmt->fetchColumn();
}

function cancel_restaurant_reservation(int $userId, int $restaurantId): void
{
    require_once __DIR__ . '/db.php';

    $stmt = db()->prepare(
        'UPDATE reservations
         SET status = \'cancelled\'
         WHERE user_id = :user_id
           AND restaurant_id = :restaurant_id
           AND status IN (\'pending\', \'approved\')'
    );
    $stmt->execute([
        'user_id' => $userId,
        'restaurant_id' => $restaurantId,
    ]);
}

function user_has_reviewed_restaurant(int $userId, int $restaurantId): bool
{
    require_once __DIR__ . '/db.php';

    $stmt = db()->prepare(
        'SELECT 1 FROM reviews WHERE user_id = :user_id AND restaurant_id = :restaurant_id LIMIT 1'
    );
    $stmt->execute([
        'user_id' => $userId,
        'restaurant_id' => $restaurantId,
    ]);

    return (bool) $stmt->fetchColumn();
}

function get_user_review_for_restaurant(int $userId, int $restaurantId): ?array
{
    require_once __DIR__ . '/db.php';

    $stmt = db()->prepare(
        'SELECT id, rating, comment, created_at
         FROM reviews
         WHERE user_id = :user_id AND restaurant_id = :restaurant_id
         LIMIT 1'
    );
    $stmt->execute([
        'user_id' => $userId,
        'restaurant_id' => $restaurantId,
    ]);
    $review = $stmt->fetch();

    return $review ?: null;
}

function delete_review_by_id(int $reviewId, int $userId): bool
{
    require_once __DIR__ . '/db.php';

    $stmt = db()->prepare(
        'DELETE FROM reviews WHERE id = :id AND user_id = :user_id'
    );
    $stmt->execute([
        'id' => $reviewId,
        'user_id' => $userId,
    ]);

    return $stmt->rowCount() > 0;
}

function get_review_by_id_for_user(int $reviewId, int $userId): ?array
{
    require_once __DIR__ . '/db.php';

    $stmt = db()->prepare(
        'SELECT rv.id, rv.rating, rv.comment, rv.restaurant_id, rest.name AS restaurant_name
         FROM reviews rv
         INNER JOIN restaurants rest ON rest.id = rv.restaurant_id
         WHERE rv.id = :id AND rv.user_id = :user_id
         LIMIT 1'
    );
    $stmt->execute([
        'id' => $reviewId,
        'user_id' => $userId,
    ]);
    $review = $stmt->fetch();

    return $review ?: null;
}

function get_active_reservation_for_restaurant(int $userId, int $restaurantId): ?array
{
    require_once __DIR__ . '/db.php';

    $stmt = db()->prepare(
        'SELECT id, reservation_date, reservation_time, party_size, status
         FROM reservations
         WHERE user_id = :user_id
           AND restaurant_id = :restaurant_id
           AND status = \'pending\'
         ORDER BY reservation_date ASC, reservation_time ASC
         LIMIT 1'
    );
    $stmt->execute([
        'user_id' => $userId,
        'restaurant_id' => $restaurantId,
    ]);
    $reservation = $stmt->fetch();

    return $reservation ?: null;
}

function cancel_reservation_by_id(int $reservationId, int $userId): bool
{
    require_once __DIR__ . '/db.php';

    $stmt = db()->prepare(
        'UPDATE reservations
         SET status = \'cancelled\'
         WHERE id = :id
           AND user_id = :user_id
           AND status = \'pending\''
    );
    $stmt->execute([
        'id' => $reservationId,
        'user_id' => $userId,
    ]);

    return $stmt->rowCount() > 0;
}

function get_restaurant_count_by_cuisine(): array
{
    require_once __DIR__ . '/db.php';

    return db()->query(
        'SELECT cuisine, COUNT(*) AS total
         FROM restaurants
         WHERE is_active = 1
         GROUP BY cuisine
         ORDER BY total DESC, cuisine ASC'
    )->fetchAll();
}

function get_reservation_count_by_status(): array
{
    require_once __DIR__ . '/db.php';

    return db()->query(
        'SELECT status, COUNT(*) AS total
         FROM reservations
         GROUP BY status
         ORDER BY total DESC'
    )->fetchAll();
}

function get_top_rated_restaurants(int $limit = 5): array
{
    require_once __DIR__ . '/db.php';

    $stmt = db()->prepare(
        'SELECT r.name,
                COALESCE(AVG(rv.rating), 0) AS avg_rating,
                COUNT(rv.id) AS review_count
         FROM restaurants r
         INNER JOIN reviews rv ON rv.restaurant_id = r.id
         WHERE r.is_active = 1
         GROUP BY r.id, r.name
         HAVING review_count > 0
         ORDER BY avg_rating DESC, review_count DESC
         LIMIT :limit'
    );
    $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

function get_admin_dashboard_stats(): array
{
    require_once __DIR__ . '/db.php';

    return [
        'users' => (int) db()->query('SELECT COUNT(*) FROM users')->fetchColumn(),
        'clients' => (int) db()->query("SELECT COUNT(*) FROM users WHERE role = 'client'")->fetchColumn(),
        'restaurants' => (int) db()->query('SELECT COUNT(*) FROM restaurants')->fetchColumn(),
        'active_restaurants' => (int) db()->query('SELECT COUNT(*) FROM restaurants WHERE is_active = 1')->fetchColumn(),
        'reservations' => (int) db()->query('SELECT COUNT(*) FROM reservations')->fetchColumn(),
        'pending_reservations' => (int) db()->query("SELECT COUNT(*) FROM reservations WHERE status = 'pending'")->fetchColumn(),
        'reviews' => (int) db()->query('SELECT COUNT(*) FROM reviews')->fetchColumn(),
        'favourites' => (int) db()->query('SELECT COUNT(*) FROM favourites')->fetchColumn(),
        'avg_rating' => (float) db()->query('SELECT COALESCE(AVG(rating), 0) FROM reviews')->fetchColumn(),
    ];
}

function get_recent_reservations_for_admin(int $limit = 5): array
{
    require_once __DIR__ . '/db.php';

    $stmt = db()->prepare(
        'SELECT r.id, r.reservation_date, r.reservation_time, r.party_size, r.status, r.created_at,
                rest.name AS restaurant_name, u.full_name AS user_name
         FROM reservations r
         INNER JOIN restaurants rest ON rest.id = r.restaurant_id
         INNER JOIN users u ON u.id = r.user_id
         ORDER BY r.created_at DESC
         LIMIT :limit'
    );
    $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

function get_recent_reviews_for_admin(int $limit = 5): array
{
    require_once __DIR__ . '/db.php';

    $stmt = db()->prepare(
        'SELECT rv.id, rv.rating, rv.comment, rv.created_at,
                rest.name AS restaurant_name, u.full_name AS user_name
         FROM reviews rv
         INNER JOIN restaurants rest ON rest.id = rv.restaurant_id
         INNER JOIN users u ON u.id = rv.user_id
         ORDER BY rv.created_at DESC
         LIMIT :limit'
    );
    $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

function get_all_restaurants_for_admin(): array
{
    require_once __DIR__ . '/db.php';

    $sql = 'SELECT r.*, COALESCE(AVG(rv.rating), 0) AS avg_rating, COUNT(rv.id) AS review_count
            FROM restaurants r
            LEFT JOIN reviews rv ON rv.restaurant_id = r.id
            GROUP BY r.id
            ORDER BY r.name ASC';

    return db()->query($sql)->fetchAll();
}

function get_restaurant_by_id_for_admin(int $id): ?array
{
    require_once __DIR__ . '/db.php';

    $stmt = db()->prepare('SELECT * FROM restaurants WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $restaurant = $stmt->fetch();

    return $restaurant ?: null;
}

function create_restaurant(array $data): bool
{
    require_once __DIR__ . '/db.php';

    $stmt = db()->prepare(
        'INSERT INTO restaurants (name, cuisine, city, province, description, address, image_path, price_range, is_active, image_credit)
         VALUES (:name, :cuisine, :city, :province, :description, :address, :image_path, :price_range, :is_active, :image_credit)'
    );
    $stmt->execute([
        'name' => $data['name'],
        'cuisine' => $data['cuisine'],
        'city' => $data['city'],
        'province' => $data['province'],
        'description' => $data['description'],
        'address' => $data['address'] !== '' ? $data['address'] : null,
        'image_path' => $data['image_path'] !== '' ? $data['image_path'] : null,
        'price_range' => $data['price_range'],
        'is_active' => $data['is_active'] ?? 1,
        'image_credit' => $data['image_credit'] !== '' ? $data['image_credit'] : null,
    ]);

    return $stmt->rowCount() > 0;
}

function update_restaurant(int $id, array $data): bool
{
    require_once __DIR__ . '/db.php';
    try {
        $stmt = db()->prepare(
            'UPDATE restaurants
            SET name = :name, cuisine = :cuisine, city = :city, province = :province,
                description = :description, address = :address, image_path = :image_path, price_range = :price_range,
                is_active = :is_active, image_credit = :image_credit
            WHERE id = :id'
        );
        $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'cuisine' => $data['cuisine'],
            'city' => $data['city'],
            'province' => $data['province'],
            'description' => $data['description'],
            'address' => $data['address'] !== '' ? $data['address'] : null,
            'image_path' => $data['image_path'] !== '' ? $data['image_path'] : null,
            'price_range' => $data['price_range'],
            'is_active' => $data['is_active'] ?? 1,
            'image_credit' => $data['image_credit'] !== '' ? $data['image_credit'] : null,
        ]);
    }
    catch (PDOException $e) {
        echo $e->getMessage();
        exit;
    }
    if ($stmt->rowCount() > 0) {
        return true;
    }
    $check = db()->prepare('SELECT 1 FROM restaurants WHERE id = :id LIMIT 1');
    $check->execute(['id' => $id]);

    return (bool) $check->fetchColumn();
}

function update_restaurant_active(int $id, int $isActive): bool
{
    require_once __DIR__ . '/db.php';

    $stmt = db()->prepare('UPDATE restaurants SET is_active = :is_active WHERE id = :id');
    $stmt->execute(['is_active' => $isActive, 'id' => $id]);

    return $stmt->rowCount() > 0;
}

function get_all_reservations_for_admin(): array
{
    require_once __DIR__ . '/db.php';

    return db()->query(
        'SELECT r.id, r.reservation_date, r.reservation_time, r.party_size, r.status, r.created_at,
                rest.name AS restaurant_name, u.full_name AS user_name
         FROM reservations r
         INNER JOIN restaurants rest ON rest.id = r.restaurant_id
         INNER JOIN users u ON u.id = r.user_id
         ORDER BY r.reservation_date DESC, r.reservation_time DESC'
    )->fetchAll();
}

function get_reservation_by_id(int $id): ?array
{
    require_once __DIR__ . '/db.php';

    $stmt = db()->prepare(
        'SELECT r.id, r.status
         FROM reservations r
         WHERE r.id = :id'
    );
    $stmt->execute(['id' => $id]);
    $reservation = $stmt->fetch();

    return $reservation ?: null;
}

function update_reservation_status_admin(int $id, string $status): bool
{
    require_once __DIR__ . '/db.php';

    $stmt = db()->prepare('UPDATE reservations SET status = :status WHERE id = :id');
    $stmt->execute(['status' => $status, 'id' => $id]);

    return $stmt->rowCount() > 0;
}

function get_all_reviews_for_admin(): array
{
    require_once __DIR__ . '/db.php';

    return db()->query(
        'SELECT rv.id, rv.rating, rv.comment, rv.created_at,
                rest.name AS restaurant_name, u.full_name AS user_name
         FROM reviews rv
         INNER JOIN restaurants rest ON rest.id = rv.restaurant_id
         INNER JOIN users u ON u.id = rv.user_id
         ORDER BY rv.created_at DESC'
    )->fetchAll();
}

function get_review_by_id_for_admin(int $id): ?array
{
    require_once __DIR__ . '/db.php';

    $stmt = db()->prepare('SELECT id FROM reviews WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $review = $stmt->fetch();

    return $review ?: null;
}

function delete_review_by_id_admin(int $id): bool
{
    require_once __DIR__ . '/db.php';

    $stmt = db()->prepare('DELETE FROM reviews WHERE id = :id');
    $stmt->execute(['id' => $id]);

    return $stmt->rowCount() > 0;
}


function image_upload($inputName, $feature = null, $currentImagePath = '') {
    $returnPath = null;
    if ($feature === null) {
        $returnPath = 'assets/images/';
    } else {
        $returnPath = 'assets/images/' . $feature . '/';
    }
    $uploadDir = asset_prefix() . $returnPath;
    $uploadDir = rtrim($uploadDir, '/') . '/';
    if (!isset($_FILES[$inputName])) {
        return ['error' => 'No file uploaded'];
    }
    $file = $_FILES[$inputName];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['error' => 'Failed to upload file'];
    }
    // check extension
    $allowedExtensions = ['jpg', 'jpeg', 'png'];
    $fileName = basename($_FILES[$inputName]['name']);
    $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    if (!in_array($extension, $allowedExtensions)) {
        return ['error' => 'Invalid file extension. Allowed extensions are: ' . implode(', ', $allowedExtensions)];
    }
    // check file size
    $maxFileSize = 5 * 1024 * 1024; // 5MB
    if ($file['size'] > $maxFileSize) {
        return ['error' => 'File size exceeds the maximum allowed size'];
    }
    // check if file already exists
    $destination = $uploadDir . $fileName;
    if ($currentImagePath === '') { // for create
        if (file_exists($destination) && $fileName !== "placeholder.jpg") {
            return ['error' => 'File already exists'];
        }
    }
    else { // for update
        if (basename($currentImagePath) !== $fileName && basename($currentImagePath) !== "placeholder.jpg") {
            if (file_exists($destination) && $fileName !== "placeholder.jpg") {
                return ['error' => 'File already exists. Please update the new image with the same name as the current image to overwrite or a different name which is not existing.'];
            }
        }
    }
    // delete current image if it exists
    if ($currentImagePath !== '' && basename($currentImagePath) !== "placeholder.jpg") {
        if (file_exists($uploadDir . basename($currentImagePath))) {
            unlink($uploadDir . basename($currentImagePath));
        }
    }
    // move file to destination
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return ['path' => $returnPath . $fileName];
    }
    return ['error' => 'Failed to move uploaded file', 'path' => null];
}