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
?>
<form method="GET" action="index.php" class="mb-3" style="display: flex; gap: 10px; flex-wrap: wrap;">
    <input type="text" name="search" class="form-control" placeholder="Search by name or email..." value="<?= htmlspecialchars($search) ?>" style="max-width: 300px;">
    
    <select name="status" class="form-control" style="max-width: 200px;">
        <option value="">All Statuses</option>
        <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
        <option value="suspended" <?= $status === 'suspended' ? 'selected' : '' ?>>Suspended</option>
    </select>
