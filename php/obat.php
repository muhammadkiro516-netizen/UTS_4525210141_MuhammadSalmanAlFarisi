<?php

interface BisaDijual
{
    public function hitungHarga($jumlah);
}

abstract class Obat implements BisaDijual
{
    const PPN = 0.11;
    public static $jumlahObat = 0;

    private $nama;
    private $harga;
    private $stok;

    public function __construct($nama, $harga, $stok)
    {
        $this->nama = $nama;
        $this->setHarga($harga);
        $this->stok = $stok;
        self::$jumlahObat++;
    }

    public function getNama()
    {
        return $this->nama;
    }

    public function getHarga()
    {
        return $this->harga;
    }

    public function getStok()
    {
        return $this->stok;
    }

    public function setHarga($harga)
    {
        if ($harga <= 0) {
            echo "Harga harus lebih dari 0\n";
            $this->harga = 1000;
        } else {
            $this->harga = $harga;
        }
    }

    public function kurangiStok($jumlah)
    {
        if ($jumlah > $this->stok) {
            return false;
        }
        $this->stok -= $jumlah;
        return true;
    }

    abstract public function jenis();

    public function hitungHarga($jumlah)
    {
        $total = $this->harga * $jumlah;
        return $total + ($total * self::PPN);
    }
}

class ObatBebas extends Obat
{
    public function jenis()
    {
        return "Obat Bebas";
    }
}

class ObatKeras extends Obat
{
    const BIAYA_RESEP = 5000;

    public function jenis()
    {
        return "Obat Keras (pakai resep)";
    }

    public function hitungHarga($jumlah)
    {
        return parent::hitungHarga($jumlah) + self::BIAYA_RESEP;
    }
}
