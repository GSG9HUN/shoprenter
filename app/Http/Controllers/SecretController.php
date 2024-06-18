<?php

namespace App\Http\Controllers;

use App\Http\Requests\SecretRequest;
use App\Services\SecretService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\ArrayToXml\ArrayToXml;

class SecretController extends Controller
{
    protected SecretService $secretService;

    public function __construct(SecretService $secretService)
    {
        $this->secretService = $secretService;
    }

    public function getSecretByHash(Request $request, string $hash): Response|JsonResponse
    {

        $secret = $this->secretService->getSecret($hash);

        if (!$secret) {
            return response()->json('Secret not found', 404);
        }

        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/xml') {

            $xml = $this->secretService->convertToXML($secret->toArray());

            return response($xml, 200)->header('Content-Type', 'application/xml');
        }

        return response()->json($secret);
    }

    public function addSecret(SecretRequest $request): JsonResponse
    {

        $this->secretService->addSecret($request->toArray());

        return response()->json('Successful operation');
    }
}
