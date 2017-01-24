<?php namespace Phaza\LaravelPostgis\Geometries;

use Countable;
use InvalidArgumentException;

class GeometryCollection extends Geometry implements Countable
{
    /**
     * @var GeometryInterface[]
     */
    protected $geometries = [];

    /**
     * @param GeometryInterface[] $geometries
     * @param int                 $srid
     */
    public function __construct(array $geometries, $srid = null)
    {
        $validated = array_filter($geometries, function ($value) {
            return $value instanceof GeometryInterface;
        });

        if (count($geometries) !== count($validated)) {
            throw new InvalidArgumentException('$geometries must be an array of Geometry objects');
        }

        $this->srid = $srid;
        $this->geometries = $geometries;
    }

    public function getGeometries()
    {
        return $this->geometries;
    }

    public function toWKT()
    {
        return sprintf('GEOMETRYCOLLECTION(%s)', (string)$this);
    }

    public function __toString()
    {
        return implode(
            ',',
            array_map(
                function (GeometryInterface $geometry) {
                    return $geometry->toWKT();
                },
                $this->geometries
            )
        );
    }

    public function count()
    {
        return count($this->geometries);
    }

    /**
     * Convert to GeoJson GeometryCollection that is jsonable to GeoJSON
     *
     * @return \GeoJson\Geometry\GeometryCollection
     */
    public function jsonSerialize()
    {
        $geometries = [];
        foreach ($this->geometries as $geometry) {
            $geometries[] = $geometry->jsonSerialize();
        }

        return new \GeoJson\Geometry\GeometryCollection($geometries);
    }
}
