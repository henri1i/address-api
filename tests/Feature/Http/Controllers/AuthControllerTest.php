<?php

namespace Test\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

use function PHPUnit\Framework\assertNotEquals;

class AuthControllerTest extends TestCase
{
    /** @test */
    public function cannot_register_with_existing_email()
    {
        User::factory(['email' => 'john.doe@example.com'])->create();

        $this->postJson(route('auth.register'), [
            'name'                  => 'John Doe',
            'email'                 => 'john.doe@example.com',
            'password'              => '$password',
            'password_confirmation' => '$password',
        ])->assertJsonValidationErrorFor('email');
    }

    /** @test */
    public function must_confirm_password()
    {
        $this->postJson(route('auth.register'), [
            'name'                  => 'John Doe',
            'email'                 => 'john.doe@example.com',
            'password'              => '$password',
        ])->assertJsonValidationErrorFor('password');
    }

    /** @test */
    public function password_should_have_at_least_eight_digits()
    {
        $this->postJson(route('auth.register'), [
            'name'                  => 'John Doe',
            'email'                 => 'john.doe@example.com',
            'password'              => 'pass',
            'password_confirmation' => 'pass',
        ])->assertJsonValidationErrorFor('password');
    }

    /** @test */
    public function can_register_user()
    {
        $this->postJson(route('auth.register'), [
            'name'                  => 'John Doe',
            'email'                 => 'john.doe@gmail.com',
            'password'              => '$password',
            'password_confirmation' => '$password',
        ]);

        $user = User::where('email', 'john.doe@gmail.com')->first();
        $this->assertNotEmpty($user);
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function needs_to_give_email_and_password_to_login_endpoint()
    {
        $this->postJson(route('auth.login'), [
            'email'    => '',
            'password' => '',
        ])->assertJsonValidationErrors(['email', 'password']);
    }

    /** @test */
    public function can_login_user()
    {
        $user = User::factory([
            'email' => 'john.doe@example.com',
            'password' => Hash::make('$password'),
        ])->create();

        $response = $this->postJson(route('auth.login'), [
            'email'    => 'john.doe@example.com',
            'password' => '$password',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['user', 'auth' => ['token', 'type']]);
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function can_refresh_token()
    {
        $user = User::factory()
            ->create();

        $token = JWTAuth::fromUser($user);

        $response = $this->postJson(route('auth.refresh'), ['token' => $token]);

        $this->assertNotEquals($token, $response->json('auth.token'));
        $this->assertEquals($user->id, JWTAuth::user()->id);
    }

    /** @test */
    public function user_can_logout()
    {
        $user = User::factory()
            ->create();

        $token = JWTAuth::fromUser($user);

        $response = $this->postJson(route('auth.logout'), ['token' => $token]);

        $response->assertOk();
    }
}