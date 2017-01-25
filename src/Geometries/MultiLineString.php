<?php namespace Phaza\LaravelPostgis\Geometries;

use Countable;
use JsonSerializable;

class MultiLineString extends \GeoIO\Geometry\MultiLineString implements Countable, JsonSerializable
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
     * Convert to GeoJson Point that is jsonable to GeoJSON
     *
     * @return \GeoJson\Geometry\MultiLineString
     */
    public function jsonSerialize()
    {
        $lineStrings = [];

        foreach ($this->getLineStrings() as $lineString) {
            $lineStrings[] = $lineString->jsonSerialize();
        }

        return new \GeoJson\Geometry\MultiLineString($lineStrings);
    }
}