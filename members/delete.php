<?php
require_once '../includes/auth_check.php';
require_once '../config/database.php';

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM loans WHERE member_id = ? AND return_date IS NULL");
    $stmt->execute([$id]);
    $activeLoans = $stmt->fetchColumn();

    if ($activeLoans > 0) {
        header("Location: index.php?error=Cannot delete member. They have active loans.");
        exit();
    } else {
        $stmt = $pdo->prepare("DELETE FROM members WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: index.php?msg=Member deleted successfully.");
        exit();
    }
}
?>