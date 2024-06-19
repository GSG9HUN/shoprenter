<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Http\Requests\SecretRequest;
use App\Services\SecretService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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

        return ResponseFormatter::formatResponse($request, $secret);
    }

    public function addSecret(SecretRequest $request): Response|JsonResponse
    {

        $secret = $this->secretService->addSecret($request->toArray());

        return ResponseFormatter::formatResponse($request, $secret);
    }
}
