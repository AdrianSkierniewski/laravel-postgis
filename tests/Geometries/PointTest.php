<?php

use GeoIO\Dimension;
use GeoIO\Geometry\Coordinates;
use GeoIO\WKT\Parser\Parser;
use Phaza\LaravelPostgis\Geometries\Point;

class PointTest extends BaseTestCase
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
        $point = $this->wtkParser->parse('POINT(1 2)');

        $this->assertInstanceOf(Point::class, $point);
        $this->assertEquals(2, $point->getY());
        $this->assertEquals(1, $point->getX());
        $this->assertSame(null, $point->getSrid());
    }

    public function testFromWKTWithSRID()
    {
        $point = $this->wtkParser->parse('SRID=4326;POINT(1 2)');

        $this->assertInstanceOf(Point::class, $point);
        $this->assertEquals(2, $point->getY());
        $this->assertEquals(1, $point->getX());
        $this->assertSame(4326, $point->getSrid());
    }

    public function testToWKT()
    {
        $point = new Point(Dimension::DIMENSION_2D, new Coordinates(2, 1));

        $this->assertEquals('POINT(2.000000 1.000000)', $this->wtkGenerator->generate($point));
    }

    public function testToWKTWithSRID()
    {
        $point = new Point(Dimension::DIMENSION_2D, new Coordinates(3.4, 1.2), 2180);

        $this->assertEquals('POINT(3.400000 1.200000)', $this->wtkGenerator->generate($point));
    }

    public function testToWKT3D()
    {
        $point = new Point(Dimension::DIMENSION_3DZ, new Coordinates(2, 1, 3));

        $this->assertEquals('POINT(2.000000 1.000000 3.000000)', $this->wtkGenerator->generate($point));
    }

    public function testGetters()
    {
        $point = new Point(Dimension::DIMENSION_2D, new Coordinates(2, 1));
        $this->assertSame(1, $point->getY());
        $this->assertSame(2, $point->getX());
    }

    public function testJsonSerialize()
    {
        $point = new Point(Dimension::DIMENSION_2D, new Coordinates(3.4, 1.2));

        $this->assertInstanceOf(\GeoJson\Geometry\Point::class, $point->jsonSerialize());
        $this->assertSame('{"type":"Point","coordinates":[1.2,3.4]}', json_encode($point));
    }
}
