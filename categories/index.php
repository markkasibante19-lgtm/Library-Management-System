<?php
require_once '../includes/auth_check.php'; require_once '../config/database.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    if ($name) { $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)"); $stmt->execute([$name]); header("Location: index.php?msg=Added."); exit(); }
}
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
require_once '../includes/header.php'; require_once '../includes/navbar.php';
?>
<div class="container mt-4"><h2>Categories</h2>
    <?php if(isset($_GET['msg'])): ?><div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div><?php endif; ?>
    <div class="row">
        <div class="col-md-4"><div class="card shadow-sm"><div class="card-body"><form method="POST"><div class="mb-3"><input type="text" name="name" class="form-control" placeholder="Name" required></div><button type="submit" class="btn btn-primary w-100">Add</button></form></div></div></div>
        <div class="col-md-8"><div class="card shadow-sm"><table class="table table-hover mb-0"><thead class="table-dark"><tr><th>ID</th><th>Name</th></tr></thead><tbody>
        <?php foreach($categories as $c): ?><tr><td><?= $c['id'] ?></td><td><?= htmlspecialchars($c['name']) ?></td></tr><?php endforeach; ?>
        </tbody></table></div></div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
