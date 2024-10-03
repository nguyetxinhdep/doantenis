<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeSlot extends Model
{
    protected $table = 'time_slots';

    protected $primaryKey = 'Time_slot_id';

    protected $fillable = [
        'Start_time',
        'End_time',
        'Status',
        'branch_id'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function priceList()
    {
        return $this->hasMany(PriceList::class, 'time_slot_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'time_slot_id');
    }
}
