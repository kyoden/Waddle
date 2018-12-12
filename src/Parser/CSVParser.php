<?php

namespace Waddle\Parser;

use Waddle\Parser;
use Waddle\Activity;
use Waddle\Lap;
use Waddle\TrackPoint;

class CSVParser extends Parser
{
    private $headers = [];

    /**
     * Parse the CSV file.
     *
     * @param string $file
     *
     * @return Activity
     *
     * @throws \Exception
     */
    public function parse(string $file): Activity
    {
        // Check that the file exists
        $this->checkForFile($file);

        // Load the CSV data
        $handle = fopen($file, 'r');
        if (!$handle) {
            throw new \Exception("Unable to open file: {$file}");
        }

        // Get the headers from the first row
        $this->headers = fgetcsv($handle);

        // Create a new activity instance
        $activity = new Activity();

        // CSV does not contain a start time, so cannot set that

        // We will treat all track points as being lap 1, even if they have a different lap number, for easier parsing
        $lap = new \Waddle\Lap();

        $maxSpeed = 0;

        // Now loop through the track points
        while ($row = fgetcsv($handle)) {
            if ($row[$this->getHeaderKey('lapNumber')] > 0) {
                $trackPoint = $lap->addTrackPoint($this->parseTrackPoint($row));
                if ($trackPoint->getSpeed() > $maxSpeed) {
                    $maxSpeed = $trackPoint->getSpeed();
                }
                $lastRow = $row;
            }
        }

        // Now do the totals calculations for the lap
        if (false !== $this->getHeaderKey('time')) {
            $lap->setTotalTime((float) $lastRow[$this->getHeaderKey('time')]);
        }

        if (false !== $this->getHeaderKey('distance')) {
            $lap->setTotalDistance((float) $lastRow[$this->getHeaderKey('distance')]);
        }

        if (false !== $this->getHeaderKey('calories')) {
            $lap->setTotalCalories((float) $lastRow[$this->getHeaderKey('calories')]);
        }

        $lap->setMaxSpeed($maxSpeed);

        $activity->addLap($lap);

        // Finally return the activity object
        return $activity;
    }

    /**
     * Parse the track point.
     *
     * @param array $trackPointNode
     *
     * @return \Waddle\TrackPoint
     */
    protected function parseTrackPoint(array $trackPointRow): TrackPoint
    {
        $point = new TrackPoint();

        // CSV format does not store the time, so can't do that

        if (false !== $this->getHeaderKey('lat') && false !== $this->getHeaderKey('lon')) {
            $point->setPosition(['lat' => (float) $trackPointRow[$this->getHeaderKey('lat')], 'lon' => (float) $trackPointRow[$this->getHeaderKey('lon')]]);
        }

        if (false !== $this->getHeaderKey('elevation')) {
            $point->setAltitude((float) $trackPointRow[$this->getHeaderKey('elevation')]);
        }

        if (false !== $this->getHeaderKey('distance')) {
            $point->setDistance((float) $trackPointRow[$this->getHeaderKey('distance')]);
        }

        if (false !== $this->getHeaderKey('speed')) {
            $point->setSpeed((float) $trackPointRow[$this->getHeaderKey('speed')]);
        }

        if (false !== $this->getHeaderKey('heartRate')) {
            $point->setHeartRate((float) $trackPointRow[$this->getHeaderKey('speed')]);
        }

        if (false !== $this->getHeaderKey('calories')) {
            $point->setCalories((float) $trackPointRow[$this->getHeaderKey('calories')]);
        }

        return $point;
    }

    /**
     * Get the key of a specific header.
     *
     * @param string $header
     * @param array  $headers
     *
     * @return string
     */
    private function getHeaderKey(string $header)
    {
        return array_search($header, $this->headers);
    }
}
