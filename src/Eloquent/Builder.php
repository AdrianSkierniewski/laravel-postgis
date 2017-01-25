<?php namespace Phaza\LaravelPostgis\Eloquent;

use GeoIO\Geometry\Geometry;
use GeoIO\WKT\Generator\Generator;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Phaza\LaravelPostgis\Exceptions\PostgisFieldTypesNotDefinedException;
use Phaza\LaravelPostgis\PostGISColumn;
use Phaza\LaravelPostgis\Geometries\WTKHandlerTrait;

class Builder extends EloquentBuilder
{
    use WTKHandlerTrait;

    public function update(array $values)
    {
        $generator = $this->getWtkGenerator();
        foreach ($values as $key => &$value) {
            if ($value instanceof Geometry) {
                $value = $this->asWKT($generator, $value, $key);
            }
        }
        return parent::update($values);
    }

    protected function getPostgisFields()
    {
        return $this->getModel()->getPostgisFields();
    }

    protected function asWKT(Generator $generator, Geometry $geometry, $key)
    {
        $wkt = $generator->generate($geometry);
        $type = $this->getModel()->getPostgisFieldType($key);

        if ($type === PostGISColumn::GEOGRAPHY) {
            return $this->getQuery()->raw(sprintf("ST_GeogFromText('%s')", $wkt));
        }
        if ($type === PostGISColumn::GEOMETRY) {
            if ($geometry->getSrid() !== null) {
                return $this->getQuery()->raw(sprintf("ST_GeomFromText('%s', %d)", $wkt, $geometry->getSrid()));
            }
            return $this->getQuery()->raw(sprintf("ST_GeomFromText('%s')", $wkt));
        }
        throw new PostgisFieldTypesNotDefinedException();
    }
}
