<?php

require_once "level.php";
require_once "status.php";

interface BisaDapatDiskon
{
    public function hitungDiskon($total);
}

abstract class Pelanggan implements BisaDapatDiskon
{
    const NO_HP_DEFAULT = "-";
    public static $jumlahPelanggan = 0;

    private $id;
    private $nama;
    private $noHp;
    private $riwayat = [];

    public function __construct($nama, $noHp = self::NO_HP_DEFAULT)
    {
        self::$jumlahPelanggan++;
        $this->id = "PLG-" . str_pad(self::$jumlahPelanggan, 3, "0", STR_PAD_LEFT);
        $this->setNama($nama);
        $this->setNoHp($noHp);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getNama()
    {
        return $this->nama;
    }

    public function getNoHp()
    {
        return $this->noHp;
    }

    public function setNama($nama)
    {
        if (trim($nama) === "") {
            echo "Nama pelanggan tidak boleh kosong, dipakai nama 'Tanpa Nama'\n";
            $this->nama = "Tanpa Nama";
        } else {
            $this->nama = trim($nama);
        }
    }

    // Nomor HP boleh kosong ("-"), tapi kalau diisi harus angka 10 sampai 13 digit.
    public function setNoHp($noHp)
    {
        if ($noHp === self::NO_HP_DEFAULT) {
            $this->noHp = $noHp;
        } elseif (preg_match("/^[0-9]{10,13}$/", $noHp)) {
            $this->noHp = $noHp;
        } else {
            echo "No HP '" . $noHp . "' tidak valid untuk " . $this->nama . ", diganti '-'\n";
            $this->noHp = self::NO_HP_DEFAULT;
        }
    }

    // Dipanggil dari constructor Transaksi, jadi transaksi yang gagal bayar pun tetap tercatat.
    public function tambahRiwayat($transaksi)
    {
        $this->riwayat[] = $transaksi;
    }

    public function getRiwayat()
    {
        return $this->riwayat;
    }

    public function getTotalBelanja()
    {
        $total = 0;
        foreach ($this->riwayat as $transaksi) {
            if ($transaksi->getStatus() === StatusBayar::Lunas) {
                $total += $transaksi->getTotalBayar();
            }
        }
        return $total;
    }

    public function jumlahTransaksiBelumBayar()
    {
        $jumlah = 0;
        foreach ($this->riwayat as $transaksi) {
            if ($transaksi->getStatus() === StatusBayar::Belum) {
                $jumlah++;
            }
        }
        return $jumlah;
    }

    abstract public function jenis();

    // Pelanggan umum tidak dapat poin, member dapat. Tiap subclass punya aturan sendiri.
    abstract public function tambahPoin($totalBayar);

    public function tampilkanProfil()
    {
        echo "ID      : " . $this->id . "\n";
        echo "Nama    : " . $this->nama . "\n";
        echo "No HP   : " . $this->noHp . "\n";
        echo "Jenis   : " . $this->jenis() . "\n";
    }

    public function cetakRiwayat()
    {
        echo "Riwayat " . $this->nama . " (" . count($this->riwayat) . " transaksi)\n";
        if (count($this->riwayat) === 0) {
            echo "  belum pernah belanja\n";
            return;
        }
        foreach ($this->riwayat as $transaksi) {
            echo "  " . $transaksi->ringkas() . "\n";
        }
        echo "  Total belanja lunas : " . Transaksi::rupiah($this->getTotalBelanja()) . "\n";
    }
}

class PelangganUmum extends Pelanggan
{
    public function jenis()
    {
        return "Pelanggan Umum";
    }

    public function hitungDiskon($total)
    {
        return 0;
    }

    public function tambahPoin($totalBayar)
    {
        return 0;
    }
}

class PelangganMember extends Pelanggan
{
    const BELANJA_PER_POIN = 10000;

    private $poin = 0;
    private $level;

    public function __construct($nama, $noHp = self::NO_HP_DEFAULT)
    {
        parent::__construct($nama, $noHp);
        $this->level = LevelMember::Reguler;
    }

    public function getPoin()
    {
        return $this->poin;
    }

    public function getLevel()
    {
        return $this->level;
    }

    public function jenis()
    {
        return "Member " . $this->level->value;
    }

    public function hitungDiskon($total)
    {
        return $total * $this->level->diskon();
    }

    // Tiap Rp 10.000 belanja jadi 1 poin. Setelah poin masuk, level dicek ulang.
    public function tambahPoin($totalBayar)
    {
        $poinBaru = intdiv((int) $totalBayar, self::BELANJA_PER_POIN);
        $this->poin += $poinBaru;

        $levelBaru = LevelMember::dariPoin($this->poin);
        if ($levelBaru !== $this->level) {
            $this->level = $levelBaru;
            echo "** " . $this->getNama() . " naik level jadi " . $levelBaru->value
                . " (diskon " . ($levelBaru->diskon() * 100) . "%) **\n";
        }
        return $poinBaru;
    }

    public function tampilkanProfil()
    {
        parent::tampilkanProfil();
        echo "Poin    : " . $this->poin . "\n";
        echo "Diskon  : " . ($this->level->diskon() * 100) . "%\n";
    }
}
