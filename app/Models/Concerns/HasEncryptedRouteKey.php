<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Crypt;

trait HasEncryptedRouteKey
{
    /**
     * Encrypted, URL-safe (via route() encoding) key for use in URLs
     * so raw database IDs are never exposed.
     */
    public function getEncryptedKeyAttribute(): string
    {
        return Crypt::encryptString((string) $this->getKey());
    }

    public static function findByEncryptedKey(string $key): ?static
    {
        try {
            $decrypted = Crypt::decryptString($key);
        } catch (\Throwable $e) {
            return null;
        }

        if (! ctype_digit($decrypted)) {
            return null;
        }

        return static::find($decrypted);
    }

    public static function findByEncryptedKeyOrFail(string $key): static
    {
        $model = static::findByEncryptedKey($key);
        abort_if($model === null, 404);

        return $model;
    }
}
