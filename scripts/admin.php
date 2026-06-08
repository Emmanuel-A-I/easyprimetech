<?php
  session_start();
  include("db.php");

  $_SESSION['error'] = '';

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['adm_email'] ?? '');
    $password = $_POST['adm_pw'] ?? '';

    $stmt = $pdo->prepare('SELECT password FROM users WHERE email = :email');
    $stmt->execute([':email' => $email]);
    $row = $stmt->fetch();

    if ($row && password_verify($password, $row['password'])) {
      $stmt = $pdo->prepare('SELECT id FROM admin_tab WHERE email = :email');
      $stmt->execute([':email' => $email]);
      $row = $stmt->fetch();
      $_SESSION['id'] = $row['id'];
      $_SESSION['email'] = $email;
      unset($_SESSION['error']);
      header('Location: ' . $_SERVER['PHP_SELF']);
      exit;
    } else {
      $_SESSION['error'] = 'Invalid username and password';
      header("Location: ../admin.php");
    }
  }

  if(isset($_POST['logout'])) {
    // Clear session data and destroy session
    session_unset();
    session_destroy();

    // DELETE THE SESSION COOKIE
    if(ini_get('session.use_cookies')) {
      $params = session_get_cookie_params();
      setcookie(
        session_name(), '',
        time() - 3600,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
      );
    }
    header('Location: ../adm_login.php');
    exit;
  }
?>