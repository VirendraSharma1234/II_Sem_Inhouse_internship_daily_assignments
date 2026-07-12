<?php
require 'db.php';
require 'auth.php';
requireLogin();

$name = trim($_GET['name'] ?? '');
$branch = trim($_GET['branch'] ?? '');
$course = trim($_GET['course'] ?? '');
$cgpaMin = trim($_GET['cgpa_min'] ?? '');
$cgpaMax = trim($_GET['cgpa_max'] ?? '');
$status = trim($_GET['status'] ?? 'Active');

$conditions = [];
$params = [];

if ($name !== '') {
    $conditions[] = '(name LIKE ? OR email LIKE ?)';
    $params[] = '%' . $name . '%';
    $params[] = '%' . $name . '%';
}
if ($branch !== '') {
    $conditions[] = 'branch = ?';
    $params[] = $branch;
}
if ($course !== '') {
    $conditions[] = 'course = ?';
    $params[] = $course;
}
if ($cgpaMin !== '' && is_numeric($cgpaMin)) {
    $conditions[] = 'cgpa >= ?';
    $params[] = $cgpaMin;
}
if ($cgpaMax !== '' && is_numeric($cgpaMax)) {
    $conditions[] = 'cgpa <= ?';
    $params[] = $cgpaMax;
}
if ($status !== 'All') {
    $conditions[] = 'status = ?';
    $params[] = $status;
}

$where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
$stmt = $pdo->prepare("SELECT * FROM students $where ORDER BY cgpa DESC, id ASC");
$stmt->execute($params);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalFiltered = count($students);
$topCgpa = $totalFiltered > 0 ? (float)$students[0]['cgpa'] : null;

$totalStudents = (int)$pdo->query('SELECT COUNT(*) FROM students')->fetchColumn();
$avgCgpa = $pdo->query('SELECT AVG(cgpa) FROM students')->fetchColumn();
$branchCounts = $pdo->query('SELECT branch, COUNT(*) AS total FROM students GROUP BY branch ORDER BY total DESC')->fetchAll(PDO::FETCH_ASSOC);
$recent = $pdo->query('SELECT name, branch, date_registered FROM students ORDER BY date_registered DESC LIMIT 5')->fetchAll(PDO::FETCH_ASSOC);

require 'header.php';
?>

<?php if (isset($_GET['added'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle-fill me-2"></i>Student added successfully!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php elseif (isset($_GET['updated'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle-fill me-2"></i>Student updated successfully!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php elseif (isset($_GET['deleted'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle-fill me-2"></i>Student deleted successfully!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row mb-4 g-3">
    <div class="col-md-3">
        <div class="card text-center h-100">
            <div class="card-body">
                <i class="bi bi-people-fill fs-3 text-primary mb-2"></i>
                <h3 class="mb-0"><?php echo $totalStudents; ?></h3>
                <small class="text-muted">Total Students</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center h-100">
            <div class="card-body">
                <i class="bi bi-graph-up-arrow fs-3 text-success mb-2"></i>
                <h3 class="mb-0"><?php echo $avgCgpa ? number_format($avgCgpa, 2) : '0.00'; ?></h3>
                <small class="text-muted">Average CGPA</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-body">
                <i class="bi bi-diagram-3-fill text-warning mb-2"></i>
                <div class="small text-muted mb-1">Students per Branch</div>
                <?php foreach ($branchCounts as $bc): ?>
                    <span class="badge bg-light text-dark border me-1 mb-1"><?php echo htmlspecialchars($bc['branch']); ?>: <?php echo $bc['total']; ?></span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="small text-muted mb-2"><i class="bi bi-clock-history me-1"></i>Recent Registrations</div>
                <?php foreach ($recent as $r): ?>
                    <div class="d-flex justify-content-between small mb-1">
                        <span><?php echo htmlspecialchars($r['name']); ?></span>
                        <span class="text-muted"><?php echo htmlspecialchars($r['branch']); ?></span>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($recent)): ?>
                    <div class="small text-muted">No registrations yet.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="index.php" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Search Name / Email</label>
                <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($name); ?>" placeholder="Search...">
            </div>
            <div class="col-md-2">
                <label class="form-label">Branch</label>
                <select class="form-select" name="branch">
                    <option value="">All</option>
                    <?php foreach (['Computer Science', 'Mechanical', 'Electrical', 'Civil', 'Information Technology'] as $b): ?>
                        <option <?php echo $branch === $b ? 'selected' : ''; ?>><?php echo $b; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Course</label>
                <select class="form-select" name="course">
                    <option value="">All</option>
                    <?php foreach (['B.Tech', 'M.Tech', 'MBA', 'BCA'] as $c): ?>
                        <option <?php echo $course === $c ? 'selected' : ''; ?>><?php echo $c; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-1">
                <label class="form-label">Min CGPA</label>
                <input type="number" step="0.01" class="form-control" name="cgpa_min" value="<?php echo htmlspecialchars($cgpaMin); ?>">
            </div>
            <div class="col-md-1">
                <label class="form-label">Max CGPA</label>
                <input type="number" step="0.01" class="form-control" name="cgpa_max" value="<?php echo htmlspecialchars($cgpaMax); ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="Active" <?php echo $status === 'Active' ? 'selected' : ''; ?>>Active</option>
                    <option value="Inactive" <?php echo $status === 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                    <option value="All" <?php echo $status === 'All' ? 'selected' : ''; ?>>All</option>
                </select>
            </div>
            <div class="col-md-1 d-grid">
                <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
            </div>
        </form>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0"><i class="bi bi-list-ul me-2"></i>Students</h4>
    <span class="badge bg-primary fs-6">Showing: <?php echo $totalFiltered; ?> students</span>
</div>

<?php if ($totalFiltered === 0): ?>
    <div class="alert alert-secondary">No students match your search.</div>
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
                <th>Course</th>
                <th>Status</th>
                <th>Registered On</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($students as $s): ?>
                <tr class="<?php echo (float)$s['cgpa'] === $topCgpa ? 'table-success' : ''; ?>">
                    <td>
                        <?php if (!empty($s['photo']) && file_exists(__DIR__ . '/uploads/' . $s['photo'])): ?>
                            <img src="uploads/<?php echo htmlspecialchars($s['photo']); ?>" style="width:40px;height:40px;object-fit:cover;border-radius:50%;">
                        <?php else: ?>
                            <i class="bi bi-person-circle fs-3 text-secondary"></i>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($s['name']); ?></td>
                    <td><?php echo htmlspecialchars($s['email']); ?></td>
                    <td><?php echo htmlspecialchars($s['cgpa']); ?></td>
                    <td><?php echo htmlspecialchars($s['branch']); ?></td>
                    <td><?php echo htmlspecialchars($s['course']); ?></td>
                    <td>
                        <span class="badge <?php echo $s['status'] === 'Active' ? 'bg-success' : 'bg-secondary'; ?>"><?php echo htmlspecialchars($s['status']); ?></span>
                    </td>
                    <td><?php echo htmlspecialchars($s['date_registered']); ?></td>
                    <td>
                        <a href="edit.php?id=<?php echo $s['id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                            <i class="bi bi-pencil-fill"></i>
                        </a>
                        <form method="POST" action="delete.php" class="d-inline" onsubmit="return confirm('Delete this student record? This cannot be undone.');">
                            <input type="hidden" name="id" value="<?php echo $s['id']; ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php require 'footer.php'; ?>
