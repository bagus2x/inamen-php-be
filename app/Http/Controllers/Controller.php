<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    protected function success($data, $message = '', int $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function failure($message = '', int $code = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null
        ], $code);
    }
}
