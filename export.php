<?php
/**
 * export.php — mengunduh struk dalam format CSV, XLSX, atau PDF.
 * Pemakaian: export.php?id={id_pesanan}&format={csv|xlsx|pdf}
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/exporters.php';

$id = (int)($_GET['id'] ?? 0);
$format = strtolower($_GET['format'] ?? 'csv');

$pesanan = get_pesanan($conn, $id);
if (!$pesanan) { http_response_code(404); exit('Pesanan tidak ditemukan.'); }

// hak akses: pemilik atau admin
$isOwner = is_logged_in() && (int)current_user()['id_user'] === (int)$pesanan['id_user'];
if (!$isOwner && !is_admin()) { http_response_code(403); exit('Akses ditolak.'); }

$items = get_pesanan_items($conn, $id);
$ustmt = $conn->prepare("SELECT nama_lengkap, no_telepon FROM users WHERE id_user = ?");
$ustmt->bind_param('i', $pesanan['id_user']); $ustmt->execute();
$pembeli = $ustmt->get_result()->fetch_assoc();

$kode = kode_struk($id);
[$statusText] = status_label($pesanan['status']);
$tanggal = date('d/m/Y H:i', strtotime($pesanan['tanggal']));
$namaFile = 'struk_' . $kode;

// =================== CSV ===================
if ($format === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $namaFile . '.csv"');
    echo "\xEF\xBB\xBF"; // BOM agar Excel membaca UTF-8
    $out = fopen('php://output', 'w');
    fputcsv($out, [STORE_NAME . ' - Struk Pembelian']);
    fputcsv($out, ['No. Struk', $kode]);
    fputcsv($out, ['Tanggal', $tanggal]);
    fputcsv($out, ['Pembeli', $pembeli['nama_lengkap']]);
    fputcsv($out, ['Metode', $pesanan['metode_bayar']]);
    fputcsv($out, ['Status', $statusText]);
    fputcsv($out, []);
    fputcsv($out, ['No', 'Produk', 'Merk', 'Harga', 'Jumlah', 'Subtotal']);
    $no = 1;
    foreach ($items as $it) {
        fputcsv($out, [$no++, $it['nama_barang'], $it['merk'], $it['harga'], $it['jumlah'], $it['total_bayar']]);
    }
    fputcsv($out, ['', '', '', '', 'TOTAL', $pesanan['total_bayar']]);
    fclose($out);
    exit;
}

// =================== XLSX ===================
if ($format === 'xlsx') {
    $rows = [];
    $rows[] = [STORE_NAME . ' - Struk Pembelian'];
    $rows[] = ['No. Struk', $kode];
    $rows[] = ['Tanggal', $tanggal];
    $rows[] = ['Pembeli', $pembeli['nama_lengkap']];
    $rows[] = ['Metode', $pesanan['metode_bayar']];
    $rows[] = ['Status', $statusText];
    $rows[] = [];
    $rows[] = ['No', 'Produk', 'Merk', 'Harga', 'Jumlah', 'Subtotal'];
    $no = 1;
    foreach ($items as $it) {
        $rows[] = [$no++, $it['nama_barang'], $it['merk'], (int)$it['harga'], (int)$it['jumlah'], (int)$it['total_bayar']];
    }
    $rows[] = ['', '', '', '', 'TOTAL', (int)$pesanan['total_bayar']];

    $data = build_xlsx($rows);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $namaFile . '.xlsx"');
    header('Content-Length: ' . strlen($data));
    echo $data;
    exit;
}

// =================== PDF ===================
if ($format === 'pdf') {
    $L = [];
    $L[] = str_pad(strtoupper(STORE_NAME) . ' - TOKO ALAT MUSIK', 50, ' ', STR_PAD_BOTH);
    $L[] = str_pad('STRUK PEMBELIAN', 50, ' ', STR_PAD_BOTH);
    $L[] = str_repeat('=', 56);
    $L[] = 'No. Struk : ' . $kode;
    $L[] = 'Tanggal   : ' . $tanggal;
    $L[] = 'Pembeli   : ' . $pembeli['nama_lengkap'];
    $L[] = 'Metode    : ' . $pesanan['metode_bayar'];
    $L[] = 'Status    : ' . $statusText;
    $L[] = str_repeat('-', 56);
    $L[] = sprintf('%-26s %4s %11s %11s', 'Produk', 'Qty', 'Harga', 'Subtotal');
    $L[] = str_repeat('-', 56);
    foreach ($items as $it) {
        $nama = $it['nama_barang'];
        if (strlen($nama) > 26) $nama = substr($nama, 0, 25) . '.';
        $L[] = sprintf('%-26s %4d %11s %11s',
            $nama, (int)$it['jumlah'],
            number_format($it['harga'], 0, ',', '.'),
            number_format($it['total_bayar'], 0, ',', '.'));
    }
    $L[] = str_repeat('-', 56);
    $L[] = sprintf('%-31s %23s', 'TOTAL', 'Rp ' . number_format($pesanan['total_bayar'], 0, ',', '.'));
    $L[] = str_repeat('=', 56);
    $L[] = '';
    $L[] = 'Terima kasih telah berbelanja di ' . STORE_NAME;

    $data = build_pdf($L);
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $namaFile . '.pdf"');
    header('Content-Length: ' . strlen($data));
    echo $data;
    exit;
}

http_response_code(400);
exit('Format tidak dikenali.');
