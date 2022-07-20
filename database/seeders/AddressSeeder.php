<?php

namespace Database\Seeders;

use App\Models\Cep;
use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AddressSeeder extends Seeder
{
    public function run()
    {
        User::all()->each(
            fn (User $user) => Cep::all()->each(
                fn (Cep $cep) => Address::factory()
                    ->for($user)
                    ->for($cep)
                    ->count(5)
                    ->create()
            )
        );
    }
}
