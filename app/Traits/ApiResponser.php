<?php

namespace App\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponser{

    protected function successResponse($data, $http_status)
	{
		return Response()->json($data, $http_status);
	}

	protected function errorResponse($exception)
	{
		return response()->json([
			'code'=> $exception->getCode(),
			'message_code' => 'ERROR_' . $exception->getCode(),
			'message' => $exception->getMessage()
		], 500);
	}

}
