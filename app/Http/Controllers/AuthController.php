<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Determine if the input is an email or username
        $loginField = str_contains($request->input('login'), '@') ? 'email' : 'name';
    
        // Find the user based on the determined field
        $user = User::where($loginField, $request->input('login'))->first();
    
        // Check if user exists and password is correct
        if (!$user || !Hash::check($request->password, hashedValue: $user->password)) {
            return response([
                'message' => ['These credentials do not match our records.']
            ], 404);
        }
    
        // Create token
        $token = $user->createToken('my-app-token')->plainTextToken;
    
        // Prepare response
            $response = [
                'user' => $user,
                'token' => $token
            ];
    
        return response($response, 201);
    }

    public function register(Request $request){
        $data = $request->validate([
            'name' => 'required|string',
            'password' => 'required|string|confirmed'
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password'])
        ]);

        $token = $user->createToken('apiToken')->plainTextToken;

        $res = [
            'user' => $user,
            'token' => $token
        ];
        return response($res, 201);
    }
}
