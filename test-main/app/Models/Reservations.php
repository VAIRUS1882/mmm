<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservations extends Model
{
    protected $fillable = [
        'check_in' ,
        'check_out' ,
        'property_id' ,
        'tenant_id',
        'status',
        'duration_days',
        'total_price',
        'completed_at',
        'payment_status'];

    public function property(){
        return $this->belongsTo(Property::class);
        
    }
    
    public function tenant(){
        return $this->belongsTo(User::class, 'tenant_id');
        
    }
}
