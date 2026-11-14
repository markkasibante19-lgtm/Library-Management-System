
       <?php
require_once '../includes/auth_check.php'; require_once '../config/database.php';
$search = $_GET['search'] ?? '';
$sql = "SELECT books.*, authors.name AS author_name, categories.name AS category_name FROM books JOIN authors ON books.author_id = authors.id JOIN categories ON books.category_id = categories.id WHERE 1=1";
$params = [];
if ($search) { $sql .= " AND (books.title LIKE ? OR books.isbn LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
$stmt = $pdo->prepare($sql); $stmt->execute($params); $books = $stmt->fetchAll();
require_once '../includes/header.php';
?>
<div class="hero">
    <div>
        <h1>Book Catalog</h1>
        <p class="lede">Manage all books in the library.</p>
    </div>
    <a href="create.php" class="btn"><svg class="icon"><use href="#i-plus"/></svg>Add Book</a>
</div>

<?php if(isset($_GET['msg'])): ?><div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div><?php endif; ?>
<?php if(isset($_GET['error'])): ?><div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div><?php endif; ?>

<form method="GET" class="mb-3" style="display: flex; gap: 10px;">
    <input type="text" name="search" class="form-control" placeholder="Search by title or ISBN..." value="<?= htmlspecialchars($search) ?>" style="max-width: 400px;">
    <button type="submit" class="btn" style="padding: 10px 20px;">Search</button>
</form>

<div class="table-wrap">
    <table class="table">
        <thead><tr><th>Title</th><th>Author</th><th>Category</th><th>Available</th><th>Actions</th></tr></thead>
        <tbody>
            <?php foreach($books as $b): ?>
            <tr>
                <td><b><?= htmlspecialchars($b['title']) ?></b></td>
                <td><?= htmlspecialchars($b['author_name']) ?></td>
                <td><?= htmlspecialchars($b['category_name']) ?></td>
                <td><span class="chip <?= $b['available_copies'] > 0 ? 'is-ok' : 'is-danger' ?>"><?= $b['available_copies'] ?> / <?= $b['total_copies'] ?></span></td>
                <td>
                    <a href="edit.php?id=<?= $b['id'] ?>" style="color: var(--green); font-weight: 500; margin-right: 12px;">Edit</a>
                    <a href="delete.php?id=<?= $b['id'] ?>" style="color: var(--warn); font-weight: 500;" onclick="return confirm('Delete this book?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require_once '../includes/footer.php'; ?>
