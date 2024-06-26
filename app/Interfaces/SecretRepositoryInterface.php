<?php

namespace App\Interfaces;

use App\Models\Secret;

interface SecretRepositoryInterface
{
    public function getSecretByHash(string $hash):Secret|null;

    public function addSecret(array $data):Secret;

    public function updateSecret(Secret $secret);
}
