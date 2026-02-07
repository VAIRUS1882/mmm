<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function addToFavorite(Request $request , $propertyId){
        try{
            $user = $request->user();
            $property = Property::findOrFail($propertyId);

            if($user->hasFavorited($propertyId)){
                return response()->json([
                    'message' => 'this property is already favorite',
                ]);
            }

            Favorite::create([
                'user_id' => $user->id,
                'property_id' => $propertyId,
            ]);

            return response()->json([
                'message' => 'property added to favorite succefully',
            ] , 200);
        }
        catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to add to favorites: ' . $e->getMessage()
            ], 500);
        }
    }

    public function removeFromFav(Request $request, $propertyId){
        try {
            $user = $request->user();

            $property = Property::findOrFail($propertyId);

            if (!$user->hasFavorited($propertyId)) {
                return response()->json([
                    'message' => 'Property is not in your favorites'
                ], 404);
            }

            $user->favorites()->detach($propertyId);

            return response()->json([
                'message' => 'Property removed from favorites successfully',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to remove favorite: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAllFav(Request $request){
        try{
            $user = $request->user();

            $favData = $user->favorites()
            ->with([
                'owner:id,first_name,last_name,phone_number',
                'ratings',
                'reservations' => function($query) use ($user) {
                    $query->where('tenant_id', $user->id)
                          ->orderBy('created_at', 'desc');
                },
                'reservations.tenant:id,first_name,last_name'
            ])
            ->get();

            return response()->json([
                'fav' => $favData,
            ]);

        }

        catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get favorites: ' . $e->getMessage()
            ], 500);
        }

    }
}
