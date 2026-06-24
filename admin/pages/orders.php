<?php
/** admin/pages/orders.php — daftar pesanan + konfirmasi/batalkan. */
$filter = $_GET['status'] ?? '';
$allowed = ['', 'menunggu', 'dikonfirmasi', 'dibatalkan'];
if (!in_array($filter, $allowed, true)) $filter = '';

$sql = "SELECT p.*, u.nama_lengkap, k.nama_karyawan
        FROM pesanan p
        JOIN users u ON p.id_user = u.id_user
        LEFT JOIN karyawan k ON p.id_karyawan = k.id_karyawan";
if ($filter) {
    $stmt = $conn->prepare($sql . " WHERE p.status = ? ORDER BY p.id_pesanan DESC");
    $stmt->bind_param('s', $filter); $stmt->execute(); $list = $stmt->get_result();
} else {
    $list = $conn->query($sql . " ORDER BY p.id_pesanan DESC");
}
?>
<div class="mb-3">
  <a href="<?= url('admin/index.php?page=orders') ?>" class="btn btn-sm <?= $filter===''?'btn-dark':'btn-outline-dark' ?>">Semua</a>
  <a href="<?= url('admin/index.php?page=orders&status=menunggu') ?>" class="btn btn-sm <?= $filter==='menunggu'?'btn-warning':'btn-outline-warning' ?>">Menunggu</a>
  <a href="<?= url('admin/index.php?page=orders&status=dikonfirmasi') ?>" class="btn btn-sm <?= $filter==='dikonfirmasi'?'btn-success':'btn-outline-success' ?>">Lunas</a>
  <a href="<?= url('admin/index.php?page=orders&status=dibatalkan') ?>" class="btn btn-sm <?= $filter==='dibatalkan'?'btn-danger':'btn-outline-danger' ?>">Dibatalkan</a>
</div>

<table class="table table-bordered align-middle bg-body">
  <thead class="table-dark">
    <tr><th>No. Struk</th><th>Tanggal</th><th>Pelanggan</th><th>Metode</th><th>Total</th><th>Status</th><th>Aksi</th></tr>
  </thead>
  <tbody>
    <?php if ($list->num_rows === 0): ?>
      <tr><td colspan="7" class="text-center text-muted py-3">Tidak ada pesanan.</td></tr>
    <?php endif; ?>
    <?php while ($r = $list->fetch_assoc()): [$st,$col] = status_label($r['status']); ?>
      <tr>
        <td><?= e(kode_struk($r['id_pesanan'])) ?></td>
        <td><small><?= date('d/m/y H:i', strtotime($r['tanggal'])) ?></small></td>
        <td><?= e($r['nama_lengkap']) ?></td>
        <td><small><?= e($r['metode_bayar']) ?></small></td>
        <td><?= rupiah($r['total_bayar']) ?></td>
        <td>
          <span class="badge bg-<?= $col ?>"><?= e($st) ?></span>
          <?php if ($r['nama_karyawan']): ?><br><small class="text-muted">oleh <?= e($r['nama_karyawan']) ?></small><?php endif; ?>
        </td>
        <td class="text-nowrap">
          <a href="<?= url('struk.php?id=' . (int)$r['id_pesanan']) ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-receipt"></i></a>
          <?php if ($r['status'] === 'menunggu'): ?>
            <form method="POST" action="<?= url('admin/index.php?page=orders') ?>" class="d-inline">
              <input type="hidden" name="id_pesanan" value="<?= (int)$r['id_pesanan'] ?>">
              <button name="konfirmasi" value="1" class="btn btn-sm btn-success" onclick="return confirm('Konfirmasi pembayaran pesanan ini?')"><i class="bi bi-check-lg"></i> Konfirmasi</button>
              <button name="batalkan" value="1" class="btn btn-sm btn-danger" onclick="return confirm('Batalkan pesanan ini?')"><i class="bi bi-x-lg"></i></button>
            </form>
          <?php endif; ?>
        </td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>
