<?php

use Seatplus\Connector\Models\Settings;

beforeEach(function () {
    Settings::firstOrCreate(['connector' => faker()->name]);
});

it('has Settings', function () {
    expect(Settings::all())->toHaveCount(1);
});

it('can set and get value', function () {
    $settings = Settings::first();

    $settings->setValue('test', 'test');

    expect($settings->getValue('test'))->toBe('test');
});
