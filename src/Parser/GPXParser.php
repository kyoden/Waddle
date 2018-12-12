<?php

namespace Waddle\Parser;

use Waddle\Parser;
use Waddle\Activity;
use Waddle\Lap;
use Waddle\TrackPoint;

class GPXParser extends Parser
{
    /**
     * Parse the GPX file.
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

        // Create a new activity instance
        $activity = new Activity();

        // Load the XML in the TCX file
        $data = simplexml_load_file($file);
        if (!isset($data->trk)) {
            throw new \Exception('Unable to find valid activity in file contents');
        }

        // Parse the first activity
        $activityNode = $data->trk;
        $activity->setStartTime(new \DateTime((string) $activityNode->trkseg[0]->trkpt[0]->time));
        $activity->setType((string) $activityNode->name[0]);

        // Now parse the trksegs (Track Segments, I assume)
        // There should only be 1 trkseg, but they are stored in an array just in case this ever changes
        foreach ($activityNode->trkseg as $lapNode) {
            $activity->addLap($this->parseLap($lapNode));
        }

        // Finally return the activity object
        return $activity;
    }

    /**
     * Parse the lap XML (trkseg).
     *
     * @param TrackPoint $lapNode
     *
     * @return \Waddle\Lap
     */
    protected function parseLap($lapNode): Lap
    {
        $lap = new Lap();

        // GPX files don't have the overall information, so we will have to calculate that afterwards
        $totalTime = 0;
        $maxSpeed = 0;
        $lastTrackPointNode = null;

        // Loop through the track points
        foreach ($lapNode->trkpt as $trackPointNode) {
            $trackPoint = $lap->addTrackPoint($this->parseTrackPoint($trackPointNode, $lastTrackPointNode));

            // Add up the time
            if (!is_null($lastTrackPointNode)) {
                $totalTime += ($trackPoint->getTime('U') - $lastTrackPointNode->getTime('U'));
            }

            if ($trackPoint->getSpeed() > $maxSpeed) {
                $maxSpeed = $trackPoint->getSpeed();
            }

            $lastTrackPointNode = $trackPoint;
        }

        // Now, using the last track point we can get the total distance
        $lap->setTotalDistance($lastTrackPointNode->getDistance());
        $lap->setTotalTime($totalTime);
        $lap->setMaxSpeed($maxSpeed);

        return $lap;
    }

    /**
     * Parse the XML of a track point.
     *
     * @param \SimpleXMLElement $trackPointNode
     *
     * @return \Waddle\TrackPoint
     */
    protected function parseTrackPoint(\SimpleXMLElement $trackPointNode, TrackPoint $previousTrackPoint = null): TrackPoint
    {
        $point = new TrackPoint();
        $point->setTime(new \DateTime((string) $trackPointNode->time));
        $point->setPosition(['lat' => (float) $trackPointNode['lat'], 'lon' => (float) $trackPointNode['lon']]);
        $point->setAltitude((float) $trackPointNode->ele);

        // GPX files don't store the distance travelled, that will have to be calculated from lat/lon
        $distance = 0;
        $speed = 0;
        if (!is_null($previousTrackPoint)) {
            // Distance
            $distanceTravelled = $this->calculateDistanceBetweenLatLon($previousTrackPoint->getPosition('lat'), $point->getPosition('lat'), $previousTrackPoint->getPosition('lon'), $point->getPosition('lon'));
            $distance = ($previousTrackPoint->getDistance() + $distanceTravelled);

            // Speed = Distance / Time
            // Each track point should be recorded 1 second after the last, but let's just confirm that
            $timeDiff = $point->getTime('U') - $previousTrackPoint->getTime('U');
            if (0 != $timeDiff) {
                $speed = ($distanceTravelled / $timeDiff); // Metres per Second
            }
        }

        $point->setDistance($distance);
        $point->setSpeed($speed);

        return $point;
    }
}
