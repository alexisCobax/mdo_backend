<?php

namespace App\Services;

/**
 * Provides Clover API configuration per environment (test/production).
 * Keys are read from config (env). Can be extended later to use DB-stored keys.
 */
class CloverConfigService
{
    /**
     * Returns the API key for the given environment, or null if not set.
     *
     * @param string $environment "test" or "production"
     * @return string|null
     */
    public function getApiKey(string $environment): ?string
    {
        $key = config("clover.{$environment}.api_key");

        return $key && trim($key) !== '' ? trim($key) : null;
    }

    /**
     * Returns the current Clover environment (test or production).
     *
     * @return string
     */
    public function getEnvironment(): string
    {
        return config('clover.environment', 'production');
    }
}
