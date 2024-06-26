<?php

namespace Tests\Feature\Http\Controllers;

use App\Http\Controllers\SecretController;
use App\Http\Requests\SecretRequest;
use App\Models\Secret;
use App\Services\SecretService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SecretControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $secretService;
    protected $secretController;

    protected function setUp(): void
    {
        parent::setUp();
        $this->secretService = $this->mock(SecretService::class);

        $this->secretController = new SecretController($this->secretService);
    }

    public function testReturnsSecretByHashAsJson()
    {
        $hash = Hash::make('test_secret');
        $request = new Request();

        $secret = new Secret([
            'hash' => $hash,
            'secretText' => 'test_secret',
            'expireAfterViews' => 1,
            'expireAfter' => null,
        ]);

        $hash = $secret->hash;

        $this->secretService
            ->expects('getSecret')
            ->with($hash)
            ->andReturns($secret);

        $response = $this->secretController->getSecretByHash($request, $hash);


        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('test_secret', $response->getData()->secretText);
    }

    public function testReturnsSecretByHashAsXml()
    {
        $hash = Hash::make('test_secret');
        $request = new Request();
        $request->headers->set('Accept','application/xml');

        $secret = new Secret([
            'hash' => $hash,
            'secretText' => 'test_secret',
            'expireAfterViews' => 1,
            'expireAfter' => null,
        ]);

        $this->secretService
            ->expects('getSecret')
            ->with($hash)
            ->andReturns($secret);

        $response = $this->secretController->getSecretByHash($request, $hash);


        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $expectedXml = <<<XML
<?xml version="1.0"?><secret><hash>$hash</hash><secretText>test_secret</secretText><expireAfterViews>1</expireAfterViews><expireAfter></expireAfter></secret>
XML;
        $this->assertXmlStringEqualsXmlString($expectedXml, $response->getContent());
    }

    public function testReturnsNotFoundResponseForInvalidHash()
    {
        $hash = 'invalid_hash';
        $request = new Request();

        $this->secretService
            ->expects('getSecret')
            ->with($hash)
            ->andReturns(null);

        $response = $this->secretController->getSecretByHash($request, $hash);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('"Secret not found"', $response->getContent());
    }

    public function testAddsSecretAndReturnJsonResponse()
    {
        $requestData = [
            'secret' => 'test_secret',
            'expireAfter' => 60,
            'expireAfterViews' => 5,
        ];

        $request = SecretRequest::create('/addSecret', 'POST', $requestData);

        $createdSecret = new Secret([
            'hash' => Hash::make('test_secret'),
            'secretText' => 'test_secret',
            'expireAfterViews' => 5,
            'expireAfter' => Carbon::now()->addMinutes(60),
        ]);

        $this->secretService
            ->expects('addSecret')
            ->with($requestData)
            ->andReturns($createdSecret);

        $response = $this->secretController->addSecret($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testAddsSecretAndReturnResponse()
    {
        $requestData = [
            'secret' => 'test_secret',
            'expireAfter' => 60,
            'expireAfterViews' => 5,
        ];

        $request = SecretRequest::create('/addSecret', 'POST', $requestData);
        $request->headers->set('Accept', 'application/xml');
        $createdSecret = new Secret([
            'hash' => Hash::make('test_secret'),
            'secretText' => 'test_secret',
            'expireAfterViews' => 5,
            'expireAfter' => Carbon::now()->addMinutes(60),
        ]);

        $this->secretService
            ->expects('addSecret')
            ->with($requestData)
            ->andReturns($createdSecret);

        $response = $this->secretController->addSecret($request);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
