<?php
/**
 * admin/index.php — router panel admin (pola ?page= seperti modul).
 * Pemrosesan aksi dilakukan sebelum output HTML agar redirect aman.
 */
require_once __DIR__ . '/../config.php';
require_admin();

$valid = ['dashboard', 'products', 'orders', 'customers', 'staff'];
$page = in_array($_GET['page'] ?? '', $valid, true) ? $_GET['page'] : 'dashboard';
$action = $_GET['action'] ?? '';

/* ===================== AKSI PRODUK ===================== */
if ($page === 'products') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {
        $id       = (int)($_POST['id_barang'] ?? 0);
        $kategori = trim($_POST['kategori']);
        $nama     = trim($_POST['nama_barang']);
        $merk     = trim($_POST['merk']);
        $harga    = (int)$_POST['harga'];
        $stok     = (int)$_POST['stok'];
        $gambar   = trim($_POST['gambar_url']);
        if ($id > 0) {
            $stmt = $conn->prepare("UPDATE barang SET kategori=?,nama_barang=?,merk=?,harga=?,stok=?,gambar_url=? WHERE id_barang=?");
            $stmt->bind_param('sssiisi', $kategori, $nama, $merk, $harga, $stok, $gambar, $id);
            $stmt->execute();
            set_flash('success', 'Produk diperbarui.');
        } else {
            $stmt = $conn->prepare("INSERT INTO barang (kategori,nama_barang,merk,harga,stok,gambar_url) VALUES (?,?,?,?,?,?)");
            $stmt->bind_param('sssiis', $kategori, $nama, $merk, $harga, $stok, $gambar);
            $stmt->execute();
            set_flash('success', 'Produk baru ditambahkan.');
        }
        redirect('admin/index.php?page=products');
    }
    if ($action === 'delete' && isset($_GET['id'])) {
        $idd = (int)$_GET['id'];
        $stmt = $conn->prepare("DELETE FROM barang WHERE id_barang=?");
        $stmt->bind_param('i', $idd); $stmt->execute();
        set_flash('info', 'Produk dihapus.');
        redirect('admin/index.php?page=products');
    }
}

/* ===================== AKSI PESANAN ===================== */
if ($page === 'orders' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pesanan = (int)($_POST['id_pesanan'] ?? 0);
    $pesanan = get_pesanan($conn, $id_pesanan);
    $idk = (int)current_admin()['id_karyawan'];

    if ($pesanan && isset($_POST['konfirmasi']) && $pesanan['status'] === 'menunggu') {
        // konfirmasi pembayaran -> kurangi stok untuk tiap item
        $items = get_pesanan_items($conn, $id_pesanan);
        $conn->begin_transaction();
        try {
            $upd = $conn->prepare("UPDATE barang SET stok = stok - ? WHERE id_barang = ? AND stok >= ?");
            foreach ($items as $it) {
                $jml = (int)$it['jumlah']; $idb = (int)$it['id_barang'];
                $upd->bind_param('iii', $jml, $idb, $jml);
                $upd->execute();
                if ($upd->affected_rows < 1) throw new Exception('Stok tidak cukup untuk ' . $it['nama_barang']);
            }
            $s = $conn->prepare("UPDATE pesanan SET status='dikonfirmasi', id_karyawan=? WHERE id_pesanan=?");
            $s->bind_param('ii', $idk, $id_pesanan); $s->execute();
            $t = $conn->prepare("UPDATE transaksi SET status_pembayaran='lunas', id_karyawan=? WHERE id_pesanan=?");
            $t->bind_param('ii', $idk, $id_pesanan); $t->execute();
            $conn->commit();
            set_flash('success', 'Pesanan ' . kode_struk($id_pesanan) . ' dikonfirmasi (lunas).');
        } catch (Exception $ex) {
            $conn->rollback();
            set_flash('danger', 'Gagal konfirmasi: ' . $ex->getMessage());
        }
    } elseif ($pesanan && isset($_POST['batalkan']) && $pesanan['status'] === 'menunggu') {
        $s = $conn->prepare("UPDATE pesanan SET status='dibatalkan', id_karyawan=? WHERE id_pesanan=?");
        $s->bind_param('ii', $idk, $id_pesanan); $s->execute();
        $t = $conn->prepare("UPDATE transaksi SET status_pembayaran='dibatalkan' WHERE id_pesanan=?");
        $t->bind_param('i', $id_pesanan); $t->execute();
        set_flash('info', 'Pesanan ' . kode_struk($id_pesanan) . ' dibatalkan.');
    }
    redirect('admin/index.php?page=orders');
}

/* ===================== AKSI KARYAWAN ===================== */
if ($page === 'staff') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {
        $id = (int)($_POST['id_karyawan'] ?? 0);
        $nama = trim($_POST['nama_karyawan']); $uname = trim($_POST['username']);
        $pass = $_POST['password']; $role = $_POST['role'] ?: 'admin';
        if ($id > 0) {
            if ($pass !== '') {
                $stmt = $conn->prepare("UPDATE karyawan SET nama_karyawan=?,username=?,password=?,role=? WHERE id_karyawan=?");
                $stmt->bind_param('ssssi', $nama, $uname, $pass, $role, $id);
            } else {
                $stmt = $conn->prepare("UPDATE karyawan SET nama_karyawan=?,username=?,role=? WHERE id_karyawan=?");
                $stmt->bind_param('sssi', $nama, $uname, $role, $id);
            }
            $stmt->execute(); set_flash('success', 'Data karyawan diperbarui.');
        } else {
            $stmt = $conn->prepare("INSERT INTO karyawan (nama_karyawan,username,password,role) VALUES (?,?,?,?)");
            $stmt->bind_param('ssss', $nama, $uname, $pass, $role);
            $stmt->execute(); set_flash('success', 'Karyawan baru ditambahkan.');
        }
        redirect('admin/index.php?page=staff');
    }
    if ($action === 'delete' && isset($_GET['id'])) {
        $idd = (int)$_GET['id'];
        if ($idd === (int)current_admin()['id_karyawan']) {
            set_flash('warning', 'Tidak bisa menghapus akun sendiri.');
        } else {
            $stmt = $conn->prepare("DELETE FROM karyawan WHERE id_karyawan=?");
            $stmt->bind_param('i', $idd); $stmt->execute();
            set_flash('info', 'Karyawan dihapus.');
        }
        redirect('admin/index.php?page=staff');
    }
}

/* ===================== AKSI PELANGGAN ===================== */
if ($page === 'customers' && $action === 'delete' && isset($_GET['id'])) {
    $idd = (int)$_GET['id'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id_user=?");
    $stmt->bind_param('i', $idd); $stmt->execute();
    set_flash('info', 'Pelanggan dihapus.');
    redirect('admin/index.php?page=customers');
}

/* ===================== TAMPILAN ===================== */
$titles = ['dashboard' => 'Dashboard', 'products' => 'Kelola Produk', 'orders' => 'Pesanan', 'customers' => 'Pelanggan', 'staff' => 'Karyawan'];
$adminTitle = $titles[$page];
require __DIR__ . '/includes/layout_top.php';
require __DIR__ . '/pages/' . $page . '.php';
require __DIR__ . '/includes/layout_bottom.php';
