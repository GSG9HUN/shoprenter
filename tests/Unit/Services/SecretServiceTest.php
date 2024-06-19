<?php

namespace Tests\Unit\Services;

use App\Interfaces\SecretRepositoryInterface;
use App\Models\Secret;
use App\Services\SecretService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Mockery\LegacyMockInterface;
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

    public function testReturnsSecretWhenValid()
    {
        $hash = 'test_hash';
        $secret = new Secret([
            'hash' => $hash,
            'secretText' => 'test_secret',
            'expireAfterViews' => 1,
            'expireAfter' => Carbon::now()->addHours(1),
        ]);

        $this->secretRepository
            ->expects('getSecretByHash')
            ->with($hash)
            ->andReturns($secret);

        $this->secretRepository
            ->expects('updateSecret')
            ->with($hash);

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
        $hash = 'test_hash';
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
        $hash = 'test_hash';
        $secret = new Secret([
            'hash' => $hash,
            'secretText' => 'test_secret',
            'expireAfterViews' => 1,
            'expireAfter' => Carbon::now()->subHours(1), // Múltbeli időpont
        ]);

        $this->secretRepository
            ->expects('getSecretByHash')
            ->with($hash)
            ->andReturns($secret);

        $result = $this->secretService->getSecret($hash);

        $this->assertNull($result);
    }
}
