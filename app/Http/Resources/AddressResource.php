<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\PostResource;

class AddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'              => $this->id,
            'house_number'    => $this->house_number,
            'reference_point' => $this->reference_point,
            'cep'             => $this->cep->number,
            'street'          => $this->cep->street,
            'district'        => $this->cep->district,
            'city'            => $this->cep->city,
            'state'           => $this->cep->state,
        ];
    }
}
