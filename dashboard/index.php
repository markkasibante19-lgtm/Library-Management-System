<?php
require_once '../includes/auth_check.php';
require_once '../config/database.php';

// --- FETCH REAL DATA FROM DATABASE ---
$total_books = $pdo->query("SELECT SUM(total_copies) FROM books")->fetchColumn() ?? 0;
$books_on_loan = $pdo->query("SELECT COUNT(*) FROM loans WHERE return_date IS NULL")->fetchColumn() ?? 0;
$overdue_books = $pdo->query("SELECT COUNT(*) FROM loans WHERE return_date IS NULL AND due_date < CURDATE()")->fetchColumn() ?? 0;
$active_members = $pdo->query("SELECT COUNT(*) FROM members WHERE status = 'active'")->fetchColumn() ?? 0;

$recent_books = $pdo->query("SELECT * FROM books ORDER BY id DESC LIMIT 4")->fetchAll();
$overdue_list = $pdo->query("SELECT loans.*, members.name as member_name, books.title as book_title FROM loans JOIN members ON loans.member_id = members.id JOIN books ON loans.book_id = books.id WHERE loans.return_date IS NULL AND loans.due_date < CURDATE() LIMIT 3")->fetchAll();
$recent_loans = $pdo->query("SELECT loans.*, members.name as member_name, books.title as book_title FROM loans JOIN members ON loans.member_id = members.id JOIN books ON loans.book_id = books.id ORDER BY loans.id DESC LIMIT 3")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Chapter – Library workspace</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Lora:wght@400;500&display=swap" rel="stylesheet">
  
  <style>
    /* ---------- YOUR EXACT CSS ---------- */
    :root {
      --bg: #fbfaf4;
      --sidebar: #eef0e6;
      --card: #fdfcf7;
      --tint: #e6eadb;
      --line: #e4e4d8;
      --ink: #2b3a2f;
      --muted: #6f7a6f;
      --green: #586b4f;
      --on-green: #ffffff;
      --warn: #b5472f;
      --chip: #ece5d4;
      --chip-ok: #d8e5d3;
      --serif: "Lora", Georgia, serif;
      --sans: "Inter", system-ui, sans-serif;
    }

    *, *::before, *::after { box-sizing: border-box; }
    body { margin: 0; background: var(--bg); color: var(--ink); font: 14px/1.5 var(--sans); }
    h1, h2, h3, p { margin: 0; }
    h1, h2 { font-family: var(--serif); font-weight: 500; }
    a { color: inherit; text-decoration: none; }
    button { font: inherit; cursor: pointer; }
    ul { list-style: none; margin: 0; padding: 0; }
    :focus-visible { outline: 2px solid var(--green); outline-offset: 2px; }
    .icon { width: 18px; height: 18px; flex: none; fill: none; stroke: currentColor; stroke-width: 1.6; stroke-linecap: round; stroke-linejoin: round; }

    .app { display: grid; grid-template-columns: 240px 1fr; min-height: 100vh; }
    .main { min-width: 0; }
    .page { max-width: 1180px; padding: 28px 32px 48px; }

    .sidebar { position: sticky; top: 0; height: 100vh; display: flex; flex-direction: column; padding: 24px 16px; background: var(--sidebar); }
    .logo { display: flex; align-items: center; gap: 10px; padding: 0 8px; }
    .logo-mark { width: 36px; height: 36px; display: grid; place-items: center; border-radius: 10px; background: var(--green); color: var(--on-green); }
    .logo-text { font: 500 24px var(--serif); }
    .sidebar-sub { padding: 6px 8px 18px; font-size: 10px; letter-spacing: .08em; text-transform: uppercase; color: var(--muted); }
    .nav { display: grid; gap: 4px; }
    .nav-link { display: flex; align-items: center; gap: 12px; padding: 10px 12px; border-radius: 10px; }
    .nav-link:hover, .nav-link.is-active { background: var(--tint); }
    .nav-link.is-active { font-weight: 500; }
    .badge { margin-left: auto; padding: 0 6px; border-radius: 6px; background: var(--card); font-size: 11px; }
    .nav-group { padding: 28px 12px 6px; font-size: 10px; letter-spacing: .08em; text-transform: uppercase; color: var(--muted); }
    .sidebar-note { display: grid; gap: 2px; margin-top: auto; padding: 16px; border-radius: 14px; background: var(--tint); font-size: 12px; color: var(--muted); }
    .sidebar-note strong { margin-top: 6px; font-weight: 500; color: var(--ink); }
    .sidebar-note small { margin-top: 10px; font-size: 11px; }
    .help { display: flex; align-items: center; gap: 10px; padding: 16px 12px 0; color: var(--muted); }

    .topbar { display: flex; align-items: center; gap: 16px; padding: 14px 32px; border-bottom: 1px solid var(--line); }
    .crumb { margin-right: auto; color: var(--muted); }
    .crumb span { margin: 0 6px; }
    .crumb b { font-weight: 400; color: var(--ink); }
    .search { display: flex; align-items: center; gap: 8px; width: min(340px, 40%); padding: 8px 12px; border: 1px solid var(--line); border-radius: 12px; background: var(--card); color: var(--muted); }
    .search input { flex: 1; min-width: 0; border: 0; outline: 0; background: none; color: var(--ink); font: inherit; }
    .search kbd { font: 11px var(--sans); }
    .bell { position: relative; border: 0; background: none; color: var(--ink); padding: 4px; }
    .bell::after { content: ""; position: absolute; top: 3px; right: 4px; width: 6px; height: 6px; border-radius: 50%; background: var(--warn); }
    .user { line-height: 1.2; }
    .user small { display: block; font-size: 12px; color: var(--muted); }

    .hero { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; margin-bottom: 24px; }
    .eyebrow { margin-bottom: 6px; font-size: 11px; color: var(--muted); }
    .hero h1 { font-size: 32px; line-height: 1.2; }
    .lede { margin-top: 4px; color: var(--muted); }
    .btn { display: inline-flex; align-items: center; gap: 8px; padding: 12px 20px; border: 0; border-radius: 10px; background: var(--green); color: var(--on-green); font-weight: 500; }

    .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 32px; }
    .card { padding: 18px; border: 1px solid var(--line); border-radius: 14px; background: var(--card); }
    .stat p { color: var(--muted); }
    .stat-num { display: flex; justify-content: space-between; align-items: center; margin: 2px 0 8px; }
    .stat-num strong { font: 400 30px var(--serif); }
    .stat-icon { width: 36px; height: 36px; display: grid; place-items: center; border-radius: 10px; background: var(--tint); color: var(--green); }
    .stat-icon.is-warm { background: var(--chip); }
    .stat small { font-size: 11px; color: var(--muted); }

    .split { display: grid; grid-template-columns: 1fr 300px; gap: 24px; margin-bottom: 32px; }
    .section-head { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 12px; }
    .section-head h2 { font-size: 20px; }
    .section-head p { font-size: 12px; color: var(--muted); }
    .link { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; color: var(--ink); }
    .link .icon { width: 14px; height: 14px; }

    .shelf { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 16px; }
    .cover { aspect-ratio: 3 / 4; display: flex; flex-direction: column; justify-content: space-between; margin-bottom: 10px; padding: 14px; border-radius: 6px; overflow: hidden; font: 400 17px/1.15 var(--serif); color: #3b3a30; }
    .cover-author { font: 500 8px var(--sans); letter-spacing: .06em; text-transform: uppercase; }
    .cover-1 { background: #dedccb; }
    .cover-2 { background: #27403f; color: #eef0e6; }
    .cover-3 { background: #b3bd9f; }
    .cover-4 { background: #d9a07a; }
    .book h3 { font: 500 12px var(--sans); }
    .book p { margin: 2px 0 8px; font-size: 11px; color: var(--muted); }
    .avail { display: flex; align-items: center; gap: 6px; font-size: 11px; color: var(--muted); }
    .avail::before { content: ""; width: 5px; height: 5px; border-radius: 50%; background: var(--green); }
    .avail.is-out { color: var(--warn); }
    .avail.is-out::before { background: var(--warn); }

    .panel { padding: 20px; border-radius: 16px; background: var(--tint); align-self: start; }
    .panel h2 { font-size: 20px; }
    .panel-sub { margin: 2px 0 14px; font-size: 12px; color: var(--muted); }
    .returns li { display: flex; align-items: center; gap: 12px; padding: 10px 0; }
    .returns b, .title-cell b { display: block; font-size: 13px; font-weight: 500; }
    .returns small, .title-cell small { font-size: 11px; color: var(--muted); }
    .today { margin-left: auto; font-size: 11px; color: var(--warn); }
    .panel-foot { display: flex; justify-content: space-between; align-items: center; margin-top: 16px; padding-top: 12px; border-top: 1px solid var(--line); font-size: 12px; }

    .mini { width: 30px; height: 40px; flex: none; border-radius: 3px; }
    .m-1 { background: #cdb98f; } .m-2 { background: #9fb3a9; } .m-3 { background: #b9c4be; }
    .m-4 { background: #d8cfae; } .m-5 { background: #a9b79d; } .m-6 { background: #c9a9a0; }

    .table-wrap { overflow-x: auto; padding: 6px 20px; border: 1px solid var(--line); border-radius: 14px; background: var(--card); }
    .table { width: 100%; border-collapse: collapse; }
    .table th { padding: 14px 12px 10px; text-align: left; white-space: nowrap; font: 500 10px var(--sans); letter-spacing: .06em; text-transform: uppercase; color: var(--muted); }
    .table td { padding: 10px 12px; border-top: 1px solid var(--line); white-space: nowrap; }
    .title-cell { display: flex; align-items: center; gap: 12px; }
    .chip { display: inline-block; padding: 2px 10px; border-radius: 6px; background: var(--chip); font-size: 11px; }
    .chip.is-ok { background: var(--chip-ok); }

    @media (max-width: 900px) {
      .app { grid-template-columns: 1fr; }
      .sidebar { position: static; height: auto; }
      .sidebar-note, .help, .nav-group { display: none; }
      .nav { display: flex; flex-wrap: wrap; }
      .split { grid-template-columns: 1fr; }
      .topbar, .page { padding-left: 16px; padding-right: 16px; }
      .search { display: none; }
      .hero { flex-direction: column; }
    }
  </style>
</head>
<body>

<svg width="0" height="0" style="position:absolute" aria-hidden="true">
  <symbol id="i-book" viewBox="0 0 24 24"><path d="M4 5a2 2 0 0 1 2-2h13v16H6a2 2 0 0 0-2 2z"/><path d="M8 7h7"/></symbol>
  <symbol id="i-grid" viewBox="0 0 24 24"><rect x="4" y="4" width="6" height="6" rx="1"/><rect x="14" y="4" width="6" height="6" rx="1"/><rect x="4" y="14" width="6" height="6" rx="1"/><rect x="14" y="14" width="6" height="6" rx="1"/></symbol>
  <symbol id="i-swap" viewBox="0 0 24 24"><path d="M4 8h14l-3-3M20 16H6l3 3"/></symbol>
  <symbol id="i-users" viewBox="0 0 24 24"><circle cx="9" cy="8" r="3"/><path d="M3 20c0-3 3-5 6-5s6 2 6 5M16 5a3 3 0 0 1 0 6M21 20c0-2-1.5-3.5-3-4"/></symbol>
  <symbol id="i-chart" viewBox="0 0 24 24"><path d="M4 20V4M4 20h16M8 16v-5M12 16V8M16 16v-3"/></symbol>
  <symbol id="i-gear" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M12 3v3M12 18v3M3 12h3M18 12h3M5.6 5.600l2.1 2.100M16.3 16.300l2.1 2.100M5.6 18.400l2.1-2.100M16.3 7.700l2.1-2.1"/></symbol>
  <symbol id="i-clock" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></symbol>
  <symbol id="i-plus" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></symbol>
  <symbol id="i-arrow" viewBox="0 0 24 24"><path d="M5 12h14m-5-5 5 5-5 5"/></symbol>
  <symbol id="i-search" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></symbol>
  <symbol id="i-bell" viewBox="0 0 24 24"><path d="M6 17V11a6 6 0 0 1 12 0v6l2 2H4zM10 21h4"/></symbol>
  <symbol id="i-help" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M9.5 9.500a2.5 2.5 0 1 1 3.5 2.300c-.7.4-1 .900-1 1.700M12 17h.01"/></symbol>
</svg>

<div class="app">

  <aside class="sidebar">
    <a class="logo" href="../dashboard/index.php">
      <span class="logo-mark"><svg class="icon"><use href="#i-book"/></svg></span>
      <span class="logo-text">chapter</span>
    </a>
    <p class="sidebar-sub">Library workspace</p>

    <nav class="nav" aria-label="Main">
      <a class="nav-link is-active" href="../dashboard/index.php" aria-current="page"><svg class="icon"><use href="#i-grid"/></svg>Overview</a>
      <a class="nav-link" href="../books/index.php"><svg class="icon"><use href="#i-book"/></svg>Book catalog</a>
      <a class="nav-link" href="../loans/index.php"><svg class="icon"><use href="#i-swap"/></svg>Loans &amp; returns</a>
      <a class="nav-link" href="../members/index.php"><svg class="icon"><use href="#i-users"/></svg>Members</a>
    </nav>

    <p class="nav-group">Manage</p>
    <nav class="nav" aria-label="Manage">
      <a class="nav-link" href="../authors/index.php"><svg class="icon"><use href="#i-gear"/></svg>Authors</a>
      <a class="nav-link" href="../categories/index.php"><svg class="icon"><use href="#i-gear"/></svg>Categories</a>
    </nav>

    <div class="sidebar-note">
      <svg class="icon"><use href="#i-book"/></svg>
      <strong>A little more organized.</strong>
      <span>A lot more discovery.</span>
      <small>Your library, thoughtfully connected.</small>
    </div>

    <a class="help" href="../auth/logout.php" style="color: var(--warn);"><svg class="icon"><use href="#i-arrow"/></svg>Logout</a>
  </aside>

  <div class="main">

    <header class="topbar">
      <div class="crumb">Workspace <span>/</span> <b>Overview</b></div>
      <label class="search">
        <svg class="icon"><use href="#i-search"/></svg>
        <input type="search" placeholder="Search books, members and more…" aria-label="Search">
        <kbd>⌘ K</kbd>
      </label>
      <button class="bell" aria-label="Notifications"><svg class="icon"><use href="#i-bell"/></svg></button>
      <div class="user">
        <strong><?= htmlspecialchars($_SESSION['username'] ?? 'Librarian') ?></strong>
        <small>Librarian</small>
      </div>
    </header>

    <main class="page">

      <section class="hero">
        <div>
          <p class="eyebrow">Your library, at a glance</p>
          <h1>A good day starts with a good book.</h1>
          <p class="lede">Welcome back. Here’s what’s happening in your library today.</p>
        </div>
        <a href="../books/create.php" class="btn" style="text-decoration: none;"><svg class="icon"><use href="#i-plus"/></svg>Add a book</a>
      </section>

      <section class="stats" aria-label="Library statistics">
        <article class="card stat">
          <p>Total books</p>
          <div class="stat-num"><strong><?= $total_books ?></strong><span class="stat-icon"><svg class="icon"><use href="#i-book"/></svg></span></div>
          <small>In catalog</small>
        </article>
        <article class="card stat">
          <p>Books on loan</p>
          <div class="stat-num"><strong><?= $books_on_loan ?></strong><span class="stat-icon"><svg class="icon"><use href="#i-swap"/></svg></span></div>
          <small>Currently out</small>
        </article>
        <article class="card stat">
          <p>Overdue</p>
          <div class="stat-num"><strong><?= $overdue_books ?></strong><span class="stat-icon is-warm"><svg class="icon"><use href="#i-clock"/></svg></span></div>
          <small>Needs attention</small>
        </article>
        <article class="card stat">
          <p>Active members</p>
          <div class="stat-num"><strong><?= $active_members ?></strong><span class="stat-icon"><svg class="icon"><use href="#i-users"/></svg></span></div>
          <small>Good standing</small>
        </article>
      </section>

      <div class="split">
        <section>
          <div class="section-head">
            <div><h2>On the shelves</h2><p>Recently added</p></div>
            <a class="link" href="../books/index.php">View catalog <svg class="icon"><use href="#i-arrow"/></svg></a>
          </div>

          <div class="shelf">
            <?php 
            $covers = ['cover-1', 'cover-2', 'cover-3', 'cover-4'];
            foreach($recent_books as $index => $book): 
                $coverClass = $covers[$index % 4];
            ?>
            <article class="book">
              <div class="cover <?= $coverClass ?>">
                  <span class="cover-title"><?= htmlspecialchars($book['title']) ?></span>
              </div>
              <h3><?= htmlspecialchars($book['title']) ?></h3>
              <p>ISBN: <?= htmlspecialchars($book['isbn']) ?></p>
              <span class="avail <?= $book['available_copies'] == 0 ? 'is-out' : '' ?>">
                  <?= $book['available_copies'] > 0 ? 'Available' : 'All copies on loan' ?>
              </span>
            </article>
            <?php endforeach; ?>
          </div>
        </section>

        <aside class="panel">
          <h2>Overdue books</h2>
          <p class="panel-sub">Books that are past their due date.</p>
          <ul class="returns">
            <?php if(empty($overdue_list)): ?>
                <li><div><b>No overdue books!</b><small>Great job</small></div></li>
            <?php else: ?>
                <?php foreach($overdue_list as $loan): ?>
                <li>
                    <span class="mini m-1"></span>
                    <div>
                        <b><?= htmlspecialchars($loan['book_title']) ?></b>
                        <small><?= htmlspecialchars($loan['member_name']) ?></small>
                    </div>
                    <span class="today">Overdue</span>
                </li>
                <?php endforeach; ?>
            <?php endif; ?>
          </ul>
          <a class="panel-foot" href="../loans/index.php">View all loans <svg class="icon"><use href="#i-arrow"/></svg></a>
        </aside>
      </div>

      <section class="borrowing">
        <div class="section-head">
          <h2>Recent borrowing</h2>
          <a class="link" href="../loans/index.php">View all activity <svg class="icon"><use href="#i-arrow"/></svg></a>
        </div>
        <div class="table-wrap">
          <table class="table">
            <thead>
              <tr><th>Book title</th><th>Member</th><th>Borrowed</th><th>Due date</th><th>Status</th></tr>
            </thead>
            <tbody>
              <?php foreach($recent_loans as $loan): ?>
              <tr>
                <td><div class="title-cell"><span class="mini m-4"></span><div><b><?= htmlspecialchars($loan['book_title']) ?></b></div></div></td>
                <td><?= htmlspecialchars($loan['member_name']) ?></td>
                <td><?= $loan['issue_date'] ?></td>
                <td><?= $loan['due_date'] ?></td>
                <td>
                    <?php if($loan['return_date']): ?>
                        <span class="chip is-ok">Returned</span>
                    <?php else: ?>
                        <span class="chip">On loan</span>
                    <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </section>

    </main>
  </div>
</div>

</body>
</html>
