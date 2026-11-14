
<?php
require_once '../includes/auth_check.php'; 
require_once '../config/database.php';

$id = $_GET['id'] ?? null;

if ($id) {
    // Rule 6: Check if the member has unreturned books
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM loans WHERE member_id = ? AND return_date IS NULL");
    $stmt->execute([$id]);
    
    if ($stmt->fetchColumn() > 0) {
        // Friendly error if they have active loans
        header("Location: index.php?error=Cannot delete member. They currently have books on loan.");
        exit();
    }

    // Try to delete (in case they have returned books in their history)
    try {
        $stmt = $pdo->prepare("DELETE FROM members WHERE id = ?"); 
        $stmt->execute([$id]);
        header("Location: index.php?msg=Member deleted successfully.");
        exit();
    } catch (PDOException $e) {
        // Catches the raw foreign key error and shows a friendly message
        header("Location: index.php?error=Cannot delete member. They have past loan records attached to them.");
        exit();
    }
}
header("Location: index.php");
exit();
?>
