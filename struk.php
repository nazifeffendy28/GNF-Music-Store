<?php
/** struk.php — tampilan struk/nota pesanan + tombol cetak & unduh. */
require_once __DIR__ . '/config.php';

$id = (int)($_GET['id'] ?? 0);
$pesanan = get_pesanan($conn, $id);
if (!$pesanan) { set_flash('warning', 'Pesanan tidak ditemukan.'); redirect('index.php'); }

// hak akses: pemilik pesanan atau admin
$isOwner = is_logged_in() && (int)current_user()['id_user'] === (int)$pesanan['id_user'];
if (!$isOwner && !is_admin()) { require_login(); }

$items = get_pesanan_items($conn, $id);
// data pembeli
$ustmt = $conn->prepare("SELECT nama_lengkap, email, no_telepon, alamat FROM users WHERE id_user = ?");
$ustmt->bind_param('i', $pesanan['id_user']); $ustmt->execute();
$pembeli = $ustmt->get_result()->fetch_assoc();

[$statusText, $statusColor] = status_label($pesanan['status']);
$pageTitle = 'Struk ' . kode_struk($id);

include __DIR__ . '/includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3 no-print">
  <a href="<?= url(is_admin() ? 'admin/index.php?page=orders' : 'account.php?tab=pesanan') ?>" class="btn btn-outline-secondary btn-sm">
    <i class="bi bi-arrow-left"></i> Kembali
  </a>
  <div class="btn-group">
    <button onclick="window.print()" class="btn btn-secondary btn-sm"><i class="bi bi-printer"></i> Print</button>
    <a href="<?= url('export.php?id=' . $id . '&format=pdf') ?>" class="btn btn-danger btn-sm"><i class="bi bi-file-pdf"></i> PDF</a>
    <a href="<?= url('export.php?id=' . $id . '&format=xlsx') ?>" class="btn btn-success btn-sm"><i class="bi bi-file-excel"></i> XLSX</a>
    <a href="<?= url('export.php?id=' . $id . '&format=csv') ?>" class="btn btn-primary btn-sm"><i class="bi bi-filetype-csv"></i> CSV</a>
  </div>
</div>

<div class="card mx-auto" style="max-width:700px">
  <div class="card-body">
    <!-- kepala struk -->
    <div class="text-center border-bottom pb-3 mb-3">
      <h4 class="mb-0"><i class="bi bi-music-note-beamed"></i> <?= e(STORE_NAME) ?></h4>
      <small class="text-muted">Toko Alat Musik &mdash; Struk Pembelian</small>
    </div>

    <div class="row mb-3">
      <div class="col-6">
        <p class="mb-1"><strong>No. Struk:</strong> <?= e(kode_struk($id)) ?></p>
        <p class="mb-1"><strong>Tanggal:</strong> <?= date('d/m/Y H:i', strtotime($pesanan['tanggal'])) ?></p>
        <p class="mb-0"><strong>Metode:</strong> <?= e($pesanan['metode_bayar']) ?></p>
      </div>
      <div class="col-6 text-md-end">
        <p class="mb-1"><strong>Pembeli:</strong> <?= e($pembeli['nama_lengkap']) ?></p>
        <p class="mb-1"><strong>Telepon:</strong> <?= e($pembeli['no_telepon'] ?: '-') ?></p>
        <p class="mb-0"><strong>Status:</strong> <span class="badge bg-<?= $statusColor ?>"><?= e($statusText) ?></span></p>
      </div>
    </div>

    <table class="table table-sm table-bordered">
      <thead class="table-light">
        <tr><th>#</th><th>Produk</th><th class="text-end">Harga</th><th class="text-center">Qty</th><th class="text-end">Subtotal</th></tr>
      </thead>
      <tbody>
        <?php $no = 1; foreach ($items as $it): ?>
          <tr>
            <td><?= $no++ ?></td>
            <td><?= e($it['nama_barang']) ?> <small class="text-muted">(<?= e($it['merk']) ?>)</small></td>
            <td class="text-end"><?= rupiah($it['harga']) ?></td>
            <td class="text-center"><?= (int)$it['jumlah'] ?></td>
            <td class="text-end"><?= rupiah($it['total_bayar']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
      <tfoot>
        <tr class="table-light">
          <th colspan="4" class="text-end">TOTAL</th>
          <th class="text-end"><?= rupiah($pesanan['total_bayar']) ?></th>
        </tr>
      </tfoot>
    </table>

    <?php if ($pesanan['status'] === 'menunggu'): ?>
      <div class="alert alert-warning mb-0 small"><i class="bi bi-clock-history"></i> Pesanan menunggu konfirmasi pembayaran dari admin.</div>
    <?php elseif ($pesanan['status'] === 'dikonfirmasi'): ?>
      <div class="alert alert-success mb-0 small"><i class="bi bi-check-circle"></i> Pembayaran sudah dikonfirmasi. Terima kasih telah berbelanja!</div>
    <?php else: ?>
      <div class="alert alert-danger mb-0 small"><i class="bi bi-x-circle"></i> Pesanan ini dibatalkan.</div>
    <?php endif; ?>

    <p class="text-center text-muted small mt-3 mb-0">Terima kasih telah berbelanja di <?= e(STORE_NAME) ?></p>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
