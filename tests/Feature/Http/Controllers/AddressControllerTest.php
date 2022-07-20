<?php

namespace Tests\Feature\Http\Controllers;

use App\Http\Resources\AddressResource;
use App\Models\Cep;
use Tests\TestCase;
use App\Models\User;
use Namshi\JOSE\JWS;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response;

class AddressControllerTest extends TestCase
{
    public User $user;

    public string $token;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->token = JWTAuth::fromUser($this->user);
    }

    /**
     * @test
     * @group show
     */
    public function it_should_list_both_address_and_cep_information()
    {
        $address = Address::factory()
            ->for($this->user)
            ->for(Cep::factory())
            ->create();

        $response = $this->getJson(route('addresses.show', ['address' => $address, 'token' => $this->token]));

        $response->assertResource(AddressResource::make($address));
    }

    /**
     * @test
     * @group list
     */
    public function it_should_return_response_errors_when_not_logged_in()
    {
        $this->getJson(route('addresses.index'))
            ->assertUnauthorized();
    }

    /**
     * @test
     * @group list
     */
    public function it_should_list_only_addresses_created_by_the_authenticated_user()
    {
        $otherUser = User::factory()->create();

        $cep = Cep::factory()->create();

        $address = Address::factory()
            ->for($this->user)
            ->for($cep)
            ->create();

        $otherAddress = Address::factory()
            ->for($otherUser)
            ->for($cep)
            ->create();

        $response = $this->getJson(route('addresses.index', ['token' => $this->token]));

        collect($response->decodeResponseJson()['data'])
            ->each(fn ($address) =>
                $this->assertNotEquals($address['id'], $otherAddress->id)
            );
    }

    /**
     * @test
     * @group list
     */
    public function can_change_items_per_page()
    {
        $response = $this->getJson(route('addresses.index', ['token' => $this->token, 'per_page' => 30]));

        $this->assertEquals(30, $response->json('meta.per_page'));
    }

    /**
     * @test
     * @group list
     */
    public function it_lists_all_addresses()
    {
        $perPage = 15;

        Address::factory()
            ->for($this->user)
            ->for(Cep::factory())
            ->count(30)
            ->create();

        $response = $this->get(route('addresses.index', ['token' => $this->token, 'per_page' => $perPage]));

        $response->assertResource(AddressResource::collection(Address::paginate($perPage)));
    }

    /**
     * @test
     * @group create
     */
    public function cep_must_have_at_least_eight_digits()
    {

        $this->postJson(route('addresses.store', ['token' => $this->token]), [
            'house_number'    => 500,
            'reference_point' => 'In front of the library',
            'cep'             => '12345',
        ])->assertJsonValidationErrorFor('cep');
    }

    /**
     * @test
     * @group create
     */
    public function it_returns_an_error_message_after_a_bad_api_request()
    {
        $cep = "12345678";

        Http::fake(['*' => Http::response(['erro' => true])]);

        $this->postJson(route('addresses.store'), [
            'house_number'    => 500,
            'cep'             => $cep,
            'token'           => $this->token,
            'reference_point' => 'Next to the coffee shop',
        ])
            ->assertJson(['message' => 'CEP field must be valid.']);
    }

    /**
     * @test
     * @group create
     */
    public function it_creates_an_address_based_on_the_given_cep()
    {
        $cepNumber = '12345678';
        Http::fake(['*' => Http::response($cep = Cep::factory(['number' => $cepNumber])->create(), Response::HTTP_CREATED)]);

        $this->postJson(route('addresses.store'), [
            'house_number'    => 500,
            'cep'             => $cepNumber,
            'token'           => $this->token,
            'reference_point' => 'Between the library and the coffee shop',
        ])
            ->status(Response::HTTP_CREATED);

        $this->assertEquals(Address::first()->cep->id, $cep->id);
        Http::assertNothingSent();
    }

    /**
     * @test
     * @group update
     */
    public function can_update_address_cep_with_existing_one()
    {
        $cep = Cep::factory(['number' => '12345678']);
        Http::fake();
        $address = Address::factory()
            ->for($this->user)
            ->for($cep)
            ->create();

        $newCep = Cep::factory(['number' => '87654321'])->create();

        $this->putJson(route('addresses.update', ['address' => $address->id]), [
            'token'   => $this->token,
            'cep'     => $newCep->number,
        ])
            ->assertOk();

        $this->assertEquals($newCep->number, $address->refresh()->cep->number);
        Http::assertNothingSent();
    }

    /**
     * @test
     * @group update
     */
    public function can_update_address_cep_with_a_new_one()
    {
        Http::fake([
            '*' => Http::response(json_decode(File::get(base_path('tests/Fixture/Services/ViaCepApi.json')), true))
        ]);

        $address = Address::factory()
            ->for($this->user)
            ->for(Cep::factory())
            ->create();

        $this->putJson(route('addresses.update', ['address' => $address->id]), [
            'token' => $this->token,
            'cep'   => '01001000'
        ]);

        $this->assertEquals('01001-000', $address->refresh()->cep->number);

        Http::assertSentCount(1);
    }

    /**
     * @test
     * @group destroy
     */
    public function can_update_every_other_address_fields()
    {
        Http::fake();

        $address = Address::factory()
            ->for($this->user)
            ->for(Cep::factory())
            ->create();

        $this->putJson(route('addresses.update', ['address' => $address->id]), [
            'token' => $this->token,
            'house_number' => 134,
            'reference_point' => 'Next to the bakery',
        ]);

        $this->assertEquals('134', $address->refresh()->house_number);
        $this->assertEquals('Next to the bakery', $address->refresh()->reference_point);
        Http::assertNothingSent();
    }

    /**
     * @test
     * @group destroy
     */
    public function can_delete_address()
    {
        $address = Address::factory()
            ->for($this->user)
            ->for(Cep::factory())
            ->create();

        $this->deleteJson(route('addresses.destroy', ['address' => $address->id, 'token' => $this->token]))
            ->assertOk();

        $this->assertEmpty(Address::all());
        $this->assertEmpty(Cep::all());
    }

    /**
     * @test
     * @group destroy
     */
    public function it_should_not_delete_cep_if_it_still_have_addresses()
    {
        $cep = Cep::factory()->create();

        $address = Address::factory()
            ->for($this->user)
            ->for($cep)
            ->create();

        Address::factory()
            ->for($this->user)
            ->for($cep)
            ->create();

        $this->deleteJson(route('addresses.destroy', ['address' => $address->id, 'token' => $this->token]))
            ->assertOk();

        $this->assertEquals(1, Address::count());
        $this->assertEquals(1, Cep::count());
    }
}
