<?php
/** logout.php — keluar pelanggan. */
require_once __DIR__ . '/config.php';
unset($_SESSION['user']);
set_flash('info', 'Kamu telah keluar. Sampai jumpa lagi!');
redirect('index.php');
