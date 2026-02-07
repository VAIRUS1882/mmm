<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function register(RegisterUserRequest $request){


        try{
            $profliePath = $request->file('profile_picture')->store('profile_picture' , 'public');

            $nationPhotoPath = $request->file('nation_picture')->store('nation_picture' , 'public');

            $user = User::create([
                ...$request->validated(),
                'profile_picture'=> $profliePath,
                'nation_picture'=>$nationPhotoPath,
                'status'=>'pending'
            ]);

            return response()->json([
                'messsage' => 'user registerd successfuly',
                'user'=> $user
            ] , 201);
        }
        catch (\Exception $e) {
            return response()->json([
                'error' => 'File upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request){

        $validate = $request->validate([
            'phone_number' => 'required|digits:10'
        ]);

        $user = User::where('phone_number' , $validate['phone_number'])->first();

        if(!$user){
            return response()->json([
                'message' => 'the user not found'
            ] , 404);
        }

        if($user->status != 'approved'){
            return response()->json([
                'message' => 'Account pending approval'
            ], 403);
        }


        $token = $user->createToken('auth_token')->plainTextToken;

        return response() ->json([
            'message'=> 'login successful',
            'user'=> $user,
            'user_state' => $user->user_state,
            'Token'=> $token
        ] , 200);
    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response() ->json([
            'message'=> 'logout successful',

        ]);
    }

    public function fun(){
        return response()->json([
            'messsage' => 'what`s up',

        ] , 201);
    }
}
