<?php namespace Phaza\LaravelPostgis\Eloquent;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Phaza\LaravelPostgis\Exceptions\PostgisFieldTypesNotDefinedException;
use Phaza\LaravelPostgis\Geometries\Geometry;
use Phaza\LaravelPostgis\Geometries\GeometryInterface;

class Builder extends EloquentBuilder
{
    public function update(array $values)
    {
        foreach ($values as $key => &$value) {
            if ($value instanceof GeometryInterface) {
                $value = $this->asWKT($value, $key);
            }
        }

        return parent::update($values);
    }

    protected function getPostgisFields()
    {
        return $this->getModel()->getPostgisFields();
    }


    protected function asWKT(GeometryInterface $geometry, $key)
    {
        $type = $this->getModel()->getPostgisFieldType($key);
        if ($type === Geometry::GEOGRAPHY) {
            return $this->getQuery()->raw(sprintf("ST_GeogFromText('%s')", $geometry->toWKT()));
        }
        if ($type === Geometry::GEOMETRY) {
            if ($geometry->getSRID() !== null) {
                return $this->getQuery()->raw(sprintf("ST_GeomFromText('%s', %d)", $geometry->toWKT(), $geometry->getSRID()));
            }
            return $this->getQuery()->raw(sprintf("ST_GeomFromText('%s')", $geometry->toWKT()));
        }
        throw new PostgisFieldTypesNotDefinedException();
    }
}
