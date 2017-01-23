<?php namespace Phaza\LaravelPostgis\Eloquent;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Arr;
use Phaza\LaravelPostgis\Exceptions\PostgisFieldsNotDefinedException;
use Phaza\LaravelPostgis\Exceptions\PostgisFieldTypesNotDefinedException;
use Phaza\LaravelPostgis\Geometries\Geometry;
use Phaza\LaravelPostgis\Geometries\GeometryCollection;
use Phaza\LaravelPostgis\Geometries\GeometryInterface;

trait PostgisTrait
{
    public $geometries = [];
    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  \Illuminate\Database\Query\Builder $query
     * @return \Phaza\LaravelPostgis\Eloquent\Builder
     */
    public function newEloquentBuilder($query)
    {
        return new Builder($query);
    }

    protected function performInsert(EloquentBuilder $query, array $options = [])
    {
        foreach ($this->attributes as $key => $value) {
            if ($value instanceof GeometryInterface ) {
                $this->geometries[$key] = $value; //Preserve the geometry objects prior to the insert
                $this->attributes[$key] = $this->buildPostgisValue($value, $key);
            }
        }

        $insert = parent::performInsert($query, $options);

        foreach($this->geometries as $key => $value){
            $this->attributes[$key] = $value; //Retrieve the geometry objects so they can be used in the model
        }

        return $insert; //Return the result of the parent insert
    }

    public function setRawAttributes(array $attributes, $sync = false)
    {
        $pgFields = $this->getPostgisFields();

        foreach ($attributes as $attribute => &$value) {
            if (in_array($attribute, $pgFields) && is_string($value) && strlen($value) >= 15) {
                $value = Geometry::fromWKB($value);
            }
        }

        parent::setRawAttributes($attributes, $sync);
    }

    public function getPostgisFields()
    {
        if (property_exists($this, 'postgisFields')) {
            return Arr::isAssoc($this->postgisFields) ? //Is the array associative?
                array_keys($this->postgisFields) : //Returns just the keys to preserve compatibility with previous versions
                $this->postgisFields; //Returns the non-associative array that doesn't define the geometry type.
        } else {
            throw new PostgisFieldsNotDefinedException(__CLASS__ . ' has to define $postgisFields');
        }

    }

    /**
     * It returns field type for specified PostGIS field
     *
     * @param $field
     *
     * @return string
     */
    public function getPostgisFieldType($field)
    {
        if (property_exists($this, 'postgisFieldTypes')) {
            return (isset($this->postgisFieldTypes[$field])) ? $this->postgisFieldTypes[$field] : Geometry::GEOGRAPHY;
        }
        return Geometry::GEOGRAPHY;
    }

    /**
     * It builds PostGIS value, so Postgres can understand it
     *
     * @param Geometry $value
     * @param          $key
     *
     * @return
     * @throws PostgisFieldTypesNotDefinedException
     */
    protected function buildPostgisValue(Geometry $value, $key)
    {
        $this->geometries[$key] = $value; //Preserve the geometry objects prior to the insert
        if ($this->getPostgisFieldType($key) === Geometry::GEOGRAPHY) {
            if ($value instanceof GeometryCollection) {
                return $this->getConnection()->raw(sprintf("ST_GeomFromText('%s', 4326)", $value->toWKT()));
            }
            return $this->getConnection()->raw(sprintf("ST_GeogFromText('%s')", $value->toWKT()));
        }
        if ($this->getPostgisFieldType($key) === Geometry::GEOMETRY) {
            return $this->getConnection()->raw(sprintf("ST_GeomFromText('%s')", $value->toWKT()));
        }
        throw new PostgisFieldTypesNotDefinedException();
    }
}
