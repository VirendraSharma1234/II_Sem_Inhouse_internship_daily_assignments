<?php
$host = 'localhost';
$dbname = 'student_registration';
$dbuser = 'root';
$dbpass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('<div style="font-family:Arial;padding:40px;color:#dc3545;">Database connection failed. Please check your MySQL credentials in db.php.</div>');
}
