<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerType extends Model
{
    protected $table = 'customer_types';

    protected $primaryKey = 'Customer_type_id';

    protected $fillable = [
        'Name', 
        'Description'
    ];

    public function customers()
    {
        return $this->hasMany(Customer::class, 'customer_type_id');
    }
}
