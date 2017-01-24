<?php namespace Phaza\LaravelPostgis\Geometries;

class LineString extends PointCollection implements GeometryInterface
{
    public function toWKT()
    {
        return sprintf('LINESTRING(%s)', $this->toPairList());
    }

    public function __toString()
    {
        return $this->toPairList();
    }

    /**
     * Convert to GeoJson LineString that is jsonable to GeoJSON
     *
     * @return \GeoJson\Geometry\LineString
     */
    public function jsonSerialize()
    {
        $points = [];
        foreach ($this->points as $point) {
            $points[] = $point->jsonSerialize();
        }

        return new \GeoJson\Geometry\LineString($points);
    }
}
