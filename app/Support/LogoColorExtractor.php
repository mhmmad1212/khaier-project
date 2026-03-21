<?php

namespace App\Support;

class LogoColorExtractor
{
    public static function suggestFromPath(?string $path): array
    {
        if (! $path || ! is_file($path)) {
            return [
                'primary_color' => null,
                'secondary_color' => null,
                'button_color' => null,
            ];
        }

        $imageData = @file_get_contents($path);
        if ($imageData === false) {
            return [
                'primary_color' => null,
                'secondary_color' => null,
                'button_color' => null,
            ];
        }

        $img = @imagecreatefromstring($imageData);
        if (! $img) {
            return [
                'primary_color' => null,
                'secondary_color' => null,
                'button_color' => null,
            ];
        }

        $width = imagesx($img);
        $height = imagesy($img);

        $stepX = max(1, (int) floor($width / 80));
        $stepY = max(1, (int) floor($height / 80));

        $palette = [];

        for ($y = 0; $y < $height; $y += $stepY) {
            for ($x = 0; $x < $width; $x += $stepX) {
                $rgba = imagecolorat($img, $x, $y);

                $a = ($rgba & 0x7F000000) >> 24;
                if ($a >= 120) {
                    continue;
                }

                $r = ($rgba >> 16) & 0xFF;
                $g = ($rgba >> 8) & 0xFF;
                $b = $rgba & 0xFF;

                // تجاهل الأبيض/الرمادي الفاتح جدًا والأسود القريب
                if (($r > 245 && $g > 245 && $b > 245) || ($r < 12 && $g < 12 && $b < 12)) {
                    continue;
                }

                // تقليل عدد الدرجات لتجميع الألوان المتقاربة
                $rQ = (int) (round($r / 24) * 24);
                $gQ = (int) (round($g / 24) * 24);
                $bQ = (int) (round($b / 24) * 24);

                $rQ = max(0, min(255, $rQ));
                $gQ = max(0, min(255, $gQ));
                $bQ = max(0, min(255, $bQ));

                $key = sprintf('%02X%02X%02X', $rQ, $gQ, $bQ);
                $palette[$key] = ($palette[$key] ?? 0) + 1;
            }
        }

        imagedestroy($img);

        if (empty($palette)) {
            return [
                'primary_color' => '#0F766E',
                'secondary_color' => '#14B8A6',
                'button_color' => '#0F766E',
            ];
        }

        arsort($palette);
        $top = array_slice(array_keys($palette), 0, 6);

        $colors = array_map(fn ($hex) => '#' . $hex, $top);

        $primary = $colors[0] ?? '#0F766E';
        $secondary = $colors[1] ?? $primary;
        $button = $colors[2] ?? $primary;

        return [
            'primary_color' => $primary,
            'secondary_color' => $secondary,
            'button_color' => $button,
        ];
    }
}
