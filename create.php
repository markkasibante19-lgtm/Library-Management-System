<?php
require_once '../includes/auth_check.php'; require_once '../config/database.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']); $isbn = trim($_POST['isbn']); $author_id = $_POST['author_id']; $category_id = $_POST['category_id']; $total = $_POST['total_copies'];
    if (empty($title) || empty($isbn)) { $error = "Required fields missing."; } 
    else {
        try {
            $stmt = $pdo->prepare("INSERT INTO books (title, isbn, author_id, category_id, total_copies, available_copies) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $isbn, $author_id, $category_id, $total, $total]);
            header("Location: index.php?msg=Book added."); exit();
        } catch (PDOException $e) { $error = "ISBN exists."; }
    }
}
$authors = $pdo->query("SELECT * FROM authors")->fetchAll(); $categories = $pdo->query("SELECT * FROM categories")->fetchAll();
require_once '../includes/header.php';
?>
<div class="hero">
    <div><h1>Add New Book</h1><p class="lede">Enter the details of the new book.</p></div>
    <a href="index.php" class="btn" style="background: var(--line); color: var(--ink);">Back to Catalog</a>
</div>

<div class="card" style="max-width: 600px;">
    <?php if($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
    <form method="POST">
        <div class="mb-3"><label class="form-label">Title</label><input type="text" name="title" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">ISBN</label><input type="text" name="isbn" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Author</label><select name="author_id" class="form-control"><?php foreach($authors as $a) echo "<option value='{$a['id']}'>{$a['name']}</option>"; ?></select></div>
        <div class="mb-3"><label class="form-label">Category</label><select name="category_id" class="form-control"><?php foreach($categories as $c) echo "<option value='{$c['id']}'>{$c['name']}</option>"; ?></select></div>
        <div class="mb-3"><label class="form-label">Total Copies</label><input type="number" name="total_copies" class="form-control" value="1" min="1"></div>
        <button type="submit" class="btn" style="width: 100%;">Save Book</button>
    </form>
</div>
<?php require_once '../includes/footer.php'; ?>