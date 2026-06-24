<?php
/** index.php — halaman depan toko (sederhana). */
require_once __DIR__ . '/config.php';
$pageTitle = STORE_NAME . ' - Toko Alat Musik';

// ambil 8 produk untuk ditampilkan di beranda
$produk = search_products($conn, ['perpage' => 8])['items'];
$cats = get_categories($conn);

include __DIR__ . '/includes/header.php';
?>

<!-- sambutan sederhana -->
<div class="p-4 mb-4 bg-light rounded border">
  <h2><i class="bi bi-music-note-beamed"></i> Selamat Datang di <?= e(STORE_NAME) ?></h2>
  <p class="mb-3 text-muted">Toko alat musik online: gitar, bass, drum, keyboard, amplifier &amp; mixer.</p>
  <a href="<?= url('shop.php') ?>" class="btn btn-primary">Lihat Semua Produk</a>
</div>

<!-- kategori (tombol biasa) -->
<div class="mb-4">
  <span class="me-2 fw-semibold">Kategori:</span>
  <?php foreach ($cats as $c): ?>
    <a href="<?= url('shop.php?kategori=' . urlencode($c)) ?>" class="btn btn-sm btn-outline-secondary mb-1"><?= e($c) ?></a>
  <?php endforeach; ?>
</div>

<!-- daftar produk -->
<h4 class="mb-3">Produk Terbaru</h4>
<div class="row g-3">
  <?php foreach ($produk as $row): ?>
    <div class="col-6 col-md-3">
      <?php include __DIR__ . '/includes/product_card.php'; ?>
    </div>
  <?php endforeach; ?>
</div>

<div class="text-center mt-4">
  <a href="<?= url('shop.php') ?>" class="btn btn-outline-primary">Lihat Produk Lainnya</a>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
