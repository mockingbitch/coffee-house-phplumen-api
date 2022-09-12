<?php

namespace App\Traits;

use Illuminate\Http\Response;

trait APIResponser {

    protected function successResponse($message = 'Ok', $code = Response::HTTP_OK)
	{
		return response()->json([
			'error'=> 0, 
			'message' => $message, 
		], $code);
	}

	protected function errorResponse($message = null, $code = Response::HTTP_GATEWAY_TIMEOUT)
	{
		return response()->json([
			'error'=> 1,
			'message' => $message
		], $code);
	}

	protected function exceptionResponse($message = null, $code = Response::HTTP_GATEWAY_TIMEOUT)
	{
		return response()->json([
			'error'=> 2,
			'message' => $message
		], $code);
	}

    protected function catchErrorResponse()
    {
        return response()->json([
			'error'=> 2,
			'message' => 'Catch Error',
		], Response::HTTP_GATEWAY_TIMEOUT);
    }
}