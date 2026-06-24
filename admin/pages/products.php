<?php
/** admin/pages/products.php — CRUD produk (form + tabel). */
$edit = ['id_barang'=>'','kategori'=>'','nama_barang'=>'','merk'=>'','harga'=>'','stok'=>'','gambar_url'=>''];
if (($_GET['action'] ?? '') === 'edit' && isset($_GET['id'])) {
    $d = get_product($conn, (int)$_GET['id']);
    if ($d) $edit = $d;
}
$cats = get_categories($conn);
$list = $conn->query("SELECT * FROM barang ORDER BY id_barang DESC");
?>
<div class="row g-4">
  <!-- FORM -->
  <div class="col-md-4">
    <div class="card">
      <div class="card-header bg-dark text-white"><?= $edit['id_barang'] ? 'Edit Produk' : 'Tambah Produk' ?></div>
      <div class="card-body">
        <form method="POST" action="<?= url('admin/index.php?page=products') ?>">
          <input type="hidden" name="id_barang" value="<?= e($edit['id_barang']) ?>">
          <div class="mb-2"><label class="form-label">Nama Barang</label>
            <input name="nama_barang" class="form-control" value="<?= e($edit['nama_barang']) ?>" required></div>
          <div class="mb-2"><label class="form-label">Kategori</label>
            <input name="kategori" class="form-control" list="cats" value="<?= e($edit['kategori']) ?>" required>
            <datalist id="cats"><?php foreach ($cats as $c) echo '<option value="' . e($c) . '">'; ?></datalist></div>
          <div class="mb-2"><label class="form-label">Merk</label>
            <input name="merk" class="form-control" value="<?= e($edit['merk']) ?>" required></div>
          <div class="mb-2"><label class="form-label">Harga</label>
            <input type="number" name="harga" class="form-control" value="<?= e($edit['harga']) ?>" required></div>
          <div class="mb-2"><label class="form-label">Stok</label>
            <input type="number" name="stok" class="form-control" value="<?= e($edit['stok']) ?>" required></div>
          <div class="mb-3"><label class="form-label">URL Gambar (opsional)</label>
            <input name="gambar_url" class="form-control" value="<?= e($edit['gambar_url']) ?>" placeholder="images/contoh.jpg"></div>
          <button name="simpan" value="1" class="btn btn-success w-100">Simpan</button>
          <?php if ($edit['id_barang']): ?>
            <a href="<?= url('admin/index.php?page=products') ?>" class="btn btn-outline-secondary w-100 mt-2">Batal</a>
          <?php endif; ?>
        </form>
      </div>
    </div>
  </div>

  <!-- TABEL -->
  <div class="col-md-8">
    <table class="table table-bordered table-striped align-middle bg-body">
      <thead class="table-dark">
        <tr><th>#</th><th>Nama</th><th>Kategori</th><th>Harga</th><th>Stok</th><th>Aksi</th></tr>
      </thead>
      <tbody>
        <?php while ($row = $list->fetch_assoc()): ?>
          <tr>
            <td><?= (int)$row['id_barang'] ?></td>
            <td><?= e($row['nama_barang']) ?><br><small class="text-muted"><?= e($row['merk']) ?></small></td>
            <td><?= e($row['kategori']) ?></td>
            <td><?= rupiah($row['harga']) ?></td>
            <td><?= (int)$row['stok'] ?></td>
            <td class="text-nowrap">
              <a href="<?= url('admin/index.php?page=products&action=edit&id=' . (int)$row['id_barang']) ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
              <a href="<?= url('admin/index.php?page=products&action=delete&id=' . (int)$row['id_barang']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus produk ini?')"><i class="bi bi-trash"></i></a>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
