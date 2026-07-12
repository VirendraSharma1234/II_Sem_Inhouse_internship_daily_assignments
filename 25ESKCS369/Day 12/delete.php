<?php
require 'db.php';
require 'auth.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $stmt = $pdo->prepare('SELECT photo FROM students WHERE id = ?');
    $stmt->execute([$id]);
    $photo = $stmt->fetchColumn();

    $stmt = $pdo->prepare('DELETE FROM students WHERE id = ?');
    $stmt->execute([$id]);

    if ($photo && file_exists(__DIR__ . '/uploads/' . $photo)) {
        unlink(__DIR__ . '/uploads/' . $photo);
    }
}

header('Location: index.php?deleted=1');
exit;
