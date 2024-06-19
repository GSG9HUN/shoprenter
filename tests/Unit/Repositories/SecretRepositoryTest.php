<?php

namespace Tests\Unit\Repositories;

use App\Models\Secret;
use App\Repositories\SecretRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecretRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected SecretRepository $secretRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->secretRepository = new SecretRepository();
    }

    public function testReturnsSecretByHash()
    {
        $hash = hash('sha256', 'test_secret');
        $secret = Secret::factory()->create([
            'hash' => $hash,
            'secretText' => 'test_secret',
        ]);

        $foundSecret = $this->secretRepository->getSecretByHash($secret->hash);

        $this->assertEquals($secret->hash, $foundSecret->hash);
        $this->assertEquals($secret->secretText, $foundSecret->secretText);
    }

    public function testAddSecret()
    {
        $hash = hash('sha256', 'test_secret');
        $data = [
            'hash' => $hash,
            'secret' => 'test_secret',
            'expireAfter' => 60,
            'expireAfterViews' => 5,
        ];

        $addedSecret = $this->secretRepository->addSecret($data);

        $this->assertDatabaseHas('secrets', [
            'hash' => $addedSecret->hash,
            'secretText' => 'test_secret',
        ]);
    }

    public function testAddSecretWithNoExpireDate()
    {
        $hash = hash('sha256', 'test_secret');
        $data = [
            'hash' => $hash,
            'secret' => 'test_secret',
            'expireAfter' => 0,
            'expireAfterViews' => 5,
        ];

        $addedSecret = $this->secretRepository->addSecret($data);

        $this->assertDatabaseHas('secrets', [
            'hash' => $addedSecret->hash,
            'secretText' => 'test_secret',
        ]);
    }
    public function testUpdateSecretExpireAfterViews()
    {
        $secret = Secret::factory()->create([
            'expireAfterViews' => 5,
        ]);

        $this->secretRepository->updateSecret($secret->hash);

        $updatedSecret = Secret::find($secret->hash);
        $this->assertNotNull($updatedSecret);
        $this->assertEquals(4, $updatedSecret->expireAfterViews);
    }
}
