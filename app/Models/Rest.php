<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use App\Traits\Constants;
use App\Traits\Util;
use App\Exceptions\CustomException;
use App\Models\Users_wallets;
use App\Models\User;
use App\Http\Resources\RestResource;
use JWTAuth;

class Rest extends Model
{
    const DEBITO = 0;
    const CREDITO = 1;
    use HasFactory;

    // Recarga de billetera
    public static function rechargeWallet($request)
    {

        if (Util::initAuth($request) == 1) {

            $validator = Validator::make($request->all(), [
                'phone' => 'required',
                'document' => 'required',
                'value' => 'required',
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

                $session_info = JWTAuth::parseToken()->authenticate();

                $usuario = User::where([
                    ['document','=', $request->document],
                    ['phone', '=', $request->phone]
                ])->first();

                if (!is_null($usuario)) {

                    $wallet = Users_wallets::create([
                        'user_id' => $usuario->id,
                        'type' => self::CREDITO,
                        'operation' => $request->value,
                        'created_at' => now()
                    ]);

                    $credito = Users_wallets::where('type', self::CREDITO)->sum('operation');
                    $debito = Users_wallets::where('type', self::DEBITO)->sum('operation');
                    $wallet['current_balance'] = $credito - $debito;

                    return new RestResource($wallet);
                } else {

                    throw new CustomException(
                        Constants::$NO_USER_REGISTER['message'],
                        Constants::$NO_USER_REGISTER['code']);
                }
            }
        }  else {

            throw new CustomException(
                Constants::$API_AUTH_ERROR['message'],
                Constants::$API_AUTH_ERROR['code']);
        }
    }

    // Consulta de billetera
    public static function checkBalance($request)
    {

        if (Util::initAuth($request) == 1) {

            $validator = Validator::make($request->all(), [
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

                $session_info = JWTAuth::parseToken()->authenticate();

                $usuario = User::where([
                    ['document','=', $request->document],
                    ['phone', '=', $request->phone]
                ])->first();

                if (!is_null($usuario)) {

                    $wallet['user_id'] = $session_info->id;
                    $wallet['created_at'] = now();
                    $credito = Users_wallets::where('type', self::CREDITO)->sum('operation');
                    $debito = Users_wallets::where('type', self::DEBITO)->sum('operation');
                    $wallet['current_balance'] = $credito - $debito;

                    return new RestResource($wallet);
                } else {

                    throw new CustomException(
                        Constants::$NO_USER_REGISTER['message'],
                        Constants::$NO_USER_REGISTER['code']);
                }
            }
        }  else {

            throw new CustomException(
                Constants::$API_AUTH_ERROR['message'],
                Constants::$API_AUTH_ERROR['code']);
        }
    }
}
