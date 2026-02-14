<?php

namespace App\Services;

class GeoService
{
    public function calculateCenter(array $coordinates): array
    {
        $latSum = 0.0;
        $lngSum = 0.0;
        $count = max(count($coordinates), 1);

        foreach ($coordinates as $coord) {
            $latSum += (float) $coord[0];
            $lngSum += (float) $coord[1];
        }

        return [
            'lat' => round($latSum / $count, 7),
            'lng' => round($lngSum / $count, 7),
        ];
    }

    public function calculateArea(array $coordinates): float
    {
        $n = count($coordinates);
        if ($n < 3) {
            return 0.0;
        }

        $area = 0.0;

        for ($i = 0; $i < $n; $i++) {
            $j = ($i + 1) % $n;
            $area += (float) $coordinates[$i][0] * (float) $coordinates[$j][1];
            $area -= (float) $coordinates[$j][0] * (float) $coordinates[$i][1];
        }

        $area = abs($area) / 2;

        return round($area * 111 * 111, 2);
    }
}

