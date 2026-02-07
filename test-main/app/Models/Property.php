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

    protected $appends = ['average_rating'];

    protected $casts = [
        'images' => 'array',
        'price' => 'decimal:2',
    ];

    public function getAverageRatingAttribute(){
        return $this->ratings()->avg('rating');
    }

    public function owner(){
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function reservations(){
        return $this->hasMany(Reservations::class);
    }

    public function ratings(){
        return $this->hasMany(PropertyRating::class);
    }

    public function averageRating(){
        return $this->ratings()->avg('rating');
    }

    public function totalReviews(){
        return $this->ratings()->count();
    }
}
