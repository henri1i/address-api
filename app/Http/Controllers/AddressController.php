<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidCepException;
use App\Http\Requests\ListAddressesRequest;
use App\Models\Address;
use Illuminate\Http\JsonResponse;
use App\Repositories\AddressRepository;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Http\Resources\AddressResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class AddressController extends Controller
{
    public function __construct(public AddressRepository $repository) {}

    public function index(ListAddressesRequest $request): AnonymousResourceCollection
    {
        return AddressResource::collection($this->repository->getPaginatedAddresses(auth()->user(), $request->input('per_page')));
    }

    public function store(StoreAddressRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $address = $this->repository->createAddress($validated, auth()->user());
        } catch(InvalidCepException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response()->json($address, Response::HTTP_CREATED);
    }

    public function show(Address $address): AddressResource
    {
        return AddressResource::make($address);
    }

    public function update(UpdateAddressRequest $request, Address $address): JsonResponse
    {
        $validated = $request->validated();

        try {
            $address = $this->repository->updateAddress($address, $validated);
        } catch (InvalidCepException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response()->json($address);
    }

    public function destroy(Address $address)
    {
        return $this->repository->destroyAddress($address);
    }
}
