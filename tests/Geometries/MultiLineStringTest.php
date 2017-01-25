<?php

use GeoIO\Dimension;
use GeoIO\Geometry\Coordinates;
use GeoIO\WKT\Parser\Parser;
use Phaza\LaravelPostgis\Geometries\LineString;
use Phaza\LaravelPostgis\Geometries\Point;
use Phaza\LaravelPostgis\Geometries\MultiLineString;

class MultiLineStringTest extends BaseTestCase
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
        $multilinestring = $this->wtkParser->parse('MULTILINESTRING((0 0,1 1,1 2),(2 3,3 2,5 4))');

        $this->assertInstanceOf(MultiLineString::class, $multilinestring);
        $this->assertSame(2, $multilinestring->count());
    }

    public function testToWKT()
    {
        $collection = new LineString(
            Dimension::DIMENSION_2D,
            [
                new Point(Dimension::DIMENSION_2D, new Coordinates(0, 0)),
                new Point(Dimension::DIMENSION_2D, new Coordinates(1, 0)),
                new Point(Dimension::DIMENSION_2D, new Coordinates(1, 1)),
                new Point(Dimension::DIMENSION_2D, new Coordinates(0, 1)),
                new Point(Dimension::DIMENSION_2D, new Coordinates(0, 0)),
            ]
        );

        $multilinestring = new MultiLineString(Dimension::DIMENSION_2D, [$collection]);

        $this->assertSame(
            'MULTILINESTRING((0.000000 0.000000, 1.000000 0.000000, 1.000000 1.000000, 0.000000 1.000000, 0.000000 0.000000))',
            $this->wtkGenerator->generate($multilinestring)
        );
    }

    public function testJsonSerialize()
    {
        $multilinestring = $this->wtkParser->parse('MULTILINESTRING((0 0,1 1,1 2),(2 3,3 2,5 4))');

        $this->assertInstanceOf(\GeoJson\Geometry\MultiLineString::class, $multilinestring->jsonSerialize());
        $this->assertSame(
            '{"type":"MultiLineString","coordinates":[[[0,0],[1,1],[2,1]],[[3,2],[2,3],[4,5]]]}',
            json_encode($multilinestring)
        );
    }
}
