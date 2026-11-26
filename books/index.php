<?php
require_once '../includes/auth_check.php'; require_once '../config/database.php';
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$author = $_GET['author'] ?? '';

$sql = "SELECT books.*, authors.name AS author_name, categories.name AS category_name FROM books JOIN authors ON books.author_id = authors.id JOIN categories ON books.category_id = categories.id WHERE 1=1";
$params = [];

if ($search) { 
    $sql .= " AND (books.title LIKE ? OR books.isbn LIKE ?)"; 
    $params[] = "%$search%"; $params[] = "%$search%"; 
}
if ($category) {
    $sql .= " AND books.category_id = ?";
    $params[] = $category;
}
if ($author) {
    $sql .= " AND books.author_id = ?";
    $params[] = $author;
}

$stmt = $pdo->prepare($sql); 
$stmt->execute($params); 
$books = $stmt->fetchAll();

// Fetch dropdown data
$all_categories = $pdo->query("SELECT * FROM categories")->fetchAll();
$all_authors = $pdo->query("SELECT * FROM authors")->fetchAll();

// Add this before require_once '../includes/header.php';
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once '../includes/header.php';
?>

<form method="GET" action="index.php" class="mb-3" style="display: flex; gap: 10px; flex-wrap: wrap;">
    <input type="text" name="search" class="form-control" placeholder="Search by title or ISBN..." value="<?= htmlspecialchars($search) ?>" style="max-width: 250px;">
    
    <select name="category" class="form-control" style="max-width: 180px;">
        <option value="">All Categories</option>
        <?php foreach($all_categories as $c): ?>
            <option value="<?= $c['id'] ?>" <?= $category == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
        <?php endforeach; ?>
    </select>

    <select name="author" class="form-control" style="max-width: 180px;">
        <option value="">All Authors</option>
        <?php foreach($all_authors as $a): ?>
            <option value="<?= $a['id'] ?>" <?= $author == $a['id'] ? 'selected' : '' ?>><?= htmlspecialchars($a['name']) ?></option>
        <?php endforeach; ?>
    </select>
    
    <button type="submit" class="btn" style="padding: 10px 20px;">Search & Filter</button>
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
 
                    <form method="POST" action="delete.php" style="display:inline;" onsubmit="return confirm('Delete this book?');">
        <input type="hidden" name="id" value="<?= $b['id'] ?>">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <button type="submit" style="background:none; border:none; color:var(--warn); font-weight:500; cursor:pointer; padding:0;">Delete</button>
    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require_once '../includes/footer.php'; ?>
