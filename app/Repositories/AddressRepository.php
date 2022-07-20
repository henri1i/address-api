<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Address;
use App\Models\Cep;
use App\Services\CepApiService;
use Illuminate\Pagination\LengthAwarePaginator;

class AddressRepository
{
    public function __construct(public CepRepository $repository, public CepApiService $service) {}

    public function getPaginatedAddresses(User $user, ?int $perPage = 15): LengthAwarePaginator
    {
        return $user->addresses()
            ->paginate($perPage);
    }

    public function getAddressById(int $addressId): Address
    {
        return Address::find($addressId);
    }

    public function createAddress(array $addressDetails, User $user): Address
    {
        $cep = $this->service->getCep($addressDetails['cep']);

        $address = new Address([
            ...collect($addressDetails)->except('cep')->toArray(),
        ]);

        $address->user()->associate($user);
        $address->cep()->associate($cep);
        $address->save();

        return $address;
    }

    public function updateAddress(Address $address, array $addressDetails): bool
    {
        if(array_key_exists('cep', $addressDetails)) {
            $this->updateCepAddress($address, $addressDetails['cep']);
        }

        return $address->update(
            collect($addressDetails)->except('cep')->toArray(),
        );
    }

    protected function updateCepAddress(Address $address, string $newCepNumber): bool
    {
        if ($this->repository->cepExists($newCepNumber)) {
            $newCep = $this->repository->getCepByNumber($newCepNumber);

            return $this->switchCepAddress($address, $newCep);
        }

        $newCep = $this->service->createCep($newCepNumber);

        return $this->switchCepAddress($address, $newCep);
    }

    protected function switchCepAddress(Address $address, Cep $newCep): bool
    {
        $address->cep()->dissociate();
        $address->cep()->associate($newCep);

        return $address->save();
    }

    public function destroyAddress(Address $address): void
    {
        $address->delete();

        $this->repository->destroyWhenCepHasNoAddresses($address->cep);
    }
}
