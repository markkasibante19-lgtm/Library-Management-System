<?php
require_once '../includes/auth_check.php'; 
require_once '../config/database.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    if (!empty($name)) {
        $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)"); 
        $stmt->execute([$name]); 
        header("Location: index.php?msg=Category added successfully."); 
        exit();
    } else {
        $error = "Category name cannot be empty.";
    }
}
require_once '../includes/header.php'; 
?>
<div class="hero">
    <div><h1>Add Category</h1><p class="lede">Add a new book category.</p></div>
    <a href="index.php" class="btn" style="background: var(--line); color: var(--ink);">Back to List</a>
</div>

<div class="card" style="max-width: 600px;">
    <?php if($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Category Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <button type="submit" class="btn">Save Category</button>
    </form>
</div>
<?php require_once '../includes/footer.php'; ?>
