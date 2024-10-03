<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Court extends Model
{
    protected $table = 'courts';

    protected $primaryKey = 'Court_id';

    protected $fillable = [
        'Name',
        'Availability',
        'branch_id'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    // public function timeSlots()
    // {
    //     return $this->hasMany(TimeSlot::class, 'court_id');
    // }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'court_id');
    }

    // public function priceList()
    // {
    //     return $this->hasMany(PriceList::class, 'court_id');
    // }
}
