<?php

namespace App\Helpers;

class Kbli2025Helper
{
    private static ?array $namesMap = null;

    private static function loadMap(): array
    {
        if (self::$namesMap === null) {
            $jsonPath = storage_path('app/kbli_2025_names.json');
            if (file_exists($jsonPath)) {
                self::$namesMap = json_decode(file_get_contents($jsonPath), true) ?? [];
            } else {
                self::$namesMap = [];
            }
        }
        return self::$namesMap;
    }

    public static function getName(string $code): string
    {
        $len = (string)strlen($code);
        $map = self::loadMap();

        if (isset($map[$len][$code])) {
            return $map[$len][$code];
        }

        // Fallback labels if not found in dictionary
        if ($len === '2') return "Golongan Pokok " . $code;
        if ($len === '3') return "Golongan " . $code;
        if ($len === '4') return "Subgolongan " . $code;
        if ($len === '5') return "Kelompok " . $code;

        return "KBLI " . $code;
    }
}
