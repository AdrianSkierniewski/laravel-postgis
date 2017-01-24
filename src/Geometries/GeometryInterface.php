<?php namespace Phaza\LaravelPostgis\Geometries;

interface GeometryInterface
{
    public function getSRID();

    public function setSRID($srid);

    public function toWKT();

    public static function fromWKT($wkt);

    public static function fromWKB($wkb);

    public function __toString();
}
