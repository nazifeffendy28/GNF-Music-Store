<?php
/** includes/header.php — header & navbar (Bootstrap default). */
if (!defined('STORE_NAME')) { require_once __DIR__ . '/../config.php'; }
$cats = get_categories($conn);
$pageTitle = $pageTitle ?? STORE_NAME;
$flash = get_flash();
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="<?= url('assets/css/style.css') ?>" rel="stylesheet">
  <script>
    // pasang tema (terang/gelap) sebelum halaman tampil
    document.documentElement.setAttribute('data-bs-theme', localStorage.getItem('tema') || 'light');
  </script>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand fw-bold" href="<?= url('index.php') ?>">
      <i class="bi bi-music-note-beamed"></i> <?= e(STORE_NAME) ?>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="<?= url('index.php') ?>">Beranda</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= url('shop.php') ?>">Produk</a></li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Kategori</a>
          <ul class="dropdown-menu">
            <?php foreach ($cats as $c): ?>
              <li><a class="dropdown-item" href="<?= url('shop.php?kategori=' . urlencode($c)) ?>"><?= e($c) ?></a></li>
            <?php endforeach; ?>
          </ul>
        </li>
      </ul>

      <form class="d-flex me-2" action="<?= url('shop.php') ?>" method="GET">
        <input class="form-control form-control-sm me-1" type="search" name="q" placeholder="Cari produk..." value="<?= e($_GET['q'] ?? '') ?>">
        <button class="btn btn-sm btn-outline-light" type="submit"><i class="bi bi-search"></i></button>
      </form>

      <ul class="navbar-nav">
        <li class="nav-item">
          <button class="btn btn-sm btn-outline-light mt-1 mt-lg-0 me-lg-2" id="tombolTema" type="button" title="Ganti tema">
            <i class="bi bi-moon-stars"></i>
          </button>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= url('cart.php') ?>">
            <i class="bi bi-cart"></i> Keranjang
            <?php if (cart_count() > 0): ?><span class="badge bg-danger"><?= cart_count() ?></span><?php endif; ?>
          </a>
        </li>
        <?php if (is_logged_in()): ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"><i class="bi bi-person-circle"></i> <?= e(current_user()['nama_lengkap']) ?></a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="<?= url('account.php') ?>">Akun Saya</a></li>
              <li><a class="dropdown-item" href="<?= url('account.php?tab=pesanan') ?>">Pesanan Saya</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="<?= url('logout.php') ?>">Logout</a></li>
            </ul>
          </li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="<?= url('login.php') ?>"><i class="bi bi-box-arrow-in-right"></i> Login</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<div class="container my-4">
<?php if ($flash): ?>
  <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show">
    <?= e($flash['msg']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>
