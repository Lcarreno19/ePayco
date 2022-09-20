<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Validator;
use App\Traits\Util;
use App\Exceptions\CustomException;
use App\Traits\Constants;
use App\Models\Authenticador;
use App\Http\Controllers\ApiController;

class AuthController extends ApiController
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }

    public function login(Request $request)
	{
		try {

            return $this->successResponse(
                Authenticador::processlogin($request),
                Constants::$HTTP_OK);

        } catch (Exception $e) {

            throw new CustomException(
                $e->getMessage(),
                Constants::$UNKNOW_ERROR['code']);

        } catch (CustomException $e) {

            return $this->errorResponse($e);
        }
	}

    /**
     * Register a User.
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {

        try {

            return $this->successResponse(
                Authenticador::processregister($request),
                Constants::$HTTP_CREATED);

        } catch (Exception $e) {

            throw new CustomException(
                $e->getMessage(),
                Constants::$UNKNOW_ERROR['code']);

        } catch (CustomException $e) {

            return $this->errorResponse($e);
        }
    }

    /**
     * Log the user out (Invalidate the token).
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            auth('api')->logout();
            return response()->json([
                'status' => true,
                'message' => 'Successfully logged out'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Sorry, cannot logout'
            ], 500);
        }
    }

    /**
     * Get the authenticated User.
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile(Request $request)
    {
        return response()->json([
            'status' => true,
            'message' => 'User found',
            'data' => auth('api')->user()
        ], 200);
    }

    /**
     * Refresh a token.
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(Request $request)
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    /**
     * Get the token array structure.
     * @param  string $token
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        $minutes = auth('api')->factory()->getTTL() * 60;
        $timestamp = now()->addMinute($minutes);
        $expires_at = date('M d, Y H:i A', strtotime($timestamp));
        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_at' => $expires_at
        ], 200);
    }
}
