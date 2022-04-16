<?php

namespace App\Http\Controllers;

use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;


class UserController extends Controller
{
    /**
     * Register New User
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $data = $request->only('first_name', 'last_name', 'email', 'password');
        $validator = Validator::make($data, [
            'first_name' => 'required|string|min:3',
            'last_name' => 'required|string|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|max:15'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        return response()->json([
            'message' => 'User Successfully Registered'
        ], 201);
    }

    /**
     * login user
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        try {
            $credentials = $request->only('email', 'password');

            //valid credential
            $validator = Validator::make($credentials, [
                'email' => 'required|email',
                'password' => 'required|string|min:6|max:15'
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }

            //Request is validated
            //Create token
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                Log::error('Not a Registered Email');
                return response()->json([
                    'message' => 'Not a Registered Email'
                ], 404);
            } elseif (!Hash::check($request->password, $user->password)) {
                Log::error('Wrong Password');
                return response()->json([
                    'message' => 'Wrong Password'
                ], 402);
            }
            //Token created, return with success response and jwt token
            $token = JWTAuth::attempt($credentials);
            Log::info('Login Successful');
            return response()->json([
                'success' => 'Login Successful',
                'token' => $token
            ], 201);
        } catch (JWTException $e) {
            return $credentials;
            Log::error('Could not create token');
            return response()->json([
                'status' => 500,
                'message' => 'Could not create token',
            ], 500);
        }
    }

    /**
     * Get User Details
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_user(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);

        $user = JWTAuth::authenticate($request->token);

        return response()->json(['user' => $user]);
    }
}
