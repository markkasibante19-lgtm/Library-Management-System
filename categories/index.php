<?php
require_once '../includes/auth_check.php'; 
require_once '../config/database.php';
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
require_once '../includes/header.php';
?>
<div class="hero">
    <div><h1>Categories</h1><p class="lede">Manage book categories.</p></div>
    <a href="create.php" class="btn"><svg class="icon"><use href="#i-plus"/></svg>Add Category</a>
</div>

<?php if(isset($_GET['msg'])): ?><div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div><?php endif; ?>

<div class="table-wrap">
    <table class="table">
        <thead><tr><th>ID</th><th>Name</th></tr></thead>
        <tbody>
            <?php foreach($categories as $c): ?>
            <tr>
                <td><?= $c['id'] ?></td>
                <td><b><?= htmlspecialchars($c['name']) ?></b></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require_once '../includes/footer.php'; ?>
