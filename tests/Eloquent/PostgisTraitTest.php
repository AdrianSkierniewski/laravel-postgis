<?php

use Mockery as m;
use GeoIO\Dimension;
use GeoIO\Geometry\Coordinates;
use GeoIO\Geometry\Point;
use Illuminate\Database\Eloquent\Model;
use Phaza\LaravelPostgis\Eloquent\PostgisTrait;
use Phaza\LaravelPostgis\PostGISColumn;
use Phaza\LaravelPostgis\PostgisConnection;

class PostgisTraitTest extends BaseTestCase
{
    /**
     * @var TestModel
     */
    protected $model;

    /**
     * @var array
     */
    protected $queries;

    public function setUp()
    {
        $this->model = new TestModel();
        $this->queries = &$this->model->getConnection()->getPdo()->queries;
    }

    public function tearDown()
    {
        $this->model->getConnection()->getPdo()->resetQueries();
    }

    public function testInsertPointHasCorrectSql()
    {
        $this->model->point               = new Point(Dimension::DIMENSION_2D, new Coordinates(2, 1));
        $this->model->point_geometry      = new Point(Dimension::DIMENSION_2D, new Coordinates(2, 1));
        $this->model->point_geometry_srid = new Point(Dimension::DIMENSION_2D, new Coordinates(2, 1), 2180);
        $this->model->save();

        $this->assertContains("ST_GeogFromText('POINT(2.000000 1.000000)')", $this->queries[0]);
        $this->assertContains("ST_GeomFromText('POINT(2.000000 1.000000)')", $this->queries[0]);
        $this->assertContains("ST_GeomFromText('POINT(2.000000 1.000000)', 2180)", $this->queries[0]);
    }

    public function testUpdatePointHasCorrectSql()
    {
        $this->model->exists = true;
        $this->model->point = new Point(Dimension::DIMENSION_2D, new Coordinates(2, 4));
        $this->model->point_geometry = new Point(Dimension::DIMENSION_2D, new Coordinates(2, 4));
        $this->model->save();

        $this->assertContains("ST_GeogFromText('POINT(2.000000 4.000000)')", $this->queries[0]);
        $this->assertContains("ST_GeomFromText('POINT(2.000000 4.000000)')", $this->queries[0]);
    }
}

class TestModel extends Model
{
    use PostgisTrait;

    protected $postgisFieldTypes = [
        'point'               => PostGISColumn::GEOGRAPHY,
        'point_geometry'      => PostGISColumn::GEOMETRY,
        'point_geometry_srid' => PostGISColumn::GEOMETRY,
    ];

    protected $postgisFields = [
        'point'               => Point::class,
        'point_geometry'      => Point::class,
        'point_geometry_srid' => Point::class
    ];

    public static $pdo;

    public static function resolveConnection($connection = null)
    {
        if (is_null(static::$pdo)) {
            static::$pdo = m::mock('TestPDO')->makePartial();
        }

        return new PostgisConnection(static::$pdo);
    }

    public function testrelatedmodels()
    {
        return $this->hasMany(TestRelatedModel::class);
    }

    public function testrelatedmodels2()
    {
        return $this->belongsToMany(TestRelatedModel::class);
    }

}

class TestRelatedModel extends TestModel
{
    public function testmodel()
    {
        return $this->belongsTo(TestModel::class);
    }

    public function testmodels()
    {
        return $this->belongsToMany(TestModel::class);
    }
}

class TestPDO extends PDO
{
    public $queries = [];
    public $counter = 1;

    public function prepare($statement, $driver_options = null)
    {
        $this->queries[] = $statement;

        $stmt = m::mock('PDOStatement');
        $stmt->shouldReceive('bindValue')->zeroOrMoreTimes();
        $stmt->shouldReceive('execute');
        $stmt->shouldReceive('fetchAll');
        $stmt->shouldReceive('rowCount');

        return $stmt;
    }

    public function lastInsertId($name = null)
    {
        return $this->counter++;
    }

    public function resetQueries()
    {
        $this->queries = [];
    }
}
