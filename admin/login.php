<?php
/** admin/login.php — login karyawan/admin. */
require_once __DIR__ . '/../config.php';
if (is_admin()) redirect('admin/index.php');
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $pass = $_POST['password'] ?? '';
    $stmt = $conn->prepare("SELECT * FROM karyawan WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $k = $stmt->get_result()->fetch_assoc();
    if ($k && $pass === $k['password']) {
        unset($k['password']);
        $_SESSION['admin'] = $k;
        redirect('admin/index.php');
    }
    $error = 'Username atau password salah.';
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Login - <?= e(STORE_NAME) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container">
  <div class="row justify-content-center align-items-center" style="min-height:100vh">
    <div class="col-md-4">
      <div class="card shadow">
        <div class="card-header bg-danger text-white text-center"><h5 class="mb-0"><i class="bi bi-shield-lock"></i> Admin Login</h5></div>
        <div class="card-body">
          <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
          <form method="POST">
            <div class="mb-3"><label class="form-label">Username</label>
              <input name="username" class="form-control" required autofocus></div>
            <div class="mb-3"><label class="form-label">Password</label>
              <input type="password" name="password" class="form-control" required></div>
            <button class="btn btn-danger w-100">Login</button>
          </form>
          <p class="text-center text-muted small mt-3 mb-0">Demo: nazif / nazif123</p>
          <p class="text-center mb-0"><a href="<?= url('index.php') ?>" class="small">&larr; Kembali ke toko</a></p>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
