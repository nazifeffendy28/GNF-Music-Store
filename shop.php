<?php
/** shop.php — daftar produk dengan pencarian, filter kategori, urutkan & paginasi. */
require_once __DIR__ . '/config.php';
$pageTitle = 'Produk - ' . STORE_NAME;

$q        = $_GET['q'] ?? '';
$kategori = $_GET['kategori'] ?? '';
$sort     = $_GET['sort'] ?? '';
$page     = (int)($_GET['page'] ?? 1);

$data = search_products($conn, [
    'q' => $q, 'kategori' => $kategori, 'sort' => $sort,
    'page' => $page, 'perpage' => 8,
]);
$cats = get_categories($conn);

include __DIR__ . '/includes/header.php';
?>

<h3 class="mb-3">Daftar Produk</h3>

<!-- form pencarian & filter -->
<form class="row g-2 mb-4" method="GET" action="<?= url('shop.php') ?>">
  <div class="col-md-5">
    <input type="text" class="form-control" name="q" placeholder="Cari nama / merk produk..." value="<?= e($q) ?>">
  </div>
  <div class="col-md-3">
    <select class="form-select" name="kategori">
      <option value="">-- Semua Kategori --</option>
      <?php foreach ($cats as $c): ?>
        <option value="<?= e($c) ?>" <?= $kategori === $c ? 'selected' : '' ?>><?= e($c) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-md-2">
    <select class="form-select" name="sort">
      <option value="">Urutkan</option>
      <option value="termurah" <?= $sort==='termurah'?'selected':'' ?>>Harga Termurah</option>
      <option value="termahal" <?= $sort==='termahal'?'selected':'' ?>>Harga Termahal</option>
      <option value="nama" <?= $sort==='nama'?'selected':'' ?>>Nama A-Z</option>
    </select>
  </div>
  <div class="col-md-2 d-grid">
    <button class="btn btn-primary" type="submit"><i class="bi bi-funnel"></i> Filter</button>
  </div>
</form>

<p class="text-muted">Menampilkan <?= count($data['items']) ?> dari <?= $data['total'] ?> produk.</p>

<?php if (empty($data['items'])): ?>
  <div class="alert alert-info">Produk tidak ditemukan.</div>
<?php else: ?>
  <div class="row g-3">
    <?php foreach ($data['items'] as $row): ?>
      <div class="col-6 col-md-3">
        <?php include __DIR__ . '/includes/product_card.php'; ?>
      </div>
    <?php endforeach; ?>
  </div>

  <?php if ($data['pages'] > 1): ?>
    <nav class="mt-4">
      <ul class="pagination justify-content-center">
        <?php for ($i = 1; $i <= $data['pages']; $i++):
          $params = http_build_query(['q'=>$q,'kategori'=>$kategori,'sort'=>$sort,'page'=>$i]); ?>
          <li class="page-item <?= $i === $data['page'] ? 'active' : '' ?>">
            <a class="page-link" href="<?= url('shop.php?' . $params) ?>"><?= $i ?></a>
          </li>
        <?php endfor; ?>
      </ul>
    </nav>
  <?php endif; ?>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
