<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\Util;
use App\Exceptions\CustomException;
use App\Traits\Constants;
use App\Models\Rest;
use App\Http\Controllers\ApiController;

class RestController extends ApiController
{

     /**
     * Register a User.
     * @return \Illuminate\Http\JsonResponse
     */
    public function Wallet(Request $request)
    {

        try {

            return $this->successResponse(
                Rest::rechargeWallet($request),
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
     * Register a User.
     * @return \Illuminate\Http\JsonResponse
     */
    public function Balance(Request $request)
    {

        try {

            return $this->successResponse(
                Rest::checkBalance($request),
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
    public function Pay(Request $request)
    {

        try {

            return $this->successResponse(
                Rest::payWallet($request),
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
    public function Verify(Request $request, $code, $token)
    {

        try {

            return $this->successResponse(
                Rest::verifyPayOnWallet($request , $code, $token),
                Constants::$HTTP_CREATED);

        } catch (Exception $e) {

            throw new CustomException(
                $e->getMessage(),
                Constants::$UNKNOW_ERROR['code']);

        } catch (CustomException $e) {

            return $this->errorResponse($e);
        }
    }
}
