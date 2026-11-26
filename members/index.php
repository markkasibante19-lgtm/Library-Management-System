<?php
require_once '../includes/auth_check.php'; require_once '../config/database.php';
$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';

$sql = "SELECT * FROM members WHERE 1=1"; 
$params = [];

if ($search) { 
    $sql .= " AND (name LIKE ? OR email LIKE ? OR member_number LIKE ?)"; 
    $params[] = "%$search%"; $params[] = "%$search%"; $params[] = "%$search%"; 
}
if ($status) {
    $sql .= " AND status = ?";
    $params[] = $status;
}

$stmt = $pdo->prepare($sql); 
$stmt->execute($params); 
$members = $stmt->fetchAll();
require_once '../includes/header.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<form method="GET" action="index.php" class="mb-3" style="display: flex; gap: 10px; flex-wrap: wrap;">
    <input type="text" name="search" class="form-control" placeholder="Search by name or email..." value="<?= htmlspecialchars($search) ?>" style="max-width: 300px;">
    
    <select name="status" class="form-control" style="max-width: 200px;">
        <option value="">All Statuses</option>
        <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
        <option value="suspended" <?= $status === 'suspended' ? 'selected' : '' ?>>Suspended</option>
    </select>
    
    <button type="submit" class="btn" style="padding: 10px 20px;">Search & Filter</button>
</form>

<div class="table-wrap">
    <table class="table">
        <thead><tr><th>Member No</th><th>Name</th><th>Email</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
            <?php foreach($members as $m): ?>
            <tr>
                <td><?= htmlspecialchars($m['member_number']) ?></td>
                <td><b><?= htmlspecialchars($m['name']) ?></b></td>
                <td><?= htmlspecialchars($m['email']) ?></td>
                <td><span class="chip <?= $m['status']=='active'?'is-ok':'is-danger' ?>"><?= ucfirst($m['status']) ?></span></td>
                <td>
                    <a href="edit.php?id=<?= $m['id'] ?>" style="color: var(--green); font-weight: 500; margin-right: 12px;">Edit</a>
                    
                    <form method="POST" action="delete.php" style="display:inline;" onsubmit="return confirm('Delete this member?');">
    <input type="hidden" name="id" value="<?= $m['id'] ?>">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    <button type="submit" style="background:none; border:none; color:var(--warn); font-weight:500; cursor:pointer; padding:0;">Delete</button>
</form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require_once '../includes/footer.php'; ?>
