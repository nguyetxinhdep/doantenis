<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminSub extends Model
{
    use HasFactory;

    protected $table = 'admin_sub';

    protected $primaryKey = 'Admin_sub_id';

    protected $fillable = [
        'Admin_sub_code',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'User_id');
    }
}
