<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Guest;
use App\Models\Room;
use App\Models\Payment;

class Reservation extends Model
{
    use HasFactory;
    protected $fillable = [
        'inv',
        'guest_id',
        'room_id',
        'check_in',
        'check_out',
        'status',
        'total_price',
        'adults',
        'children',
        'special_requests'
    ];

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}