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
        <div class="col-md-4"><div class="card shadow-sm"><div class="card-body"><form metho…
