<?php

namespace Tests\Feature\Http\Requests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class SecretRequestTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testValidSecretRequest()
    {
        $data = [
            'secret' => 'test_secret',
            'expireAfter' => 60,
            'expireAfterViews' => 5,
        ];

        $response = $this->postJson('/v1/secret', $data);

        $response->assertStatus(Response::HTTP_OK);
    }

    public function testInvalidSecretRequest()
    {
        $data = [
            'secret' => '',
            'expireAfter' => -1,
            'expireAfterViews' => 0,
        ];

        $response = $this->postJson('/v1/secret', $data);

        $response->assertStatus(405);
        $response->assertContent('"Invalid input"');
    }
}
