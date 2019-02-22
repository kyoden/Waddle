<?php

namespace Waddle\Parsers;

class TXCParserTest extends \PHPUnit\Framework\TestCase
{
    public $parser;

    public $activity;

    public function setUp()
    {
        $this->parser = new \Waddle\Parser\TCXParser();
        $this->activity = $this->parser->parse(__DIR__.'/../run.tcx');
    }

    public function testDetectsNamespace()
    {
        $reflector = new \ReflectionClass($this->parser);
        $method = $reflector->getMethod('detectsNamespace');
        $method->setAccessible(true);
        $method->invokeArgs($this->parser, [simplexml_load_file(__DIR__.'/../run_garmin.tcx')]);
        $reflector = new \ReflectionClass($this->parser);
        $property = $reflector->getProperty('nameNSActivityExtensionV2');
        $property->setAccessible(true);

        $this->assertEquals('ns3', $property->getValue($this->parser));
    }

    public function testDetectsNamespaceSeveralParse()
    {
        $reflector = new \ReflectionClass($this->parser);
        $method = $reflector->getMethod('detectsNamespace');
        $method->setAccessible(true);
        $reflector = new \ReflectionClass($this->parser);
        $property = $reflector->getProperty('nameNSActivityExtensionV2');

        $method->invokeArgs($this->parser, [simplexml_load_file(__DIR__.'/../run_garmin.tcx')]);
        $property->setAccessible(true);
        $this->assertEquals('ns3', $property->getValue($this->parser));

        $method->invokeArgs($this->parser, [simplexml_load_file(__DIR__.'/../run.tcx')]);
        $property->setAccessible(true);
        $this->assertEquals('x', $property->getValue($this->parser));
    }

    private function getActivity($filename)
    {
        return $this->parser->parse(__DIR__.'/../'.$filename.'.tcx');
    }

    /**
     * @dataProvider dataParser
     */
    public function testParser($filename)
    {
        $this->assertInstanceOf(\Waddle\Activity::class, $this->getActivity($filename));
    }

    public function dataParser()
    {
        return [
            'run' => ['run'],
            'run_garmin' => ['run_garmin'],
            'run_without_track' => ['run_without_track'],
        ];
    }

    public function testActivityLaps()
    {
        $this->assertEquals(1, count($this->getActivity('run')->getLaps()));
    }

    public function testActivityTotalDistance()
    {
        $this->assertEquals(4824.94, $this->getActivity('run')->getTotalDistance());
    }

    public function testActivityTotalDuration()
    {
        $this->assertEquals(1424, $this->getActivity('run')->getTotalDuration());
    }

    public function testActivityAveragePacePerMile()
    {
        $this->assertEquals('00:07:54', $this->getActivity('run')->getAveragePacePerMile());
    }

    public function testActivityAveragePacePerKilometre()
    {
        $this->assertEquals('00:04:55', $this->getActivity('run')->getAveragePacePerKilometre());
    }

    public function testActivityAverageSpeedMPH()
    {
        $this->assertEquals('7.58', round($this->getActivity('run')->getAverageSpeedInMPH(), 2));
    }

    public function testActivityAverageSpeedKPH()
    {
        $this->assertEquals('12.20', round($this->getActivity('run')->getAverageSpeedInKPH(), 2));
    }

    public function testActivityTotalCalories()
    {
        $this->assertEquals(372, $this->getActivity('run')->getTotalCalories());
    }

    public function testActivityMaxSpeedMPH()
    {
        $this->assertEquals('10.45', round($this->getActivity('run')->getMaxSpeedInMPH(), 2));
    }

    public function testActivityMaxSpeedKPH()
    {
        $this->assertEquals('16.81', round($this->getActivity('run')->getMaxSpeedInKPH(), 2));
    }

    public function testActivityTotalAscent()
    {
        $result = $this->getActivity('run')->getTotalAscentDescent();
        $this->assertEquals(50.9, $result['ascent']);
    }

    public function testActivityTotalDescent()
    {
        $result = $this->getActivity('run')->getTotalAscentDescent();
        $this->assertEquals(50.2, $result['descent']);
    }

    /**
     * @dataProvider dataActivitySplits
     */
    public function testActivitySplitsInMiles($expected, $distances)
    {
        $this->assertCount($expected, $this->getActivity('run')->getSplits($distances));
    }

    public function dataActivitySplits()
    {
        return [
            'miles' => [3, 1609.34],
            'kilometer' => [5, 1000],
        ];
    }

    public function testActivityAvgHeartRate()
    {
        $this->assertSame(142, $this->getActivity('run_garmin')->getAvgHeartRate());
    }

    public function testActivityMaxHeartRate()
    {
        $this->assertSame(153, $this->getActivity('run_garmin')->getMaxHeartRate());
    }

    /**
     * @dataProvider dataLapHeartRate
     */
    public function testLapHeartRate($expectedAvg, $expectedMap, $indexLap)
    {
        $lap = $this->getActivity('run_garmin')->getLap($indexLap);

        $this->assertSame($expectedAvg, $lap->getAvgHeartRate());
        $this->assertSame($expectedMap, $lap->getMaxHeartRate());
    }

    public function dataLapHeartRate()
    {
        return [
            [137, 150, 0],
            [144, 147, 2],
        ];
    }

    /**
     * @dataProvider dataTrackPointHeartRate
     */
    public function testTrackPointHeartRate($expected, $indexLap, $indexTrackPoint)
    {
        $lap = $this->getActivity('run_garmin')->getLap($indexLap);

        $this->assertSame($expected, $lap->getTrackPoint($indexTrackPoint)->getHeartRate());
    }

    public function dataTrackPointHeartRate()
    {
        return [
            [112, 0, 0],
            [146, 2, 1],
        ];
    }

    /**
     * @dataProvider dataLapCadence
     */
    public function testLapCadence($expected, $filename, $indexLap)
    {
        $lap = $this->getActivity($filename)->getLap($indexLap);

        $this->assertSame($expected, $lap->getCadence());
    }

    public function dataLapCadence()
    {
        return [
            [174, 'run_garmin', 0],
            [168, 'run_garmin', 2],
            [84, 'test_2018-11-04_09-12-38', 0],
            [76, 'test_2018-11-04_09-12-38', 1],
        ];
    }

    /**
     * @dataProvider dataTrackPointCadence
     */
    public function testTrackPointCadence($expected, $filename, $indexLap, $indexTrapPoint)
    {
        $lap = $this->getActivity($filename)->getLap($indexLap);

        $this->assertSame($expected, $lap->getTrackPoint($indexTrapPoint)->getCadence());
    }

    public function dataTrackPointCadence()
    {
        return [
            [186, 'run_garmin', 0, 1],
            [0, 'test_2018-11-04_09-12-38', 0, 1],
        ];
    }
}
