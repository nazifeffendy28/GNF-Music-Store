<?php
/** account.php — profil pelanggan & daftar pesanan. */
require_once __DIR__ . '/config.php';
require_login();
$pageTitle = 'Akun Saya - ' . STORE_NAME;
$user = current_user();
$tab = $_GET['tab'] ?? 'profil';

// update profil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan_profil'])) {
    $nama = trim($_POST['nama']); $telp = trim($_POST['telepon']); $alamat = trim($_POST['alamat']);
    $uid = (int)$user['id_user'];
    $stmt = $conn->prepare("UPDATE users SET nama_lengkap=?, no_telepon=?, alamat=? WHERE id_user=?");
    $stmt->bind_param('sssi', $nama, $telp, $alamat, $uid);
    $stmt->execute();
    $_SESSION['user']['nama_lengkap'] = $nama;
    $_SESSION['user']['no_telepon'] = $telp;
    $_SESSION['user']['alamat'] = $alamat;
    set_flash('success', 'Profil diperbarui.');
    redirect('account.php');
}

// daftar pesanan
$stmt = $conn->prepare("SELECT * FROM pesanan WHERE id_user = ? ORDER BY id_pesanan DESC");
$uid = (int)$user['id_user'];
$stmt->bind_param('i', $uid); $stmt->execute();
$pesananList = $stmt->get_result();

include __DIR__ . '/includes/header.php';
?>

<h3 class="mb-3">Akun Saya</h3>
<ul class="nav nav-tabs mb-3">
  <li class="nav-item"><a class="nav-link <?= $tab==='profil'?'active':'' ?>" href="<?= url('account.php?tab=profil') ?>">Profil</a></li>
  <li class="nav-item"><a class="nav-link <?= $tab==='pesanan'?'active':'' ?>" href="<?= url('account.php?tab=pesanan') ?>">Pesanan Saya</a></li>
</ul>

<?php if ($tab === 'pesanan'): ?>
  <?php if ($pesananList->num_rows === 0): ?>
    <div class="alert alert-info">Belum ada pesanan. <a href="<?= url('shop.php') ?>">Belanja sekarang</a>.</div>
  <?php else: ?>
    <table class="table table-bordered align-middle bg-body">
      <thead class="table-light">
        <tr><th>No. Struk</th><th>Tanggal</th><th>Metode</th><th>Total</th><th>Status</th><th>Aksi</th></tr>
      </thead>
      <tbody>
        <?php while ($p = $pesananList->fetch_assoc()): [$st,$col] = status_label($p['status']); ?>
          <tr>
            <td><?= e(kode_struk($p['id_pesanan'])) ?></td>
            <td><?= date('d/m/Y H:i', strtotime($p['tanggal'])) ?></td>
            <td><?= e($p['metode_bayar']) ?></td>
            <td><?= rupiah($p['total_bayar']) ?></td>
            <td><span class="badge bg-<?= $col ?>"><?= e($st) ?></span></td>
            <td><a href="<?= url('struk.php?id=' . (int)$p['id_pesanan']) ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-receipt"></i> Struk</a></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  <?php endif; ?>

<?php else: ?>
  <div class="card">
    <div class="card-body">
      <form method="POST">
        <div class="row">
          <div class="col-md-6 mb-3"><label class="form-label">Nama Lengkap</label>
            <input name="nama" class="form-control" value="<?= e($user['nama_lengkap']) ?>" required></div>
          <div class="col-md-6 mb-3"><label class="form-label">Email</label>
            <input class="form-control" value="<?= e($user['email']) ?>" disabled></div>
          <div class="col-md-6 mb-3"><label class="form-label">No. Telepon</label>
            <input name="telepon" class="form-control" value="<?= e($user['no_telepon'] ?? '') ?>"></div>
          <div class="col-12 mb-3"><label class="form-label">Alamat</label>
            <textarea name="alamat" class="form-control" rows="2"><?= e($user['alamat'] ?? '') ?></textarea></div>
        </div>
        <button name="simpan_profil" value="1" class="btn btn-primary">Simpan Perubahan</button>
      </form>
    </div>
  </div>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
