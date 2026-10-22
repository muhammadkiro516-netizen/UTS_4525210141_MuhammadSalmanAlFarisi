<?php

require_once "obat.php";
require_once "pelanggan.php";
require_once "transaksi.php";

// ---------- 1. Stok obat ----------
$paracetamol = new ObatBebas("Paracetamol", 5000, 50);
$vitaminC = new ObatBebas("Vitamin C", 12000, 30);
$amoxicillin = new ObatKeras("Amoxicillin", 15000, 5);

$daftarObat = [$paracetamol, $vitaminC, $amoxicillin];

echo "=== Daftar Obat ===\n";
foreach ($daftarObat as $obat) {
    echo $obat->getNama() . " | " . $obat->jenis() . " | stok " . $obat->getStok() . "\n";
}
echo "Jumlah obat: " . Obat::$jumlahObat . "\n\n";

// ---------- 2. Pendaftaran pelanggan ----------
echo "=== Pendaftaran Pelanggan ===\n";
$budi = new PelangganMember("Budi", "08123456789");
$sari = new PelangganUmum("Sari");
$rina = new PelangganMember("Rina", "081298765432");
$doni = new PelangganUmum("Doni", "abc123"); // sengaja salah, buat nunjukin validasi

// semua pelanggan masuk satu array, jenis() dan hitungDiskon() beda tiap objek
$daftarPelanggan = [$budi, $sari, $rina, $doni];

echo "\n";
foreach ($daftarPelanggan as $p) {
    $p->tampilkanProfil();
    echo "\n";
}
echo "Jumlah pelanggan: " . Pelanggan::$jumlahPelanggan . "\n\n";

// ---------- 3. Belanja dan pembayaran ----------
echo "=== Transaksi ===\n";

// Budi mulai dari level Reguler (tanpa diskon), lalu naik level karena poin terkumpul
$t1 = new Transaksi($budi, $vitaminC, 10);
$t2 = new Transaksi($budi, $paracetamol, 20);
$t3 = new Transaksi($budi, $vitaminC, 10);   // dibayar saat sudah Silver, kena diskon 3%
$t4 = new Transaksi($budi, $amoxicillin, 10); // stok cuma 5, harus gagal
$t5 = new Transaksi($sari, $amoxicillin, 2);
$t6 = new Transaksi($rina, $amoxicillin, 3);
$t7 = new Transaksi($doni, $paracetamol, 5);
$t8 = new Transaksi($rina, $paracetamol, 5);  // dibiarkan belum dibayar

$t1->bayar();
$t2->bayar();
$t3->bayar();
$t4->bayar();
$t5->bayar();
$t6->bayar();
$t7->bayar();
$t1->bayar(); // coba bayar dua kali, harus ditolak
echo "\n";

$semuaTransaksi = [$t1, $t2, $t3, $t4, $t5, $t6, $t7, $t8];
foreach ($semuaTransaksi as $t) {
    $t->cetak();
}

// ---------- 4. Riwayat dan profil akhir pelanggan ----------
echo "=== Riwayat Pelanggan ===\n";
foreach ($daftarPelanggan as $p) {
    $p->cetakRiwayat();
    echo "\n";
}

echo "=== Profil Akhir ===\n";
foreach ($daftarPelanggan as $p) {
    $p->tampilkanProfil();
    echo "\n";
}

// ---------- 5. Peringkat pelanggan ----------
echo "=== Peringkat Pelanggan (total belanja lunas) ===\n";
usort($daftarPelanggan, function ($a, $b) {
    return $b->getTotalBelanja() <=> $a->getTotalBelanja();
});
foreach ($daftarPelanggan as $i => $p) {
    echo ($i + 1) . ". " . $p->getNama() . " | " . $p->jenis() . " | "
        . Transaksi::rupiah($p->getTotalBelanja()) . "\n";
}

echo "\n=== Sisa Stok ===\n";
foreach ($daftarObat as $obat) {
    echo $obat->getNama() . ": " . $obat->getStok() . "\n";
}
echo "Jumlah transaksi tercatat: " . Transaksi::$jumlahTransaksi . "\n";
