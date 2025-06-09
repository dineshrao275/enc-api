<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function __construct() {}

    public function login_get()
    {
        return response()->json(['success' => false ,"message" => "Unauthenticated User"],401);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, "message" => $validator->errors()->first()], 422);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'api_key' => Str::random(32),
            'password' => Hash::make($request->password),
        ]);

        $data['token'] = $user->createToken('auth_token')->plainTextToken;
        $data['user'] = $user;

        return response()->json([
            "success" => true,
            "message" => "User registered successfully",
            "data" => $data

        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|string|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, "message" => $validator->errors()->first()], 422);
        }


        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(["success" => false, "message" => "Invalid credentials"], 401);
        }

        $user->api_key = Str::random(32); 
        $user->save();

        $data['token'] = $user->createToken('auth_token')->plainTextToken;

        $data['user'] = $user;

        return response()->json([
            "success" => true,
            "message" => "Login successful",
            "data" => $data
        ]);
    }
}
