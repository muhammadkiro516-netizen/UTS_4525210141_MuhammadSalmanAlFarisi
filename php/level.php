<?php

// Enum dengan method: tiap level punya batas poin dan persentase diskon sendiri.
enum LevelMember: string
{
    case Reguler = "Reguler";
    case Silver = "Silver";
    case Gold = "Gold";

    public function minPoin(): int
    {
        return match ($this) {
            self::Reguler => 0,
            self::Silver => 20,
            self::Gold => 50,
        };
    }

    public function diskon(): float
    {
        return match ($this) {
            self::Reguler => 0,
            self::Silver => 0.03,
            self::Gold => 0.05,
        };
    }

    // Cari level tertinggi yang masih terjangkau oleh jumlah poin.
    public static function dariPoin(int $poin): LevelMember
    {
        $hasil = self::Reguler;
        foreach (self::cases() as $level) {
            if ($poin >= $level->minPoin()) {
                $hasil = $level;
            }
        }
        return $hasil;
    }
}
