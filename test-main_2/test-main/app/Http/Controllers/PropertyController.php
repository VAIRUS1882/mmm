<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropertyRequest;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PropertyController extends Controller
{
    public function allData(Request $request){
        $query = Property::where('status', 'available')
            ->with('owner:id,first_name,last_name,phone_number');

        if ($request->has('city') && !empty($request->city)) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        if ($request->has('governorate') && !empty($request->governorate)) {
            $query->where('governorate', 'like', '%' . $request->governorate . '%');
        }

        if ($request->has('min_price') && is_numeric($request->min_price)) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price') && is_numeric($request->max_price)) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->has('rooms') && is_numeric($request->rooms)) {
            $query->where('rooms', $request->rooms);
        }

        if ($request->has('min_area') && is_numeric($request->min_area)) {
            $query->where('area', '>=', $request->min_area);
        }

        if ($request->has('max_area') && is_numeric($request->max_area)) {
            $query->where('area', '<=', $request->max_area);
        }



        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhere('governorate' , 'like' , '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhere('address', 'like', '%' . $search . '%')
                  ->orWhere('city', 'like', '%' . $search . '%');
            });
        }

        $sortBy = $request->get('sort_by' , 'created_at');
        $sortOrder = $request->get('sort_order' , 'desc');

        $validSortColumns = ['price' , 'created_at' , 'area' , 'rooms'];
        if(in_array($sortBy , $validSortColumns)){
            $query->orderBy($sortBy , $sortOrder);

        }
        else{
            $query->orderBy('created_at', 'desc');
        }

        $properties = $query->paginate(10);

        return response()->json($properties);
    }

    public function cities(){

        $properties = Property::where('status', 'available')
            ->with('owner:id,first_name,last_name,phone_number')
            ->get();


        $groupedByCity = $properties->groupBy('city');


        $result = $groupedByCity->map(function ($properties, $city) {
            return [
                'city' => $city,
                'property_count' => $properties->count(),
                'properties' => $properties->map(function ($property) {
                    return [
                        'id' => $property->id,
                        'title' => $property->title,
                        'price' => $property->price,
                        'owner' => $property->owner ? [
                            'id' => $property->owner->id,
                            'first_name' => $property->owner->first_name,
                            'last_name' => $property->owner->last_name,
                            'phone_number' => $property->owner->phone_number
                        ] : null
                    ];
                })
            ];
        })->values();

        return response()->json($result);
    }

    public function store(PropertyRequest $request){
        if(!$request->user()->isOwner()){
            return response()->json([
                'error' => 'only the onwer access requierd'
            ] , 403);
        }

        try{
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('properties', 'public');
            }

            $propertey = Property::create([
                ...$request->validated(),
                'owner_id' => $request->user()->id,
                'images' => $imagePaths,
                'status' => 'available'
            ]);

            return response()->json([
                'message' => 'property added successfully',
                'property' => $propertey,
            ] , 201);
        }
        catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to add property: ' . $e->getMessage()
            ], 500);
        }

    }

    public function update(PropertyRequest $request , $id){
        $property = Property::findOrFail($id);

        try{
            $data = $request->validated();

            if($property->owner_id !== $request->user()->id){
                return response()->json([
                    'error' => 'you can only update your own properties '
                ] , 403);
            }

            if($request->hasFile('images')){
                $imagePaths = [];
                foreach($request->file('images') as $image){
                    $imagePaths[] = $image->store('properties', 'public');
                }

                $data['images'] = $imagePaths;
            }

            $property->update($data);

            return response()->json([
                'message' => 'Property update successfully',
                'property' => $property
            ]);


        }
        catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to update property: ' . $e->getMessage()
            ], 500);
    }

    }

    public function removeProperty( $id){
        $property = Property::findOrFail($id);

        if ($property->owner_id !== Auth::user()->id) {
            return response()->json([
                'error' => 'You can only delete your own properties'
            ], 403);
        }

        $property->delete();

        return response()->json([
            'message' => 'Propery deleted successfully'
        ] , 200);
    }

    public function myProperties(Request $request){
        $properties = $request->user()->properties()
        ->withCount('reservations')
        ->get();

        return response()->json($properties);
    }


}
