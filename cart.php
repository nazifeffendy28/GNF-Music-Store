<?php
/** cart.php — keranjang belanja (proses di server, tanpa AJAX). */
require_once __DIR__ . '/config.php';
$pageTitle = 'Keranjang - ' . STORE_NAME;

// proses aksi keranjang
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'] ?? '';
    if ($aksi === 'tambah') {
        $p = get_product($conn, (int)$_POST['id']);
        if ($p && (int)$p['stok'] > 0) {
            cart_add($p['id_barang'], max(1, (int)($_POST['qty'] ?? 1)));
            set_flash('success', 'Produk ditambahkan ke keranjang.');
        } else {
            set_flash('danger', 'Produk tidak tersedia.');
        }
    } elseif ($aksi === 'ubah') {
        foreach (($_POST['qty'] ?? []) as $id => $qty) cart_set((int)$id, (int)$qty);
        set_flash('success', 'Keranjang diperbarui.');
    } elseif ($aksi === 'hapus') {
        cart_remove((int)$_POST['id']);
        set_flash('info', 'Item dihapus.');
    } elseif ($aksi === 'kosongkan') {
        cart_clear();
        set_flash('info', 'Keranjang dikosongkan.');
    }
    redirect('cart.php');
}

// kumpulkan isi keranjang
$items = []; $total = 0;
foreach (cart_items() as $id => $qty) {
    $p = get_product($conn, $id);
    if (!$p) { cart_remove($id); continue; }
    $qty = min($qty, max(1, (int)$p['stok']));
    $subtotal = $qty * (int)$p['harga'];
    $total += $subtotal;
    $items[] = ['p' => $p, 'qty' => $qty, 'subtotal' => $subtotal];
}

include __DIR__ . '/includes/header.php';
?>

<h3 class="mb-3"><i class="bi bi-cart"></i> Keranjang Belanja</h3>

<?php if (empty($items)): ?>
  <div class="alert alert-info">Keranjang masih kosong. <a href="<?= url('shop.php') ?>">Mulai belanja</a>.</div>
<?php else: ?>
  <form method="POST" action="<?= url('cart.php') ?>">
    <input type="hidden" name="aksi" value="ubah">
    <table class="table table-bordered align-middle bg-body">
      <thead class="table-light">
        <tr><th>Produk</th><th>Harga</th><th width="120">Jumlah</th><th>Subtotal</th><th></th></tr>
      </thead>
      <tbody>
        <?php foreach ($items as $it): $p = $it['p']; ?>
          <tr>
            <td>
              <img src="<?= e(product_image($p)) ?>" width="50" height="50" style="object-fit:cover" class="rounded me-2">
              <?= e($p['nama_barang']) ?>
            </td>
            <td><?= rupiah($p['harga']) ?></td>
            <td><input type="number" name="qty[<?= (int)$p['id_barang'] ?>]" value="<?= $it['qty'] ?>" min="1" max="<?= (int)$p['stok'] ?>" class="form-control form-control-sm"></td>
            <td><?= rupiah($it['subtotal']) ?></td>
            <td>
              <button type="submit" form="hapus<?= (int)$p['id_barang'] ?>" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
      <tfoot>
        <tr class="table-light">
          <th colspan="3" class="text-end">Total</th>
          <th colspan="2" class="text-primary"><?= rupiah($total) ?></th>
        </tr>
      </tfoot>
    </table>

    <div class="d-flex gap-2">
      <a href="<?= url('shop.php') ?>" class="btn btn-outline-secondary">Lanjut Belanja</a>
      <button type="submit" class="btn btn-outline-primary">Perbarui Keranjang</button>
      <a href="<?= url('checkout.php') ?>" class="btn btn-success ms-auto"><i class="bi bi-bag-check"></i> Checkout</a>
    </div>
  </form>

  <!-- form hapus per item (terpisah agar tidak bentrok dengan form ubah) -->
  <?php foreach ($items as $it): $p = $it['p']; ?>
    <form id="hapus<?= (int)$p['id_barang'] ?>" method="POST" action="<?= url('cart.php') ?>" class="d-none">
      <input type="hidden" name="aksi" value="hapus">
      <input type="hidden" name="id" value="<?= (int)$p['id_barang'] ?>">
    </form>
  <?php endforeach; ?>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
