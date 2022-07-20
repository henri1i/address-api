<?php

namespace App\Services;

use App\Exceptions\InvalidCep;
use App\Models\Cep;
use App\Repositories\CepRepository;
use Illuminate\Support\Facades\Http;

class CepApiService
{
    public function __construct(public CepRepository $repository) {}

    public function getCep(string $cep): Cep
    {
        return $this->repository->getCepByNumber($cep)
            ?: $this->createCep($cep);
    }

    public function createCep(string $cep): Cep
    {
        $response = $this->fetch($cep);

        if (! $response->successful()) {
            throw new InvalidCep('CEP field must be valid.');
        }

        if ($response->json('erro')) {
            throw new InvalidCep('CEP field must be valid.');
        }

        return $this->repository->createCep([
            'number'   => $response->json('cep'),
            'street'   => $response->json('logradouro'),
            'district' => $response->json('bairro'),
            'city'     => $response->json('localidade'),
            'state'    => $response->json('uf')
        ]);
    }

    protected function fetch(string $cep)
    {
        return Http::get(config('cep.base_url') . "/{$cep}/" . config('cep.response_type'));
    }
}
