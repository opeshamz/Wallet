<?php

namespace App\Helper;


class Response
{
    public static function success(string $message, $data,   int $status)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $status);
    }

    public static function error(string $message, int $status)
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], $status);
    }
}
