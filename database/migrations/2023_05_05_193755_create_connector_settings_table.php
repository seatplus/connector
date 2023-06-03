<?php

use Illuminate\Support\Facades\Schema;

return new class extends \Illuminate\Database\Migrations\Migration
{
    public function up()
    {
        Schema::create('connector_settings', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->id();
            $table->string('connector')->unique()->index();
            $table->json('settings')->default(json_encode([]));
            $table->timestamps();
        });
    }
};
