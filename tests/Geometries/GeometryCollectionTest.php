<?php

use GeoIO\Dimension;
use GeoIO\Geometry\Coordinates;
use GeoIO\WKT\Generator\Generator;
use GeoIO\WKT\Parser\Parser;
use Phaza\LaravelPostgis\Geometries\GeometryCollection;
use Phaza\LaravelPostgis\Geometries\LineString;
use Phaza\LaravelPostgis\Geometries\Point;

class GeometryCollectionTest extends BaseTestCase
{
    use \Phaza\LaravelPostgis\Geometries\WTKHandlerTrait;

    /**
     * @var GeometryCollection
     */
    private $collection;

    /**
     * @var Parser
     */
    private $wtkParser;

    /**
     * @var Generator
     */
    private $wtkGenerator;

    protected function setUp()
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

        $point = new Point(Dimension::DIMENSION_2D, new Coordinates(200, 100));

        $this->collection   = new GeometryCollection(Dimension::DIMENSION_2D, [$collection, $point]);
        $this->wtkParser    = $this->getWktParser();
        $this->wtkGenerator = $this->getWtkGenerator();
    }


    public function testFromWKT()
    {
        /**
         * @var GeometryCollection $geometryCollection
         */
        $geometryCollection = $this->wtkParser->parse('GEOMETRYCOLLECTION(POINT(2 3),LINESTRING(2 3,3 4))');
        $this->assertInstanceOf(GeometryCollection::class, $geometryCollection);

        $this->assertEquals(2, $geometryCollection->count());
        $this->assertInstanceOf(Point::class, $geometryCollection->getGeometries()[0]);
        $this->assertInstanceOf(LineString::class, $geometryCollection->getGeometries()[1]);
    }

    public function testToWKT()
    {
        $this->assertEquals(
            'GEOMETRYCOLLECTION(' .
            'LINESTRING(0.000000 0.000000, 1.000000 0.000000, 1.000000 1.000000, 0.000000 1.000000, 0.000000 0.000000), ' .
            'POINT(200.000000 100.000000)' .
            ')',
            $this->wtkGenerator->generate($this->collection)
        );
    }

    public function testJsonSerialize()
    {
        $this->assertInstanceOf(
            \GeoJson\Geometry\GeometryCollection::class,
            $this->collection->jsonSerialize()
        );

        $this->assertSame(
            '{"type":"GeometryCollection","geometries":[{"type":"LineString","coordinates":[[0,0],[0,1],[1,1],[1,0],[0,0]]},{"type":"Point","coordinates":[100,200]}]}',
            json_encode($this->collection->jsonSerialize())
        );

    }
}
