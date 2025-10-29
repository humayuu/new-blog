<?php

// Connection to database

$dsn = 'mysql:host=localhost;dbname=news_blog_db;charset=utf8mb4;';
$user = 'root';
$password = '';
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false
];

try {
    $conn = new PDO($dsn, $user, $password, $options);
} catch (PDOException $e) {
    throw new PDOException('Database connection Failed ' . $e->getMessage());
}