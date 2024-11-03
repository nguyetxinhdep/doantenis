<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Manager extends Model
{
    protected $table = 'managers';

    protected $primaryKey = 'Manager_id';

    protected $fillable = [
        'Manager_code',
        'user_id',
        'branch_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function branches()
    {
        return $this->hasMany(Branch::class, 'manager_id');
    }
}
