<?php namespace Phaza\LaravelPostgis\Geometries;

use Countable;
use JsonSerializable;

class LineString extends \GeoIO\Geometry\LineString implements Countable, JsonSerializable
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
        return count($this->getPoints());
    }

    /**
     * Convert to GeoJson LineString that is jsonable to GeoJSON
     *
     * @return \GeoJson\Geometry\LineString
     */
    public function jsonSerialize()
    {
        $points = [];
        foreach ($this->getPoints() as $point) {
            $points[] = $point->jsonSerialize();
        }
        return new \GeoJson\Geometry\LineString($points);
    }
}
