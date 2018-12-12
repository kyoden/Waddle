<?php

namespace Waddle;

abstract class Parser
{
    /**
     * @var string
     *
     * @return Activity
     */
    abstract public function parse(string $file): Activity;

    /**
     * Check if file is available.
     *
     * @param $file
     *
     * @throws \Exception
     */
    protected function checkForFile(string $file): void
    {
        if (!is_file($file)) {
            throw new \Exception("Could not load file: {$file}");
        }
    }

    /**
     * Calculate the distance in KM, between two lat/lon points.
     *
     * @param float $fromLat
     * @param float $toLat
     * @param float $fromLon
     * @param float $toLon
     * @param float $earthRadius
     *
     * @return float
     */
    protected function calculateDistanceBetweenLatLon(
        float $fromLat,
        float $toLat,
        float $fromLon,
        float $toLon,
        float $earthRadius = 6373
    ): float {
        $latDelta = deg2rad($toLat - $fromLat);
        $lonDelta = deg2rad($toLon - $fromLon);

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos(deg2rad($fromLat)) * cos(deg2rad($toLat)) * pow(sin($lonDelta / 2), 2)));

        $distance = $angle * $earthRadius;

        return Converter::convertKilometresToMetres($distance);
    }
}
