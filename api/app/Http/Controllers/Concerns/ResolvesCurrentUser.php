<?php

namespace App\Http\Controllers\Concerns;

use App\Models\User;
use Illuminate\Support\Str;

trait ResolvesCurrentUser
{
    /**
     * Read a claim from decoded Keycloak token when available.
     */
    private function getTokenClaim(string $claim)
    {
        try {
            $guard = auth('api');
            if (!method_exists($guard, 'token')) {
                return null;
            }

            $rawToken = $guard->token();
            if (!is_string($rawToken) || $rawToken === '') {
                return null;
            }

            $decoded = json_decode($rawToken, true);
            if (!is_array($decoded)) {
                return null;
            }

            return $decoded[$claim] ?? null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Build a deterministic UUID-like string from a stable identity value.
     */
    private function buildDeterministicUuid(string $value): string
    {
        $hash = md5(strtolower(trim($value)));

        return substr($hash, 0, 8)
            . '-' . substr($hash, 8, 4)
            . '-' . substr($hash, 12, 4)
            . '-' . substr($hash, 16, 4)
            . '-' . substr($hash, 20, 12);
    }

    /**
     * Resolve authenticated principal (Keycloak/local) to a persisted local User row.
     */
    private function resolveCurrentLocalUser(): ?User
    {
        $authUser = auth()->user();
        if (!$authUser) {
            return null;
        }

        if ($authUser instanceof User && $authUser->exists) {
            return $authUser;
        }

        $authIdentifier = method_exists($authUser, 'getAuthIdentifier') ? $authUser->getAuthIdentifier() : null;
        $authId = $authUser->id ?? $authIdentifier;
        $uuid = $authUser->uuid
            ?? $authUser->sub
            ?? $this->getTokenClaim('sub')
            ?? (!is_numeric((string) $authId) ? $authId : null);
        $email = $authUser->email ?? $this->getTokenClaim('email');
        if (is_string($email) && $email !== '') {
            $email = strtolower(trim($email));
        }
        $preferredUsername = $authUser->preferred_username
            ?? $authUser->username
            ?? $this->getTokenClaim('preferred_username')
            ?? $this->getTokenClaim('username');
        $name = $authUser->name
            ?? $authUser->given_name
            ?? $this->getTokenClaim('name')
            ?? $this->getTokenClaim('given_name')
            ?? 'User';
        $surname = $authUser->surname
            ?? $authUser->family_name
            ?? $this->getTokenClaim('family_name')
            ?? 'Keycloak';

        if (is_numeric((string) $authId)) {
            $user = User::find((int) $authId);
            if ($user) {
                return $user;
            }
        }

        if (!$uuid && $preferredUsername) {
            $uuid = $this->buildDeterministicUuid((string) $preferredUsername);
        }

        if (!$uuid && $email) {
            $uuid = $this->buildDeterministicUuid($email);
        }

        if (!$email && $uuid) {
            $email = sprintf('kc_%s@local.invalid', substr(sha1((string) $uuid), 0, 24));
        }

        if (!$uuid && !$email) {
            return null;
        }

        if ($uuid) {
            $user = User::where('uuid', $uuid)->first();
            if ($user) {
                if (!$user->email && $email) {
                    $user->email = $email;
                    $user->save();
                }

                return $user;
            }
        }

        if ($email) {
            $user = User::where('email', $email)->first();
            if ($user) {
                if (!$user->uuid && $uuid) {
                    $user->uuid = $uuid;
                    $user->save();
                }

                return $user;
            }
        }

        return User::create([
            'email' => $email,
            'name' => $name,
            'surname' => $surname,
            'uuid' => $uuid,
            'password' => bcrypt(Str::random(32)),
        ]);
    }
}
