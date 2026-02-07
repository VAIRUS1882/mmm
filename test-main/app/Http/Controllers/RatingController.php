<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyRating;
use App\Models\Reservations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{

    public function store(Request $request, $reservation_id){
        $validate = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000'
        ]);

        $reservation = Reservations::findOrFail($reservation_id);

        if ($reservation->tenant_id !== Auth::id()) {
            return response()->json([
                'message' => 'You can only rate properties you have booked'
            ], 403);
        }

        if ($reservation->status !== 'confirmed') {
            return response()->json([
                'message' => 'You can only rate properties after the owner confirm'
            ], 400);
        }

        $existingRating = PropertyRating::where('reservation_id', $reservation_id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingRating) {
            return response()->json([
                'message' => 'You have already rated this reservation'
            ], 409);
        }

        $rating = PropertyRating::create([
            'property_id' => $reservation->property_id,
            'user_id' => Auth::id(),
            'reservation_id' => $reservation_id,
            'rating' => $validate['rating'],
            'review' => $validate['review'] ?? null
        ]);

        return response()->json([
            'message' => 'Rating submitted successfully',
            'rating' => $rating
        ], 201);
    }

    public function update(Request $request, $rating_id){
        $validate = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000'
        ]);

        $rating = PropertyRating::findOrFail($rating_id);

        if ($rating->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'You can only update your own ratings'
            ], 403);
        }

        $rating->update([
            'rating' => $validate['rating'],
            'review' => $validate['review'] ?? null
        ]);

        return response()->json([
            'message' => 'Rating updated successfully',
            'rating' => $rating
        ]);
    }

    public function destroy($rating_id){
        $rating = PropertyRating::findOrFail($rating_id);

        if ($rating->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'You can only delete your own ratings'
            ], 403);
        }

        $rating->delete();

        return response()->json([
            'message' => 'Rating deleted successfully'
        ]);
    }

    public function getPropertyRatings($property_id){
        $ratings = PropertyRating::where('property_id', $property_id)
            ->with('user:id,first_name,last_name')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($ratings);
    }

    public function getUserRatings(Request $request){
        $ratings = PropertyRating::where('user_id', Auth::id())
            ->with('property:id,title,city')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($ratings);
    }

    public function canRateReservation($reservation_id){
        $reservation = Reservations::findOrFail($reservation_id);

        $canRate = false;
        $message = '';

        if ($reservation->tenant_id !== Auth::id()) {
            $message = 'This is not your reservation';
        } elseif ($reservation->status !== 'completed') {
            $message = 'You can only rate completed reservations';
        } else {
            $existingRating = PropertyRating::where('reservation_id', $reservation_id)
                ->where('user_id', Auth::id())
                ->exists();

            if ($existingRating) {
                $message = 'You have already rated this reservation';
            } else {
                $canRate = true;
                $message = 'You can rate this reservation';
            }
        }

        return response()->json([
            'can_rate' => $canRate,
            'message' => $message,
            'reservation' => [
                'id' => $reservation->id,
                'property_title' => $reservation->property->title,
                'check_in' => $reservation->check_in,
                'check_out' => $reservation->check_out,
                'status' => $reservation->status
            ]
        ]);
    }
}
