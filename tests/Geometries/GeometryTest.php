<?php

use Phaza\LaravelPostgis\Geometries\Geometry;
use Phaza\LaravelPostgis\Geometries\GeometryCollection;
use Phaza\LaravelPostgis\Geometries\LineString;
use Phaza\LaravelPostgis\Geometries\MultiLineString;
use Phaza\LaravelPostgis\Geometries\MultiPoint;
use Phaza\LaravelPostgis\Geometries\MultiPolygon;
use Phaza\LaravelPostgis\Geometries\Point;
use Phaza\LaravelPostgis\Geometries\Polygon;

class GeometryTest extends BaseTestCase
{
    public function testFromWKT()
    {
        $this->assertEquals(
            Point::class,
            get_class(Geometry::fromWKT('POINT(0 0)'))
        );
        $this->assertEquals(
            LineString::class,
            get_class(Geometry::fromWKT('LINESTRING(0 0,1 1,1 2)'))
        );
        $this->assertEquals(
            Polygon::class,
            get_class(Geometry::fromWKT('POLYGON((0 0,4 0,4 4,0 4,0 0),(1 1, 2 1, 2 2, 1 2,1 1))'))
        );
        $this->assertEquals(
            MultiPoint::class,
            get_class(Geometry::fromWKT('MULTIPOINT((0 0),(1 2))'))
        );
        $this->assertEquals(
            MultiLineString::class,
            get_class(Geometry::fromWKT('MULTILINESTRING((0 0,1 1,1 2),(2 3,3 2,5 4))'))
        );
        $this->assertEquals(
            MultiPolygon::class,
            get_class(
                Geometry::fromWKT(
                    'MULTIPOLYGON(((0 0,4 0,4 4,0 4,0 0),(1 1,2 1,2 2,1 2,1 1)), ((-1 -1,-1 -2,-2 -2,-2 -1,-1 -1)))'
                )
            )
        );
        $this->assertEquals(
            GeometryCollection::class,
            get_class(Geometry::fromWKT('GEOMETRYCOLLECTION(POINT(2 3),LINESTRING(2 3,3 4))'))
        );
    }

    public function testGetWKBClass()
    {
        $this->assertInstanceOf(
            Point::class,
            Geometry::fromWKB('0101000000000000000000f03f0000000000000040')
        );
        $this->assertInstanceOf(
            LineString::class,
            Geometry::fromWKB('010200000002000000000000000000f03f000000000000004000000000000008400000000000001040')
        );
        $this->assertInstanceOf(
            Polygon::class,
            Geometry::fromWKB('01030000000100000004000000000000000000f03f00000000000000400000000000000840000000000000104000000000000014400000000000001840000000000000f03f0000000000000040')
        );
        $this->assertInstanceOf(
            MultiPoint::class,
            Geometry::fromWKB('0104000000020000000101000000000000000000f03f0000000000000040010100000000000000000008400000000000001040')
        );
        $this->assertInstanceOf(
            MultiLineString::class,
            Geometry::fromWKB('010500000001000000010200000002000000000000000000f03f000000000000004000000000000008400000000000001040')
        );
        $this->assertInstanceOf(
            MultiLineString::class,
            Geometry::fromWKB('010500000002000000010200000002000000000000000000f03f000000000000004000000000000008400000000000001040010200000002000000000000000000144000000000000018400000000000001c400000000000002040')
        );
        $this->assertInstanceOf(
            MultiPolygon::class,
            Geometry::fromWKB('01060000000200000001030000000100000004000000000000000000f03f00000000000000400000000000000840000000000000104000000000000014400000000000001840000000000000f03f000000000000004001030000000300000004000000000000000000f03f00000000000000400000000000000840000000000000104000000000000014400000000000001840000000000000f03f000000000000004004000000000000000000264000000000000028400000000000002a400000000000002c400000000000002e4000000000000030400000000000002640000000000000284004000000000000000000354000000000000036400000000000003740000000000000384000000000000039400000000000003a4000000000000035400000000000003640')
        );
        $this->assertInstanceOf(
            GeometryCollection::class,
            Geometry::fromWKB('0107000000010000000101000000000000000000f03f0000000000000040')
        );
        $this->assertInstanceOf(
            GeometryCollection::class,
            Geometry::fromWKB('0107000000020000000101000000000000000000f03f0000000000000040010200000002000000000000000000f03f000000000000004000000000000008400000000000001040')
        );
    }
}
