<?php
/** includes/product_card.php — kartu produk sederhana. Membutuhkan $row. */
$stok = (int)$row['stok'];
?>
<div class="card h-100 shadow-sm">
  <a href="<?= url('product.php?id=' . (int)$row['id_barang']) ?>" class="ratio ratio-1x1 d-block">
    <img src="<?= e(product_image($row)) ?>" class="card-img-top" alt="<?= e($row['nama_barang']) ?>" style="object-fit:cover;">
  </a>
  <div class="card-body d-flex flex-column">
    <small class="text-muted"><?= e($row['kategori']) ?> &middot; <?= e($row['merk']) ?></small>
    <h6 class="card-title mt-1">
      <a href="<?= url('product.php?id=' . (int)$row['id_barang']) ?>" class="text-decoration-none text-reset"><?= e($row['nama_barang']) ?></a>
    </h6>
    <p class="fw-bold text-primary mb-2"><?= rupiah($row['harga']) ?></p>
    <div class="mt-auto">
      <?php if ($stok > 0): ?>
        <form action="<?= url('cart.php') ?>" method="POST" class="d-grid">
          <input type="hidden" name="aksi" value="tambah">
          <input type="hidden" name="id" value="<?= (int)$row['id_barang'] ?>">
          <button class="btn btn-sm btn-primary"><i class="bi bi-cart-plus"></i> Tambah</button>
        </form>
      <?php else: ?>
        <button class="btn btn-sm btn-secondary w-100" disabled>Stok Habis</button>
      <?php endif; ?>
    </div>
  </div>
</div>
