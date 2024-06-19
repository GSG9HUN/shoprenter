<?php

namespace App\Helpers;

use Spatie\ArrayToXml\ArrayToXml;

class Converter
{
    public static function convertToXML(array $array): string
    {
        return ArrayToXml::convert($array, [
            'rootElementName' => 'secret',
            'xmlEncoding' => 'UTF-8'
        ]);
    }
}
