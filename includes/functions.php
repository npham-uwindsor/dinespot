<?php

function client_path(string $path): string
{
    return $GLOBALS['assetPrefix'] . 'client/' . $path;
}
function admin_path(string $path): string
{
    return $GLOBALS['assetPrefix'] . 'admin/' . $path;
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function current_script_path(): string
{
    return str_replace('\\', '/', $_SERVER['PHP_SELF'] ?? '');
}

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
        'classic' => 'Classic',
        'midnight' => 'Midnight',
    ];
}

function get_active_theme(): string
{
    $themes = array_keys(get_available_themes());

    if (isset($_COOKIE['dinespot_theme']) && in_array($_COOKIE['dinespot_theme'], $themes, true)) {
        return $_COOKIE['dinespot_theme'];
    }

    return 'classic';
}

function theme_stylesheet(): string
{
    return 'theme-' . get_active_theme() . '.css';
}

function theme_switch_url(string $theme): string
{
    $prefix = $GLOBALS['assetPrefix'] ?? '';
    $redirect = $_SERVER['REQUEST_URI'] ?? ($prefix . 'index.php');

    return $prefix . 'admin/theme/switch.php?theme=' . urlencode($theme) . '&redirect=' . urlencode($redirect);
}

function price_range_label(int $range): string
{
    return str_repeat('$', max(1, min(4, $range)));
}

function restaurant_image_url(array $restaurant, string $prefix = ''): string
{
    return $prefix . ($restaurant['image_path'] ?? 'assets/images/DineSpot-logo.jpg');
}

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
           AND status IN (\'pending\', \'approved\')
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
           AND status IN (\'pending\', \'approved\')'
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
        'SELECT name, avg_rating, review_count
         FROM restaurants
         WHERE is_active = 1 AND review_count > 0
         ORDER BY avg_rating DESC, review_count DESC
         LIMIT :limit'
    );
    $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}