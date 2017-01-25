<?php namespace Phaza\LaravelPostgis\Geometries;

use Countable;
use JsonSerializable;

class Polygon extends \GeoIO\Geometry\Polygon implements Countable, JsonSerializable
{
    /**
     * Count elements of an object
     *
     * @link  http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     *        </p>
     *        <p>
     *        The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return count($this->getLineStrings());
    }

    /**
     * Convert to GeoJson Polygon that is jsonable to GeoJSON
     *
     * @return \GeoJson\Geometry\Polygon
     */
    public function jsonSerialize()
    {
        $linearrings = [];
        foreach ($this->getLineStrings() as $linestring) {
            $linearrings[] = new \GeoJson\Geometry\LinearRing($linestring->jsonSerialize()->getCoordinates());
        }

        return new \GeoJson\Geometry\Polygon($linearrings);
    }
}