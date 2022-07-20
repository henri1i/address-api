<?php

namespace App\Repositories;

use App\Models\Cep;

class CepRepository
{
    public function createCep(array $data): Cep
    {
        return Cep::create($data);
    }

    public function getCepByNumber(string $cep): ?Cep
    {
        return Cep::where('number', $cep)->first();
    }

    public function cepExists(string $cepNumber): bool
    {
        return (bool)Cep::where('number', $cepNumber)->count();
    }

    public function destroyWhenCepHasNoAddresses(Cep $cep): bool
    {
        return $cep->addresses()->count()
            ? false
            : $cep->delete();
    }
}