<?php

use Illuminate\Support\Facades\Schema;

return new class extends \Illuminate\Database\Migrations\Migration
{
    public function up()
    {
        Schema::create('connector_users', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->id('user_id');
            $table->string('connector_id')->unique();
            $table->string('connector_type')->index();
            $table->timestamps();
        });
    }
};
