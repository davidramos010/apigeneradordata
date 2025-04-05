<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    //
    public function register(Request $request)
    {
        try {
            // Validate the incoming request data
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|min:10|max:100',
                'role' => 'required|string|in:admin,user',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            // Create a new user instance
            User::create([
                'name' => $request->input('name'),
                'role' => $request->input('role'),
                'email' => $request->input('email'),
                'password' => bcrypt($request->input('password')),
            ]);

            // Return a success response
            return response()->json(['message' => 'User registered successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Login a user.
     *
     * @bodyParam email string required The email of the user. Example: user@example.com
     * @bodyParam password string required The password of the user. Example: secret
     *
     * @response 200 {
     *   "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
     * }
     */
    public function login(Request $request)
    {
        // Initialize the response array
        $arrResponse['message'] = '';
        $arrResponse['token'] = '';
        $arrResponse['status'] = 200;
        try {
            // Validate the incoming request data
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string|min:8',
            ]);

            if ($validator->fails()) {
                $arrResponse['message'] = 'Validation error';
                $arrResponse['status'] = 422;
                throw new \Exception($arrResponse['message'],$arrResponse['status']);
            }

            $credentials = $request->only(['email', 'password']);
            $token = JWTAuth::attempt($credentials);
            if (!$token) {
                $arrResponse['message'] = 'Cloud not create token';
                $arrResponse['status'] = 401;
                throw new \Exception($arrResponse['message'],$arrResponse['status']);
            }else {
                $arrResponse['message'] = 'Token created successfully';
                $arrResponse['token'] = $token;
                $arrResponse['status'] = 200;
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }

        return response()->json($arrResponse, $arrResponse['status']);
    }

    /**
     * finalice session
     *
     * @param Request $request
     * @return void
     */
    public function logout(Request $request)
    {
        try {
            // Revoke the token that was used to authenticate the user
            JWTAuth::invalidate(JWTAuth::getToken());
            // Return a success response
            return response()->json(['message' => 'User logged out successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * get all information of the user
     *
     * @return void
     */
    public function getUser()
    {
        try {
            // Get the authenticated user
            $user = Auth::user();
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }
            // Return the user as a JSON response
            return response()->json($user, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get the authenticated user.
     */
    public function user()
    {
        return response()->json(Auth::user(), 200);
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function refreshToken()
    {
        try {
            // Refresh the token
            $token = JWTAuth::refresh(JWTAuth::getToken());
            if (!$token) {
                return response()->json(['message' => 'Could not refresh token'], 401);
            }
            // Return the new token as a JSON response
            return response()->json(['token' => $token], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
