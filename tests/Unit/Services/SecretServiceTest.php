<?php

namespace Tests\Unit\Services;

use App\Interfaces\SecretRepositoryInterface;
use App\Models\Secret;
use App\Services\SecretService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Mockery\LegacyMockInterface;
use ReflectionClass;
use Tests\TestCase;

class SecretServiceTest extends TestCase
{
    use RefreshDatabase;

    protected SecretRepositoryInterface|LegacyMockInterface $secretRepository;
    protected SecretService $secretService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->secretRepository = Mockery::mock(SecretRepositoryInterface::class);
        $this->secretService = new SecretService($this->secretRepository);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function testAddSecret()
    {
        $data = [
            'secret' => 'test_secret',
            'expireAfter' => 60,
            'expireAfterViews' => 5,
        ];

        $expectedSecret = new Secret([
            'hash' => Hash::make('test_secret'),
            'secretText' => 'test_secret',
            'expireAfterViews' => 5,
            'expireAfter' => now()->addMinutes(60),
        ]);

        $this->secretRepository
            ->expects('addSecret')
            ->with($data)
            ->andReturns($expectedSecret);

        $result = $this->secretService->addSecret($data);

        $this->assertInstanceOf(Secret::class, $result);
        $this->assertEquals($expectedSecret->secretText, $result->secretText);
        $this->assertEquals($expectedSecret->expireAfterViews, $result->expireAfterViews);
        $this->assertTrue(Hash::check('test_secret', $result->hash));
    }

    public function testReturnsSecretWhenValid()
    {
        $hash = Hash::make('test_secret');
        $secret = new Secret([
            'hash' => $hash,
            'secretText' => 'test_secret',
            'expireAfterViews' => 1,
            'expireAfter' => Carbon::now()->addHours(1),
        ]);

        $this->secretRepository
            ->expects('getSecretByHash')
            ->with($hash)
            ->twice()
            ->andReturns($secret);

        $this->secretRepository
            ->expects('updateSecret')
            ->with($secret);

        $result = $this->secretService->getSecret($hash);

        $this->assertInstanceOf(Secret::class, $result);
        $this->assertEquals('test_secret', $result->secretText);
    }

    public function testReturnsSecretWhenValidWithNoExpireDate()
    {
        $hash = Hash::make('test_secret');
        $secret = new Secret([
            'hash' => $hash,
            'secretText' => 'test_secret',
            'expireAfterViews' => 1,
            'expireAfter' => null,
        ]);

        $this->secretRepository
            ->expects('getSecretByHash')
            ->with($hash)
            ->times(2)
            ->andReturns($secret);

        $this->secretRepository
            ->expects('updateSecret')
            ->with($secret);

        $result = $this->secretService->getSecret($hash);

        $this->assertInstanceOf(Secret::class, $result);
        $this->assertEquals('test_secret', $result->secretText);
    }

    public function testReturnsNullWhenSecretNotFound()
    {
        $hash = 'non_existing_hash';

        $this->secretRepository
            ->expects('getSecretByHash')
            ->with($hash)
            ->andReturnNull();

        $result = $this->secretService->getSecret($hash);

        $this->assertNull($result);
    }

    public function testReturnsNullWhenExpireAfterViewsZero()
    {
        $hash = Hash::make('test_secret');
        $secret = new Secret([
            'hash' => $hash,
            'secretText' => 'test_secret',
            'expireAfterViews' => 0,
            'expireAfter' => Carbon::now()->addHours(1),
        ]);

        $this->secretRepository
            ->expects('getSecretByHash')
            ->with($hash)
            ->andReturns($secret);

        $result = $this->secretService->getSecret($hash);

        $this->assertNull($result);
    }

    public function testReturnsNullWhenExpired()
    {
        $hash = Hash::make('test_secret');
        $secret = new Secret([
            'hash' => $hash,
            'secretText' => 'test_secret',
            'expireAfterViews' => 1,
            'expireAfter' => Carbon::now()->subHours(1),
        ]);

        $this->secretRepository
            ->expects('getSecretByHash')
            ->with($hash)
            ->andReturns($secret);

        $result = $this->secretService->getSecret($hash);

        $this->assertNull($result);
    }

    public function testUpdateSecret()
    {
        $hash = Hash::make('test_secret');
        $secret = new Secret([
            'hash' => $hash,
            'secretText' => 'test_secret',
            'expireAfterViews' => 1,
            'expireAfter' => Carbon::now()->addHours(5),
        ]);

        $this->secretRepository
            ->expects('getSecretByHash')
            ->with($hash)
            ->twice()
            ->andReturns($secret);

        $this->secretRepository
            ->expects('updateSecret')
            ->with($secret);

        $secretServiceClass = new SecretService($this->secretRepository);
        $reflectionSecretServiceClass = new ReflectionClass($secretServiceClass);
        $method = $reflectionSecretServiceClass->getMethod('updateSecret');

        $method->invoke($secretServiceClass, $hash);

        $result = $this->secretRepository->getSecretByHash($hash);

        $this->assertEquals(0, $result->expireAfterViews);
    }

}
