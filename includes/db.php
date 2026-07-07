<?php
/*
Author: Tuong Nguyen Pham
Student ID: 110192780
COMP 3340 - Web Development
Couse Project
HTML5, CSS, JS, PHP, MySQL
*/
require_once __DIR__ . '/config.php';

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        DB_HOST,
        DB_PORT,
        DB_NAME,
        DB_CHARSET
    );

    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}

function database_is_ready(): bool
{
    try {
        return (int) db()->query('SELECT COUNT(*) FROM restaurants')->fetchColumn() >= 20;
    } catch (Throwable $e) {
        return false;
    }
}
