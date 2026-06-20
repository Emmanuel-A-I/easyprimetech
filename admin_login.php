<?php
  session_start();
  $error = $_SESSION['error'] ?? '';
  unset($_SESSION['error']);
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login — EasyPrimeTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  </head>
  <link href="styles/portal_login.css" rel="stylesheet">
  </head>
  <body>

  <div class="login-card shadow">
    <div class="brand-top">
      <img src="logo.png" alt="EasyPrimeTech" />
    </div>
    <div class="card-body">
      <h4 class="mb-1">Admin Portal</h4>
      <p class="text-muted-small mb-3">Sign in using your admin account to manage the system.</p>

      <?php if(!empty($error)): ?>
        <div class="error-box"><strong>Error:</strong> <?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <form action="scripts/admin.php" method="post" autocomplete="on">
        <div class="mb-3">
          <label for="email" class="form-label">Email address</label>
          <input type="email" class="form-control" id="email" name="adm_email" placeholder="admin@easyprime.tech" required autofocus>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" class="form-control" id="password" name="adm_pw" placeholder="Password" required>
        </div>
        <div class="d-grid mb-2">
          <button type="submit" class="btn btn-primary btn-lg">Sign In</button>
        </div>
        <div class="text-center">
          <a href="#" class="text-decoration-none" style="color:var(--brand-dark)">Forgot password?</a>
        </div>
      </form>

      <div class="footer-note">EasyPrimeTech — secure admin access</div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>