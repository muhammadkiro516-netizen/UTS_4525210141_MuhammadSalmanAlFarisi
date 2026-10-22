<?php

require_once "status.php";

class Transaksi
{
    const PREFIX = "TRX-";
    public static $jumlahTransaksi = 0;

    private $nomor;
    private $pelanggan;
    private $obat;
    private $jumlah;
    private $status;
    private $diskonTersimpan = null;

    public function __construct($pelanggan, $obat, $jumlah)
    {
        self::$jumlahTransaksi++;
        $this->nomor = self::PREFIX . str_pad(self::$jumlahTransaksi, 3, "0", STR_PAD_LEFT);
        $this->pelanggan = $pelanggan;
        $this->obat = $obat;
        $this->jumlah = $jumlah;
        $this->status = StatusBayar::Belum;

        $pelanggan->tambahRiwayat($this);
    }

    public static function rupiah($angka)
    {
        return "Rp " . number_format($angka, 0, ",", ".");
    }

    public function getNomor()
    {
        return $this->nomor;
    }

    public function getStatus()
    {
        return $this->status;
    }

    // Harga obat sudah termasuk PPN dan biaya resep (kalau ada), diskon belum.
    public function getSubtotal()
    {
        return $this->obat->hitungHarga($this->jumlah);
    }

    // Sebelum lunas, diskon masih estimasi dari level pelanggan saat ini.
    // Begitu lunas, nilainya dikunci supaya naik level sesudahnya tidak mengubah struk lama.
    public function getDiskon()
    {
        if ($this->diskonTersimpan !== null) {
            return $this->diskonTersimpan;
        }
        return $this->pelanggan->hitungDiskon($this->getSubtotal());
    }

    public function getTotalBayar()
    {
        return $this->getSubtotal() - $this->getDiskon();
    }

    public function bayar()
    {
        if ($this->status === StatusBayar::Lunas) {
            echo $this->nomor . " sudah lunas, tidak bisa dibayar dua kali\n";
            return;
        }

        if ($this->obat->kurangiStok($this->jumlah)) {
            $this->diskonTersimpan = $this->getDiskon();
            $this->status = StatusBayar::Lunas;
            $poin = $this->pelanggan->tambahPoin($this->getTotalBayar());
            if ($poin > 0) {
                echo $this->pelanggan->getNama() . " dapat " . $poin . " poin dari " . $this->nomor . "\n";
            }
        } else {
            echo "Stok " . $this->obat->getNama() . " tidak cukup untuk " . $this->nomor
                . " (diminta " . $this->jumlah . ", sisa " . $this->obat->getStok() . ")\n";
        }
    }

    public function ringkas()
    {
        return $this->nomor . " | " . $this->obat->getNama() . " x" . $this->jumlah
            . " | " . self::rupiah($this->getTotalBayar()) . " | " . $this->status->value;
    }

    public function cetak()
    {
        echo "No      : " . $this->nomor . "\n";
        echo "Pembeli : " . $this->pelanggan->getNama() . " (" . $this->pelanggan->jenis() . ")\n";
        echo "Obat    : " . $this->obat->getNama() . " x" . $this->jumlah . "\n";
        echo "Subtotal: " . self::rupiah($this->getSubtotal()) . "\n";
        echo "Diskon  : " . self::rupiah($this->getDiskon()) . "\n";
        echo "Total   : " . self::rupiah($this->getTotalBayar()) . "\n";
        echo "Status  : " . $this->status->value . "\n\n";
    }
}
