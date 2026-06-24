<?php
/**
 * checkout.php — membuat pesanan berstatus "menunggu konfirmasi admin".
 * Stok BELUM dikurangi di sini; pengurangan stok dilakukan saat admin
 * mengonfirmasi pembayaran.
 */
require_once __DIR__ . '/config.php';
require_login();
$pageTitle = 'Checkout - ' . STORE_NAME;
$user = current_user();

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
if (empty($items)) { set_flash('warning', 'Keranjang kosong.'); redirect('shop.php'); }

// proses pesanan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pesan'])) {
    $metode = $_POST['metode'] ?? 'Transfer Bank';
    $uid = (int)$user['id_user'];

    // 1) buat header pesanan
    $stmt = $conn->prepare("INSERT INTO pesanan (id_user, metode_bayar, total_bayar, status) VALUES (?,?,?, 'menunggu')");
    $stmt->bind_param('isi', $uid, $metode, $total);
    $stmt->execute();
    $id_pesanan = $conn->insert_id;

    // 2) simpan tiap item sebagai baris transaksi
    $stmtT = $conn->prepare(
        "INSERT INTO transaksi (id_user, id_barang, jumlah, total_bayar, status_pembayaran, id_pesanan)
         VALUES (?,?,?,?, 'menunggu', ?)");
    foreach ($items as $it) {
        $idb = (int)$it['p']['id_barang']; $jml = (int)$it['qty']; $sub = (int)$it['subtotal'];
        $stmtT->bind_param('iiiii', $uid, $idb, $jml, $sub, $id_pesanan);
        $stmtT->execute();
    }

    cart_clear();
    set_flash('success', 'Pesanan dibuat! Menunggu konfirmasi pembayaran dari admin.');
    redirect('struk.php?id=' . $id_pesanan);
}

include __DIR__ . '/includes/header.php';
?>

<h3 class="mb-3">Checkout</h3>
<div class="row g-4">
  <div class="col-md-7">
    <div class="card">
      <div class="card-header">Data Pembeli</div>
      <div class="card-body">
        <p class="mb-1"><strong>Nama:</strong> <?= e($user['nama_lengkap']) ?></p>
        <p class="mb-1"><strong>Email:</strong> <?= e($user['email']) ?></p>
        <p class="mb-1"><strong>Telepon:</strong> <?= e($user['no_telepon'] ?: '-') ?></p>
        <p class="mb-0"><strong>Alamat:</strong> <?= e($user['alamat'] ?: '-') ?></p>
      </div>
    </div>

    <form method="POST" action="<?= url('checkout.php') ?>" class="mt-3">
      <div class="card">
        <div class="card-header">Metode Pembayaran</div>
        <div class="card-body">
          <select name="metode" class="form-select">
            <option>Transfer Bank</option>
            <option>E-Wallet (OVO / GoPay / Dana)</option>
            <option>Kartu Kredit</option>
            <option>Bayar di Tempat (COD)</option>
          </select>
          <small class="text-muted d-block mt-2">
            Setelah pesanan dibuat, pembayaran akan <strong>diverifikasi oleh admin</strong> terlebih dahulu sebelum pesanan dianggap lunas.
          </small>
        </div>
      </div>
      <button type="submit" name="pesan" value="1" class="btn btn-success mt-3 w-100"><i class="bi bi-check-circle"></i> Buat Pesanan</button>
    </form>
  </div>

  <div class="col-md-5">
    <div class="card">
      <div class="card-header">Ringkasan Pesanan</div>
      <ul class="list-group list-group-flush">
        <?php foreach ($items as $it): ?>
          <li class="list-group-item d-flex justify-content-between">
            <span><?= e($it['p']['nama_barang']) ?> <small class="text-muted">x<?= $it['qty'] ?></small></span>
            <span><?= rupiah($it['subtotal']) ?></span>
          </li>
        <?php endforeach; ?>
        <li class="list-group-item d-flex justify-content-between fw-bold">
          <span>Total</span><span class="text-primary"><?= rupiah($total) ?></span>
        </li>
      </ul>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
