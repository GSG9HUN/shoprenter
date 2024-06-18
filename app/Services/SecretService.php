<?php

namespace App\Services;

use App\Interfaces\SecretRepositoryInterface;
use App\Models\Secret;
use Carbon\Carbon;
use Spatie\ArrayToXml\ArrayToXml;

class SecretService
{
    protected SecretRepositoryInterface $secretRepository;

    public function __construct(SecretRepositoryInterface $secretRepository)
    {
        $this->secretRepository = $secretRepository;
    }

    public function getSecret(string $hash): Secret|null
    {
        $secret = $this->secretRepository->getSecretByHash($hash);

        if (!$secret) {
            return null;
        }

        if ($secret->expireAfterViews < 1) {
            return null;
        }

        if ($this->isExpired($secret)) {
            return null;
        }

        $this->updateSecret($hash);

        return $secret;
    }

    public function addSecret(array $data): void
    {
        $this->secretRepository->addSecret($data);
    }

    public function convertToXML(array $array): string
    {
        return ArrayToXml::convert($array, [
            'rootElementName' => 'secret',
            'xmlEncoding' => 'UTF-8'
        ]);
    }

    private function isExpired(Secret $secret): bool
    {
        if ($secret->expireAfter == null) {
            return false;
        }

        $expiresAt = Carbon::parse($secret->expireAfter);

        return $expiresAt->isPast();
    }

    private function updateSecret(string $hash): void
    {
        $this->secretRepository->updateSecret($hash);
    }
}
