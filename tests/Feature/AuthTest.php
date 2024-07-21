<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;

class AuthTest extends TestCase
{
    public function test_login_success()
    {
        $this->seed([UserSeeder::class]);
        $response = $this->post('/api/login', [
            'username' => 'testing1user',
            'password' => 'testing1user'
        ]);

        $response->assertStatus(200);

        $response->assertJson(fn (AssertableJson $json) =>
            $json->has('data')
                ->whereType('data.token', 'string|null')
        );
    }

    public function test_login_fails_with_incorrect_credentials()
    {
        $this->seed([UserSeeder::class]);
        $response = $this->post('/api/login', [
            'username' => 'nouser',
            'password' => 'nopassword'
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'username atau password salah'
        ]);
    }

    public function test_login_fails_due_to_missing_password() {
        $this->seed([UserSeeder::class]);
        $response = $this->post('/api/login', [
            'username' => 'nouser'
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'message' => 'Invalid input',
            'validations' => [
                "password" => [
                    "The password field is required."
                ]
            ]
        ]);
    }
}
