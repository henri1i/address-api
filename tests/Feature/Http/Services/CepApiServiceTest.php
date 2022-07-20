<?php

namespace Tests\Feature\Http\Services;

use App\Models\Cep;
use Tests\TestCase;
use App\Exceptions\InvalidCepException;
use App\Repositories\CepRepository;
use App\Services\CepApiService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request;

class CepApiServiceTest extends TestCase
{
    /** @test */
    public function it_should_take_cep_from_database_if_it_already_exists_there()
    {
        Http::fake();
        $cepNumber = '12345678';
        Cep::factory(['number' => $cepNumber])->create();

        $cep = (new CepApiService(new CepRepository))->getCep($cepNumber);

        $this->assertEquals(1, Cep::count());
        $this->assertEquals(Cep::first()->id, $cep->id);
        Http::assertNotSent(fn (Request $request) => $request);
    }

    /** @test */
    public function it_throws_an_exception_when_the_response_is_not_as_the_expected()
    {
        $this->expectException(InvalidCepException::class);

        Http::fake([
            '*' => Http::response([], 400)
        ]);

        (new CepApiService(new CepRepository))->createCep('12345678');;
    }

    /** @test */
    public function it_creates_cep_according_to_api_response()
    {
        Http::fake([
            '*' => Http::response(json_decode(File::get(base_path('tests/Fixture/Services/ViaCepApi.json')), true), 200)
        ]);

        (new CepApiService(new CepRepository))->createCep('12345678');

        $cep = Cep::first();
        $this->assertEquals('01001-000', $cep->number);
        $this->assertEquals('Praça da Sé', $cep->street);
        $this->assertEquals('Sé', $cep->district);
        $this->assertEquals('São Paulo', $cep->city);
        $this->assertEquals('SP', $cep->state);
    }
}
