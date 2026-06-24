<?php
/**
 * includes/functions.php
 * Kumpulan fungsi bantu untuk aplikasi Toko Musik.
 */

// Menampilkan teks dengan aman (mencegah XSS)
function e($str) {
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}

// Format harga Rupiah
function rupiah($angka) {
    return 'Rp ' . number_format((int)$angka, 0, ',', '.');
}

// Membuat URL relatif terhadap folder aplikasi
function url($path = '') {
    return BASE_URL . '/' . ltrim($path, '/');
}

// Pindah halaman
function redirect($path) {
    if (strpos($path, '://') !== false) { header('Location: ' . $path); }
    else { header('Location: ' . url($path)); }
    exit;
}

// ----------------- LOGIN -----------------
function is_logged_in() { return isset($_SESSION['user']); }
function current_user()  { return $_SESSION['user'] ?? null; }
function require_login() {
    if (!is_logged_in()) {
        set_flash('warning', 'Silakan login terlebih dahulu.');
        redirect('login.php');
    }
}
function is_admin()      { return isset($_SESSION['admin']); }
function current_admin() { return $_SESSION['admin'] ?? null; }
function require_admin() {
    if (!is_admin()) redirect('admin/login.php');
}

// ----------------- FLASH MESSAGE -----------------
function set_flash($type, $msg) { $_SESSION['flash'] = ['type' => $type, 'msg' => $msg]; }
function get_flash() {
    if (!empty($_SESSION['flash'])) { $f = $_SESSION['flash']; unset($_SESSION['flash']); return $f; }
    return null;
}

// ----------------- KERANJANG (session) -----------------
function cart_items() { return $_SESSION['cart'] ?? []; }
function cart_count() { return array_sum(array_map('intval', cart_items())); }
function cart_add($id, $qty = 1) {
    $id = (int)$id;
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + (int)$qty;
}
function cart_set($id, $qty) {
    $id = (int)$id; $qty = (int)$qty;
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    if ($qty < 1) unset($_SESSION['cart'][$id]);
    else $_SESSION['cart'][$id] = $qty;
}
function cart_remove($id) { unset($_SESSION['cart'][(int)$id]); }
function cart_clear() { $_SESSION['cart'] = []; }

// ----------------- PRODUK -----------------
function get_product($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM barang WHERE id_barang = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Daftar kategori (untuk menu/filter)
function get_categories($conn) {
    $out = [];
    $res = $conn->query("SELECT DISTINCT kategori FROM barang ORDER BY kategori");
    while ($r = $res->fetch_assoc()) $out[] = $r['kategori'];
    return $out;
}

/**
 * Pencarian & filter produk (aman dengan prepared statement).
 * $opts: q, kategori, sort, page, perpage
 */
function search_products($conn, $opts = []) {
    $q        = trim($opts['q'] ?? '');
    $kategori = trim($opts['kategori'] ?? '');
    $sort     = $opts['sort'] ?? '';
    $page     = max(1, (int)($opts['page'] ?? 1));
    $perpage  = max(1, (int)($opts['perpage'] ?? 8));

    $where = []; $params = []; $types = '';
    if ($q !== '') {
        $where[] = "(nama_barang LIKE ? OR merk LIKE ?)";
        $like = "%$q%"; $params[] = $like; $params[] = $like; $types .= 'ss';
    }
    if ($kategori !== '') { $where[] = "kategori = ?"; $params[] = $kategori; $types .= 's'; }
    $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

    switch ($sort) {
        case 'termurah': $orderSql = 'ORDER BY harga ASC'; break;
        case 'termahal': $orderSql = 'ORDER BY harga DESC'; break;
        case 'nama':     $orderSql = 'ORDER BY nama_barang ASC'; break;
        default:         $orderSql = 'ORDER BY id_barang ASC';
    }

    $countSql = "SELECT COUNT(*) AS total FROM barang $whereSql";
    if ($params) {
        $stmt = $conn->prepare($countSql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $total = (int)$stmt->get_result()->fetch_assoc()['total'];
    } else {
        $total = (int)$conn->query($countSql)->fetch_assoc()['total'];
    }
    $pages = max(1, (int)ceil($total / $perpage));
    $page  = min($page, $pages);
    $offset = ($page - 1) * $perpage;

    $sql = "SELECT * FROM barang $whereSql $orderSql LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $p2 = array_merge($params, [$perpage, $offset]);
    $stmt->bind_param($types . 'ii', ...$p2);
    $stmt->execute();
    $res = $stmt->get_result();
    $items = [];
    while ($r = $res->fetch_assoc()) $items[] = $r;

    return ['items' => $items, 'total' => $total, 'pages' => $pages, 'page' => $page];
}

/**
 * URL gambar produk. Pakai file asli di assets/ bila ada,
 * jika tidak pakai gambar SVG bawaan (placeholder.php).
 */
function product_image($row) {
    $rel = $row['gambar_url'] ?? '';
    if ($rel && is_file(__DIR__ . '/../assets/' . $rel)) {
        return url('assets/' . $rel);
    }
    return url('assets/placeholder.php?cat=' . urlencode($row['kategori'])
        . '&name=' . urlencode($row['nama_barang']) . '&merk=' . urlencode($row['merk']));
}

// ----------------- PESANAN / STRUK -----------------

// label & warna badge status pesanan
function status_label($s) {
    $map = [
        'menunggu'     => ['Menunggu Konfirmasi', 'warning'],
        'dikonfirmasi' => ['Lunas / Selesai', 'success'],
        'dibatalkan'   => ['Dibatalkan', 'danger'],
    ];
    return $map[$s] ?? [ucfirst($s), 'secondary'];
}

// nomor struk yang rapi
function kode_struk($id_pesanan) {
    return 'TRX' . str_pad($id_pesanan, 5, '0', STR_PAD_LEFT);
}

// ambil 1 pesanan
function get_pesanan($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM pesanan WHERE id_pesanan = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// ambil item-item (baris transaksi) dalam sebuah pesanan
function get_pesanan_items($conn, $id_pesanan) {
    $stmt = $conn->prepare(
        "SELECT t.*, b.nama_barang, b.merk, b.kategori, b.harga
         FROM transaksi t JOIN barang b ON t.id_barang = b.id_barang
         WHERE t.id_pesanan = ?");
    $stmt->bind_param('i', $id_pesanan);
    $stmt->execute();
    $res = $stmt->get_result();
    $out = [];
    while ($r = $res->fetch_assoc()) $out[] = $r;
    return $out;
}
