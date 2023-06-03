<?php

namespace Seatplus\Connector\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $guarded = [];

    protected $primaryKey = 'user_id';

    protected $table = 'connector_users';

    public function seatplusUser(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\Seatplus\Auth\Models\User::class, 'user_id', 'id');
    }

    public function roles()
    {
        return $this->seatplusUser()->first()->roles;
    }
}
