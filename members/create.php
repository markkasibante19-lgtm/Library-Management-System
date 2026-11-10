<?php
require_once '../includes/auth_check.php';
require_once '../config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $member_number = trim($_POST['member_number']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $status = $_POST['status'] ?? 'active';

    if (empty($member_number) || empty($name) || empty($email)) {
        $error = "All fields are required.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO members (member_number, name, email, status) VALUES (?, ?, ?, ?)");
            $stmt->execute([$member_number, $name, $email, $status]);
            $success = "Member added successfully!";
        } catch (PDOException $e) {
            $error = "Error: Email or Member Number already exists.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Member</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card shadow mx-auto" style="max-width: 600px;">
            <div class="card-body">
                <h3 class="mb-4">Register New Member</h3>
                
                <?php if($error): ?> <div class="alert alert-danger"><?= $error ?></div> <?php endif; ?>
                <?php if($success): ?> <div class="alert alert-success"><?= $success ?> <a href="index.php">View List</a></div> <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Member Number</label>
                        <input type="text" name="member_number" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active">Active</option>
                            <option value="suspended">Suspended</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Member</button>
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>