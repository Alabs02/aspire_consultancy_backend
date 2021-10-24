<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class AdminProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'admin_id',
        'name',
        'contact',
        'address',
        'services',
    ];

    protected $casts = [
        'services' => 'array',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
