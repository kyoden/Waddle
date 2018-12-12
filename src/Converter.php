<?php

namespace Waddle;

abstract class Converter
{
    const KILOMETER = 1000;
    const MILES = 1609.34;

    /**
     * Convert metres per second, to miles per hour.
     *
     * @param float $val
     *
     * @return float
     */
    public static function convertMetresPerSecondToMilesPerHour(float $val): float
    {
        return $val * 2.23694;
    }

    /**
     * Convert metres per second, to kilometres per hour.
     *
     * @param float $val
     *
     * @return float
     */
    public static function convertMetresPerSecondToKilometresPerHour(float $val): float
    {
        return $val * 3.6;
    }

    /**
     * Convert metres to kilometres.
     *
     * @param float $val
     *
     * @return float
     */
    public static function convertMetresToKilometres(float $val): float
    {
        return $val / self::KILOMETER;
    }

    /**
     * Convert metres to miles.
     *
     * @param float $val
     *
     * @return float
     */
    public static function convertMetresToMiles(float $val): float
    {
        return $val / self::MILES;
    }

    /**
     * Convert metres to feet.
     *
     * @param float $val
     *
     * @return float
     */
    public static function convertMetresToFeet(float $val): float
    {
        return $val * 3.28084;
    }

    /**
     * Convert miles to metres.
     *
     * @param float $val
     *
     * @return float
     */
    public static function convertMilesToMetres(float $val): float
    {
        return $val * self::MILES;
    }

    /**
     * Convert kilometres to metres.
     *
     * @param float $val
     *
     * @return float
     */
    public static function convertKilometresToMetres(float $val): float
    {
        return $val * self::KILOMETER;
    }

    /**
     * Convert hours, minutes, seconds, to an hour decimal
     * e.g. 00 30 00 = 0.5 hours.
     *
     * @param int $hours
     * @param int $minutes
     * @param int $secondes
     *
     * @return float
     */
    public static function convertHoursMinutesSecondsToDecimal(
        int $hours,
        int $minutes,
        int $secondes
    ): float {
        $total = 0;
        $total += $hours;
        $total += ((1 / 60) * $minutes);
        $total += ((1 / 3600) * $secondes);

        return $total;
    }

    /**
     * Convert seconds to a human readable hh:mm:ss.
     *
     * @param int $val
     *
     * @return string
     */
    public static function convertSecondsToHumanReadable(int $val): string
    {
        return sprintf('%02d:%02d:%02d', ($val / 3600), ($val / 60 % 60), ($val % 60));
    }

    /**
     * Convert the hh:mm:ss human readable time, back into seconds.
     *
     * @param string $val
     *
     * @return int
     */
    public static function convertHumanReadableToSeconds(string $val): int
    {
        $explode = explode(':', $val);

        return ($explode[0] * 3600) + ($explode[1] * 60) + $explode[2];
    }
}
