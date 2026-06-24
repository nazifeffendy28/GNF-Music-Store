<?php
/** admin/includes/layout_top.php — header admin (Bootstrap default). Butuh $page, $adminTitle. */
$admin = current_admin();
$page = $page ?? 'dashboard';
$adminTitle = $adminTitle ?? 'Dashboard';
$adminFlash = get_flash();
$menu = [
    'dashboard' => 'Dashboard',
    'products'  => 'Produk',
    'orders'    => 'Pesanan',
    'customers' => 'Pelanggan',
    'staff'     => 'Karyawan',
];
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($adminTitle) ?> - Admin <?= e(STORE_NAME) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="<?= url('assets/css/style.css') ?>" rel="stylesheet">
  <script>document.documentElement.setAttribute('data-bs-theme', localStorage.getItem('tema') || 'light');</script>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-danger">
  <div class="container">
    <a class="navbar-brand fw-bold" href="<?= url('admin/index.php') ?>"><i class="bi bi-shield-lock"></i> Admin Panel</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#anav"><span class="navbar-toggler-icon"></span></button>
    <div class="collapse navbar-collapse" id="anav">
      <ul class="navbar-nav me-auto">
        <?php foreach ($menu as $key => $label): ?>
          <li class="nav-item"><a class="nav-link <?= $page === $key ? 'active fw-bold' : '' ?>" href="<?= url('admin/index.php?page=' . $key) ?>"><?= $label ?></a></li>
        <?php endforeach; ?>
      </ul>
      <ul class="navbar-nav">
        <li class="nav-item"><button class="btn btn-sm btn-outline-light mt-1 mt-lg-0 me-lg-2" id="tombolTema" type="button"><i class="bi bi-moon-stars"></i></button></li>
        <li class="nav-item"><a class="nav-link" href="<?= url('index.php') ?>" target="_blank"><i class="bi bi-shop"></i> Toko</a></li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"><i class="bi bi-person-circle"></i> <?= e($admin['nama_karyawan']) ?></a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="<?= url('admin/logout.php') ?>">Logout</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container my-4">
  <h3 class="mb-3"><?= e($adminTitle) ?></h3>
  <?php if ($adminFlash): ?>
    <div class="alert alert-<?= e($adminFlash['type']) ?> alert-dismissible fade show"><?= e($adminFlash['msg']) ?>
      <button class="btn-close" data-bs-dismiss="alert"></button></div>
  <?php endif; ?>
