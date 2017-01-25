<?php namespace Eloquent;

use BaseTestCase;
use GeoIO\Dimension;
use GeoIO\Geometry\Coordinates;
use GeoIO\Geometry\LineString;
use GeoIO\Geometry\Point;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Query\Expression;
use Mockery as m;
use Phaza\LaravelPostgis\Eloquent\Builder;
use Phaza\LaravelPostgis\Eloquent\PostgisTrait;
use Phaza\LaravelPostgis\Geometries\Polygon;
use Phaza\LaravelPostgis\PostGISColumn;

class BuilderTest extends BaseTestCase
{
    protected $builder;

    /**
     * @var \Mockery\MockInterface $queryBuilder
     */
    protected $queryBuilder;

    protected function setUp()
    {
        $this->queryBuilder = m::mock(QueryBuilder::class);
        $this->queryBuilder->makePartial();

        $this->queryBuilder
          ->shouldReceive('from')
          ->andReturn($this->queryBuilder);

        $this->queryBuilder
          ->shouldReceive('take')
          ->with(1)
          ->andReturn($this->queryBuilder);

        $this->queryBuilder
          ->shouldReceive('get')
          ->andReturn([]);
    }

    public function testUpdate()
    {
        $this->queryBuilder
          ->shouldReceive('raw')
          ->with("ST_GeogFromText('POINT(2.000000 1.000000)')")
          ->andReturn(new Expression("ST_GeogFromText('POINT(2.000000 1.000000)')"));

        $this->queryBuilder
          ->shouldReceive('update')
          ->andReturn(1);

        $builder = m::mock(Builder::class, [$this->queryBuilder])->makePartial();
        $builder->setModel(new TestBuilderModel());
        $builder->shouldAllowMockingProtectedMethods();
        $builder
          ->shouldReceive('addUpdatedAtColumn')
          ->andReturn(['point' => new Point(Dimension::DIMENSION_2D, new Coordinates(2, 1))]);
        $builder->update(['point' => new Point(Dimension::DIMENSION_2D, new Coordinates(2, 1))]);
    }

    public function testUpdateLinestring()
    {
        $this->queryBuilder
          ->shouldReceive('raw')
          ->with("ST_GeogFromText('LINESTRING(0.000000 0.000000, 1.000000 1.000000, 2.000000 2.000000)')")
          ->andReturn(new Expression("ST_GeogFromText('LINESTRING(0.000000 0.000000, 1.000000 1.000000, 2.000000 2.000000)')"));

        $this->queryBuilder
          ->shouldReceive('update')
          ->andReturn(1);

        $lineString = new LineString(
            Dimension::DIMENSION_2D,
            [
                new Point(Dimension::DIMENSION_2D, new Coordinates(0, 0)),
                new Point(Dimension::DIMENSION_2D, new Coordinates(1, 1)),
                new Point(Dimension::DIMENSION_2D, new Coordinates(2, 2)),
            ]
        );

        $builder = m::mock(Builder::class, [$this->queryBuilder])->makePartial();
        $builder->setModel(new TestBuilderModel());
        $builder->shouldAllowMockingProtectedMethods();
        $builder
          ->shouldReceive('addUpdatedAtColumn')
          ->andReturn(['linestring' => $lineString]);

        $builder
            ->shouldReceive('asWKT')->with(m::any(), $lineString, 'linestring')->once();

        $builder->update(['linestring' => $lineString]);
    }
}

class TestBuilderModel extends Model
{
    use PostgisTrait;

    protected $postgisFieldTypes = [
        'point'      => PostGISColumn::GEOGRAPHY,
        'linestring' => PostGISColumn::GEOGRAPHY,
        'polygon'    => PostGISColumn::GEOGRAPHY
    ];

    protected $postgisFields = [
        'point'      => Point::class,
        'linestring' => LineString::class,
        'polygon'    => Polygon::class
    ];
}
