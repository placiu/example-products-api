<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_will_register_user_with_valid_data(): void
    {
        $this->assertDatabaseEmpty(User::class);

        $this->post('/api/v1/register', [
            'name' => 'test_user',
            'email' => 'test_user@example.com',
            'password' => 'password',
            'confirm_password' => 'password'
        ]);

        $this->assertDatabaseCount(User::class, 1);
        $this->assertDatabaseHas(User::class, [
            'name' => 'test_user',
            'email' => 'test_user@example.com',
        ]);
    }

    public function test_it_will_respond_with_valid_status_after_registration(): void
    {
        $response = $this->post('/api/v1/register', [
            'name' => 'test_user',
            'email' => 'test_user@example.com',
            'password' => 'password',
            'confirm_password' => 'password'
        ]);

        $response->assertCreated();
    }

    public function test_it_will_respond_with_valid_user_data_after_registration(): void
    {
        $response = $this->post('/api/v1/register', [
            'name' => 'test_user',
            'email' => 'test_user@example.com',
            'password' => 'password',
            'confirm_password' => 'password'
        ]);

        $response->assertJson(fn (AssertableJson $json) =>
            $json->has('name')
                 ->has('email')
                 ->has('token')
        );
    }

    public function test_it_will_login_user_with_valid_data(): void
    {
        $user = User::factory()->create();

        $this->post('/api/v1/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
    }

    public function test_it_will_respond_with_valid_status_after_login(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/api/v1/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertOk();
    }

    public function test_it_will_respond_with_valid_user_data_after_login(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/api/v1/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertJson(fn (AssertableJson $json) =>
            $json->has('name')
                 ->has('email')
                 ->has('token')
        );
    }
}
