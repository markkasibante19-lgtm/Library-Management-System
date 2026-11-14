<?php
require_once '../includes/auth_check.php'; require_once '../config/database.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $num = trim($_POST['member_number']); $name = trim($_POST['name']); $email = trim($_POST['email']); $status = $_POST['status'];
    try {
        $stmt = $pdo->prepare("INSERT INTO members (member_number, name, email, status) VALUES (?, ?, ?, ?)");
        $stmt->execute([$num, $name, $email, $status]);
        header("Location: index.php?msg=Member registered."); exit();
    } catch (PDOException $e) { $error = "Email or Member Number exists."; }
}
require_once '../includes/header.php'; require_once '../includes/navbar.php';
?>
<div class="container mt-4"><div class="card shadow-sm mx-auto" style="max-width: 600px;"><div class="card-body">
    <?php if($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
    <form method="POST">
        <div class="mb-3"><label>Member No</label><input type="text" name="member_number" class="form-control" required></div>
        <div class="mb-3"><label>Name</label><input type="text" name="name" class="form-control" required></div>
        <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" required></div>
        <div class="mb-3"><label>Status</label><select name="status" class="form-select"><option value="active">Active</option><option value="suspended">Suspended</option></select></div>
        <button type="submit" class="btn btn-primary w-100">Save</button>
    </form>
</div></div></div>
<?php require_once '../includes/footer.php'; ?>
