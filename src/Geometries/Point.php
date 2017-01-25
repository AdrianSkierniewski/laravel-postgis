<?php namespace Phaza\LaravelPostgis\Geometries;

use JsonSerializable;

class Point extends \GeoIO\Geometry\Point implements JsonSerializable
{
    /**
     * Convert to GeoJson Point that is jsonable to GeoJSON
     *
     * @return \GeoJson\Geometry\Point
     */
    public function jsonSerialize()
    {
        return new \GeoJson\Geometry\Point([$this->getY(), $this->getX()]);
    }
}
