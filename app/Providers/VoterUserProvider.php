<?php

namespace App\Providers;

use App\Models\Voter;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;

class VoterUserProvider implements UserProvider
{
    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function retrieveById($identifier): ?Voter
    {
        // find() is polymorphic (array|Arrayable input returns a Collection); this contract's
        // $identifier is always a single scalar id, so use whereKey()->first() instead, which
        // Psalm can actually type as a single model-or-null.
        return Voter::query()->whereKey($identifier)->first();
    }

    /**
     * {@inheritdoc}
     * @throws Exception
     */
    #[\Override]
    public function retrieveByToken($identifier, $token)
    {
        throw new Exception('Voter does not support remember-me tokens');
    }

    /**
     * {@inheritdoc}
     * @throws Exception
     */
    #[\Override]
    public function updateRememberToken(Authenticatable $user, $token)
    {
        throw new Exception('Voter does not support remember-me tokens');
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function retrieveByCredentials(array $credentials)
    {
        if (!array_key_exists('token', $credentials)) {
            return null;
        }

        return Voter::where('token', $credentials['token'])->first();
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function validateCredentials(
        Authenticatable $user,
        array $credentials
    ) {
        $token = $credentials['token'];

        return hash_equals($user->getAuthPassword(), $token);
    }

    /**
     * {@inheritdoc}
     * Voters authenticate by a raw token, not a hashed password - nothing to rehash.
     */
    #[\Override]
    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false): void
    {
        //
    }
}
