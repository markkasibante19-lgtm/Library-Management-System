<?php
require_once '../includes/auth_check.php';
require_once '../config/database.php';

$id = $_GET['id'] ?? null;
if (!$id) { header("Location: index.php"); exit(); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']); $isbn = trim($_POST['isbn']);
    $author_id = $_POST['author_id']; $category_id = $_POST['category_id']; $total = $_POST['total_copies'];
    $stmt = $pdo->prepare("UPDATE books SET title=?, isbn=?, author_id=?, category_id=?, total_copies=? WHERE id=?");
    $stmt->execute([$title, $isbn, $author_id, $category_id, $total, $id]);
    header("Location: index.php?msg=Book updated successfully"); exit();
}
$stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?"); $stmt->execute([$id]); $book = $stmt->fetch();
$authors = $pdo->query("SELECT * FROM authors")->fetchAll(); $categories = $pdo->query("SELECT * FROM categories")->fetchAll();
require_once '../includes/header.php'; require_once '../includes/navbar.php';
?>
<div class="container mt-4">
    <div class="card shadow-sm mx-auto" style="max-width: 600px;">
        <div class="card-header bg-white"><h4>Edit Book</h4></div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3"><label class="form-label">Title</label><input type="text" name="title" class="form-control" value="<?= htmlspecialchars($book['title']) ?>" required></div>
                <div class="mb-3"><label class="form-label">ISBN</label><input type="text" name="isbn" class="form-control" value="<?= htmlspecialchars($book['isbn']) ?>" required></div>
                <div class="mb-3"><label class="form-label">Author</label>
                    <select name="author_id" class="form-select"><?php foreach($authors as $a) echo "<option value='{$a['id']}' ".($a['id']==$book['author_id']?'selected':'').">{$a['name']}</option>"; ?></select>
                </div>
                <div class="mb-3"><label class="form-label">Category</label>
                    <select name="category_id" class="form-select"><?php foreach($categories as $c) echo "<option value='{$c['id']}' ".($c['id']==$book['category_id']?'selected':'').">{$c['name']}</option>"; ?></select>
                </div>
                <div class="mb-3"><label class="form-label">Total Copies</label><input type="number" name="total_copies" class="form-control" value="<?= $book['total_copies'] ?>" required></div>
                <button type="submit" class="btn btn-primary w-100">Update Book</button>
            </form>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
