<?php
/** login.php — login pelanggan. */
require_once __DIR__ . '/config.php';
if (is_logged_in()) redirect('index.php');
$pageTitle = 'Login - ' . STORE_NAME;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $u = $stmt->get_result()->fetch_assoc();
    if ($u && $pass === $u['password']) {
        unset($u['password']);
        $_SESSION['user'] = $u;
        set_flash('success', 'Berhasil login. Selamat datang, ' . $u['nama_lengkap'] . '!');
        redirect('index.php');
    }
    $error = 'Email atau password salah.';
}
include __DIR__ . '/includes/header.php';
?>

<div class="row justify-content-center">
  <div class="col-md-5">
    <div class="card">
      <div class="card-header bg-dark text-white text-center"><h5 class="mb-0">Login Pelanggan</h5></div>
      <div class="card-body">
        <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
        <form method="POST">
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <button class="btn btn-primary w-100">Login</button>
        </form>
        <p class="text-center mt-3 mb-0">Belum punya akun? <a href="<?= url('register.php') ?>">Daftar</a></p>
        <p class="text-center text-muted small mt-2 mb-0">Demo: user@gmail.com / user123</p>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
