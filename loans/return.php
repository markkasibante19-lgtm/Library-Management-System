<?php
require_once '../includes/auth_check.php';
require_once '../config/database.php';

$loan_id = $_GET['id'] ?? null;

if ($loan_id) {
    $stmt = $pdo->prepare("SELECT book_id FROM loans WHERE id = ? AND return_date IS NULL");
    $stmt->execute([$loan_id]);
    $book_id = $stmt->fetchColumn();

    if ($book_id) {
        try {
            $pdo->beginTransaction();
            
            $stmt = $pdo->prepare("UPDATE loans SET return_date = CURDATE() WHERE id = ?");
            $stmt->execute([$loan_id]);

            $stmt = $pdo->prepare("UPDATE books SET available_copies = available_copies + 1 WHERE id = ?");
            $stmt->execute([$book_id]);

            $pdo->commit();
            header("Location: index.php?msg=Book returned successfully.");
            exit();
        } catch (Exception $e) {
            $pdo->rollBack();
            header("Location: index.php?error=Could not return book.");
            exit();
        }
    }
}
header("Location: index.php");
exit();
?>