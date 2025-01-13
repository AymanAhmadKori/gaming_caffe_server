<?php
$SERVER_NAME = "localhost";
$USER_NAME = "root";
$PASSWORD = "";

$DBNAME = "game-coffe-mang-db";

try {
    $pdo = new PDO("mysql:host=$SERVER_NAME;dbname=$DBNAME", $USER_NAME, $PASSWORD);
    // ضبط وضع الخطأ في PDO إلى الاستثناءات
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Database connection Error: " . $e -> getMessage();
}
