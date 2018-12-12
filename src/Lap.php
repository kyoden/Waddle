<?php

namespace Waddle;

class Lap
{
    /**
     * Total time in seconds.
     *
     * @var float
     */
    protected $totalTime;

    /**
     * Totam distance in meters.
     *
     * @var float
     */
    protected $totalDistance;

    /**
     * Metres per second.
     *
     * @var float
     */
    protected $maxSpeed;

    /**
     * @var int
     */
    protected $totalCalories;

    /**
     * @var int
     */
    protected $avgHeartRate;

    /**
     * @var int
     */
    protected $maxHeartRate;

    /**
     * @var int
     */
    protected $cadence;

    /**
     * @var array<TrackPoint>
     */
    protected $trackPoints = [];

    /**
     * Get the total lap time.
     *
     * @return float
     */
    public function getTotalTime(): float
    {
        return $this->totalTime;
    }

    /**
     * Get the total lap distance.
     *
     * @return float
     */
    public function getTotalDistance(): float
    {
        return $this->totalDistance;
    }

    /**
     * Get the max speed achieved during the lap.
     *
     * @return float|null
     */
    public function getMaxSpeed(): ?float
    {
        return $this->maxSpeed;
    }

    /**
     * Get the calories burnt during the lap.
     *
     * @return float
     */
    public function getTotalCalories(): ?float
    {
        return $this->totalCalories;
    }

    /**
     * Get the array of track points.
     *
     * @return array
     */
    public function getTrackPoints(): array
    {
        return $this->trackPoints;
    }

    /**
     * Get a specific track point, by its number.
     *
     * @param int $num
     *
     * @return TrackPoint
     */
    public function getTrackPoint(int $num): ?TrackPoint
    {
        return (array_key_exists($num, $this->trackPoints)) ? $this->trackPoints[$num] : false;
    }

    /**
     * Get last track point.
     *
     * @return TrackPoint
     */
    public function getLastTrackPoint(): ?TrackPoint
    {
        return $this->getTrackPoint(count($this->trackPoints) - 1);
    }

    /**
     * Get the average heart rate achieved during the lap.
     *
     * @return int|null
     */
    public function getAvgHeartRate(): ?int
    {
        return $this->avgHeartRate;
    }

    /**
     * Get the maximum heart rate achieved during the lap.
     *
     * @return int|null
     */
    public function getMaxHeartRate(): ?int
    {
        return $this->maxHeartRate;
    }

    /**
     * Get the cadence achieved during the lap.
     *
     * @return int|null
     */
    public function getCadence(): ?int
    {
        return $this->cadence;
    }

    /**
     * Set the total lap time (seconds).
     *
     * @param float $val
     *
     * @return $this
     */
    public function setTotalTime(float $val): Lap
    {
        $this->totalTime = $val;

        return $this;
    }

    /**
     * Set the total lap distance (metres).
     *
     * @param float $val
     *
     * @return $this
     */
    public function setTotalDistance(float $val): Lap
    {
        $this->totalDistance = $val;

        return $this;
    }

    /**
     * Set the max lap speed (metres per second).
     *
     * @param float $val
     *
     * @return $this
     */
    public function setMaxSpeed(float $val): Lap
    {
        $this->maxSpeed = $val;

        return $this;
    }

    /**
     * Set the total calories burnt.
     *
     * @param float $val
     *
     * @return $this
     */
    public function setTotalCalories(float $val): Lap
    {
        $this->totalCalories = $val;

        return $this;
    }

    /**
     * Add a track point to the lap.
     *
     * @param TrackPoint $point
     *
     * @return TrackPoint
     */
    public function addTrackPoint(TrackPoint $point): TrackPoint
    {
        $this->trackPoints[] = $point;

        return $point;
    }

    /**
     * Set the average heart rate in lap.
     *
     * @param int $val
     *
     * @return $this
     */
    public function setAvgHeartRate(int $val): Lap
    {
        $this->avgHeartRate = $val;

        return $this;
    }

    /**
     * Set the maximum heart rate in lap.
     *
     * @param int $val
     *
     * @return $this
     */
    public function setMaxHeartRate(int $val): Lap
    {
        $this->maxHeartRate = $val;

        return $this;
    }

    /**
     * Set the cadence in lap.
     *
     * @param type $val
     *
     * @return $this
     */
    public function setCadence($val): Lap
    {
        $this->cadence = $val;

        return $this;
    }
}
