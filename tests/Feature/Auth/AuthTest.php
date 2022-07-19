<?php

namespace Test\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthTest extends TestCase
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
}