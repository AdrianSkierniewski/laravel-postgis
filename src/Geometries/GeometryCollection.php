<?php namespace Phaza\LaravelPostgis\Geometries;

use Countable;
use JsonSerializable;

class GeometryCollection extends \GeoIO\Geometry\GeometryCollection implements Countable, JsonSerializable
{
    /**
     * Convert to GeoJson GeometryCollection that is jsonable to GeoJSON
     *
     * @return \GeoJson\Geometry\GeometryCollection
     */
    public function jsonSerialize()
    {
        $geometries = [];
        foreach ($this->getGeometries() as $geometry) {
            $geometries[] = $geometry->jsonSerialize();
        }

        return new \GeoJson\Geometry\GeometryCollection($geometries);
    }
}
