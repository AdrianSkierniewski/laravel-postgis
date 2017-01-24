<?php namespace Phaza\LaravelPostgis\Geometries;

use GeoIO\WKB\Parser\Parser as WKBParser;
use GeoIO\WKT\Parser\Parser as WKTParser;

abstract class Geometry implements GeometryInterface, \JsonSerializable
{
    const GEOMETRY = 'GEOMETRY';
    const GEOGRAPHY = 'GEOGRAPHY';

    /**
     * @var int|null
     */
    protected $srid;

    /**
     * @return int
     */
    public function getSRID()
    {
        return $this->srid;
    }

    /**
     * @param int $srid
     */
    public function setSRID($srid)
    {
        $this->srid = $srid;
    }

    /**
     * @param $wkb
     *
     * @return $this
     */
    public static function fromWKB($wkb)
    {
        $parser = new WKBParser(new Factory());

        return $parser->parse($wkb);
    }

    /**
     * @param $wkt
     *
     * @return $this
     */
    public static function fromWKT($wkt)
    {
        $parser = new WKTParser(new Factory());

        return $parser->parse($wkt);
    }
}
