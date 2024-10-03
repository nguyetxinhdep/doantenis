<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $table = 'staff';

    protected $primaryKey = 'Staff_id';

    protected $fillable = [
        'Staff_code',
        'user_id',
        'branch_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'User_id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
