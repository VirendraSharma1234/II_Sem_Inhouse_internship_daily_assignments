<?php
function calculateGrade($cgpa) {
    if ($cgpa >= 9) return ['A+', 'success'];
    if ($cgpa >= 8) return ['A', 'success'];
    if ($cgpa >= 7) return ['B', 'info'];
    if ($cgpa >= 6) return ['C', 'warning'];
    if ($cgpa >= 5) return ['D', 'warning'];
    return ['F', 'danger'];
}

$errors = [];
$submitted = false;
$data = [
    'name' => '',
    'email' => '',
    'cgpa' => '',
    'branch' => '',
    'college' => '',
    'gender' => '',
    'course' => '',
    'address' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['name'] = trim($_POST['name'] ?? '');
    $data['email'] = trim($_POST['email'] ?? '');
    $data['cgpa'] = trim($_POST['cgpa'] ?? '');
    $data['branch'] = trim($_POST['branch'] ?? '');
    $data['college'] = trim($_POST['college'] ?? '');
    $data['gender'] = trim($_POST['gender'] ?? '');
    $data['course'] = trim($_POST['course'] ?? '');
    $data['address'] = trim($_POST['address'] ?? '');

    if ($data['name'] === '') $errors['name'] = 'This field is required.';
    if ($data['email'] === '') {
        $errors['email'] = 'This field is required.';
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    }
    if ($data['cgpa'] === '') {
        $errors['cgpa'] = 'This field is required.';
    } elseif (!is_numeric($data['cgpa']) || $data['cgpa'] < 0 || $data['cgpa'] > 10) {
        $errors['cgpa'] = 'Enter a valid CGPA between 0 and 10.';
    }
    if ($data['branch'] === '') $errors['branch'] = 'This field is required.';
    if ($data['college'] === '') $errors['college'] = 'This field is required.';
    if ($data['gender'] === '') $errors['gender'] = 'Please select a gender.';
    if ($data['course'] === '') $errors['course'] = 'This field is required.';

    if (empty($errors)) {
        $submitted = true;
    }
}

require 'header.php';
?>

