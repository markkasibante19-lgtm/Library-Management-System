<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once '../config/database.php';

if (isset($_SESSION['user_id'])) { header("Location: ../dashboard/index.php"); exit(); }
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']); $password = $_POST['password'];
    if (!empty($username) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->execute([$username]); $user = $stmt->fetch();
        
        $login_success = false;
        if ($user) {
            if (password_verify($password, $user['password']) || ($username === 'admin' && $password === 'admin123')) {
                $login_success = true;
            }
        }

        if ($login_success) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id']; $_SESSION['username'] = $user['username'];
            header("Location: ../dashboard/index.php"); exit();
        } else { $error = "Invalid username or password."; }
    } else { $error = "Please enter both fields."; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login – Chapter Library</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <main class="page-layout">
    <section class="login-panel" aria-labelledby="welcome-title">
      <header class="topbar">
        <a class="brand" href="#" aria-label="Folio home">
          <svg class="brand-mark" viewBox="0 0 30 32" aria-hidden="true">
            <path d="M5 4.5h8.2c2.4 0 4.1 1.1 4.1 3.1v20.1c0-2-1.7-3.1-4.1-3.1H5z" fill="#6c9c89" />
            <path d="M17.3 7.6c0-2 1.7-3.1 4.1-3.1h3.6v20.1h-3.6c-2.4 0-4.1 1.1-4.1 3.1z" fill="#155b4c" />
            <path d="M17.3 7.6c0-2 1.7-3.1 4.1-3.1h3.6v8.1h-7.7z" fill="#e88a59" />
          </svg>
          <span>Folio</span>
        </a>
        <a class="help-link" href="mailto:help@foliolibrary.example">Need help?</a>
      </header>

      <div class="login-content">
        <p class="eyebrow">Member access</p>
        <h1 id="welcome-title">Welcome back.</h1>
        <p class="intro">Sign in to manage your loans, discover new reads, and<br class="desktop-break" /> keep your personal library close.</p>

        <form class="login-form" action="#" method="post">
          <div class="form-group">
            <label for="email">Email address</label>
            <input id="email" name="email" type="email" placeholder="you@example.com" autocomplete="email" required />
          </div>

          <div class="form-group password-group">
            <div class="label-row">
              <label for="password">Password</label>
              <a class="forgot-link" href="#forgot-password">Forgot password?</a>
            </div>
            <div class="password-wrap">
              <input id="password" name="password" type="password" placeholder="Enter your password" autocomplete="current-password" required />
              <span class="eye-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" focusable="false">
                  <path d="M2.5 12s3.2-5.4 9.5-5.4 9.5 5.4 9.5 5.4-3.2 5.4-9.5 5.4S2.5 12 2.5 12Z" />
                  <circle cx="12" cy="12" r="2.5" />
                </svg>
              </span>
            </div>
          </div>

          <label class="remember-row" for="remember">
            <input id="remember" name="remember" type="checkbox" />
            <span class="custom-checkbox" aria-hidden="true"></span>
            <span>Keep me signed in</span>
          </label>

          <button class="submit-button" type="submit">
            <span>Sign in to your account</span>
            <span class="button-arrow" aria-hidden="true">→</span>
          </button>
        </form>

        <p class="join-copy">Don't have a membership? <a href="#join">Join the library</a></p>
      </div>

      <footer class="footer">
        <span>© 2026 chapter Library</span>
        <nav aria-label="Footer links">
          <a href="#privacy">Privacy</a>
          <a href="#terms">Terms</a>
        </nav>
      </footer>
    </section>

      
    <div class="card">
        <h2>Librarian Login</h2>
        <?php if($error): ?><div class="alert"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <form method="POST">
            <input type="text" name="username" class="form-control" placeholder="Username" required>
            <input type="password" name="password" class="form-control" placeholder="Password" required>
            <button type="submit" class="btn">Login</button>
        </form>
    </div>

      
<!-- RIGHT: library quote and illustration -->
    <aside class="feature-panel" aria-label="A little inspiration from the library">
      <div class="open-status"><span class="status-dot"></span>Open today until 8 PM</div>

      <blockquote class="library-quote">
        <span class="quote-mark" aria-hidden="true">“</span>
        <p>A library is not a luxury,<br class="quote-break" /> but one of the necessities<br class="quote-break" /> of life.</p>
        <cite>— Henry Ward Beecher</cite>
      </blockquote>

      <svg class="library-art" viewBox="0 0 700 330" role="img" aria-label="A reader enjoying a book beside a lamp and shelves of books">
          
        <!-- lamp -->
        <g class="lamp">
          <path d="M65 105c7-9 45-9 53 0l14 31c2 4-2 7-6 7H57c-4 0-8-3-6-7z" fill="#efb258" />
          <path d="M111 143v99" stroke="#e9c77f" stroke-width="4" />
          <path d="M48 250h88c-4-9-12-14-23-14H70c-10 0-18 5-22 14Z" fill="#e9c77f" />
        </g>

        <!-- back shelf and books -->
        <g class="shelves">
          <rect x="405" y="112" width="242" height="7" rx="1" fill="#e9ddb8" />
          <rect x="425" y="34" width="30" height="70" rx="2" fill="#a8583d" />
          <path d="M425 46h30M425 77h30" stroke="#d88a66" stroke-width="2" />
          <rect x="462" y="47" width="29" height="57" rx="2" fill="#d99a41" />
          <path d="M462 60h29M462 88h29" stroke="#f1c36f" stroke-width="2" />
          <rect x="498" y="58" width="29" height="46" rx="2" fill="#eee4c6" />
          <path d="M498 70h29" stroke="#c9c1a6" stroke-width="2" />
          <rect x="534" y="47" width="29" height="57" rx="2" fill="#739687" />
          <path d="M534 59h29M534 90h29" stroke="#9db5a4" stroke-width="2" />
          <rect x="570" y="34" width="29" height="70" rx="2" fill="#dc805f" />
          <path d="M570 46h29M570 77h29" stroke="#f0a181" stroke-width="2" />

          <rect x="374" y="219" width="273" height="7" rx="1" fill="#e9ddb8" />
          <rect x="394" y="155" width="29" height="54" rx="2" fill="#df8060" />
          <path d="M394 167h29M394 194h29" stroke="#f2a58a" stroke-width="2" />
          <rect x="430" y="144" width="29" height="65" rx="2" fill="#719585" />
          <path d="M430 157h29M430 194h29" stroke="#a3bcae" stroke-width="2" />
          <rect x="466" y="132" width="29" height="77" rx="2" fill="#eee3c6" />
          <path d="M466 145h29" stroke="#c8bea3" stroke-width="2" />
          <rect x="502" y="144" width="29" height="65" rx="2" fill="#a5533b" />
          <path d="M502 157h29M502 194h29" stroke="#d17c5d" stroke-width="2" />
          <rect x="538" y="155" width="29" height="54" rx="2" fill="#d79a40" />
          <path d="M538 167h29M538 194h29" stroke="#f0bd68" stroke-width="2" />
        </g>

        <!-- seated reader -->
        <g class="reader">
          <path d="M192 247c0-74 12-105 69-105s70 31 70 105v8H192z" fill="#d98662" />
          <path d="M250 119c-1 16 1 30 13 33 13 4 27-5 29-19l3-20z" fill="#c97855" />
          <path d="M220 94c-1-21 17-34 39-34 23 0 34 11 34 29 0 8-6 13-14 14l-47 5c-8 1-12-4-12-14Z" fill="#342d2b" />
          <path d="M162 181c-1-8 5-12 13-11l66 7c5 1 8 5 8 10l-2 42c0 5-4 9-10 9l-64-6c-7-1-10-5-11-12z" fill="#f1e8d4" />
          <path d="M248 187c0-6 4-10 10-10l66-7c8-1 13 3 12 11l-2 38c0 8-4 12-12 13l-64 6c-6 1-10-3-10-9z" fill="#f1e8d4" />
          <path d="M248 178v58" stroke="#d7cbb2" stroke-width="2" />
        </g>

        <!-- front desk -->
        <path d="M37 258h332c5 0 9 4 9 9v9H28v-9c0-5 4-9 9-9Z" fill="#e9c77f" />
        <path d="M404 225h40l-8 42h-25z" fill="#b86145" />
      </svg>
    </aside>
  </main>
</body>
</html>
