<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Constants;
use App\Traits\Util;
use App\Exceptions\CustomException;
use App\Http\Resources\UserResource;
use App\Models\User;
use Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Exception;
use Illuminate\Support\Facades\Auth;

class Authenticador extends Model
{
    use HasFactory;

    // Intefaz de acceso para Inicio de Sesion
    public static function processlogin($request)
    {

        if (Util::initAuth($request) == 1) {

            $validator = Validator::make($request->all(), [
                'email' => 'required',
                'password' => 'required'
            ]);

            $errors = array();
            if ($validator->fails()) {

                $e_index = 0;
                foreach($validator->errors()->messages() as $key=>$errorsmsges) {

                    $errors[$e_index++] = $errorsmsges[0];
                }
            }

            if (count($errors) > 0) {

                throw new CustomException(
                    Constants::$MISSING_PARAMETERS['message'],
                    Constants::$MISSING_PARAMETERS['code']);
            } else {

                // JWToken
                $credentials = $request->only('email', 'password');
                try {

                    $user = User::where('email', $credentials['email'])->first();
                    if (empty($user) || ! Hash::check($credentials['password'], $user->password)) {

                        throw new CustomException(
                            Constants::$LOGIN_ERROR['message'],
                            Constants::$LOGIN_ERROR['code']);
                    } else {

                        $token = JWTAuth::fromUser($user);
                    }
                } catch (JWTException $e) {

                    throw new CustomException(
                        Constants::$JWT_ERROR_CREATING_TOKEN['message'],
                        Constants::$JWT_ERROR_CREATING_TOKEN['code']);
                } catch (CustomException $e) {

                    throw $e;
                }

                return [
                    'access_token' => $token,
                    'email' => $user->email
                ];
            }
        }  else {

            throw new CustomException(
                Constants::$API_AUTH_ERROR['message'],
                Constants::$API_AUTH_ERROR['code']);
        }
    }

    public static function processregister($request)
    {
        if (Util::initAuth($request) == 1) {

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|min:3|max:50',
                'email' => 'required|string|email|max:100|unique:users',
                'password' => 'required|string|min:8',
                'phone' => 'required',
                'document' => 'required',
            ]);

            $errors = array();
            if ($validator->fails()) {

                $e_index = 0;
                foreach($validator->errors()->messages() as $key=>$errorsmsges) {

                    $errors[$e_index++] = $errorsmsges[0];
                }
            }

            if (count($errors) > 0) {
                throw new CustomException(
                    Constants::$MISSING_PARAMETERS['message'],
                    Constants::$MISSING_PARAMETERS['code']);
            } else {

                $user = new User();
                $user->name = $request->name;
                $user->email = $request->email;
                $user->document = $request->document;
                $user->phone = $request->phone;
                $user->password = bcrypt($request->password);
                $user->save();

                return new UserResource($user);
            }
        }  else {

            throw new CustomException(
                Constants::$API_AUTH_ERROR['message'],
                Constants::$API_AUTH_ERROR['code']);
        }
    }

}
