<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Config;

class AuthenticatedTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_login_access()
    {
        $this->withoutExceptionHandling();
        $baseUrl = Config::get('app.url') . '/api/login';

        $user = User::factory()->create();

        $response = $this->json('POST', $baseUrl . '/', [
            'email' => $user->email,
            'password' => $user->password
        ]);

        $response->assertStatus(200)->assertJsonStructure([
            'access_token', 'token_type', 'expires_in'
        ]);
    }
}
