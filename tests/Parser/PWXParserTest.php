<?php

namespace Waddle\Parsers;

class PWXParserTest extends \PHPUnit\Framework\TestCase
{
    public $parser;

    public $activity;

    public function setUp()
    {
        $this->parser = new \Waddle\Parser\PWXParser();
        $this->activity = $this->parser->parse(__DIR__.'/../run.pwx');
    }

    // Not sure how to test this, as if run on a system with different timezone/daylight saving, will be different
//    public function testActivityStartTime(){
//        $this->assertEquals('2017-05-27 09:13:01', $this->activity->getStartTime('Y-m-d H:i:s'));
//    }

    public function testActivityLaps()
    {
        $this->assertEquals(1, count($this->activity->getLaps()));
    }

    public function testActivityTotalDistance()
    {
        $this->assertEquals(4824.94, $this->activity->getTotalDistance());
    }

    public function testActivityTotalDuration()
    {
        $this->assertEquals(1424, $this->activity->getTotalDuration());
    }

    public function testActivityAveragePacePerMile()
    {
        $this->assertEquals('00:07:54', $this->activity->getAveragePacePerMile());
    }

    public function testActivityAveragePacePerKilometre()
    {
        $this->assertEquals('00:04:55', $this->activity->getAveragePacePerKilometre());
    }

    public function testActivityAverageSpeedMPH()
    {
        $this->assertEquals('7.58', round($this->activity->getAverageSpeedInMPH(), 2));
    }

    public function testActivityAverageSpeedKPH()
    {
        $this->assertEquals('12.20', round($this->activity->getAverageSpeedInKPH(), 2));
    }

    public function testActivityTotalCalories()
    {
        $this->assertEquals(0, $this->activity->getTotalCalories());
    }

    public function testActivityMaxSpeedMPH()
    {
        $this->assertEquals('10.45', round($this->activity->getMaxSpeedInMPH(), 2));
    }

    public function testActivityMaxSpeedKPH()
    {
        $this->assertEquals('16.81', round($this->activity->getMaxSpeedInKPH(), 2));
    }

    public function testActivityTotalAscent()
    {
        $result = $this->activity->getTotalAscentDescent();
        $this->assertEquals(50.9, $result['ascent']);
    }

    public function testActivityTotalDescent()
    {
        $result = $this->activity->getTotalAscentDescent();
        $this->assertEquals(50.2, $result['descent']);
    }

    /**
     * @dataProvider dataActivitySplits
     */
    public function testActivitySplitsInMiles($expected, $distances)
    {
        $this->assertCount($expected, $this->activity->getSplits($distances));
    }

    public function dataActivitySplits()
    {
        return [
            'miles' => [3, 1609.34],
            'kilometer' => [5, 1000],
        ];
    }
}
