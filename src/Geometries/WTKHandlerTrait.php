<?php namespace Phaza\LaravelPostgis\Geometries;

use GeoIO\Geometry\Extractor;
use GeoIO\WKT\Generator\Generator;
use GeoIO\WKT\Parser\Parser;

trait WTKHandlerTrait {

    /**
     * @return Generator
     */
    protected function getWtkGenerator()
    {
        return new Generator(new Extractor(), ['case' => Generator::CASE_UPPER]);
    }

    /**
     * @return Parser
     */
    protected function getWktParser()
    {
        return new Parser(new Factory());
    }

}