<?php
$pageTitle = "Student Registration Portal";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
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
    <nav class="navbar navbar-dark" style="background: linear-gradient(90deg, #0d6efd, #6610f2);">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fa-solid fa-graduation-cap me-2"></i>Student Registration Portal
            </a>
            <span class="navbar-text text-white-50">30-Minute Hackathon Build</span>
        </div>
    </nav>
    <div class="container my-5">
