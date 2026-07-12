<?php
session_start();
include "common/db_connect.php";

$errors = [];
$_SESSION['old'] = $_POST;

// Validation
if (empty(trim($_POST['name']))) {
    $errors[] = "Name is required";
}

if (empty(trim($_POST['email']))) {
    $errors[] = "Email is required";
} elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format";
}

if (empty(trim($_POST['college']))) {
    $errors[] = "College is required";
}

if (empty(trim($_POST['branch']))) {
    $errors[] = "Branch is required";
}

// If error
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header("Location: register.php");
    exit;
}

// Insert Data
$name = $_POST['name'];
$email = $_POST['email'];
$college = $_POST['college'];
$branch = $_POST['branch'];

$sql = "INSERT INTO students (name, email, college, branch)
        VALUES ('$name', '$email', '$college', '$branch')";

if (mysqli_query($conn, $sql)) {
    $_SESSION['success'] = "Student registered successfully!";
    unset($_SESSION['old']);
} else {
    $_SESSION['errors'][] = "Database error occurred";
}

header("Location: register.php");
exit;
