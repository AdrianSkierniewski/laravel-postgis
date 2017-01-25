<?php namespace Phaza\LaravelPostgis\Schema;

use Phaza\LaravelPostgis\PostGISColumn;

class Blueprint extends \Bosnadev\Database\Schema\Blueprint
{
    /**
     * Add a point column on the table
     *
     * @param string $column
     * @param string $geomType
     * @param int    $srid
     *
     * @return \Illuminate\Support\Fluent
     */
    public function point($column, $geomType = PostGISColumn::GEOGRAPHY, $srid = 4326)
    {
        return $this->addColumn('point', $column, compact('geomType', 'srid'));
    }

    /**
     * Add a multipoint column on the table
     *
     * @param        $column
     * @param string $geomType
     * @param int    $srid
     *
     * @return \Illuminate\Support\Fluent
     */
    public function multipoint($column, $geomType = PostGISColumn::GEOGRAPHY, $srid = 4326)
    {
        return $this->addColumn('multipoint', $column, compact('geomType', 'srid'));
    }

    /**
     * Add a polygon column on the table
     *
     * @param        $column
     * @param string $geomType
     * @param int    $srid
     *
     * @return \Illuminate\Support\Fluent
     */
    public function polygon($column, $geomType = PostGISColumn::GEOGRAPHY, $srid = 4326)
    {
        return $this->addColumn('polygon', $column, compact('geomType', 'srid'));
    }

    /**
     * Add a multipolygon column on the table
     *
     * @param        $column
     * @param string $geomType
     * @param int    $srid
     *
     * @return \Illuminate\Support\Fluent
     */
    public function multipolygon($column, $geomType = PostGISColumn::GEOGRAPHY, $srid = 4326)
    {
        return $this->addColumn('multipolygon', $column, compact('geomType', 'srid'));
    }

    /**
     * Add a linestring column on the table
     *
     * @param        $column
     * @param string $geomType
     * @param int    $srid
     *
     * @return \Illuminate\Support\Fluent
     */
    public function linestring($column, $geomType = PostGISColumn::GEOGRAPHY, $srid = 4326)
    {
        return $this->addColumn('linestring', $column, compact('geomType', 'srid'));
    }

    /**
     * Add a multilinestring column on the table
     *
     * @param        $column
     * @param string $geomType
     * @param int    $srid
     *
     * @return \Illuminate\Support\Fluent
     */
    public function multilinestring($column, $geomType = PostGISColumn::GEOGRAPHY, $srid = 4326)
    {
        return $this->addColumn('multilinestring', $column, compact('geomType', 'srid'));
    }

    /**
     * Add a geography column on the table
     *
     * @param   string  $column
     * @return \Illuminate\Support\Fluent
     */
    public function geography($column)
    {
        return $this->addColumn('geography', $column);
    }

    /**
     * Add a geometry column on the table
     *
     * @param   string $column
     *
     * @return \Illuminate\Support\Fluent
     */
    public function geometry($column)
    {
        return $this->addColumn('geometry', $column);
    }

    /**
     * Add a geometrycollection column on the table
     *
     * @param      $column
     * @param null $srid
     * @param int $dimensions
     * @param bool $typmod
     * @return \Illuminate\Support\Fluent
     */
    public function geometrycollection($column, $srid = null, $dimensions = 2, $typmod = true)
    {
        return $this->addCommand('geometrycollection', compact('column', 'srid', 'dimensions', 'typmod'));
    }

    /**
     * Enable postgis on this database.
     * Will create the extension in the database.
     *
     * @return \Illuminate\Support\Fluent
     */
    public function enablePostgis()
    {
        return $this->addCommand('enablePostgis');
    }

    /**
     * Disable postgis on this database.
     * WIll drop the extension in the database.
     * @return \Illuminate\Support\Fluent
     */
    public function disablePostgis()
    {
        return $this->addCommand('disablePostgis');
    }

}
