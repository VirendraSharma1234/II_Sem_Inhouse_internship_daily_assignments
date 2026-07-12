<?php
require 'db.php';
require 'auth.php';
requireLogin();
require 'functions.php';

$errors = [];
$old = ['name' => '', 'email' => '', 'cgpa' => '', 'branch' => '', 'college' => '', 'gender' => '', 'course' => '', 'address' => '', 'status' => 'Active'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($old as $key => $val) {
        $old[$key] = trim($_POST[$key] ?? $val);
    }

    $errors = validateStudent($old['name'], $old['email'], $old['cgpa'], $old['branch'], $old['college'], $old['gender'], $old['course'], $old['address']);

    if (empty($errors) && emailTaken($pdo, $old['email'])) {
        $errors[] = 'This email is already registered.';
    }

    $photoName = null;
    if (empty($errors)) {
        list($photoName, $photoError) = handlePhotoUpload();
        if ($photoError) $errors[] = $photoError;
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('INSERT INTO students (name, email, cgpa, branch, college, gender, course, address, photo, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$old['name'], $old['email'], $old['cgpa'], $old['branch'], $old['college'], $old['gender'], $old['course'], $old['address'], $photoName, $old['status']]);
        header('Location: index.php?added=1');
        exit;
    }
}

require 'header.php';
?>

<div class="card mx-auto" style="max-width: 700px;">
    <div class="card-header bg-white">
        <h4 class="mb-0"><i class="bi bi-person-plus-fill me-2"></i>Add New Student</h4>
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
        <form method="POST" action="add.php" enctype="multipart/form-data" novalidate>
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
                            <i class="bi bi-camera-fill"></i>
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
                    <i class="bi bi-send-fill me-2"></i>Save Student
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
        } else {
            preview.innerHTML = '<i class="bi bi-camera-fill"></i>';
        }
    });
</script>

<?php require 'footer.php'; ?>
