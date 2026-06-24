<?php
/** register.php — pendaftaran pelanggan baru. */
require_once __DIR__ . '/config.php';
if (is_logged_in()) redirect('index.php');
$pageTitle = 'Daftar - ' . STORE_NAME;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama   = trim($_POST['nama'] ?? '');
    $email  = trim($_POST['email'] ?? '');
    $pass   = $_POST['password'] ?? '';
    $telp   = trim($_POST['telepon'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');

    if (!$nama || !$email || !$pass) {
        $error = 'Nama, email, dan password wajib diisi.';
    } else {
        $cek = $conn->prepare("SELECT id_user FROM users WHERE email = ?");
        $cek->bind_param('s', $email); $cek->execute();
        if ($cek->get_result()->num_rows > 0) {
            $error = 'Email sudah terdaftar.';
        } else {
            $stmt = $conn->prepare("INSERT INTO users (nama_lengkap, email, password, no_telepon, alamat) VALUES (?,?,?,?,?)");
            $stmt->bind_param('sssss', $nama, $email, $pass, $telp, $alamat);
            $stmt->execute();
            $_SESSION['user'] = [
                'id_user' => $stmt->insert_id, 'nama_lengkap' => $nama, 'email' => $email,
                'no_telepon' => $telp, 'alamat' => $alamat,
            ];
            set_flash('success', 'Pendaftaran berhasil. Selamat datang!');
            redirect('index.php');
        }
    }
}
include __DIR__ . '/includes/header.php';
?>

<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card">
      <div class="card-header bg-dark text-white text-center"><h5 class="mb-0">Daftar Akun Baru</h5></div>
      <div class="card-body">
        <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
        <form method="POST">
          <div class="mb-3"><label class="form-label">Nama Lengkap</label>
            <input name="nama" class="form-control" value="<?= e($_POST['nama'] ?? '') ?>" required></div>
          <div class="mb-3"><label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?= e($_POST['email'] ?? '') ?>" required></div>
          <div class="mb-3"><label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required></div>
          <div class="mb-3"><label class="form-label">No. Telepon</label>
            <input name="telepon" class="form-control" value="<?= e($_POST['telepon'] ?? '') ?>"></div>
          <div class="mb-3"><label class="form-label">Alamat</label>
            <textarea name="alamat" class="form-control" rows="2"><?= e($_POST['alamat'] ?? '') ?></textarea></div>
          <button class="btn btn-primary w-100">Daftar</button>
        </form>
        <p class="text-center mt-3 mb-0">Sudah punya akun? <a href="<?= url('login.php') ?>">Login</a></p>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
