<?php

namespace Waddle;

class Activity
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var \DateTime
     */
    protected $startTime;

    /**
     * @var array<Lap>
     */
    protected $laps = [];

    /**
     * Get the type of activity, e.g. "Running" or "Cycling".
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Set the activity type.
     *
     * @param string $type
     *
     * @return $this
     */
    public function setType(string $type): Activity
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get the start time in a particular format.
     *
     * @param string|null $format
     *
     * @return \DateTime|string
     */
    public function getStartTime(string $format = null)
    {
        return ($this->startTime instanceof \DateTime && null !== $format)
                ? $this->startTime->format($format)
                : $this->startTime;
    }

    /**
     * Get the start time in a particular format.
     *
     * @param string|null $format
     *
     * @return \DateTime|string
     */
    public function getEndTime(string $format = null)
    {
        $endTime = clone $this->startTime;
        $endTime->add(new \DateInterval(sprintf('PT%dS', $this->getTotalDuration())));

        return (null !== $format)
                ? $endTime->format($format)
                : $endTime;
    }

    /**
     * Set the start time.
     *
     * @param \DateTime $time
     *
     * @return $this
     */
    public function setStartTime(\DateTime $time): Activity
    {
        $time->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        $this->startTime = $time;

        return $this;
    }

    /**
     * Get all laps on the activity.
     *
     * @return array<Lap>
     */
    public function getLaps(): array
    {
        return $this->laps;
    }

    /**
     * Get a specific lap number.
     *
     * @param int $num
     *
     * @return Lap|false
     */
    public function getLap(int $num)
    {
        return (array_key_exists($num, $this->laps)) ? $this->laps[$num] : false;
    }

    /**
     * Add a lap to the activity.
     *
     * @param Lap $lap
     */
    public function addLap(Lap $lap)
    {
        $this->laps[] = $lap;

        return $this;
    }

    /**
     * Set the array of laps on the activity.
     *
     * @param array $laps
     *
     * @return $this
     */
    public function setLaps(array $laps): Activity
    {
        $this->laps = $laps;

        return $this;
    }

    /**
     * Get the total distance covered in the whole activity.
     *
     * @return float
     */
    public function getTotalDistance(): float
    {
        $total = 0;

        foreach ($this->laps as $lap) {
            $total += $lap->getTotalDistance();
        }

        return $total;
    }

    /**
     * Get the total duration of the whole activity.
     *
     * @return float
     */
    public function getTotalDuration(): float
    {
        $total = 0;

        foreach ($this->laps as $lap) {
            $total += $lap->getTotalTime();
        }

        return $total;
    }

    /**
     * Get the average heart rate.
     *
     * @return int
     */
    public function getAvgHeartRate(): ?int
    {
        $bpm = 0;
        $duration = 0;
        foreach ($this->laps as $lap) {
            $bpm += $lap->getAvgHeartRate() * $lap->getTotalTime();
            $duration += $lap->getTotalTime();
        }

        if ($duration > 0) {
            return round($bpm / $duration);
        }
        return null;
    }

    /**
     * Get the max heart rate.
     *
     * @return int
     */
    public function getMaxHeartRate(): int
    {
        $max = 0;

        foreach ($this->laps as $lap) {
            $max = max($max, $lap->getMaxHeartRate());
        }

        return $max;
    }

    /**
     * Get the average pace per mile.
     *
     * @return string
     */
    public function getAveragePacePerMile(): string
    {
        return Converter::convertSecondsToHumanReadable(($this->getTotalDuration() / Converter::convertMetresToMiles($this->getTotalDistance())));
    }

    /**
     * Get the average pace per kilometre.
     *
     * @return string
     */
    public function getAveragePacePerKilometre(): string
    {
        return Converter::convertSecondsToHumanReadable(($this->getTotalDuration() / Converter::convertMetresToKilometres($this->getTotalDistance())));
    }

    /**
     * Get the average speed in mph.
     *
     * @return float
     */
    public function getAverageSpeedInMPH(): float
    {
        return Converter::convertMetresToMiles($this->getTotalDistance()) / ($this->getTotalDuration() / 3600);
    }

    /**
     * Get the average speed in kph.
     *
     * @return float
     */
    public function getAverageSpeedInKPH(): float
    {
        return Converter::convertMetresToKilometres($this->getTotalDistance()) / ($this->getTotalDuration() / 3600);
    }

    /**
     * Get total calories burned across whole activity.
     *
     * @return int
     */
    public function getTotalCalories(): int
    {
        $total = 0;

        foreach ($this->laps as $lap) {
            $total += $lap->getTotalCalories();
        }

        return $total;
    }

    /**
     * Get the max speed in mph.
     *
     * @return float
     */
    public function getMaxSpeedInMPH(): float
    {
        $max = 0;

        foreach ($this->laps as $lap) {
            if ($lap->getMaxSpeed() > $max) {
                $max = $lap->getMaxSpeed();
            }
        }

        return Converter::convertMetresPerSecondToMilesPerHour($max);
    }

    /**
     * Get the max speed in kph.
     *
     * @return float
     */
    public function getMaxSpeedInKPH(): float
    {
        $max = 0;

        foreach ($this->laps as $lap) {
            if ($lap->getMaxSpeed() > $max) {
                $max = $lap->getMaxSpeed();
            }
        }

        return Converter::convertMetresPerSecondToKilometresPerHour($max);
    }

    /**
     * Add up the total ascent and descent across the activity
     * In the future, might change this to look up lat/long points for more accuracy?
     *
     * @return array
     */
    public function getTotalAscentDescent(): array
    {
        $result = [
            'ascent' => 0,
            'descent' => 0,
        ];

        // First lap
        $last = $this->getLap(0)->getTrackPoint(0)->getAltitude();

        // Loop through each lap and point and add it all up
        foreach ($this->laps as $lap) {
            foreach ($lap->getTrackPoints() as $point) {
                if ($point->getAltitude() > $last) {
                    $result['ascent'] += ($point->getAltitude() - $last);
                } elseif ($point->getAltitude() < $last) {
                    $result['descent'] += ($last - $point->getAltitude());
                }

                $last = $point->getAltitude();
            }
        }

        return $result;
    }

    /**
     * Gives some information about the geographical properties of this track like extremal points.
     *
     * @return array
     */
    public function getGeographicInformation(): array
    {
        $result = [
            'north' => PHP_INT_MIN,
            'east' => PHP_INT_MIN,
            'south' => PHP_INT_MAX,
            'west' => PHP_INT_MAX,
            'highest' => PHP_INT_MIN,
            'lowest' => PHP_INT_MAX,
        ];

        // First lap
        $last = $this->getLap(0)->getTrackPoint(0)->getAltitude();

        // Loop through each lap and point and add it all up
        foreach ($this->laps as $lap) {
            foreach ($lap->getTrackPoints() as $point) {
                $lat = $point->getPosition('lat');
                $long = $point->getPosition('lon');
                $altitude = $point->getAltitude();

                $result['highest'] = max($altitude, $result['highest']);
                $result['lowest'] = min($altitude, $result['lowest']);

                $result['north'] = max($lat, $result['north']);
                $result['south'] = min($lat, $result['south']);
                $result['east'] = max($long, $result['east']);
                $result['west'] = min($long, $result['west']);
            }
        }

        return $result;
    }

    /**
     * Get an array of splits, in miles.
     *
     * @param int $distance
     *
     * @return array
     */
    public function getSplits(int $distance): array
    {
        $splits = [];
        $diff = 0;

        foreach ($this->laps as $lap) {
            foreach ($lap->getTrackPoints() as $key => $point) {
                if (($point->getDistance() - $diff) >= $distance) {
                    $splits[] = $key;
                    $diff = $point->getDistance();
                }
            }
        }

        // Get the last split, even if it's not a full mile
        if ($point->getDistance() > $diff) {
            $splits[] = $key;
        }

        return $splits;
    }
}
