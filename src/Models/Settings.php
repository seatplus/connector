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

    public function getValue($key)
    {
        return $this->settings[$key] ?? null;
    }

    public function setValue($key, $value)
    {
        $this->settings[$key] = $value;

        $this->save();
    }
}
