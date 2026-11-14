<?php
require_once '../includes/auth_check.php'; require_once '../config/database.php';
$search = $_GET['search'] ?? '';
$filter = $_GET['filter'] ?? '';

$sql = "SELECT loans.*, members.name AS member_name, books.title AS book_title FROM loans JOIN members ON loans.member_id = members.id JOIN books ON loans.book_id = books.id WHERE 1=1";
$params = [];

if ($search) {
    $sql .= " AND (members.name LIKE ? OR books.title LIKE ?)";
    $params[] = "%$search%"; $params[] = "%$search%";
}

if ($filter === 'out') {
    $sql .= " AND loans.return_date IS NULL";
} elseif ($filter === 'returned') {
    $sql .= " AND loans.return_date IS NOT NULL";
} elseif ($filter === 'overdue') {
    $sql .= " AND loans.return_date IS NULL AND loans.due_date < CURDATE()";
}

$sql .= " ORDER BY loans.id DESC";
$stmt = $pdo->prepare($sql); 
$stmt->execute($params); 
$loans = $stmt->fetchAll();

require_once '../includes/header.php';
?>
<form method="GET" action="index.php" class="mb-3" style="display: flex; gap: 10px; flex-wrap: wrap;">
    <input type="text" name="search" class="form-control" placeholder="Search by member or book..." value="<?= htmlspecialchars($search) ?>" style="max-width:
