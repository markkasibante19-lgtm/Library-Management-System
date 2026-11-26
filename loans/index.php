<?php
require_once '../includes/auth_check.php'; require_once '../config/database.php';
$search = $_GET['search'] ?? '';
$filter = $_GET['filter'] ?? '';

$sql = "SELECT loans.*, members.name AS member_name, books.title AS book_title FROM loans JOIN members ON loans.member_id = members.id JOIN books ON loans.book_id = books.id WHERE 1=1";
$params = [];

if ($search) {
    $sql .= " AND (members.name LIKE ? OR books.title LIKE ?)";
    $params[] = "%$search%"; $params[] = "%$search%";
}

if ($filter === 'out') {
    $sql .= " AND loans.return_date IS NULL";
} elseif ($filter === 'returned') {
    $sql .= " AND loans.return_date IS NOT NULL";
} elseif ($filter === 'overdue') {
    $sql .= " AND loans.return_date IS NULL AND loans.due_date < CURDATE()";
}

$sql .= " ORDER BY loans.id DESC";
$stmt = $pdo->prepare($sql); 
$stmt->execute($params); 
$loans = $stmt->fetchAll();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once '../includes/header.php';
?>

<div class="hero">
    <div><h1>Loans & Returns</h1><p class="lede">Track which books are currently borrowed.</p></div>
    <a href="issue.php" class="btn"><svg class="icon"><use href="#i-plus"/></svg>Issue Book</a>
</div>

<?php if(isset($_GET['msg'])): ?><div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div><?php endif; ?>
<?php if(isset($_GET['error'])): ?><div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div><?php endif; ?>

<form method="GET" action="index.php" class="mb-3" style="display: flex; gap: 10px; flex-wrap: wrap;">
    <input type="text" name="search" class="form-control" placeholder="Search by member or book..." value="<?= htmlspecialchars($search) ?>" style="max-width: 300px;">
    
    <select name="filter" class="form-control" style="max-width: 200px;">
        <option value="">All Loans</option>
        <option value="out" <?= $filter === 'out' ? 'selected' : '' ?>>Currently Out</option>
        <option value="returned" <?= $filter === 'returned' ? 'selected' : '' ?>>Returned</option>
        <option value="overdue" <?= $filter === 'overdue' ? 'selected' : '' ?>>Overdue</option>
    </select>
    
    <button type="submit" class="btn" style="padding: 10px 20px;">Search & Filter</button>
</form>

<div class="table-wrap">
    <table class="table">
        <thead><tr><th>Member</th><th>Book</th><th>Due Date</th><th>Status</th><th>Action</th></tr></thead>
        <tbody>
            <?php foreach($loans as $l): ?>
            <tr>
                <td><b><?= htmlspecialchars($l['member_name']) ?></b></td>
                <td><?= htmlspecialchars($l['book_title']) ?></td>
                <td style="<?= (!$l['return_date'] && $l['due_date'] < date('Y-m-d')) ? 'color: var(--warn); font-weight: bold;' : '' ?>"><?= $l['due_date'] ?></td>
                <td>
                    <?php if($l['return_date']): ?><span class="chip is-ok">Returned</span>
                    <?php elseif($l['due_date'] < date('Y-m-d')): ?><span class="chip is-danger">Overdue</span>
                    <?php else: ?><span class="chip">Out</span><?php endif; ?>
                </td>
                <td>
                    <?php if(!$l['return_date']): ?>
                    <form method="POST" action="return.php" style="display:inline;" onsubmit="return confirm('Mark this book as returned?');">
                        <input type="hidden" name="id" value="<?= $l['id'] ?>">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <button type="submit" style="background:none; border:none; color:var(--green); font-weight:500; cursor:pointer; padding:0;">Return</button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require_once '../includes/footer.php'; ?>
