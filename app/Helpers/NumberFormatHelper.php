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
            return 0.0;
        }

        if (is_int($value) || is_float($value)) {
            return (float) $value;
        }

        $normalized = trim((string) $value);
        if ($normalized === '') {
            return 0.0;
        }

        // Clean characters except digits, dots, commas, and minus
        $normalized = preg_replace('/[^0-9.,\-]/', '', $normalized) ?? '';
        if ($normalized === '' || $normalized === '-') {
            return 0.0;
        }

        $hasComma = str_contains($normalized, ',');
        $hasDot = str_contains($normalized, '.');

        if ($hasComma && $hasDot) {
            $lastComma = strrpos($normalized, ',');
            $lastDot = strrpos($normalized, '.');
            if ($lastComma > $lastDot) {
                // Indonesian format: 1.250.000,50 -> dot is thousands, comma is decimal
                $normalized = str_replace('.', '', $normalized);
                $normalized = str_replace(',', '.', $normalized);
            } else {
                // US format: 1,250,000.50 -> comma is thousands, dot is decimal
                $normalized = str_replace(',', '', $normalized);
            }
        } elseif ($hasComma) {
            // Indonesian format decimal: 98,57 or 595,00
            if (substr_count($normalized, ',') > 1) {
                $normalized = str_replace(',', '', $normalized);
            } else {
                $normalized = str_replace(',', '.', $normalized);
            }
        } elseif ($hasDot) {
            // Multiple dots means thousand separators: 10.000.000
            if (substr_count($normalized, '.') > 1) {
                $normalized = str_replace('.', '', $normalized);
            }
            // Single dot: standard float from DB/JS (e.g. "595.00", "85.00", "83.57", "465.00")
            // Keep the dot as decimal point.
        }

        return is_numeric($normalized) ? (float) $normalized : 0.0;
    }
}

