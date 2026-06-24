<?php
/**
 * assets/placeholder.php
 * Generator gambar produk berbasis SVG (fallback bila gambar asli belum ada).
 * Menghasilkan ilustrasi bertema kategori dengan palet warna Harmoni.
 *
 * Parameter: id, cat (kategori), name (nama_barang), merk
 */
header('Content-Type: image/svg+xml; charset=utf-8');
header('Cache-Control: public, max-age=86400');

$id   = (int)($_GET['id'] ?? 0);
$cat  = $_GET['cat']  ?? 'Musik';
$name = $_GET['name'] ?? '';
$merk = $_GET['merk'] ?? '';

function esc($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// Palet gradient per kategori (selaras tema Sweelee: merah, dark, abu).
$palettes = [
    'Gitar'          => ['#2b2d42', '#8d99ae'],
    'Bass'           => ['#1d2b3a', '#3a6073'],
    'Drum'           => ['#3a1c1c', '#a23b3b'],
    'Keyboard'       => ['#1a1a2e', '#533483'],
    'Amp dan Mixer'  => ['#222222', '#444444'],
];
$pal = $palettes[$cat] ?? ['#2b2d42', '#8d99ae'];

// Silhouette path per kategori (viewBox 0 0 100 100, digambar di tengah).
$icons = [
    'Gitar' => '<path d="M50 8c-3 0-5 2-5 5 0 2 1 3 1 5l-1 18c-9 3-15 11-15 21 0 13 11 22 24 22s24-9 24-22c0-10-6-18-15-21l-1-18c0-2 1-3 1-5 0-3-2-5-5-5h-8zm4 44a10 10 0 110 20 10 10 0 010-20z"/>',
    'Bass' => '<path d="M50 8c-3 0-5 2-5 5 0 2 1 3 1 5l-1 18c-9 3-15 11-15 21 0 13 11 22 24 22s24-9 24-22c0-10-6-18-15-21l-1-18c0-2 1-3 1-5 0-3-2-5-5-5h-8zm4 44a10 10 0 110 20 10 10 0 010-20z"/>',
    'Drum' => '<path d="M50 20c-16 0-29 5-29 12v36c0 7 13 12 29 12s29-5 29-12V32c0-7-13-12-29-12zm0 6c13 0 23 3 23 6s-10 6-23 6-23-3-23-6 10-6 23-6z"/>',
    'Keyboard' => '<path d="M14 38h72a4 4 0 014 4v22a4 4 0 01-4 4H14a4 4 0 01-4-4V42a4 4 0 014-4zm6 4v14h6V42h-6zm14 0v14h6V42h-6zm14 0v14h6V42h-6zm14 0v14h6V42h-6zm14 0v14h6V42h-6z"/>',
    'Amp dan Mixer' => '<path d="M22 14h56a6 6 0 016 6v60a6 6 0 01-6 6H22a6 6 0 01-6-6V20a6 6 0 016-6zm28 16a22 22 0 100 44 22 22 0 000-44zm0 10a12 12 0 110 24 12 12 0 010-24zm22-22a4 4 0 110 8 4 4 0 010-8z"/>',
];
$icon = $icons[$cat] ?? '<circle cx="50" cy="50" r="30"/>';

// Potong nama agar muat.
$display = mb_strlen($name) > 34 ? (mb_substr($name, 0, 33) . '…') : $name;
?>
<svg xmlns="http://www.w3.org/2000/svg" width="600" height="600" viewBox="0 0 600 600" role="img" aria-label="<?= esc($name) ?>">
  <defs>
    <linearGradient id="bg<?= $id ?>" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="<?= esc($pal[0]) ?>"/>
      <stop offset="100%" stop-color="<?= esc($pal[1]) ?>"/>
    </linearGradient>
    <radialGradient id="glow<?= $id ?>" cx="50%" cy="40%" r="60%">
      <stop offset="0%" stop-color="#ffffff" stop-opacity="0.18"/>
      <stop offset="100%" stop-color="#ffffff" stop-opacity="0"/>
    </radialGradient>
  </defs>

  <rect width="600" height="600" fill="url(#bg<?= $id ?>)"/>
  <rect width="600" height="600" fill="url(#glow<?= $id ?>)"/>

  <!-- pola titik halus -->
  <g fill="#ffffff" opacity="0.05">
    <?php for ($y = 40; $y < 600; $y += 48): for ($x = 40; $x < 600; $x += 48): ?>
      <circle cx="<?= $x ?>" cy="<?= $y ?>" r="2"/>
    <?php endfor; endfor; ?>
  </g>

  <!-- ikon instrumen -->
  <g transform="translate(150,120) scale(3)" fill="#ffffff" opacity="0.92">
    <?= $icon ?>
  </g>

  <!-- label merk -->
  <text x="300" y="500" text-anchor="middle" font-family="Segoe UI, Arial, sans-serif"
        font-size="34" font-weight="700" fill="#ffffff"><?= esc(strtoupper($merk)) ?></text>
  <!-- nama produk -->
  <text x="300" y="540" text-anchor="middle" font-family="Segoe UI, Arial, sans-serif"
        font-size="20" fill="#ffffff" opacity="0.85"><?= esc($display) ?></text>

  <!-- watermark toko -->
  <text x="300" y="575" text-anchor="middle" font-family="Segoe UI, Arial, sans-serif"
        font-size="14" letter-spacing="3" fill="#ffffff" opacity="0.5">GNF MUSIC STORE · <?= esc(strtoupper($cat)) ?></text>
</svg>
