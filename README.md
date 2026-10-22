# UTS PBO - Apotek

Mata kuliah : Pemrograman Berbasis Objek, kelas A
PJ : (isi sendiri)
Tema : Apotek
Bahasa : PHP (java nya ga dikerjain, boleh pilih salah satu)
Nama / NPM : (isi sendiri)

## Anggota

| Nama | NPM | Kerjaan |
| --- | --- | --- |
| (isi) | (isi) | (isi) |

## Soal ini tentang apa

Program kecil buat jualan obat di apotek. Ada daftar obat, ada pembeli, terus ada transaksinya. Kalau stok cukup transaksi jadi lunas, kalau ga cukup tetap "Belum Bayar" dan stok ga berkurang. Sengaja dibuat pendek biar gampang dijelasin pas tanya jawab.

Fitur:
1. lihat daftar obat + jenisnya + stok
2. beli obat (harga dihitung plus PPN 11%)
3. obat keras kena biaya resep Rp 5.000
4. cek status bayar

Cara jalanin: buka folder php, terus `php main.php`

## Isi file

### status.php
Isinya cuma enum StatusBayar (Lunas sama Belum). Dulu sempet mau pakai string biasa tapi di pertemuan 6 kan udah ada enum jadi pakai itu aja.

### obat.php
Paling banyak isinya. Ada interface BisaDijual, abstract class Obat, terus ObatBebas dan ObatKeras yang extends Obat.
- nama, harga, stok dibuat private, aksesnya lewat getter (pertemuan 2)
- constructor + static $jumlahObat + const PPN (pertemuan 3)
- ObatBebas/ObatKeras pewarisan, ObatKeras override hitungHarga pakai parent:: (pertemuan 4)
- di main, semua obat dimasukin satu array lalu dipanggil jenis() , hasilnya beda tiap objek (pertemuan 5 polimorfisme)
- abstract jenis(), interface, enum (pertemuan 6)

### pelanggan.php
Class biasa, nama sama no hp. No hp boleh ga diisi (default "-").

### transaksi.php
Nyimpen objek Pelanggan sama objek Obat di dalamnya, itu relasi objek (pertemuan 7). Ada bayar() sama cetak().

### main.php
File yang dijalanin. Bikin 3 obat, 2 pembeli, 3 transaksi. Transaksi ketiga sengaja beli 10 amoxicillin padahal stok tinggal 3 setelah transaksi kedua, buat nunjukin kalau stok kurang statusnya tetap Belum Bayar.

## Output

(tempel screenshot hasil `php main.php` disini)

## Github

Pertemuan 1 sampai 7 sama kayak di repo, tinggal push folder ini ke repo masing-masing sebelum pulang.

## Kesimpulan

Ternyata bikin sistem kecil aja udah kepake semua materi dari pertemuan 1-7. Yang paling kerasa gunanya itu abstract class sama polimorfisme, soalnya kalau mau nambah jenis obat baru (misal obat herbal) tinggal bikin class baru extends Obat, main.php ga perlu diubah banyak.
