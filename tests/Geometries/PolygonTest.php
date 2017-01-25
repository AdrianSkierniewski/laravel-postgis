<?php

use GeoIO\Dimension;
use GeoIO\Geometry\Coordinates;
use GeoIO\WKT\Parser\Parser;
use Phaza\LaravelPostgis\Geometries\LinearRing;
use Phaza\LaravelPostgis\Geometries\Point;
use Phaza\LaravelPostgis\Geometries\Polygon;

class PolygonTest extends BaseTestCase
{
    use \Phaza\LaravelPostgis\Geometries\WTKHandlerTrait;

    private $polygon;

    /**
     * @var Parser
     */
    private $wtkParser;

    /**
     * @var \GeoIO\WKT\Generator\Generator
     */
    private $wtkGenerator;

    protected function setUp()
    {
        $collection = new LinearRing(
            Dimension::DIMENSION_2D,
            [
                new Point(Dimension::DIMENSION_2D, new Coordinates(0, 0)),
                new Point(Dimension::DIMENSION_2D, new Coordinates(1, 0)),
                new Point(Dimension::DIMENSION_2D, new Coordinates(1, 1)),
                new Point(Dimension::DIMENSION_2D, new Coordinates(0, 1)),
                new Point(Dimension::DIMENSION_2D, new Coordinates(0, 0))
            ]
        );

        $this->polygon      = new Polygon(Dimension::DIMENSION_2D, [$collection]);
        $this->wtkGenerator = $this->getWtkGenerator();
        $this->wtkParser    = $this->getWktParser();
    }


    public function testFromWKT()
    {
        $polygon = $this->wtkParser->parse('POLYGON((0 0,4 0,4 4,0 4,0 0),(1 1, 2 1, 2 2, 1 2,1 1))');
        $this->assertInstanceOf(Polygon::class, $polygon);

        $this->assertEquals(2, $polygon->count());
    }

    public function testToWKT()
    {
        $this->assertEquals(
            'POLYGON((0.000000 0.000000, 1.000000 0.000000, 1.000000 1.000000, 0.000000 1.000000, 0.000000 0.000000))',
            $this->wtkGenerator->generate($this->polygon)
        );
    }

    public function testJsonSerialize()
    {
        $this->assertInstanceOf(\GeoJson\Geometry\Polygon::class, $this->polygon->jsonSerialize());
        $this->assertSame(
            '{"type":"Polygon","coordinates":[[[0,0],[0,1],[1,1],[1,0],[0,0]]]}',
            json_encode($this->polygon)
        );

    }
}
