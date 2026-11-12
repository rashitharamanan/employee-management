<?php

require_once __DIR__ . '/config/database.php';

try {
    $database = new Database();
    $pdo = $database->connect();

    echo "Database connection successful!";
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
}