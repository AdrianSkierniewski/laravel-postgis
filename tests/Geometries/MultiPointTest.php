<?php

use GeoIO\Dimension;
use GeoIO\Geometry\Coordinates;
use GeoIO\WKT\Parser\Parser;
use Phaza\LaravelPostgis\Geometries\Point;
use Phaza\LaravelPostgis\Geometries\MultiPoint;

class MultiPointTest extends BaseTestCase
{
    use \Phaza\LaravelPostgis\Geometries\WTKHandlerTrait;

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
        $this->wtkGenerator = $this->getWtkGenerator();
        $this->wtkParser    = $this->getWktParser();
    }

    public function testFromWKT()
    {
        $multiPoint = $this->wtkParser->parse('MULTIPOINT((0 0),(1 0),(1 1))');
        $this->assertInstanceOf(MultiPoint::class, $multiPoint);

        $this->assertEquals(3, $multiPoint->count());
    }

    public function testToWKT()
    {
        $collection = [
            new Point(Dimension::DIMENSION_2D, new Coordinates(0, 0)),
            new Point(Dimension::DIMENSION_2D, new Coordinates(1, 0)),
            new Point(Dimension::DIMENSION_2D, new Coordinates(1, 1))
        ];

        $multiPoint = new MultiPoint(Dimension::DIMENSION_2D, $collection);

        $this->assertEquals(
            'MULTIPOINT((0.000000 0.000000), (1.000000 0.000000), (1.000000 1.000000))',
            $this->wtkGenerator->generate($multiPoint)
        );
    }

    public function testJsonSerialize()
    {
        $collection = [
            new Point(Dimension::DIMENSION_2D, new Coordinates(0, 0)),
            new Point(Dimension::DIMENSION_2D, new Coordinates(1, 0)),
            new Point(Dimension::DIMENSION_2D, new Coordinates(1, 1))
        ];

        $multiPoint = new MultiPoint(Dimension::DIMENSION_2D, $collection);

        $this->assertInstanceOf(\GeoJson\Geometry\MultiPoint::class, $multiPoint->jsonSerialize());
        $this->assertSame('{"type":"MultiPoint","coordinates":[[0,0],[0,1],[1,1]]}', json_encode($multiPoint));
    }
}
