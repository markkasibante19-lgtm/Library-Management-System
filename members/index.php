<?php
require_once '../includes/auth_check.php';
require_once '../config/database.php';

$sql = "SELECT * FROM members WHERE 1=1";
$params = [];

if (!empty($_GET['search'])) {
    $sql .= " AND (name LIKE ? OR email LIKE ? OR member_number LIKE ?)";
    $searchTerm = "%" . $_GET['search'] . "%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

if (!empty($_GET['status'])) {
    $sql .= " AND status = ?";
    $params[] = $_GET['status'];
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$members = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Members</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between mb-3">
            <h2>Library Members</h2>
            <a href="create.php" class="btn btn-success">+ Add New Member</a>
        </div>

        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
        <?php endif; ?>
        <?php if(isset($_GET['error'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>

        <form method="GET" class="row mb-4 bg-light p-3 rounded">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control" placeholder="Search by name or email..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="active" <?= (($_GET['status'] ?? '') === 'active') ? 'selected' : '' ?>>Active</option>
                    <option value="suspended" <?= (($_GET['status'] ?? '') === 'suspended') ? 'selected' : '' ?>>Suspended</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">Search / Filter</button>
            </div>
        </form>

        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Member No.</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($members as $member): ?>
                <tr>
                    <td><?= htmlspecialchars($member['member_number']) ?></td>
                    <td><?= htmlspecialchars($member['name']) ?></td>
                    <td><?= htmlspecialchars($member['email']) ?></td>
                    <td>
                        <span class="badge <?= $member['status'] === 'active' ? 'bg-success' : 'bg-danger' ?>">
                            <?= ucfirst(htmlspecialchars($member['status'])) ?>
                        </span>
                    </td>
                    <td>
                        <a href="edit.php?id=<?= $member['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                        <a href="delete.php?id=<?= $member['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this member?');">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($members)): ?>
                <tr><td colspan="5" class="text-center">No members found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>