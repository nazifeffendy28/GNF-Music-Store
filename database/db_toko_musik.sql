-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 24 Jun 2026 pada 12.38
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_toko_musik`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `barang`
--

CREATE TABLE `barang` (
  `id_barang` int(11) NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `nama_barang` varchar(150) NOT NULL,
  `merk` varchar(50) NOT NULL,
  `harga` int(11) NOT NULL,
  `stok` int(11) DEFAULT 0,
  `gambar_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `barang`
--

INSERT INTO `barang` (`id_barang`, `kategori`, `nama_barang`, `merk`, `harga`, `stok`, `gambar_url`) VALUES
(1, 'Gitar', 'Fender Player Stratocaster Maple', 'Fender', 13500000, 5, 'images/gitar-fender-strat.jpg'),
(2, 'Gitar', 'Gibson Les Paul Standard 60s', 'Gibson', 38000000, 2, 'images/gitar-gibson-lp.jpg'),
(3, 'Gitar', 'Ibanez RG421EX Electric Guitar', 'Ibanez', 5200000, 7, 'images/gitar-ibanez-rg.jpg'),
(4, 'Gitar', 'Yamaha APX600 Acoustic Electric', 'Yamaha', 3100000, 10, 'images/gitar-yamaha-apx.jpg'),
(5, 'Gitar', 'Epiphone Casino Archtop', 'Epiphone', 9800000, 3, 'images/gitar-epi-casino.jpg'),
(6, 'Bass', 'Fender Player Jazz Bass', 'Fender', 14200000, 4, 'images/bass-fender-jbass.jpg'),
(7, 'Bass', 'Ibanez SR300E Electric Bass', 'Ibanez', 4800000, 6, 'images/bass-ibanez-sr.jpg'),
(8, 'Bass', 'Music Man StingRay Special', 'Music Man', 32500000, 1, 'images/bass-musicman.jpg'),
(9, 'Bass', 'Yamaha TRBX304 Electric Bass', 'Yamaha', 4300000, 5, 'images/bass-yamaha-trbx.jpg'),
(10, 'Bass', 'Squier Classic Vibe 60s Precision', 'Squier', 6700000, 4, 'images/bass-squier-pbass.jpg'),
(11, 'Drum', 'Yamaha DTX402K Electronic Drum', 'Yamaha', 6200000, 3, 'images/drum-yamaha-dtx.jpg'),
(12, 'Drum', 'Roland TD-07KV V-Drums', 'Roland', 15500000, 2, 'images/drum-roland-td07.jpg'),
(13, 'Drum', 'Tama Imperialstar Acoustic Drum', 'Tama', 9500000, 2, 'images/drum-tama-imperial.jpg'),
(14, 'Drum', 'Pearl Export EXX725 Acoustic', 'Pearl', 11000000, 3, 'images/drum-pearl-export.jpg'),
(15, 'Drum', 'Alesis Nitro Max Electronic Kit', 'Alesis', 7200000, 5, 'images/drum-alesis-nitro.jpg'),
(16, 'Keyboard', 'Roland XPS-10 Synthesizer', 'Roland', 7500000, 4, 'images/keyboard-roland-xps.jpg'),
(17, 'Keyboard', 'Yamaha PSR-E373 Portable', 'Yamaha', 3400000, 12, 'images/keyboard-yamaha-psr.jpg'),
(18, 'Keyboard', 'Korg Kross 2 61 Workstation', 'Korg', 11200000, 2, 'images/keyboard-korg-kross.jpg'),
(19, 'Keyboard', 'Casio Privia PX-S1100 Piano', 'Casio', 9300000, 3, 'images/keyboard-casio-privia.jpg'),
(20, 'Keyboard', 'Novation Launchkey 49 MK3 MIDI', 'Novation', 3800000, 6, 'images/keyboard-novation-midi.jpg'),
(21, 'Amp dan Mixer', 'Marshall MG15G Guitar Amp', 'Marshall', 1950000, 8, 'images/amp-marshall-mg15.jpg'),
(22, 'Amp dan Mixer', 'BOSS Katana 50 MKII Combo', 'BOSS', 4500000, 5, 'images/amp-boss-katana.jpg'),
(23, 'Amp dan Mixer', 'Yamaha MG10XU 10-Input Mixer', 'Yamaha', 3900000, 6, 'images/mixer-yamaha-mg10.jpg'),
(24, 'Amp dan Mixer', 'Fender Champion 40 Guitar Amp', 'Fender', 3400000, 4, 'images/amp-fender-champ.jpg'),
(25, 'Amp dan Mixer', 'Behringer Xenyx Q802USB Mixer', 'Behringer', 1650000, 7, 'images/mixer-behringer-q802.jpg');

-- --------------------------------------------------------

--
-- Struktur dari tabel `karyawan`
--

CREATE TABLE `karyawan` (
  `id_karyawan` int(11) NOT NULL,
  `nama_karyawan` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) DEFAULT 'admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `karyawan`
--

INSERT INTO `karyawan` (`id_karyawan`, `nama_karyawan`, `username`, `password`, `role`) VALUES
(1, 'muhammad nazif hamza', 'nazif', 'nazif123', 'admin'),
(2, 'ahmad fauzan noor', 'jan62', 'fauzan123', 'admin'),
(3, 'iqbal ghani effendy', 'ghani', 'iqbal123', 'admin');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `total_bayar` int(11) NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp(),
  `status_pembayaran` varchar(20) DEFAULT 'pending',
  `id_karyawan` int(11) DEFAULT NULL,
  `id_pesanan` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pesanan` (header pesanan / struk)
--

CREATE TABLE `pesanan` (
  `id_pesanan` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp(),
  `metode_bayar` varchar(30) NOT NULL,
  `total_bayar` int(11) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'menunggu',
  `id_karyawan` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `no_telepon` varchar(15) DEFAULT NULL,
  `alamat` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id_user`, `nama_lengkap`, `email`, `password`, `no_telepon`, `alamat`) VALUES
(1, 'user', 'user@gmail.com', 'user123', '081234567890', 'petukangan');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id_barang`);

--
-- Indeks untuk tabel `karyawan`
--
ALTER TABLE `karyawan`
  ADD PRIMARY KEY (`id_karyawan`);

--
-- Indeks untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`);

--
-- Indeks untuk tabel `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id_pesanan`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `barang`
--
ALTER TABLE `barang`
  MODIFY `id_barang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT untuk tabel `karyawan`
--
ALTER TABLE `karyawan`
  MODIFY `id_karyawan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id_pesanan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
