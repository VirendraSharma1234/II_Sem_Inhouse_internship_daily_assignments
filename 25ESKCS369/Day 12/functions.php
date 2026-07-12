<?php
function validateStudent($name, $email, $cgpa, $branch, $college, $gender, $course, $address) {
    $errors = [];

    if ($name === '') {
        $errors[] = 'Full name is required.';
    } elseif (!preg_match('/^[A-Za-z\s]+$/', $name)) {
        $errors[] = 'Name should not contain numbers or special characters.';
    }
    if ($email === '') {
        $errors[] = 'Email address is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    if ($cgpa === '') {
        $errors[] = 'CGPA is required.';
    } elseif (!is_numeric($cgpa) || $cgpa < 0 || $cgpa > 10) {
        $errors[] = 'Enter a valid CGPA between 0 and 10.';
    }
    if ($branch === '') $errors[] = 'Please select a branch.';
    if ($college === '') $errors[] = 'College name is required.';
    if ($gender === '') $errors[] = 'Please select a gender.';
    if ($course === '') $errors[] = 'Please select a course.';
    if ($address === '') {
        $errors[] = 'Address is required.';
    } elseif (strlen($address) < 10) {
        $errors[] = 'Address should be at least 10 characters long.';
    }

    return $errors;
}

function emailTaken($pdo, $email, $excludeId = null) {
    if ($excludeId) {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM students WHERE email = ? AND id != ?');
        $stmt->execute([$email, $excludeId]);
    } else {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM students WHERE email = ?');
        $stmt->execute([$email]);
    }
    return $stmt->fetchColumn() > 0;
}

function handlePhotoUpload() {
    if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
        return [null, null];
    }
    $uploadDir = __DIR__ . '/uploads';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($ext, $allowed)) {
        return [null, 'Photo must be a JPG, JPEG, PNG or GIF file.'];
    }
    $photoName = uniqid('photo_') . '.' . $ext;
    move_uploaded_file($_FILES['photo']['tmp_name'], $uploadDir . '/' . $photoName);
    return [$photoName, null];
}
