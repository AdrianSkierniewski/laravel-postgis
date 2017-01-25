<?php namespace Phaza\LaravelPostgis\Geometries;

use GeoIO\Geometry\Extractor;
use GeoIO\WKB\Generator\Generator;
use GeoIO\WKB\Parser\Parser;

trait WKBHandlerTrait {

    protected function getWkbGenerator()
    {
        return new Generator(new Extractor());
    }

    protected function getWkbParser()
    {
        return new Parser(new Factory());
    }

}