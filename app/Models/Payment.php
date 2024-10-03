<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';

    protected $primaryKey = 'Payment_id';

    protected $fillable = [
        'Amount', 
        'Payment_method', 
        'Payment_date', 
        'Debt', 
        'Paid', 
        'Status', 
        'branch_id', 
        'booking_id'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }
}
