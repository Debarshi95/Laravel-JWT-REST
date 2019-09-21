<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed']
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'All fields are required',
                'error' => $validator->errors()
            ], 422);
        }
        $user = User::create([
            'firstname' => $request->get('firstname'),
            'lastname' => $request->get('lastname'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password'))
        ]);
        $token = JWTAuth::fromUser($user);
        return response()->json([
            'message' => 'User created',
            'user' => $user,
            'token' => $token
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'min:6']
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'All fields are required',
                'error' => $validator->errors()
            ], 422);
        }
        try {
            if (!$token = JWTAuth::attempt(['email' => $request->get('email'), 'password' => $request->get('password')])) {
                return response()->json([
                    'message' => 'Invalid Credentials'
                ], 404);
            }
        } catch (JWTException $e) {
            return response()->json([
                'message' => 'Couldnot create token'
            ], 500);
        }
        return response()->json([
            'token' => $token
        ], 200);
    }

    public function validateUser()
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json([
                    'message' => 'User doesnot exist'
                ], 404);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json([
                'message' => 'Token expired'
            ], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json([
                'message' => 'Invalid Token'
            ], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json([
                'message' => 'Token absent'
            ], $e->getStatusCode());
        }
        return response()->json([
            'user' => $user
        ], 200);
    }
}
