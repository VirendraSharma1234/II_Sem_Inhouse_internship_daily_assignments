<?php
require 'db.php';

$errors = [];
$success = isset($_GET['success']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $cgpa = trim($_POST['cgpa'] ?? '');
    $branch = trim($_POST['branch'] ?? '');
    $college = trim($_POST['college'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $course = trim($_POST['course'] ?? '');
    $address = trim($_POST['address'] ?? '');

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

    if (empty($errors)) {
        $check = $pdo->prepare('SELECT COUNT(*) FROM students WHERE email = ?');
        $check->execute([$email]);
        if ($check->fetchColumn() > 0) {
            $errors[] = 'This email is already registered.';
        }
    }

    $photoName = null;
    if (empty($errors) && isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/uploads';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($ext, $allowed)) {
            $photoName = uniqid('photo_') . '.' . $ext;
            move_uploaded_file($_FILES['photo']['tmp_name'], $uploadDir . '/' . $photoName);
        } else {
            $errors[] = 'Photo must be a JPG, JPEG, PNG or GIF file.';
        }
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare('INSERT INTO students (name, email, cgpa, branch, college, gender, course, address, photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$name, $email, $cgpa, $branch, $college, $gender, $course, $address, $photoName]);
            header('Location: index.php?success=1');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'Something went wrong while saving your record. Please try again.';
        }
    }
}

$students = [];
$totalStudents = 0;
try {
    $stmt = $pdo->query('SELECT * FROM students ORDER BY cgpa DESC, id ASC');
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $totalStudents = count($students);
} catch (PDOException $e) {
    $errors[] = 'Could not load student records right now.';
}

$topCgpa = $totalStudents > 0 ? (float)$students[0]['cgpa'] : null;

require 'header.php';
?>

<?php if ($success): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fa-solid fa-circle-check me-2"></i>Registration successful!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card mx-auto mb-5" style="max-width: 700px;">
    <div class="card-header bg-white">
        <h4 class="mb-0"><i class="fa-solid fa-user-pen me-2"></i>Student Registration</h4>
    </div>
    <div class="card-body">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <strong><i class="fa-solid fa-triangle-exclamation me-2"></i>Please fix the following:</strong>
                <ul class="mb-0 mt-2">
                    <?php foreach ($errors as $err): ?>
                        <li><?php echo htmlspecialchars($err); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form method="POST" action="index.php" enctype="multipart/form-data" novalidate>
            <div class="mb-3">
                <label for="name" class="form-label">Full Name <span class="required-star">*</span></label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email Address <span class="required-star">*</span></label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="cgpa" class="form-label">CGPA <span class="required-star">*</span></label>
                    <input type="number" step="0.01" min="0" max="10" class="form-control" id="cgpa" name="cgpa" value="<?php echo htmlspecialchars($_POST['cgpa'] ?? ''); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="branch" class="form-label">Branch <span class="required-star">*</span></label>
                    <select class="form-select" id="branch" name="branch">
                        <option value="">-- Select --</option>
                        <?php foreach (['Computer Science', 'Mechanical', 'Electrical', 'Civil', 'Information Technology'] as $b): ?>
                            <option <?php echo ($_POST['branch'] ?? '') === $b ? 'selected' : ''; ?>><?php echo $b; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label for="college" class="form-label">College <span class="required-star">*</span></label>
                <input type="text" class="form-control" id="college" name="college" value="<?php echo htmlspecialchars($_POST['college'] ?? ''); ?>">
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label d-block">Gender <span class="required-star">*</span></label>
                    <?php foreach (['Male', 'Female', 'Other'] as $g): ?>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="gender<?php echo $g; ?>" value="<?php echo $g; ?>" <?php echo ($_POST['gender'] ?? '') === $g ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="gender<?php echo $g; ?>"><?php echo $g; ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="course" class="form-label">Course <span class="required-star">*</span></label>
                    <select class="form-select" id="course" name="course">
                        <option value="">-- Select --</option>
                        <?php foreach (['B.Tech', 'M.Tech', 'MBA', 'BCA'] as $c): ?>
                            <option <?php echo ($_POST['course'] ?? '') === $c ? 'selected' : ''; ?>><?php echo $c; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address <span class="required-star">*</span></label>
                <textarea class="form-control" id="address" name="address" rows="2"><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
            </div>
            <div class="mb-4">
                <label for="photo" class="form-label">Profile Photo (Optional)</label>
                <div class="d-flex align-items-center gap-3">
                    <div id="photoPreview" class="profile-placeholder">
                        <i class="fa-solid fa-camera"></i>
                    </div>
                    <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                </div>
            </div>
            <div class="d-flex justify-content-between">
                <button type="reset" class="btn btn-outline-secondary">Clear</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-paper-plane me-2"></i>Submit Form
                </button>
            </div>
        </form>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0"><i class="fa-solid fa-list me-2"></i>Registered Students</h4>
    <span class="badge bg-primary fs-6">Total: <?php echo $totalStudents; ?> students</span>
</div>

<?php if ($totalStudents === 0): ?>
    <div class="alert alert-secondary">No students registered yet.</div>
<?php else: ?>
<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle bg-white">
        <thead class="table-dark">
            <tr>
                <th>Photo</th>
                <th>Name</th>
                <th>Email</th>
                <th>CGPA</th>
                <th>Branch</th>
                <th>College</th>
                <th>Gender</th>
                <th>Course</th>
                <th>Address</th>
                <th>Registered On</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($students as $s): ?>
                <tr class="<?php echo (float)$s['cgpa'] === $topCgpa ? 'table-success' : ''; ?>">
                    <td>
                        <?php if (!empty($s['photo']) && file_exists(__DIR__ . '/uploads/' . $s['photo'])): ?>
                            <img src="uploads/<?php echo htmlspecialchars($s['photo']); ?>" style="width:40px;height:40px;object-fit:cover;border-radius:50%;">
                        <?php else: ?>
                            <i class="fa-solid fa-user-circle fs-3 text-secondary"></i>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($s['name']); ?></td>
                    <td><?php echo htmlspecialchars($s['email']); ?></td>
                    <td><?php echo htmlspecialchars($s['cgpa']); ?></td>
                    <td><?php echo htmlspecialchars($s['branch']); ?></td>
                    <td><?php echo htmlspecialchars($s['college']); ?></td>
                    <td><?php echo htmlspecialchars($s['gender']); ?></td>
                    <td><?php echo htmlspecialchars($s['course']); ?></td>
                    <td><?php echo htmlspecialchars($s['address']); ?></td>
                    <td><?php echo htmlspecialchars($s['date_registered']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<script>
    document.getElementById('photo').addEventListener('change', function(e) {
        const preview = document.getElementById('photoPreview');
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(ev) {
                preview.innerHTML = '<img src="' + ev.target.result + '" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">';
            };
            reader.readAsDataURL(file);
        } else {
            preview.innerHTML = '<i class="fa-solid fa-camera"></i>';
        }
    });
</script>

<?php require 'footer.php'; ?>
