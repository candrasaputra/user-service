<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Testing\Fluent\AssertableJson;

class UserTest extends TestCase
{
    /**
     * @group getUsers
     */
    public function test_get_all_users_success(): void
    {
        $this->seed([UserSeeder::class]);
        $user = User::where('username', 'testing1user')->first();

        $token = $user->createToken('TestToken')->plainTextToken;
    
        $response = $this->get('/api/users',[ 'Authorization' => "Bearer $token" ]);

        $response->assertStatus(200);
        $response->assertJson(fn (AssertableJson $json) =>
            $json->has('data')
                ->whereType('data', 'array|null')
        );
    }

    /**
     * @group getUser
     */
    public function test_get_user_success(): void
    {
        $this->seed([UserSeeder::class]);
        $user = User::where('username', 'testing1user')->first();

        $token = $user->createToken('TestToken')->plainTextToken;
    
        $response = $this->get("/api/users/$user->id",[ 'Authorization' => "Bearer $token" ]);

        $response->assertStatus(200);
        $response->assertJson(fn (AssertableJson $json) =>
            $json->has('data')
                ->whereType('data.username', 'string|null')
        );
    }

    /**
     * @group getUser
     */
    public function test_get_user_fails_due_to_invalid_user(): void
    {
        $this->seed([UserSeeder::class]);
        $user = User::where('username', 'testing1user')->first();

        $token = $user->createToken('TestToken')->plainTextToken;
    
        $response = $this->get('/api/users/0',[ 'Authorization' => "Bearer $token" ]);

        $response->assertStatus(400);
        $response->assertJson([
            'message' => 'Invalid input',
            'validations' => [
                "id" => [
                    "The selected id is invalid."
                ]
            ]
        ]);
    }

    /**
     * @group createUser
     */
    public function test_create_user_success(): void
    {
        $this->seed([UserSeeder::class]);
        $user = User::where('username', 'testing1user')->first();

        $token = $user->createToken('TestToken')->plainTextToken;
    
        $response = $this->post('/api/users', [
            'name' => 'candra saputra',
            'username' => 'candrasaputra',
            'password' => 'admin#1234',
            'confirm_password' => 'admin#1234'
        ],
        [
            'Authorization' => "Bearer $token"
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'username berhasil disimpan'
        ]);
    }

