<?php

namespace Waddle\Parser;

use Waddle\Parser;
use Waddle\Activity;
use Waddle\Lap;
use Waddle\TrackPoint;

class TCXParser extends Parser
{
    const NS_ACTIVITY_EXTENSION_V2 = 'http://www.garmin.com/xmlschemas/ActivityExtension/v2';

    /**
     * @var string
     */
    private $nameNSActivityExtensionV2;

    /**
     * Parse the TCX file.
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
        if (!isset($data->Activities->Activity)) {
            throw new \Exception('Unable to find valid activity in file contents');
        }
        $this->detectsNamespace($data);

        // Parse the first activity
        $activityNode = $data->Activities->Activity[0];
        $activity->setStartTime(new \DateTime((string) $activityNode->Id));
        $activity->setType((string) $activityNode['Sport']);

        // Now parse the laps
        // There should only be 1 lap, but they are stored in an array just in case this ever changes
        foreach ($activityNode->Lap as $lapNode) {
            $activity->addLap($this->parseLap($lapNode));
        }

        // Finally return the activity object
        return $activity;
    }

    /**
     * @var \SimpleXMLElement
     */
    protected function detectsNamespace(\SimpleXMLElement $xml): void
    {
        $this->nameNSActivityExtensionV2 = null;
        $namespaces = $xml->getNamespaces(true);
        foreach ($namespaces as $name => $ns) {
            if (self::NS_ACTIVITY_EXTENSION_V2 === $ns) {
                $this->nameNSActivityExtensionV2 = $name;
            }
        }
    }

    /**
     * Parse the lap XML.
     *
     * @param \SimpleXMLElement $lapNode
     *
     * @return \Waddle\Lap
     */
    protected function parseLap(\SimpleXMLElement $lapNode)
    {
        $lap = new Lap();
        $lap->setTotalTime((float) $lapNode->TotalTimeSeconds);
        $lap->setTotalDistance((float) $lapNode->DistanceMeters);
        $lap->setMaxSpeed((float) $lapNode->MaximumSpeed);
        $lap->setTotalCalories((float) $lapNode->Calories);

        if (isset($lapNode->AverageHeartRateBpm)) {
            $lap->setAvgHeartRate((int) $lapNode->AverageHeartRateBpm->Value);
        }

        if (isset($lapNode->MaximumHeartRateBpm)) {
            $lap->setMaxHeartRate((int) $lapNode->MaximumHeartRateBpm->Value);
        }

        if (isset($lapNode->Cadence)) {
            $lap->setCadence((int) $lapNode->Cadence);
        }

        if (isset($lapNode->Extensions) && $this->nameNSActivityExtensionV2) {
            $extension = $lapNode->Extensions->children($this->nameNSActivityExtensionV2, true)->LX->children($this->nameNSActivityExtensionV2, true);
            if (isset($extension->AvgRunCadence)) {
                $lap->setCadence(2 * (int) $extension->AvgRunCadence);
            }
        }

        // Loop through the track points
        if (isset($lapNode->Track)) {
            foreach ($lapNode->Track->Trackpoint as $trackPointNode) {
                $lap->addTrackPoint($this->parseTrackPoint($trackPointNode));
            }
        }

        if ($lap->getAvgHeartRate() === null) {
            $heartRate = [];
            foreach ($lap->getTrackPoints() as $trackPoint) {
                /** @var TrackPoint $trackPoint */
                $heartRate[] = $trackPoint->getHeartRate();
            }
            $lap->setAvgHeartRate(array_sum($heartRate) / count($heartRate));
            $lap->setMaxHeartRate(max($heartRate));
        }

        return $lap;
    }

    /**
     * Parse the XML of a track point.
     *
     * @param \SimpleXMLElement $trackPointNode
     *
     * @return \Waddle\TrackPoint
     */
    protected function parseTrackPoint(\SimpleXMLElement $trackPointNode)
    {
        $point = new TrackPoint();
        $point->setTime(new \DateTime((string) $trackPointNode->Time));
        $point->setPosition(['lat' => (float) $trackPointNode->Position->LatitudeDegrees, 'lon' => (float) $trackPointNode->Position->LongitudeDegrees]);
        $point->setAltitude((float) $trackPointNode->AltitudeMeters);
        $point->setDistance((float) $trackPointNode->DistanceMeters);

        if (isset($trackPointNode->HeartRateBpm)) {
            if (isset($trackPointNode->HeartRateBpm->Value)) {
                $point->setHeartRate((int) $trackPointNode->HeartRateBpm->Value);
            }
        }

        if (isset($trackPointNode->Cadence)) {
            $point->setCadence((int) $trackPointNode->Cadence);
        }

        if (isset($trackPointNode->Extensions) && $this->nameNSActivityExtensionV2) {
            $extensions = $trackPointNode->Extensions->children($this->nameNSActivityExtensionV2, true)->TPX->children($this->nameNSActivityExtensionV2, true);
            if (isset($extensions->Speed)) {
                $point->setSpeed((float) $extensions->Speed);
            }
            if (isset($extensions->RunCadence)) {
                $point->setCadence(2 * (int) $extensions->RunCadence);
            }
        }

        return $point;
    }
}
