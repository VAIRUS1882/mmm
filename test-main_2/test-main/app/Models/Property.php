<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Property extends Model
{
    protected $fillable = [
        'title', 'description', 'price', 'location', 'address',
        'status', 'owner_id','city', 'governorate' , 'rooms' , 'area',
        'images',
        
    ];

    protected $casts = [
        'images' => 'array',
        'price' => 'decimal:2',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
