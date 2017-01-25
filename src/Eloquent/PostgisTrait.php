<?php namespace Phaza\LaravelPostgis\Eloquent;

use GeoIO\Geometry\Geometry;
use GeoIO\WKB\Parser\Parser;
use GeoIO\WKT\Generator\Generator;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Arr;
use Phaza\LaravelPostgis\Exceptions\PostgisFieldsNotDefinedException;
use Phaza\LaravelPostgis\Exceptions\PostgisFieldTypesNotDefinedException;
use Phaza\LaravelPostgis\Geometries\Factory;
use Phaza\LaravelPostgis\PostGISColumn;
use Phaza\LaravelPostgis\Geometries\WTKHandlerTrait;

trait PostgisTrait
{
    use WTKHandlerTrait;

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
        $generator = $this->getWtkGenerator();
        foreach ($this->attributes as $key => $value) {
            if ($value instanceof Geometry) {
                $this->geometries[$key] = $value; //Preserve the geometry objects prior to the insert
                $this->attributes[$key] = $this->buildPostgisValue($generator, $value, $key);
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
                $value = $this->getWkbParser()->parse($value);
            }
        }

        parent::setRawAttributes($attributes, $sync);
    }

    public function getPostgisFields()
    {
        if (property_exists($this, 'postgisFields')) {
            return Arr::isAssoc($this->postgisFields) ? array_keys($this->postgisFields) : $this->postgisFields;
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
            return (isset($this->postgisFieldTypes[$field])) ? $this->postgisFieldTypes[$field] : PostGISColumn::GEOGRAPHY;
        }
        return PostGISColumn::GEOGRAPHY;
    }

    /**
     * It builds PostGIS value, so Postgres can understand it
     *
     * @param Generator $generator
     * @param Geometry  $geometry
     * @param           $key
     *
     * @return
     */
    protected function buildPostgisValue(Generator $generator, Geometry $geometry, $key)
    {
        $wkt = $generator->generate($geometry);
        if ($this->getPostgisFieldType($key) === PostGISColumn::GEOGRAPHY) {
            return $this->getConnection()->raw(sprintf("ST_GeogFromText('%s')", $wkt));
        }
        if ($this->getPostgisFieldType($key) === PostGISColumn::GEOMETRY) {

            if ($geometry->getSrid() !== null) {
                return $this->getConnection()->raw(sprintf("ST_GeomFromText('%s', %d)", $wkt, $geometry->getSRID()));
            }
            return $this->getConnection()->raw(sprintf("ST_GeomFromText('%s')", $wkt));
        }
        throw new PostgisFieldTypesNotDefinedException();
    }

    protected function getWkbParser()
    {
        return new Parser(new Factory());
    }
}
