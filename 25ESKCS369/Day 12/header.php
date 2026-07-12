<?php
$pageTitle = "Student Management Portal";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7f6;
            font-family: Arial, sans-serif;
        }
        .navbar-brand {
            font-weight: bold;
        }
        .card {
            border-radius: 8px;
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            animation: fadeInUp 0.4s ease;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .confirm-header {
            background: linear-gradient(90deg, #0d6efd, #6610f2);
            color: #fff;
            border-radius: 8px 8px 0 0;
            padding: 25px;
        }
        .profile-placeholder {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background-color: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            color: #adb5bd;
        }
        .error-text {
            color: #dc3545;
            font-size: 13px;
            margin-top: 4px;
        }
        .required-star {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark navbar-expand" style="background: linear-gradient(90deg, #0d6efd, #6610f2);">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-mortarboard-fill me-2"></i>Student Management Portal
            </a>
            <div>
                <a href="index.php" class="btn btn-sm btn-outline-light me-2"><i class="bi bi-list-ul me-1"></i>All Students</a>
                <a href="add.php" class="btn btn-sm btn-light me-2"><i class="bi bi-person-plus-fill me-1"></i>Add Student</a>
                <span class="text-white-50 me-2"><?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?></span>
                <a href="logout.php" class="btn btn-sm btn-outline-light"><i class="bi bi-box-arrow-right me-1"></i>Logout</a>
            </div>
        </div>
    </nav>
    <div class="container my-5">
