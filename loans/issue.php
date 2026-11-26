<?php
require_once '../includes/auth_check.php'; require_once '../config/database.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $member_id = $_POST['member_id']; $book_id = $_POST['book_id'];
    $issue_date = date('Y-m-d'); $due_date = date('Y-m-d', strtotime('+14 days'));
    
    $stmt = $pdo->prepare("SELECT status FROM members WHERE id = ?"); $stmt->execute([$member_id]); $status = $stmt->fetchColumn();
    $stmt = $pdo->prepare("SELECT available_copies FROM books WHERE id = ?"); $stmt->execute([$book_id]); $avail = $stmt->fetchColumn();
    
    if ($status === 'suspended') { $error = "Error: Member is suspended."; } 
    elseif ($avail <= 0) { $error = "Error: No copies available."; } 
    else {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM loans WHERE member_id = ? AND book_id = ? AND return_date IS NULL"); $stmt->execute([$member_id, $book_id]);
        if ($stmt->fetchColumn() > 0) { $error = "Error: Member already has this book."; } 
        else {
            try {
                $pdo->beginTransaction();
                $stmt = $pdo->prepare("INSERT INTO loans (book_id, member_id, issue_date, due_date) VALUES (?, ?, ?, ?)"); $stmt->execute([$book_id, $member_id, $issue_date, $due_date]);
                $stmt = $pdo->prepare("UPDATE books SET available_copies = available_copies - 1 WHERE id = ?"); $stmt->execute([$book_id]);
                $pdo->commit(); header("Location: index.php?msg=Book issued."); exit();
            } catch (Exception $e) { $pdo->rollBack(); $error = "Transaction failed."; }
        }
    }
}
$members = $pdo->query("SELECT * FROM members")->fetchAll(); $books = $pdo->query("SELECT * FROM books WHERE available_copies > 0")->fetchAll();
require_once '../includes/header.php'; require_once '../includes/navbar.php';
?>
<div class="container mt-4"><div class="card shadow-sm mx-auto" style="max-width: 600px;"><div class="card-body">
    <?php if($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
    <form method="POST">
        <div class="mb-3"><label>Member</label><select name="member_id" class="form-select" required><option value="">Choose...</option><?php foreach($members as $m) echo "<option value='{$m['id']}'>{$m['name']} ({$m['status']})</option>"; ?></select></div>
        <div class="mb-3"><label>Book</label><select name="book_id" class="form-select" required><option value="">Choose...</option><?php foreach($books as $b) echo "<option value='{$b['id']}'>{$b['title']} (Avail: {$b['available_copies']})</option>"; ?></select></div>
        <button type="submit" class="btn btn-primary w-100">Issue</button>
    </form>
</div></div></div>
<?php require_once '../includes/footer.php'; ?>
