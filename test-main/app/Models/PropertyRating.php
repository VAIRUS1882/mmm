<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'user_id',
        'reservation_id',
        'rating',
        'review'
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reservation()
    {
        return $this->belongsTo(Reservations::class);
    }
}