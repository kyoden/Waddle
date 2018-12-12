<?php

namespace Waddle;

class TrackPoint
{
    /**
     * Timestamp of the time this point was recorded (generally every second).
     *
     * @var \DateTime
     */
    protected $time;

    /**
     * @var array
     */
    protected $position = []; // Array of lat/lon

    /**
     * Altitude in metres.
     *
     * @var float
     */
    protected $altitude;

    /**
     * Distance travelled so far in metres.
     *
     * @var float
     */
    protected $distance;

    /**
     * Metres per second.
     *
     * @var float
     */
    protected $speed;

    /**
     * @var int
     */
    protected $heartRate;

    /**
     * @var int
     */
    protected $cadence;

    /**
     * @var int
     */
    protected $calories;

    /**
     * Get the timestamp in a given format.
     *
     * @param string $format
     *
     * @return \DateTime|null
     */
    public function getTime(string $format = null)
    {
        return ($this->time instanceof \DateTime && null !== $format)
                ? $this->time->format($format)
                : $this->time;
    }

    /**
     * Get either the lat/long array or a specific value from it, if "lat" or "long" is passed in.
     *
     * @param string $type
     *
     * @return string|array
     */
    public function getPosition(string $type = null)
    {
        return (!is_null($type) && array_key_exists($type, $this->position))
                ? $this->position[$type]
                : $this->position;
    }

    /**
     * Get the altitude.
     *
     * @return float
     */
    public function getAltitude(): float
    {
        return $this->altitude;
    }

    /**
     * Get the distance so far.
     *
     * @return float
     */
    public function getDistance(): float
    {
        return $this->distance;
    }

    /**
     * Get the current speed at this point.
     *
     * @return float
     */
    public function getSpeed(): float
    {
        return $this->speed;
    }

    /**
     * Get the current heart rate at this point.
     *
     * @return int
     */
    public function getHeartRate(): int
    {
        return $this->heartRate;
    }

    /**
     * Get the current cadence rate at this point.
     *
     * @return int
     */
    public function getCadence(): int
    {
        return $this->cadence;
    }

    /**
     * Get the number of calories burnt so far.
     *
     * @return int
     */
    public function getCalories(): int
    {
        return $this->calories;
    }

    /**
     * Set the timestamp of this point.
     *
     * @param \DateTime $time
     *
     * @return $this
     */
    public function setTime(\DateTime $time): TrackPoint
    {
        $time->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        $this->time = $time;

        return $this;
    }

    /**
     * Set the position array.
     *
     * @param array $val
     *
     * @return $this
     */
    public function setPosition(array $val): TrackPoint
    {
        $this->position = $val;

        return $this;
    }

    /**
     * Set the altitude.
     *
     * @param float $val
     *
     * @return $this
     */
    public function setAltitude(float $val): TrackPoint
    {
        $this->altitude = $val;

        return $this;
    }

    /**
     * Set the distance.
     *
     * @param float $val
     *
     * @return $this
     */
    public function setDistance($val): TrackPoint
    {
        $this->distance = $val;

        return $this;
    }

    /**
     * Set the speed.
     *
     * @param float $val
     *
     * @return $this
     */
    public function setSpeed(float $val): TrackPoint
    {
        $this->speed = $val;

        return $this;
    }

    /**
     * Set the heart rate.
     *
     * @param int $val
     *
     * @return $this
     */
    public function setHeartRate(int $val): TrackPoint
    {
        $this->heartRate = $val;

        return $this;
    }

    /**
     * Set the cadence rate.
     *
     * @param int $val
     *
     * @return $this
     */
    public function setCadence(int $val): TrackPoint
    {
        $this->cadence = $val;

        return $this;
    }

    /**
     * Set the calories burnt so far.
     *
     * @param int $val
     *
     * @return $this
     */
    public function setCalories(int $val): TrackPoint
    {
        $this->calories = $val;

        return $this;
    }
}
