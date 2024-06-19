<?php

namespace Tests\Unit\Helpers;

use App\Helpers\Converter;
use App\Helpers\ResponseFormatter;
use App\Models\Secret;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Request;
use Tests\TestCase;

class ResponseFormatterTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testFormatResponseWithXml()
    {
        $secret = Secret::factory()->create();
        $request = Request::create('/test', 'GET', [], [], [], ['HTTP_ACCEPT' => 'application/xml']);

        $response = ResponseFormatter::formatResponse($request, $secret);

        $this->assertEquals(200, $response->getStatusCode());

        $contentType = $response->headers->get('Content-Type');
        $this->assertEquals('application/xml', $contentType);

        $expectedXml = Converter::convertToXML($secret->toArray());

        $this->assertXmlStringEqualsXmlString($expectedXml, $response->getContent());

    }

    public function testFormatResponseWithJson()
    {
        $secret = Secret::factory()->create();
        $request = Request::create('/test', 'GET', [], [], [], ['HTTP_ACCEPT' => 'application/json']);

        $response = ResponseFormatter::formatResponse($request, $secret);

        $this->assertEquals(200, $response->getStatusCode());

        $contentType = $response->headers->get('Content-Type');
        $this->assertEquals('application/json', $contentType);

        $expectedJson = $secret->toJson();

        $this->assertEquals($expectedJson, $response->getContent());

    }
}