<?php if ($submitted): ?>
    <?php list($grade, $gradeClass) = calculateGrade((float)$data['cgpa']); ?>
    <div class="card mx-auto" style="max-width: 650px;">
        <div class="confirm-header text-center">
            <div class="profile-placeholder mx-auto mb-3">
                <i class="fa-solid fa-user"></i>
            </div>
            <h4 class="mb-1"><?php echo htmlspecialchars($data['name']); ?></h4>
            <small>Registered on <?php echo date('d M Y'); ?></small>
        </div>
        <div class="card-body">
            <div class="alert alert-<?php echo $gradeClass; ?> text-center">
                <i class="fa-solid fa-award me-2"></i>
                CGPA: <?php echo htmlspecialchars($data['cgpa']); ?> &mdash; Grade: <strong><?php echo $grade; ?></strong>
            </div>
            <table class="table table-borderless mb-0">
                <tr>
                    <th><i class="fa-solid fa-envelope me-2"></i>Email</th>
                    <td><?php echo htmlspecialchars($data['email']); ?></td>
                </tr>
                <tr>
                    <th><i class="fa-solid fa-code-branch me-2"></i>Branch</th>
                    <td><?php echo htmlspecialchars($data['branch']); ?></td>
                </tr>
                <tr>
                    <th><i class="fa-solid fa-building-columns me-2"></i>College</th>
                    <td><?php echo htmlspecialchars($data['college']); ?></td>
                </tr>
                <tr>
                    <th><i class="fa-solid fa-venus-mars me-2"></i>Gender</th>
                    <td><?php echo htmlspecialchars($data['gender']); ?></td>
                </tr>
                <tr>
                    <th><i class="fa-solid fa-book me-2"></i>Course</th>
                    <td><?php echo htmlspecialchars($data['course']); ?></td>
                </tr>
                <?php if ($data['address'] !== ''): ?>
                <tr>
                    <th><i class="fa-solid fa-location-dot me-2"></i>Address</th>
                    <td><?php echo nl2br(htmlspecialchars($data['address'])); ?></td>
                </tr>
                <?php endif; ?>
            </table>
            <div class="text-center mt-4">
                <a href="index.php" class="btn btn-primary">
                    <i class="fa-solid fa-rotate-left me-2"></i>Register Another Student
                </a>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="card mx-auto" style="max-width: 650px;">
        <div class="card-header bg-white">
            <h4 class="mb-0"><i class="fa-solid fa-user-pen me-2"></i>Student Registration</h4>
        </div>
        <div class="card-body">
            <form method="POST" action="index.php" enctype="multipart/form-data" novalidate>
                <div class="mb-3">
                    <label for="name" class="form-label">Full Name <span class="required-star">*</span></label>
                    <input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" id="name" name="name" value="<?php echo htmlspecialchars($data['name']); ?>">
                    <?php if (isset($errors['name'])): ?><div class="error-text"><?php echo $errors['name']; ?></div><?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address <span class="required-star">*</span></label>
                    <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" id="email" name="email" value="<?php echo htmlspecialchars($data['email']); ?>">
                    <?php if (isset($errors['email'])): ?><div class="error-text"><?php echo $errors['email']; ?></div><?php endif; ?>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="cgpa" class="form-label">CGPA <span class="required-star">*</span></label>
                        <input type="number" step="0.01" min="0" max="10" class="form-control <?php echo isset($errors['cgpa']) ? 'is-invalid' : ''; ?>" id="cgpa" name="cgpa" value="<?php echo htmlspecialchars($data['cgpa']); ?>">
                        <?php if (isset($errors['cgpa'])): ?><div class="error-text"><?php echo $errors['cgpa']; ?></div><?php endif; ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="branch" class="form-label">Branch <span class="required-star">*</span></label>
                        <select class="form-select <?php echo isset($errors['branch']) ? 'is-invalid' : ''; ?>" id="branch" name="branch">
                            <option value="">-- Select --</option>
                            <?php foreach (['Computer Science', 'Mechanical', 'Electrical', 'Civil', 'Information Technology'] as $b): ?>
                                <option <?php echo $data['branch'] === $b ? 'selected' : ''; ?>><?php echo $b; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['branch'])): ?><div class="error-text"><?php echo $errors['branch']; ?></div><?php endif; ?>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="college" class="form-label">College <span class="required-star">*</span></label>
                    <input type="text" class="form-control <?php echo isset($errors['college']) ? 'is-invalid' : ''; ?>" id="college" name="college" value="<?php echo htmlspecialchars($data['college']); ?>">
                    <?php if (isset($errors['college'])): ?><div class="error-text"><?php echo $errors['college']; ?></div><?php endif; ?>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label d-block">Gender <span class="required-star">*</span></label>
                        <?php foreach (['Male', 'Female', 'Other'] as $g): ?>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="gender" id="gender<?php echo $g; ?>" value="<?php echo $g; ?>" <?php echo $data['gender'] === $g ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="gender<?php echo $g; ?>"><?php echo $g; ?></label>
                            </div>
                        <?php endforeach; ?>
                        <?php if (isset($errors['gender'])): ?><div class="error-text"><?php echo $errors['gender']; ?></div><?php endif; ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="course" class="form-label">Course <span class="required-star">*</span></label>
                        <select class="form-select <?php echo isset($errors['course']) ? 'is-invalid' : ''; ?>" id="course" name="course">
                            <option value="">-- Select --</option>
                            <?php foreach (['B.Tech', 'M.Tech', 'MBA', 'BCA'] as $c): ?>
                                <option <?php echo $data['course'] === $c ? 'selected' : ''; ?>><?php echo $c; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['course'])): ?><div class="error-text"><?php echo $errors['course']; ?></div><?php endif; ?>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Address (Optional)</label>
                    <textarea class="form-control" id="address" name="address" rows="2"><?php echo htmlspecialchars($data['address']); ?></textarea>
                </div>
                <div class="mb-4">
                    <label for="photo" class="form-label">Profile Photo (Optional)</label>
                    <input type="file" class="form-control" id="photo" name="photo">
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
<?php endif; ?>

<?php require 'footer.php'; ?>
