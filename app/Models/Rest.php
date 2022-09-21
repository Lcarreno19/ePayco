<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use App\Traits\Constants;
use App\Traits\Util;
use App\Exceptions\CustomException;
use App\Http\Resources\ResponseResource;
use App\Http\Resources\RestResource;
use App\Models\Users_wallets;
use App\Models\User;
use App\Models\Tokens;
use JWTAuth;
use Illuminate\Support\Facades\Mail;

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
                'phone'    => 'required',
                'document' => 'required',
                'value'    => 'required',
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

                $usuario = User::where([
                    ['document','=', $request->document],
                    ['phone', '=', $request->phone]
                ])->first();

                if (!is_null($usuario)) {

                    $wallet = Users_wallets::create([
                        'user_id'    => $usuario->id,
                        'type'       => self::CREDITO,
                        'operation'  => $request->value,
                        'created_at' => now()
                    ]);
                    $wallet['current_balance'] = Rest::currentBalance($usuario->id);
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
                'phone'    => 'required',
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
                    $wallet = new Users_wallets();
                    $wallet['user_id']         = $session_info->id;
                    $wallet['created_at']      = now();
                    $wallet['current_balance'] = Rest::currentBalance($session_info->id);
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

    // Pago de billetera
    public static function payWallet($request)
    {

        if (Util::initAuth($request) == 1) {

            $validator = Validator::make($request->all(), [
                'value' => 'required'
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
                $token = explode(' ', $request->header('Authorization'));
                $balance = Rest::currentBalance($session_info->id) - $request->value;
                //Validacion para saber si tiene saldo
                if ($balance > 0) {

                    $data['code']              = mt_rand(100000, 999999);;
                    $data['email']             = $session_info->email;
                    $data['name']              = $session_info->name;
                    $data['confirmation_code'] = $token[1];

                    $token = Tokens::create([
                        'code'        => $data['code'],
                        'token'       => $token[1],
                        'user_id'     => $session_info->id,
                        'operation'   => $request->value,
                    ]);

                    // Send confirmation code
                    Mail::send('emails.token', $data, function($message) use ($data) {
                        $message->to($data['email'], $data['name'])->subject('Por favor confirma tu compra');
                    });
                    return new ResponseResource($token);
                } else {

                    throw new CustomException(
                        Constants::$USER_NOT_BALANCE['message'],
                        Constants::$USER_NOT_BALANCE['code']);
                }

            }
        }  else {

            throw new CustomException(
                Constants::$API_AUTH_ERROR['message'],
                Constants::$API_AUTH_ERROR['code']);
        }
    }

     // Pago de billetera
     public static function verifyPayOnWallet($request, $code, $token)
     {

        $token_exist = Tokens::where([
            ['code', '=', $code],
            ['token', '=', $token]
        ])->first();

        if (!is_null($token_exist)) {

            $balance = Rest::currentBalance($token_exist->user_id) - $token_exist->operation;
            //Validacion para saber si tiene saldo
            if ($balance > 0) {

                $wallet = Users_wallets::create([
                    'user_id'    => $token_exist->user_id,
                    'type'       => self::DEBITO,
                    'operation'  => $token_exist->operation,
                    'created_at' => now()
                ]);
                $wallet = new Users_wallets();
                $wallet['user_id']         = $token_exist->user_id;
                $wallet['created_at']      = now();
                $wallet['current_balance'] = Rest::currentBalance($token_exist->user_id);
                return new RestResource($wallet);

            } else {

                throw new CustomException(
                    Constants::$USER_NOT_BALANCE['message'],
                    Constants::$USER_NOT_BALANCE['code']);
            }
        } else {

            throw new CustomException(
                Constants::$USER_CODE_CONFIRMATION_NO_EXIST['message'],
                Constants::$USER_CODE_CONFIRMATION_NO_EXIST['code']);
        }

     }
    // Funcion para calculo de balance
    public static function currentBalance($user_id){
        $credito = Users_wallets::where('user_id', '=',$user_id)->where('type', self::CREDITO)->sum('operation');
        $debito = Users_wallets::where('user_id', '=', $user_id)->where('type', self::DEBITO)->sum('operation');
        return $credito - $debito;
    }
}
