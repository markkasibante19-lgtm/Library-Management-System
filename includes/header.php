<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
// Determine current folder for active links
$current_page = basename($_SERVER['PHP_SELF']);
$current_dir = basename(dirname($_SERVER['PHP_SELF']));

require_once __DIR__ . '/../config/database.php';
$notif_stmt = $pdo->query("SELECT COUNT(*) FROM loans WHERE return_date IS NULL AND due_date < CURDATE()");
$overdue_count = $notif_stmt->fetchColumn();

$current_page = basename($_SERVER['PHP_SELF']);
$current_dir = basename(dirname($_SERVER['PHP_SELF']));
?>
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
    :root {
      --bg: #fbfaf4; --sidebar: #eef0e6; --card: #fdfcf7; --tint: #e6eadb; --line: #e4e4d8;
      --ink: #2b3a2f; --muted: #6f7a6f; --green: #586b4f; --on-green: #ffffff; --warn: #b5472f;
      --chip: #ece5d4; --chip-ok: #d8e5d3; --serif: "Lora", Georgia, serif; --sans: "Inter", system-ui, sans-serif;
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

    /* Form Elements */
    .form-control { width: 100%; padding: 10px 14px; border: 1px solid var(--line); border-radius: 10px; background: var(--card); font: inherit; color: var(--ink); }
    .form-control:focus { outline: 2px solid var(--green); border-color: transparent; }
    .form-label { display: block; margin-bottom: 6px; font-weight: 500; font-size: 13px; }
    .mb-3 { margin-bottom: 16px; }
    select.form-control { appearance: none; background-image: url("data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%236f7a6f%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E"); background-repeat: no-repeat; background-position: right 14px top 50%; background-size: 10px auto; }

    .app { display: grid; grid-template-columns: 240px 1fr; min-height: 100vh; }
    .main { min-width: 0; display: flex; flex-direction: column; }
    .page { flex: 1; max-width: 1180px; padding: 28px 32px 48px; width: 100%; }

    .sidebar { position: sticky; top: 0; height: 100vh; display: flex; flex-direction: column; padding: 24px 16px; background: var(--sidebar); }
    .logo { display: flex; align-items: center; gap: 10px; padding: 0 8px; }
    .logo-mark { width: 36px; height: 36px; display: grid; place-items: center; border-radius: 10px; background: var(--green); color: var(--on-green); }
    .logo-text { font: 500 24px var(--serif); }
    .sidebar-sub { padding: 6px 8px 18px; font-size: 10px; letter-spacing: .08em; text-transform: uppercase; color: var(--muted); }
    .nav { display: grid; gap: 4px; }
    .nav-link { display: flex; align-items: center; gap: 12px; padding: 10px 12px; border-radius: 10px; }
    .nav-link:hover, .nav-link.is-active { background: var(--tint); }
    .nav-link.is-active { font-weight: 500; }
    .nav-group { padding: 28px 12px 6px; font-size: 10px; letter-spacing: .08em; text-transform: uppercase; color: var(--muted); }
    .sidebar-note { display: grid; gap: 2px; margin-top: auto; padding: 16px; border-radius: 14px; background: var(--tint); font-size: 12px; color: var(--muted); }
    .sidebar-note strong { margin-top: 6px; font-weight: 500; color: var(--ink); }
    .sidebar-note small { margin-top: 10px; font-size: 11px; }
    .help { display: flex; align-items: center; gap: 10px; padding: 16px 12px 0; color: var(--muted); }

    .topbar { display: flex; align-items: center; gap: 16px; padding: 14px 32px; border-bottom: 1px solid var(--line); background: var(--bg); }
    .crumb { margin-right: auto; color: var(--muted); }
    .crumb span { margin: 0 6px; }
    .crumb b { font-weight: 400; color: var(--ink); text-transform: capitalize; }
    .user { line-height: 1.2; }
    .user small { display: block; font-size: 12px; color: var(--muted); }

    .hero { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; margin-bottom: 24px; }
    .hero h1 { font-size: 32px; line-height: 1.2; }
    .btn { display: inline-flex; align-items: center; gap: 8px; padding: 12px 20px; border: 0; border-radius: 10px; background: var(--green); color: var(--on-green); font-weight: 500; }
    .btn:hover { background: #4a5c42; }

    /* Tables & Chips */
    .table-wrap { overflow-x: auto; padding: 6px 20px; border: 1px solid var(--line); border-radius: 14px; background: var(--card); margin-bottom: 24px; }
    .table { width: 100%; border-collapse: collapse; }
    .table th { padding: 14px 12px 10px; text-align: left; white-space: nowrap; font: 500 10px var(--sans); letter-spacing: .06em; text-transform: uppercase; color: var(--muted); border-bottom: 1px solid var(--line); }
    .table td { padding: 14px 12px; border-top: 1px solid var(--line); vertical-align: middle; }
    .chip { display: inline-block; padding: 4px 10px; border-radius: 6px; background: var(--chip); font-size: 11px; font-weight: 500; }
    .chip.is-ok { background: var(--chip-ok); }
    .chip.is-danger { background: #f7dcd7; color: var(--warn); }

    /* Alerts */
    .alert { padding: 14px 18px; border-radius: 10px; margin-bottom: 20px; font-size: 13px; }
    .alert-success { background: var(--chip-ok); color: var(--green); border: 1px solid #c4d8ba; }
    .alert-danger { background: #fae6e6; color: var(--warn); border: 1px solid #f2c7c7; }

    .card { padding: 24px; border: 1px solid var(--line); border-radius: 14px; background: var(--card); }
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
      <a class="nav-link <?= $current_dir == 'dashboard' ? 'is-active' : '' ?>" href="../dashboard/index.php"><svg class="icon"><use href="#i-grid"/></svg>Overview</a>
      <a class="nav-link <?= $current_dir == 'books' ? 'is-active' : '' ?>" href="../books/index.php"><svg class="icon"><use href="#i-book"/></svg>Book catalog</a>
      <a class="nav-link <?= $current_dir == 'loans' ? 'is-active' : '' ?>" href="../loans/index.php"><svg class="icon"><use href="#i-swap"/></svg>Loans &amp; returns</a>
      <a class="nav-link <?= $current_dir == 'members' ? 'is-active' : '' ?>" href="../members/index.php"><svg class="icon"><use href="#i-users"/></svg>Members</a>
    </nav>

    <p class="nav-group">Manage</p>
    <nav class="nav" aria-label="Manage">
      <a class="nav-link <?= $current_dir == 'authors' ? 'is-active' : '' ?>" href="../authors/index.php"><svg class="icon"><use href="#i-gear"/></svg>Authors</a>
      <a class="nav-link <?= $current_dir == 'categories' ? 'is-active' : '' ?>" href="../categories/index.php"><svg class="icon"><use href="#i-gear"/></svg>Categories</a>
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
      <div class="crumb">Workspace <span>/</span> <b><?= ucfirst($current_dir) ?></b></div>



     <a href="../loans/index.php?filter=overdue" class="bell" aria-label="Notifications" title="<?= $overdue_count ?> Overdue Books">
    <svg class="icon"><use href="#i-bell"/></svg>
    <?php if($overdue_count > 0): ?>
        <!-- This makes the red dot appear only if there are overdue books -->
        <style>.bell::after { display: block !important; }</style>
    <?php else: ?>
        <style>.bell::after { display: none !important; }</style>
    <?php endif; ?>
</a>

      <div class="user">
        <strong><?= htmlspecialchars($_SESSION['username'] ?? 'Librarian') ?></strong>
        <small>Librarian</small>
      </div>
    </header>

    <main class="page">
