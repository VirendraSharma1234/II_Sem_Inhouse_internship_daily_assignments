<?php
require 'db.php';
require 'auth.php';
requireLogin();
require 'functions.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM students WHERE id = ?');
$stmt->execute([$id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    header('Location: index.php');
    exit;
}

$errors = [];
$old = $student;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (['name', 'email', 'cgpa', 'branch', 'college', 'gender', 'course', 'address', 'status'] as $key) {
        $old[$key] = trim($_POST[$key] ?? '');
    }

    $errors = validateStudent($old['name'], $old['email'], $old['cgpa'], $old['branch'], $old['college'], $old['gender'], $old['course'], $old['address']);

    if (empty($errors) && emailTaken($pdo, $old['email'], $id)) {
        $errors[] = 'This email is already registered to another student.';
    }

    $photoName = $student['photo'];
    if (empty($errors) && isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        list($newPhoto, $photoError) = handlePhotoUpload();
        if ($photoError) {
            $errors[] = $photoError;
        } else {
            $photoName = $newPhoto;
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('UPDATE students SET name=?, email=?, cgpa=?, branch=?, college=?, gender=?, course=?, address=?, photo=?, status=? WHERE id=?');
        $stmt->execute([$old['name'], $old['email'], $old['cgpa'], $old['branch'], $old['college'], $old['gender'], $old['course'], $old['address'], $photoName, $old['status'], $id]);
        header('Location: index.php?updated=1');
        exit;
    }
}

require 'header.php';
?>

<div class="card mx-auto" style="max-width: 700px;">
    <div class="card-header bg-white">
        <h4 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Student</h4>
    </div>
    <div class="card-body">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <strong><i class="bi bi-exclamation-triangle-fill me-2"></i>Please fix the following:</strong>
                <ul class="mb-0 mt-2">
                    <?php foreach ($errors as $err): ?>
                        <li><?php echo htmlspecialchars($err); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form method="POST" action="edit.php?id=<?php echo $id; ?>" enctype="multipart/form-data" novalidate>
            <div class="mb-3">
                <label for="name" class="form-label">Full Name <span class="required-star">*</span></label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($old['name']); ?>">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email Address <span class="required-star">*</span></label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($old['email']); ?>">
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="cgpa" class="form-label">CGPA <span class="required-star">*</span></label>
                    <input type="number" step="0.01" min="0" max="10" class="form-control" id="cgpa" name="cgpa" value="<?php echo htmlspecialchars($old['cgpa']); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="branch" class="form-label">Branch <span class="required-star">*</span></label>
                    <select class="form-select" id="branch" name="branch">
                        <option value="">-- Select --</option>
                        <?php foreach (['Computer Science', 'Mechanical', 'Electrical', 'Civil', 'Information Technology'] as $b): ?>
                            <option <?php echo $old['branch'] === $b ? 'selected' : ''; ?>><?php echo $b; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label for="college" class="form-label">College <span class="required-star">*</span></label>
                <input type="text" class="form-control" id="college" name="college" value="<?php echo htmlspecialchars($old['college']); ?>">
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label d-block">Gender <span class="required-star">*</span></label>
                    <?php foreach (['Male', 'Female', 'Other'] as $g): ?>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="gender<?php echo $g; ?>" value="<?php echo $g; ?>" <?php echo $old['gender'] === $g ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="gender<?php echo $g; ?>"><?php echo $g; ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="course" class="form-label">Course <span class="required-star">*</span></label>
                    <select class="form-select" id="course" name="course">
                        <option value="">-- Select --</option>
                        <?php foreach (['B.Tech', 'M.Tech', 'MBA', 'BCA'] as $c): ?>
                            <option <?php echo $old['course'] === $c ? 'selected' : ''; ?>><?php echo $c; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option <?php echo $old['status'] === 'Active' ? 'selected' : ''; ?>>Active</option>
                        <option <?php echo $old['status'] === 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="photo" class="form-label">Profile Photo (Optional)</label>
                    <div class="d-flex align-items-center gap-3">
                        <div id="photoPreview" class="profile-placeholder" style="width:45px;height:45px;font-size:18px;">
                            <?php if (!empty($student['photo']) && file_exists(__DIR__ . '/uploads/' . $student['photo'])): ?>
                                <img src="uploads/<?php echo htmlspecialchars($student['photo']); ?>" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                            <?php else: ?>
                                <i class="bi bi-camera-fill"></i>
                            <?php endif; ?>
                        </div>
                        <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                    </div>
                </div>
            </div>
            <div class="mb-4">
                <label for="address" class="form-label">Address <span class="required-star">*</span></label>
                <textarea class="form-control" id="address" name="address" rows="2"><?php echo htmlspecialchars($old['address']); ?></textarea>
            </div>
            <div class="d-flex justify-content-between">
                <a href="index.php" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save-fill me-2"></i>Update Student
                </button>
            </div>
        </form>
    </div>
</div>

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
        }
    });
</script>

<?php require 'footer.php'; ?>
