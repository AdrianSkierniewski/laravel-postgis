<?php namespace Phaza\LaravelPostgis\Geometries;

use Countable;
use InvalidArgumentException;

class MultiPolygon extends Geometry implements Countable
{
    /**
     * @var Polygon[]
     */
    protected $polygons;

    /**
     * @param Polygon[] $polygons
     * @param int       $srid
     */
    public function __construct(array $polygons, $srid = null)
    {
        $validated = array_filter($polygons, function ($value) {
            return $value instanceof Polygon;
        });

        if (count($polygons) !== count($validated)) {
            throw new InvalidArgumentException('$polygons must be an array of Points');
        }
        $this->polygons = $polygons;
        $this->srid = $srid;
    }

    public function toWKT()
    {
        return sprintf('MULTIPOLYGON(%s)', (string) $this);
    }

    public function __toString()
    {
        return implode(',', array_map(function (Polygon $polygon) {
            return sprintf('(%s)', (string) $polygon);
        }, $this->polygons));
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     *
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     *       </p>
     *       <p>
     *       The return value is cast to an integer.
     */
    public function count()
    {
        return count($this->polygons);
    }

    /**
     * Get the polygons that make up this MultiPolygon
     *
     * @return array|Polygon[]
     */
    public function getPolygons()
    {
        return $this->polygons;
    }

    /**
     * Convert to GeoJson MultiPolygon that is jsonable to GeoJSON
     *
     * @return \GeoJson\Geometry\MultiPolygon
     */
    public function jsonSerialize()
    {
        $polygons = [];
        foreach ($this->polygons as $polygon) {
            $polygons[] = $polygon->jsonSerialize();
        }

        return new \GeoJson\Geometry\MultiPolygon($polygons);
    }
}
