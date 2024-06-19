<?php

namespace App\Helpers;

use App\Http\Requests\SecretRequest;
use App\Models\Secret;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ResponseFormatter
{
    public static function formatResponse(SecretRequest|Request $request, Secret $secret): Response|JsonResponse
    {
        $acceptHeader = $request->header('Accept');
        if ($acceptHeader === 'application/xml') {

            $xml = Converter::convertToXML($secret->toArray());

            return response($xml, 200)->header('Content-Type', 'application/xml');
        }

        return response()->json($secret);
    }
}
