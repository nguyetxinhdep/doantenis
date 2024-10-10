<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = 'bookings';

    protected $primaryKey = 'Booking_id';

    protected $fillable = [
        'Status',
        'Date_booking',
        'court_id',
        'time_slot_id',
        'customer_id',
        'price_list_id',
        'branch_id',
        'Start_time',
        'End_time'
    ];

    public function court()
    {
        return $this->belongsTo(Court::class, 'court_id');
    }

    public function timeSlot()
    {
        return $this->belongsTo(TimeSlot::class, 'time_slot_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function priceList()
    {
        return $this->belongsTo(PriceList::class, 'price_list_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'booking_id');
    }
}
