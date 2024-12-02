<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'root_id_traccar', 'root_pass_traccar', 'device_ids', 'permissions'];

    protected $casts = [
        'device_ids' => 'json',
        'permissions' => 'json',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
