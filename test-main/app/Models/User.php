<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; 

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'phone_number',
        'user_state',
        'status',
        'profile_picture',
        'nation_picture',
        'date_of_birth',
        'password'
        
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'remember_token',
        'password'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'password' => 'hashed'
        ];
    }

    public function properties(){
        return $this->hasMany(Property::class, 'owner_id');
    }

    public function isOwner(){
        return $this->user_state === 'owner';
    }

    public function isTenant(){
        return $this->user_state === 'tenant';
    }

    public function reservations(){
        return $this->hasMany(Reservations::class , 'tenant_id');
    }

    public function favorites(){
        return $this->belongsToMany(Property::class, 'favorites', 'user_id', 'property_id');
    }

    public function hasFavorited($propertyId){
        return $this->favorites()->where('property_id', $propertyId)->exists();
    }
}
