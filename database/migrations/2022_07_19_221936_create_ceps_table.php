<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ceps', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->string('street');
            $table->string('district');
            $table->string('city');
            $table->string('state');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ceps');
    }
};
