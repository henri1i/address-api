<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidCep;
use App\Http\Requests\ListAddressesRequest;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Repositories\AddressRepository;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Http\Resources\AddressResource;
use App\Services\AddressService;

class AddressController extends Controller
{
    public function __construct(public AddressRepository $repository) {}

    public function index(ListAddressesRequest $request)
    {
        return AddressResource::collection($this->repository->getPaginatedAddresses(auth()->user(), $request->input('per_page', 15)));
    }

    public function store(StoreAddressRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $address = $this->repository->createAddress($validated, auth()->user());
        } catch(InvalidCep $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json($address, 201);
    }

    public function show(Address $address)
    {
        return new AddressResource($address);
    }

    public function update(UpdateAddressRequest $request, Address $address)
    {
        $validated = $request->validated();

        try {
            $address = $this->repository->updateAddress($address, $validated);
        } catch (InvalidCep $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json($address);
    }

    public function destroy(Address $address)
    {
        return $this->repository->destroyAddress($address);
    }
}
