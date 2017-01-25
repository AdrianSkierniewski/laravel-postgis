<?php

use Phaza\LaravelPostgis\Geometries\GeometryCollection;
use Phaza\LaravelPostgis\Geometries\LineString;
use Phaza\LaravelPostgis\Geometries\MultiLineString;
use Phaza\LaravelPostgis\Geometries\MultiPoint;
use Phaza\LaravelPostgis\Geometries\MultiPolygon;
use Phaza\LaravelPostgis\Geometries\Point;
use Phaza\LaravelPostgis\Geometries\Polygon;

class ParsersTest extends BaseTestCase
{
    use \Phaza\LaravelPostgis\Geometries\WTKHandlerTrait;
    use \Phaza\LaravelPostgis\Geometries\WKBHandlerTrait;

    /**
     * @var \GeoIO\WKB\Parser\Parser
     */
    protected $wkbParser;

    /**
     * @var \GeoIO\WKT\Parser\Parser
     */
    protected $wtkParser;

    protected function setUp()
    {
        $this->wkbParser = $this->getWkbParser();
        $this->wtkParser = $this->getWktParser();
    }

    public function testFromWKT()
    {
        $this->assertEquals(
            Point::class,
            get_class($this->wtkParser->parse('POINT(0 0)'))
        );
        $this->assertEquals(
            LineString::class,
            get_class($this->wtkParser->parse('LINESTRING(0 0,1 1,1 2)'))
        );
        $this->assertEquals(
            Polygon::class,
            get_class($this->wtkParser->parse('POLYGON((0 0,4 0,4 4,0 4,0 0),(1 1, 2 1, 2 2, 1 2,1 1))'))
        );
        $this->assertEquals(
            MultiPoint::class,
            get_class($this->wtkParser->parse('MULTIPOINT((0 0),(1 2))'))
        );
        $this->assertEquals(
            MultiLineString::class,
            get_class($this->wtkParser->parse('MULTILINESTRING((0 0,1 1,1 2),(2 3,3 2,5 4))'))
        );
        $this->assertEquals(
            MultiPolygon::class,
            get_class(
                $this->wtkParser->parse(
                    'MULTIPOLYGON(((0 0,4 0,4 4,0 4,0 0),(1 1,2 1,2 2,1 2,1 1)), ((-1 -1,-1 -2,-2 -2,-2 -1,-1 -1)))'
                )
            )
        );
        $this->assertEquals(
            GeometryCollection::class,
            get_class($this->wtkParser->parse('GEOMETRYCOLLECTION(POINT(2 3),LINESTRING(2 3,3 4))'))
        );
    }

    public function testGetWKBClass()
    {
        $this->assertInstanceOf(
            Point::class,
            $this->wkbParser->parse('0101000000000000000000f03f0000000000000040')
        );
        $this->assertInstanceOf(
            LineString::class,
            $this->wkbParser->parse(
                '010200000002000000000000000000f03f000000000000004000000000000008400000000000001040'
            )
        );
        $this->assertInstanceOf(
            Polygon::class,
            $this->wkbParser->parse(
                '01030000000100000004000000000000000000f03f00000000000000400000000000000840000000000000104000000000000014400000000000001840000000000000f03f0000000000000040'
            )
        );
        $this->assertInstanceOf(
            MultiPoint::class,
            $this->wkbParser->parse(
                '0104000000020000000101000000000000000000f03f0000000000000040010100000000000000000008400000000000001040'
            )
        );
        $this->assertInstanceOf(
            MultiLineString::class,
            $this->wkbParser->parse(
                '010500000001000000010200000002000000000000000000f03f000000000000004000000000000008400000000000001040'
            )
        );
        $this->assertInstanceOf(
            MultiLineString::class,
            $this->wkbParser->parse(
                '010500000002000000010200000002000000000000000000f03f000000000000004000000000000008400000000000001040010200000002000000000000000000144000000000000018400000000000001c400000000000002040'
            )
        );
        $this->assertInstanceOf(
            MultiPolygon::class,
            $this->wkbParser->parse(
                '01060000000200000001030000000100000004000000000000000000f03f00000000000000400000000000000840000000000000104000000000000014400000000000001840000000000000f03f000000000000004001030000000300000004000000000000000000f03f00000000000000400000000000000840000000000000104000000000000014400000000000001840000000000000f03f000000000000004004000000000000000000264000000000000028400000000000002a400000000000002c400000000000002e4000000000000030400000000000002640000000000000284004000000000000000000354000000000000036400000000000003740000000000000384000000000000039400000000000003a4000000000000035400000000000003640'
            )
        );
        $this->assertInstanceOf(
            GeometryCollection::class,
            $this->wkbParser->parse('0107000000010000000101000000000000000000f03f0000000000000040')
        );
        $this->assertInstanceOf(
            GeometryCollection::class,
            $this->wkbParser->parse(
                '0107000000020000000101000000000000000000f03f0000000000000040010200000002000000000000000000f03f000000000000004000000000000008400000000000001040'
            )
        );
    }
}
