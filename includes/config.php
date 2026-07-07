<?php

/*
    Author: Tuong Nguyen Pham
    Student ID: 110192780
    COMP 3340 - Web Development
    Couse Project
    HTML5, CSS, JS, PHP, MySQL
*/
$env = 'development';

if ($env == 'development') {
    define('DB_HOST', 'localhost');
    define('DB_PORT', '3306');
    define('DB_NAME', 'dinespot');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_CHARSET', 'utf8mb4');
}
else {
    define('DB_HOST', 'localhost');
    define('DB_PORT', '3306');
    define('DB_NAME', 'pham39_dinespot');
    define('DB_USER', 'pham39_dinespot');
    define('DB_PASS', 'comp3340-project');
    define('DB_CHARSET', 'utf8mb4');
}

define('SITE_NAME', 'DineSpot');
define('SITE_TAGLINE', 'Discover, review, and reserve restaurants across Canada');
define('SITE_EMAIL', 'hello@dinespot.ca');
define('SITE_PHONE', '(519) 555-0142');
define('SITE_ADDRESS', '401 Sunset Avenue, Windsor, ON N9B 3P4');
define('SITE_SUPPORT_HOURS', '9:00 AM - 7:00 PM (EST)');
define('SITE_SUPPORT_EMAIL', 'support@dinespot.ca');