    /**
     * @group createUser
     */
    public function test_create_user_fails_due_to_missing_password(): void
    {
        $this->seed([UserSeeder::class]);
        $user = User::where('username', 'testing1user')->first();

        $token = $user->createToken('TestToken')->plainTextToken;
    
        $response = $this->post('/api/users', [
            'name' => 'candra saputra',
            'username' => 'candrasaputra',
            'confirm_password' => 'admin#1234'
        ],
        [
            'Authorization' => "Bearer $token"
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

    /**
     * @group createUser
     */
    public function test_create_user_fails_due_to_token_expied(): void
    {
        $this->seed([UserSeeder::class]);
        $user = User::where('username', 'testing1user')->first();

        $token = $user->createToken('TestToken')->plainTextToken;

        // Get the token record from the database
        $tokenRecord = $user->tokens()->first();

        // Manually set the token's expiration date to the past
        $tokenRecord->expires_at = Carbon::now()->subDays(1);
        $tokenRecord->save();

        $response = $this->post('/api/users', [
            'name' => 'candra saputra',
            'username' => 'candrasaputra',
            'password' => 'admin#1234',
            'confirm_password' => 'admin#1234'
        ],
        [
            'Accept' => 'application/json',
            'Authorization' => "Bearer $tokenRecord->token"
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Unauthenticated.'
        ]);
    }

    /**
     * @group updateUser
     */
    public function test_update_user_success(): void
    {
        $this->seed([UserSeeder::class]);
        $user = User::where('username', 'testing1user')->first();

        $token = $user->createToken('TestToken')->plainTextToken;
    
        $response = $this->patch("/api/users/$user->id",
            [
                'name' => 'new name',
                'username' => 'newusername'
            ],
            [
                'Authorization' => "Bearer $token"
            ]
        );

        $user = User::where('id', $user->id)->first();
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'username berhasil diperbarui'
        ]);
        self::assertEquals('new name', $user->name);
        self::assertEquals('newusername', $user->username);
    }

    /**
     * @group updateUser
     */
    public function test_update_user_fails_due_to_invalid_user(): void
    {
        $this->seed([UserSeeder::class]);
        $user = User::where('username', 'testing1user')->first();

        $token = $user->createToken('TestToken')->plainTextToken;
    
        $response = $this->patch("/api/users/0",
            [
                'name' => 'new name',
                'username' => 'newusername'
            ],
            [
                'Authorization' => "Bearer $token"
            ]
        );

        $response->assertStatus(400);
        $response->assertJson([
            'message' => 'Invalid input',
            'validations' => [
                "id" => [
                    "The selected id is invalid."
                ]
            ]
        ]);
    }

    /**
     * @group updateUser
     */
    public function test_update_user_fails_due_to_name_less_then_four(): void
    {
        $this->seed([UserSeeder::class]);
        $user = User::where('username', 'testing1user')->first();

        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->patch("/api/users/$user->id",
            [
                'name' => 'new',
                'username' => 'newusername'
            ],
            [
                'Authorization' => "Bearer $token"
            ]
        );

        $response->assertStatus(400);
        $response->assertJson([
            'message' => 'Invalid input',
            'validations' => [
                "name" => [
                    "The name field must be at least 4 characters."
                ]
            ]
        ]);
    }

    /**
     * @group updatePassword
     */
    public function test_update_password_success(): void
    {
        $this->seed([UserSeeder::class]);
        $user = User::where('username', 'testing1user')->first();

        $token = $user->createToken('TestToken')->plainTextToken;
    
        $response = $this->patch("/api/users/$user->id/update-password",
            [
                'password' => 'anewpassword',
                'confirm_password' => 'anewpassword'
            ],
            [
                'Authorization' => "Bearer $token"
            ]
        );

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'password berhasil diperbarui'
        ]);
    }

    /**
     * @group updatePassword
     */
    public function test_update_password_fails_due_to_invalid_user(): void
    {
        $this->seed([UserSeeder::class]);
        $user = User::where('username', 'testing1user')->first();

        $token = $user->createToken('TestToken')->plainTextToken;
    
        $response = $this->patch("/api/users/0/update-password",
            [
                'password' => 'anewpassword',
                'confirm_password' => 'anewpassword'
            ],
            [
                'Authorization' => "Bearer $token"
            ]
        );

        $response->assertStatus(400);
        $response->assertJson([
            'message' => 'Invalid input',
            'validations' => [
                "id" => [
                    "The selected id is invalid."
                ]
            ]
        ]);
    }

    /**
     * @group updatePassword
     */
    public function test_update_password_fails_due_to_miss_match_password(): void
    {
        $this->seed([UserSeeder::class]);
        $user = User::where('username', 'testing1user')->first();

        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->patch("/api/users/$user->id/update-password",
            [
                'password' => 'anewpassword',
                'confirm_password' => 'missmatchanewpassword'
            ],
            [
                'Authorization' => "Bearer $token"
            ]
        );

        $response->assertStatus(400);
        $response->assertJson([
            'message' => 'Invalid input',
            'validations' => [
                "confirm_password" => [
                    "The confirm password field must match password."
                ]
            ]
        ]);
    }

    /**
     * @group deletePassword
     */
    public function test_delete_password_success(): void
    {
        $this->seed([UserSeeder::class]);
        $user = User::where('username', 'testing2user')->first();

        $token = $user->createToken('TestToken')->plainTextToken;
    
        $response = $this->delete("/api/users/$user->id/delete-password",
            [
                'confirm_password' => 'testing2user'
            ],
            [
                'Authorization' => "Bearer $token"
            ]
        );

        $user = User::where('username', 'testing2user')->first();

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'password berhasil dihapus'
        ]);
        self::assertEquals(null, $user->password);
    }

    /**
     * @group deletePassword
     */
    public function test_delete_password_fails_due_to_invalid_user(): void
    {
        $this->seed([UserSeeder::class]);
        $user = User::where('username', 'testing1user')->first();
        $token = $user->createToken('TestToken')->plainTextToken;
    
        $response = $this->delete("/api/users/0/delete-password",
            [
                'confirm_password' => 'testing1user'
            ],
            [
                'Authorization' => "Bearer $token"
            ]
        );

        $response->assertStatus(400);
        $response->assertJson([
            'message' => 'Invalid input',
            'validations' => [
                "id" => [
                    "The selected id is invalid."
                ]
            ]
        ]);
    }

    /**
     * @group deletePassword
     */
    public function test_delete_password_fails_due_to_confirm_password_less_then_eight(): void
    {
        $this->seed([UserSeeder::class]);
        $user = User::where('username', 'testing1user')->first();
        $token = $user->createToken('TestToken')->plainTextToken;
    
        $response = $this->delete("/api/users/$user->id/delete-password",
            [
                'confirm_password' => 'seven'
            ],
            [
                'Authorization' => "Bearer $token"
            ]
        );

        $response->assertStatus(400);
        $response->assertJson([
            'message' => 'Invalid input',
            'validations' => [
                "confirm_password" => [
                    "The confirm password field must be at least 8 characters."
                ]
            ]
        ]);
    }

    /**
     * @group deletePassword
     */
    public function test_delete_password_fails_due_to_miss_match_password(): void
    {
        $this->seed([UserSeeder::class]);
        $user = User::where('username', 'testing1user')->first();

        $token = $user->createToken('TestToken')->plainTextToken;
        $response = $this->delete("/api/users/$user->id/delete-password",
        [
            'confirm_password' => 'invalidpassword'
        ],
        [
            'Authorization' => "Bearer $token"
            ]
        );
        
        $response->assertStatus(400);
        $response->assertJson([
            'message' => 'Invalid input',
            'validations' => [
                "confirm_password" => [
                    "The provided password does not match our records."
                ]
            ]
        ]);
    }
}
