<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Reservations;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ReservationsController extends Controller
{
    // for testing not for use
    public function completeReservation(Request $request , $reservation_id){
        $reservation = Reservations::findOrFail($reservation_id)
        ->where('status', 'confirmed');

        $reservation->update([
            'status'=>"completed",
        ]);

        return response()->json([
            'message' => $reservation
        ]);
    }

    public function store(Request $request , $propertyId){

        if(!$request->user()->isTenant()){
            return response()->json([
                'message' => 'the user how can cancle only'
            ] , 403);
        }

        $validate = $request->validate([
            'check_in' => 'required|date|after:today',
            'check_out' => 'required|date|after:check_in',
        ]);

        $property = Property::findOrFail($propertyId);

        $checkIn = Carbon::parse($validate['check_in']);
        $checkOut = Carbon::parse($validate['check_out']);

        $hasConflict = Reservations::where('property_id', $propertyId)
        ->whereIn('status', ['confirmed', 'pending'])
        ->where(function ($query) use ($checkIn, $checkOut) {
            $query->where('check_in', '<', $checkOut)
                  ->where('check_out', '>', $checkIn);
            })
        ->exists();

        if($hasConflict){
            return response()->json('property is already booked');
        }

        $checkIn = Carbon::parse($validate['check_in']);
        $checkOut = Carbon::parse($validate['check_out']);
        $durationDays = $checkIn->diffInDays($checkOut);
        $totalPrice = $property->price * $durationDays;

        $reservation = Reservations::create([
            'property_id' => $propertyId,
            'tenant_id' => Auth::id(),
            'check_in' => $validate['check_in'],
            'check_out' => $validate['check_out'],
            'duration_days' => $durationDays,
            'total_price' => $totalPrice,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Reservation create successfully',
            'reservation' => $reservation
        ] , 201);
    }


    public function getBookedDates($propertyId){

        $bookedDates = Reservations::where('property_id', $propertyId )
            ->whereIn('status', ['confirmed', 'pending'])
            ->where('check_out', '>', now())
            ->get(['check_in', 'check_out']);

        return response()->json([
            'booked_periods' => $bookedDates
        ]);
    }


    public function getpendingReservation(Request $request){

        if(!$request->user()->isOwner()){
            return response()->json([
                'message' => 'the owner how can pending only'
            ] , 403);
        }

        $ownerProperties = $request->user()->properties()->pluck('id');

        $reservations = Reservations::whereIn('property_id', $ownerProperties)
            ->where('status', 'pending')
            ->with(['property', 'tenant:id,first_name,last_name,phone_number'])
            ->paginate(10);

        return response()->json($reservations);

    }


    // This for test
    public function getConfiremReservationTenant(Request $request){

        if(!$request->user()->isTenant()){
            return response()->json([
                'message' => 'the tenant how can access confirem Reservation only'
            ] , 403);
        }

        $ownerProperties = $request->user()->reservations()->pluck('id');

        $reservations = Reservations::whereIn('property_id', $ownerProperties)
            ->where('status', 'confirmed')
            ->with(['property:id,title', 'tenant:id,first_name,last_name,phone_number'])
            ->paginate(10);

        return response()->json($reservations);

    }

    public function ownerConfirmeReservation(Request $request , $reservation_id){

        if(!$request->user()->isOwner()){
            return response()->json([
                'message' => 'the owner how can confirem only'
            ] , 403);
        }

        $reservation = Reservations::findOrFail($reservation_id);

        if ($reservation->property->owner_id !== $request->user()->id) {
            return response()->json([
                'message' => 'You can only confirm reservations for your own properties'
            ], 403);
        }

        $reservation->update(['status' => 'confirmed']);


        return response()->json([
            'message' => 'reservation confiremd successfully',
            'user' => $reservation
        ]);
    }

    public function tenantCancelReservation(Request $request , $reservation_id){
        if(!$request->user()->isTenant()){
            return response()->json([
                'message' => 'the tenat how can cancle only'
            ] , 403);
        }

        $reservation = Reservations::findOrFail($reservation_id);

        if ($reservation->tenant_id !== $request->user()->id) {
            return response()->json([
                'message' => 'You can only cancel reservations for your own properties'
            ], 403);
        }

        $reservation->update(['status' => 'canceled']);


        return response()->json([
            'message' => 'reservation canceled successfully',
            'user' => $reservation
        ]);
    }

    public function updateReservation(Request $request, $reservation_id){
        if(!$request->user()->isTenant()){
            return response()->json([
                'message' => 'the tent how can update only'
            ] , 403);
        }

        $validate = $request->validate([
            'check_in' => 'required|date|after:today',
            'check_out' => 'required|date|after:check_in',
        ]);

        $reservation = Reservations::findOrFail($reservation_id);

        if ($reservation->tenant_id !== Auth::id()) {
            return response()->json([
                'message' => 'You can only update your own reservations'
            ], 403);
        }

        $checkIn = Carbon::parse($validate['check_in']);
        $checkOut = Carbon::parse($validate['check_out']);

        $hasConflict = Reservations::where('property_id', $reservation->property_id)
            ->whereIn('status', ['confirmed', 'pending'])
            ->where('id', '!=', $reservation_id)
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->where('check_in', '<', $checkOut)
                      ->where('check_out', '>', $checkIn);
            })
            ->exists();

        if ($hasConflict) {
            return response()->json([
                'message' => 'These dates are already booked by another reservation'
            ], 409);
        }

        $durationDays = $checkIn->diffInDays($checkOut);
        $totalPrice = $reservation->property->price * $durationDays;

        $reservation->update([
            'status' => 'pending',
            'check_in' => $validate['check_in'],
            'check_out' => $validate['check_out'],
            'duration_days' => $durationDays,
            'total_price' => $totalPrice,
        ]);

        return response()->json([
            'message' => 'Reservation updated successfully',
            'reservation' => $reservation
        ], 200);
    }

    public function getAllTentReservation(Request $request){
        if(!$request->user()->isTenant()){
            return response()->json([
                'message' => 'the tenant how can access confirem Reservation only'
            ] , 403);
        }

        $reservations = Reservations::where('tenant_id', $request->user()->id)
        ->whereIn('status', ['confirmed', 'pending', 'canceled', 'completed'])
        ->with(['property:id,title,city,address,rooms,area,images,governorate,price,images'])
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    return response()->json($reservations);

    }



}
