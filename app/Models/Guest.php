<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'city',
        'country',
        'date_of_birth',
        'id_type',
        'id_number',
        'special_requests'
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
