<?php
require_once '../includes/auth_check.php'; require_once '../config/database.php';
$id = $_GET['id'] ?? null; if (!$id) { header("Location: index.php"); exit(); }
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $num = trim($_POST['member_number']); $name = trim($_POST['name']); $email = trim($_POST['email']); $status = $_POST['status'];
    $stmt = $pdo->prepare("UPDATE members SET member_number=?, name=?, email=?, status=? WHERE id=?");
    $stmt->execute([$num, $name, $email, $status, $id]);
    header("Location: index.php?msg=Member updated."); exit();
}
$stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?"); $stmt->execute([$id]); $m = $stmt->fetch();
require_once '../includes/header.php'; require_once '../includes/navbar.php';
?>
<div class="container mt-4"><div class="card shadow-sm mx-auto" style="max-width: 600px;"><div class="card-body">
    <form method="POST">
        <div class="mb-3"><label>Member No</label><input type="text" name="member_number" class="form-control" value="<?= htmlspecialchars($m['member_number']) ?>" required></div>
        <div class="mb-3"><label>Name</label><input type="text" name="name" class="form-control" value="<?= htmlspecialchars($m['name']) ?>" required></div>
        <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($m['email']) ?>" required></div>
        <div class="mb-3"><label>Status</label><select name="status" class="form-select"><option value="active" <?= $m['status']=='active'?'selected':'' ?>>Active</option><option value="suspended" <?= $m['status']=='suspended'?'selected':'' ?>>Suspended</option></select></div>
        <button type="submit" class="btn btn-primary w-100">Update</button>
    </form>
</div></div></div>
<?php require_once '../includes/footer.php'; ?>
