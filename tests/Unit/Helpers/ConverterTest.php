<?php

namespace Tests\Unit\Helpers;

use App\Helpers\Converter;
use Tests\TestCase;

class ConverterTest extends TestCase
{

    public function testConvertToXML()
    {
        $array = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'age' => 30
        ];

        $xmlString = Converter::convertToXML($array);

        $expectedXml = <<<XML
<?xml version="1.0"?>
<secret>
    <name>John Doe</name>
    <email>john@example.com</email>
    <age>30</age>
</secret>
XML;


        $normalizedExpectedXml = preg_replace('/\s+/', '', $expectedXml);
        $normalizedXmlString = preg_replace('/\s+/', '', $xmlString);

        $this->assertEquals($normalizedExpectedXml, $normalizedXmlString);
    }
}
