<?php
/** admin/pages/customers.php — daftar pelanggan. */
$list = $conn->query("SELECT * FROM users ORDER BY id_user DESC");
?>
<table class="table table-bordered table-striped align-middle bg-body">
  <thead class="table-dark">
    <tr><th>#</th><th>Nama</th><th>Email</th><th>Telepon</th><th>Alamat</th><th>Aksi</th></tr>
  </thead>
  <tbody>
    <?php while ($r = $list->fetch_assoc()): ?>
      <tr>
        <td><?= (int)$r['id_user'] ?></td>
        <td><?= e($r['nama_lengkap']) ?></td>
        <td><?= e($r['email']) ?></td>
        <td><?= e($r['no_telepon'] ?: '-') ?></td>
        <td><?= e($r['alamat'] ?: '-') ?></td>
        <td>
          <a href="<?= url('admin/index.php?page=customers&action=delete&id=' . (int)$r['id_user']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus pelanggan ini?')"><i class="bi bi-trash"></i></a>
        </td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>
