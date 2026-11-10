<?php
require_once '../includes/auth_check.php';
require_once '../config/database.php';

$members = $pdo->query("SELECT id, name, member_number, status FROM members")->fetchAll();
$books = $pdo->query("SELECT id, title, available_copies FROM books")->fetchAll();

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $member_id = $_POST['member_id'];
    $book_id = $_POST['book_id'];
    $issue_date = date('Y-m-d');
    $due_date = date('Y-m-d', strtotime('+14 days'));

    $stmt = $pdo->prepare("SELECT status FROM members WHERE id = ?");
    $stmt->execute([$member_id]);
    $member_status = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT available_copies FROM books WHERE id = ?");
    $stmt->execute([$book_id]);
    $available = $stmt->fetchColumn();

    if ($member_status === 'suspended') {
        $error = "Error: This member is suspended and cannot borrow books.";
    } elseif ($available <= 0) {
        $error = "Error: No available copies for this book.";
    } else {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM loans WHERE member_id = ? AND book_id = ? AND return_date IS NULL");
        $stmt->execute([$member_id, $book_id]);
        if ($stmt->fetchColumn() > 0) {
            $error = "Error: This member already has this book issued.";
        } else {
            try {
                $pdo->beginTransaction();
                $stmt = $pdo->prepare("INSERT INTO loans (book_id, member_id, issue_date, due_date) VALUES (?, ?, ?, ?)");
                $stmt->execute([$book_id, $member_id, $issue_date, $due_date]);

                $stmt = $pdo->prepare("UPDATE books SET available_copies = available_copies - 1 WHERE id = ?");
                $stmt->execute([$book_id]);

                $pdo->commit();
                header("Location: index.php?msg=Book issued successfully.");
                exit();
            } catch (Exception $e) {
                $pdo->rollBack();
                $error = "System error: Could not issue book.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Issue Book</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card shadow mx-auto" style="max-width: 600px;">
            <div class="card-body">
                <h3 class="mb-4">Issue Book to Member</h3>
                
                <?php if($error): ?> <div class="alert alert-danger"><?= $error ?></div> <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Select Member</label>
                        <select name="member_id" class="form-select" required>
                            <option value="">-- Choose Member --</option>
                            <?php foreach($members as $m): ?>
                                <option value="<?= $m['id'] ?>">
                                    <?= htmlspecialchars($m['name']) ?> (<?= htmlspecialchars($m['member_number']) ?>) 
                                    - <?= ucfirst($m['status']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Select Book</label>
                        <select name="book_id" class="form-select" required>
                            <option value="">-- Choose Book --</option>
                            <?php foreach($books as $b): ?>
                                <option value="<?= $b['id'] ?>">
                                    <?= htmlspecialchars($b['title']) ?> (Available: <?= $b['available_copies'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Issue Book</button>
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>