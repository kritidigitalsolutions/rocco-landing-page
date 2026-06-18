<?php
/**
 * Rocco Play Admin — Database Connection
 * PDO with prepared statements, utf8mb4
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'rocco_play');          // local database name
define('DB_USER', 'root');               // XAMPP default local MySQL user
define('DB_PASS', '');                    // XAMPP default local MySQL password is empty

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    die("Database connection failed. Please check your configuration.");
}
