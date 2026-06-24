<?php
/** admin/pages/staff.php — kelola karyawan (CRUD). */
$edit = ['id_karyawan'=>'','nama_karyawan'=>'','username'=>'','role'=>'admin'];
if (($_GET['action'] ?? '') === 'edit' && isset($_GET['id'])) {
    $stmt = $conn->prepare("SELECT id_karyawan,nama_karyawan,username,role FROM karyawan WHERE id_karyawan=?");
    $idd = (int)$_GET['id']; $stmt->bind_param('i', $idd); $stmt->execute();
    $d = $stmt->get_result()->fetch_assoc(); if ($d) $edit = $d;
}
$list = $conn->query("SELECT id_karyawan,nama_karyawan,username,role FROM karyawan ORDER BY id_karyawan ASC");
$me = (int)current_admin()['id_karyawan'];
?>
<div class="row g-4">
  <div class="col-md-4">
    <div class="card">
      <div class="card-header bg-dark text-white"><?= $edit['id_karyawan'] ? 'Edit Karyawan' : 'Tambah Karyawan' ?></div>
      <div class="card-body">
        <form method="POST" action="<?= url('admin/index.php?page=staff') ?>">
          <input type="hidden" name="id_karyawan" value="<?= e($edit['id_karyawan']) ?>">
          <div class="mb-2"><label class="form-label">Nama Lengkap</label>
            <input name="nama_karyawan" class="form-control" value="<?= e($edit['nama_karyawan']) ?>" required></div>
          <div class="mb-2"><label class="form-label">Username</label>
            <input name="username" class="form-control" value="<?= e($edit['username']) ?>" required></div>
          <div class="mb-2"><label class="form-label">Password <?= $edit['id_karyawan'] ? '<small class="text-muted">(kosongkan jika tetap)</small>' : '' ?></label>
            <input name="password" class="form-control" <?= $edit['id_karyawan'] ? '' : 'required' ?>></div>
          <div class="mb-3"><label class="form-label">Role</label>
            <select name="role" class="form-select">
              <option value="admin" <?= $edit['role']==='admin'?'selected':'' ?>>admin</option>
              <option value="kasir" <?= $edit['role']==='kasir'?'selected':'' ?>>kasir</option>
            </select></div>
          <button name="simpan" value="1" class="btn btn-success w-100">Simpan</button>
          <?php if ($edit['id_karyawan']): ?>
            <a href="<?= url('admin/index.php?page=staff') ?>" class="btn btn-outline-secondary w-100 mt-2">Batal</a>
          <?php endif; ?>
        </form>
      </div>
    </div>
  </div>
  <div class="col-md-8">
    <table class="table table-bordered table-striped align-middle bg-body">
      <thead class="table-dark">
        <tr><th>#</th><th>Nama</th><th>Username</th><th>Role</th><th>Aksi</th></tr>
      </thead>
      <tbody>
        <?php while ($r = $list->fetch_assoc()): ?>
          <tr>
            <td><?= (int)$r['id_karyawan'] ?></td>
            <td><?= e($r['nama_karyawan']) ?> <?= $r['id_karyawan']==$me ? '<span class="badge bg-secondary">Anda</span>' : '' ?></td>
            <td><?= e($r['username']) ?></td>
            <td><?= e($r['role']) ?></td>
            <td class="text-nowrap">
              <a href="<?= url('admin/index.php?page=staff&action=edit&id=' . (int)$r['id_karyawan']) ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
              <a href="<?= url('admin/index.php?page=staff&action=delete&id=' . (int)$r['id_karyawan']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus karyawan ini?')"><i class="bi bi-trash"></i></a>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
