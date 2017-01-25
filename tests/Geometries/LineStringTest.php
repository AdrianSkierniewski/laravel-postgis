<?php

use GeoIO\Dimension;
use GeoIO\Geometry\Coordinates;
use GeoIO\WKT\Generator\Generator;
use GeoIO\WKT\Parser\Parser;
use Phaza\LaravelPostgis\Geometries\LineString;
use Phaza\LaravelPostgis\Geometries\Point;

class LineStringTest extends BaseTestCase
{
    use \Phaza\LaravelPostgis\Geometries\WTKHandlerTrait;

    /**
     * @var Parser
     */
    private $wtkParser;

    /**
     * @var Generator
     */
    private $wtkGenerator;

    private $points;

    protected function setUp()
    {
        $this->points = [
            new Point(Dimension::DIMENSION_2D, new Coordinates(0, 0)),
            new Point(Dimension::DIMENSION_2D, new Coordinates(1, 1)),
            new Point(Dimension::DIMENSION_2D, new Coordinates(2, 2))
        ];
        $this->wtkParser = $this->getWktParser();
        $this->wtkGenerator = $this->getWtkGenerator();
    }

    public function testToWKT()
    {
        $linestring = new LineString(Dimension::DIMENSION_2D, $this->points);

        $this->assertEquals(
            'LINESTRING(0.000000 0.000000, 1.000000 1.000000, 2.000000 2.000000)',
            $this->wtkGenerator->generate($linestring)
        );
    }

    public function testFromWKT()
    {
        $linestring = $this->wtkParser->parse('LINESTRING(0 0, 1 1, 2 2)');
        $this->assertInstanceOf(LineString::class, $linestring);

        $this->assertEquals(3, $linestring->count());
    }

    public function testJsonSerialize()
    {
        $lineString = new LineString(Dimension::DIMENSION_2D, $this->points);

        $this->assertInstanceOf(\GeoJson\Geometry\LineString::class, $lineString->jsonSerialize());
        $this->assertSame('{"type":"LineString","coordinates":[[0,0],[1,1],[2,2]]}', json_encode($lineString));
    }
}
