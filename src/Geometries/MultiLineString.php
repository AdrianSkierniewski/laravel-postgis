<?php namespace Phaza\LaravelPostgis\Geometries;

use Countable;
use InvalidArgumentException;

class MultiLineString extends Geometry implements Countable
{
    /**
     * @var LineString[]
     */
    protected $lineStrings = [];

    /**
     * @param LineString[] $lineStrings
     * @param int          $srid
     */
    public function __construct(array $lineStrings, $srid = null)
    {
        if (count($lineStrings) < 1) {
            throw new InvalidArgumentException('$linestrings must contain at least one entry');
        }

        $validated = array_filter($lineStrings, function ($value) {
            return $value instanceof LineString;
        });

        if (count($lineStrings) !== count($validated)) {
            throw new InvalidArgumentException('$linestrings must be an array of Points');
        }

        $this->srid = $srid;
        $this->lineStrings = $lineStrings;
    }

    public function getLineStrings()
    {
        return $this->lineStrings;
    }

    public function toWKT()
    {
        return sprintf('MULTILINESTRING(%s)', (string)$this);
    }

    public function __toString()
    {
        return implode(',', array_map(function (LineString $linestring) {
            return sprintf('(%s)', (string)$linestring);
        }, $this->getLineStrings()));
    }

    public function count()
    {
        return count($this->lineStrings);
    }

    /**
     * Convert to GeoJson Point that is jsonable to GeoJSON
     *
     * @return \GeoJson\Geometry\MultiLineString
     */
    public function jsonSerialize()
    {
        $linestrings = [];

        foreach ($this->lineStrings as $linestring) {
            $linestrings[] = $linestring->jsonSerialize();
        }

        return new \GeoJson\Geometry\MultiLineString($linestrings);
    }
}
