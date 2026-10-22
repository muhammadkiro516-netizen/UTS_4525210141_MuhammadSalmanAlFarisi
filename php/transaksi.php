<?php

require_once "status.php";

class Transaksi
{
    private $pelanggan;
    private $obat;
    private $jumlah;
    private $status;

    public function __construct($pelanggan, $obat, $jumlah)
    {
        $this->pelanggan = $pelanggan;
        $this->obat = $obat;
        $this->jumlah = $jumlah;
        $this->status = StatusBayar::Belum;
    }

    public function bayar()
    {
        if ($this->obat->kurangiStok($this->jumlah)) {
            $this->status = StatusBayar::Lunas;
        } else {
            echo "Stok " . $this->obat->getNama() . " tidak cukup\n";
        }
    }

    public function cetak()
    {
        echo "Pembeli : " . $this->pelanggan->getNama() . "\n";
        echo "Obat    : " . $this->obat->getNama() . " x" . $this->jumlah . "\n";
        echo "Total   : Rp " . number_format($this->obat->hitungHarga($this->jumlah), 0, ",", ".") . "\n";
        echo "Status  : " . $this->status->value . "\n\n";
    }
}
