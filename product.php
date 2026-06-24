<?php
/** product.php — detail produk (sederhana). */
require_once __DIR__ . '/config.php';
$id = (int)($_GET['id'] ?? 0);
$row = get_product($conn, $id);
if (!$row) { set_flash('warning', 'Produk tidak ditemukan.'); redirect('shop.php'); }
$pageTitle = $row['nama_barang'] . ' - ' . STORE_NAME;
$stok = (int)$row['stok'];

include __DIR__ . '/includes/header.php';
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<?= url('index.php') ?>">Beranda</a></li>
    <li class="breadcrumb-item"><a href="<?= url('shop.php?kategori=' . urlencode($row['kategori'])) ?>"><?= e($row['kategori']) ?></a></li>
    <li class="breadcrumb-item active"><?= e($row['nama_barang']) ?></li>
  </ol>
</nav>

<div class="row g-4">
  <div class="col-md-5">
    <img src="<?= e(product_image($row)) ?>" class="img-fluid rounded border" alt="<?= e($row['nama_barang']) ?>">
  </div>
  <div class="col-md-7">
    <h3><?= e($row['nama_barang']) ?></h3>
    <p class="text-muted mb-2">Kategori: <?= e($row['kategori']) ?> &middot; Merk: <?= e($row['merk']) ?></p>
    <h4 class="text-primary"><?= rupiah($row['harga']) ?></h4>
    <p>
      <?php if ($stok > 0): ?>
        <span class="badge bg-success">Stok tersedia: <?= $stok ?></span>
      <?php else: ?>
        <span class="badge bg-secondary">Stok habis</span>
      <?php endif; ?>
    </p>

    <?php if ($stok > 0): ?>
      <form action="<?= url('cart.php') ?>" method="POST" class="row g-2 align-items-end" style="max-width:380px">
        <input type="hidden" name="aksi" value="tambah">
        <input type="hidden" name="id" value="<?= $id ?>">
        <div class="col-4">
          <label class="form-label">Jumlah</label>
          <input type="number" name="qty" class="form-control" value="1" min="1" max="<?= $stok ?>">
        </div>
        <div class="col-8">
          <button class="btn btn-primary w-100"><i class="bi bi-cart-plus"></i> Tambah ke Keranjang</button>
        </div>
      </form>
    <?php endif; ?>

    <table class="table table-bordered mt-4">
      <tbody>
        <tr><th style="width:30%">Merk</th><td><?= e($row['merk']) ?></td></tr>
        <tr><th>Kategori</th><td><?= e($row['kategori']) ?></td></tr>
        <tr><th>Harga</th><td><?= rupiah($row['harga']) ?></td></tr>
        <tr><th>Stok</th><td><?= $stok ?> unit</td></tr>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
