<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    //use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'base_price',
        'capacity',
        'facilities',
        'bed_type',
        'size'
    ];
    protected $casts = [
        'facilities' => 'array',
        'base_price' => 'decimal:2',
        'size' => 'decimal:2',
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
