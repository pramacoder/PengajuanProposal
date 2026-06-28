<?php

namespace App\Helpers;

class NumberFormatHelper
{
    public static function format(int|float|string|null $value, int $decimals = 0): string
    {
        if ($value === null || $value === '') {
            $value = 0;
        }

        if (is_string($value)) {
            $value = self::parse($value);
        }

        return number_format((float) $value, $decimals, ',', '.');
    }

    public static function rupiah(int|float|string|null $value, int $decimals = 0): string
    {
        return 'Rp ' . self::format($value, $decimals);
    }

    public static function parse(int|float|string|null $value): float
    {
        if ($value === null) {
            return 0;
        }

        if (is_int($value) || is_float($value)) {
            return (float) $value;
        }

        $normalized = trim((string) $value);
        if ($normalized === '') {
            return 0;
        }

        $normalized = str_replace([' ', '.'], '', $normalized);
        $normalized = str_replace(',', '.', $normalized);
        $normalized = preg_replace('/[^0-9.\-]/', '', $normalized) ?? '0';

        return is_numeric($normalized) ? (float) $normalized : 0;
    }
}

