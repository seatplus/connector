<?php

namespace Seatplus\Connector\Models;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $guarded = [];

    protected $casts = [
        'settings' => AsArrayObject::class,
    ];

    protected $table = 'connector_settings';

    public function getValue(string $key, $default = null)
    {
        return data_get($this->settings, $key, $default);
    }

    public function setValue(string $key, $value)
    {
        $settings = $this->settings;

        data_set($settings, $key, $value);

        $this->save();
    }
}
