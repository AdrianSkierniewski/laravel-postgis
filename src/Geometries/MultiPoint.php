<?php namespace Phaza\LaravelPostgis\Geometries;

class MultiPoint extends PointCollection implements GeometryInterface, \JsonSerializable
{
    public function toWKT()
    {
        return sprintf('MULTIPOINT(%s)', (string)$this);
    }

    public function __toString()
    {
        return implode(',', array_map(function (Point $point) {
            return sprintf('(%s)', $point->toPair());
        }, $this->points));
    }

    /**
     * Convert to GeoJson MultiPoint that is jsonable to GeoJSON
     *
     * @return \GeoJson\Geometry\MultiPoint
     */
    public function jsonSerialize()
    {
        $points = [];
        foreach ($this->points as $point) {
            $points[] = $point->jsonSerialize();
        }

        return new \GeoJson\Geometry\MultiPoint($points);
    }
}
