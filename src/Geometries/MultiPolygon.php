<?php namespace Phaza\LaravelPostgis\Geometries;

use Countable;
use JsonSerializable;

class MultiPolygon extends \GeoIO\Geometry\MultiPolygon implements Countable, JsonSerializable
{
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
        return count($this->getPolygons());
    }

    /**
     * Convert to GeoJson MultiPolygon that is jsonable to GeoJSON
     *
     * @return \GeoJson\Geometry\MultiPolygon
     */
    public function jsonSerialize()
    {
        $polygons = [];
        foreach ($this->getPolygons() as $polygon) {
            $polygons[] = $polygon->jsonSerialize();
        }

        return new \GeoJson\Geometry\MultiPolygon($polygons);
    }
}