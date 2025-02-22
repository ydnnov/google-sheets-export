<?php

namespace App\Models;

use App\Enums\Entry\Status;
use Illuminate\Database\Eloquent\Model;

class Entry extends Model
{
    protected $fillable = ['status', 'content'];

    protected $casts = [
        'status' => Status::class,
    ];

    public function scopeAllowed($query)
    {
        return $query->where('status', 'allowed');
    }
}
