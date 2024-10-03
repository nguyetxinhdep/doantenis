<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $table = 'branches';

    protected $primaryKey = 'Branch_id';

    protected $fillable = [
        'Name',
        'Location',
        'Phone',
        'Email',
        'manager_id',
        'Status',
        'Image',
        'Cover_image',
        'link_map',
        // 'staff_id'
    ];

    public function manager()
    {
        return $this->belongsTo(Manager::class, 'manager_id');
    }

    public function staff()
    {
        return $this->hasMany(Staff::class, 'branch_id');
    }

    public function courts()
    {
        return $this->hasMany(Court::class, 'branch_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'branch_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'branch_id');
    }

    public function timeSlots()
    {
        return $this->hasMany(TimeSlot::class, 'branch_id');
    }
}
