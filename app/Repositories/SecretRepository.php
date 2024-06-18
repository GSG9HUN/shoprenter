<?php

namespace App\Repositories;

use App\Interfaces\SecretRepositoryInterface;
use App\Models\Secret;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class SecretRepository implements SecretRepositoryInterface
{

    public function getSecretByHash(string $hash): Secret|null
    {
        return Secret::where('hash', $hash)->first();
    }

    public function addSecret(array $data): void
    {
        $expireAfter = intval($data['expireAfter']);

        $secret = new Secret();
        $secret->hash = Hash::make($data['secret']);
        $secret->secretText = $data['secret'];

        if ($expireAfter === 0) {
            $secret->expireAfter = null;
        } else {
            $secret->expireAfter = Carbon::now()->addMinutes($expireAfter);
        }

        $secret->expireAfterViews = $data['expireAfterViews'];

        $secret->save();
    }

    public function updateSecret(string $hash): void
    {
        $secret = $this->getSecretByHash($hash);

        $secret->expireAfterViews -= 1;

        $secret->update();
    }
}
