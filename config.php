<?php
/**
 * config.php
 * Bootstrap untuk aplikasi Toko Musik "Harmoni".
 * - Memulai session
 * - Membuat koneksi ke database (mysqli)
 * - Memuat helper functions
 *
 * Kredensial dapat dioverride lewat environment variable
 * (DB_HOST, DB_USER, DB_PASS, DB_NAME) agar mudah dipindah-pindah.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ---- Konfigurasi Database ----
define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'db_toko_musik');

// ---- Identitas Toko ----
define('STORE_NAME', 'GNF Music Store');
define('STORE_TAGLINE', 'Toko Alat Musik');

// ---- Base URL helper ----
// Menghitung folder root aplikasi secara dinamis agar link tetap benar
// baik di-deploy di root domain maupun di subfolder (mis. /PemWeb).
if (!defined('BASE_URL')) {
    // Hitung root aplikasi dari lokasi config.php relatif terhadap DOCUMENT_ROOT.
    // Cara ini konsisten apapun script yang menangani request (root, /admin, /api)
    // dan tetap benar walau di-deploy dalam subfolder (mis. htdocs/PemWeb).
    $base = '';
    $docroot = isset($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT']) : false;
    $appdir  = realpath(__DIR__);
    if ($docroot && $appdir && strncmp($appdir, $docroot, strlen($docroot)) === 0) {
        $base = str_replace('\\', '/', substr($appdir, strlen($docroot)));
    } else {
        // Fallback: turunkan dari SCRIPT_NAME, buang segmen /admin atau /api.
        $dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
        $dir = preg_replace('#/(admin|api)$#', '', $dir);
        $base = ($dir === '/' ) ? '' : $dir;
    }
    define('BASE_URL', rtrim($base, '/'));
}

// ---- Koneksi Database (mysqli) ----
mysqli_report(MYSQLI_REPORT_OFF); // tangani error manual, tampilkan pesan ramah
$conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_errno) {
    // Halaman error yang ramah jika DB belum disiapkan.
    http_response_code(500);
    $err = htmlspecialchars($conn->connect_error, ENT_QUOTES);
    echo "<!doctype html><html lang='id'><head><meta charset='utf-8'>
    <title>Koneksi Database Gagal</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    </head><body class='bg-light'>
    <div class='container py-5'>
      <div class='card shadow-sm mx-auto' style='max-width:680px'>
        <div class='card-body p-4'>
          <h4 class='text-danger mb-3'>⚠️ Koneksi ke database gagal</h4>
          <p class='text-muted'>Aplikasi tidak dapat terhubung ke database <code>" . DB_NAME . "</code>.</p>
          <p><strong>Pesan:</strong> <code>$err</code></p>
          <hr>
          <p class='mb-1'>Langkah perbaikan:</p>
          <ol class='small text-muted'>
            <li>Jalankan MySQL/MariaDB (mis. lewat XAMPP).</li>
            <li>Import file <code>database/db_toko_musik.sql</code> melalui phpMyAdmin.</li>
            <li>Periksa kembali kredensial di <code>config.php</code>.</li>
          </ol>
        </div>
      </div>
    </div></body></html>";
    exit;
}
$conn->set_charset('utf8mb4');

require_once __DIR__ . '/includes/functions.php';
