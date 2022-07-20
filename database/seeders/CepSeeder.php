<?php

namespace Database\Seeders;

use App\Models\Cep;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CepSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Cep::factory()
            ->count(5)
            ->create();
    }
}
