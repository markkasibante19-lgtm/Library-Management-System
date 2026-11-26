<?php
require_once '../includes/auth_check.php'; 
require_once '../config/database.php';

// Verify it is a POST request and the CSRF token is valid
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    
    //  Get the ID from POST instead of GET
    $id = $_POST['id'] ?? null;

    if ($id) {
        // Rule 6: Check if the book has active loans before trying to delete
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM loans WHERE book_id = ? AND return_date IS NULL");
        $stmt->execute([$id]);
        
        if ($stmt->fetchColumn() > 0) {
            // Friendly error if it has active loans
            header("Location: index.php?error=Cannot delete book. It is currently on loan.");
            exit();
        }

        // Try to delete (in case there are returned loans attached to it)
        try {
            $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?"); 
            $stmt->execute([$id]);
            header("Location: index.php?msg=Book deleted successfully.");
            exit();
        } catch (PDOException $e) {
            // Catches the raw foreign key error and shows a friendly message instead
            header("Location: index.php?error=Cannot delete book. It has past loan records attached to it.");
            exit();
        }
    }
} else {
    // If someone tries to access this file directly via URL, block them
    header("Location: index.php?error=Invalid security token.");
    exit();
}

header("Location: index.php");
exit();
?>
