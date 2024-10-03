<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceList extends Model
{
    protected $table = 'price_list';

    protected $primaryKey = 'Price_list_id';

    protected $fillable = [
        'Price',
        // 'branch_id',
        'time_slot_id',
        'customer_type_id'
    ];

    // public function branch()
    // {
    //     return $this->belongsTo(Branch::class, 'branch_id');
    // }

    public function timeSlot()
    {
        return $this->belongsTo(TimeSlot::class, 'time_slot_id');
    }

    public function customerType()
    {
        return $this->belongsTo(CustomerType::class, 'customer_type_id');
    }
}
