<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\SecretRequest;
use App\Interfaces\SecretRepositoryInterface;
use App\Services\SecretService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SecretController extends Controller
{
    protected SecretService $secretService;

    public function __construct(SecretService $secretService)
    {
        $this->secretService = $secretService;
    }

    public function getSecretByHash(Request $request, string $hash): JsonResponse
    {

        $secret = $this->secretService->getSecret($hash);

        if (!$secret) {
            return response()->json("Secret not found", 404);
        }

        return response()->json($secret);
    }

    public function addSecret(SecretRequest $request): JsonResponse
    {

        $this->secretService->addSecret($request->toArray());

        return response()->json("Successful operation");
    }
}
