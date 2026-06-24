<?php
/** admin/pages/dashboard.php — ringkasan sederhana. */
$totalProduk = (int)$conn->query("SELECT COUNT(*) c FROM barang")->fetch_assoc()['c'];
$totalUser   = (int)$conn->query("SELECT COUNT(*) c FROM users")->fetch_assoc()['c'];
$menunggu    = (int)$conn->query("SELECT COUNT(*) c FROM pesanan WHERE status='menunggu'")->fetch_assoc()['c'];
$pendapatan  = (int)$conn->query("SELECT COALESCE(SUM(total_bayar),0) c FROM pesanan WHERE status='dikonfirmasi'")->fetch_assoc()['c'];

$recent = $conn->query(
    "SELECT p.*, u.nama_lengkap FROM pesanan p JOIN users u ON p.id_user=u.id_user
     ORDER BY p.id_pesanan DESC LIMIT 8");
?>
<div class="row g-3 mb-4">
  <div class="col-6 col-md-3">
    <div class="card text-center"><div class="card-body">
      <i class="bi bi-box-seam fs-2 text-primary"></i>
      <h4 class="mb-0"><?= $totalProduk ?></h4><small class="text-muted">Produk</small>
    </div></div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card text-center"><div class="card-body">
      <i class="bi bi-people fs-2 text-info"></i>
      <h4 class="mb-0"><?= $totalUser ?></h4><small class="text-muted">Pelanggan</small>
    </div></div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card text-center"><div class="card-body">
      <i class="bi bi-clock-history fs-2 text-warning"></i>
      <h4 class="mb-0"><?= $menunggu ?></h4><small class="text-muted">Pesanan Menunggu</small>
    </div></div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card text-center"><div class="card-body">
      <i class="bi bi-cash-coin fs-2 text-success"></i>
      <h5 class="mb-0"><?= rupiah($pendapatan) ?></h5><small class="text-muted">Pendapatan</small>
    </div></div>
  </div>
</div>

<div class="card">
  <div class="card-header">Pesanan Terbaru</div>
  <div class="card-body p-0">
    <table class="table mb-0 align-middle">
      <thead class="table-light">
        <tr><th>No. Struk</th><th>Pelanggan</th><th>Total</th><th>Status</th><th>Aksi</th></tr>
      </thead>
      <tbody>
        <?php if ($recent->num_rows === 0): ?>
          <tr><td colspan="5" class="text-center text-muted py-3">Belum ada pesanan.</td></tr>
        <?php endif; ?>
        <?php while ($r = $recent->fetch_assoc()): [$st,$col] = status_label($r['status']); ?>
          <tr>
            <td><?= e(kode_struk($r['id_pesanan'])) ?></td>
            <td><?= e($r['nama_lengkap']) ?></td>
            <td><?= rupiah($r['total_bayar']) ?></td>
            <td><span class="badge bg-<?= $col ?>"><?= e($st) ?></span></td>
            <td><a href="<?= url('struk.php?id=' . (int)$r['id_pesanan']) ?>" class="btn btn-sm btn-outline-primary">Struk</a></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
