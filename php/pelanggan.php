<?php

class Pelanggan
{
    // Materi 1: enkapsulasi; data pelanggan hanya bisa diakses dari dalam kelas.
    private $nama;
    private $noHp;

    // Materi 2: konstruktor mengisi data saat objek Pelanggan dibuat.
    // Nomor HP memakai nilai default "-" jika tidak diberikan.
    public function __construct($nama, $noHp = "-")
    {
        $this->nama = $nama;
        $this->noHp = $noHp;
    }

    // Materi 1: getter menyediakan akses untuk membaca properti private.
    public function getNama()
    {
        return $this->nama;
    }

    // Materi 1: getter untuk membaca nomor HP tanpa mengakses properti langsung.
    public function getNoHp()
    {
        return $this->noHp;
    }
}
