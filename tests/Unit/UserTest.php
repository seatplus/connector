<?php

beforeEach(function () {
    $user = \Seatplus\Auth\Models\User::factory()->create();

    \Seatplus\Connector\Models\User::create([
        'user_id' => $user->id,
        'connector_id' => faker()->numberBetween(1, 100),
        'connector_type' => faker()->name,
    ]);
});

it('has has seatplus user', function () {
    $user = \Seatplus\Connector\Models\User::first();

    expect($user->seatplusUser)->toBeInstanceOf(\Seatplus\Auth\Models\User::class);
});

it('has roles', function () {
    $user = \Seatplus\Connector\Models\User::first();

    expect($user->roles())->toBeInstanceOf(\Illuminate\Database\Eloquent\Collection::class);
});
