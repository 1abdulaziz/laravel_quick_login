<?php

namespace LaravelQuickLogin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Models\User; // Ensure this is the correct path to your User model
use Illuminate\Support\Facades\Config;

class OneTimeLoginService
{
    protected string $cachePrefix = 'one_time_login_';
    protected int $defaultExpirationMinutes = 2; // Default expiration: 2 minutes

    /**
     * Generate a one-time login token for a specific user.
     *
     * @param int $userId The ID of the user.
     * @param int|null $minutesToExpire Token expiration in minutes (uses default if null).
     * @return string The generated token.
     * @throws \InvalidArgumentException If the user is not found.
     */
    public function generateToken(int $userId, ?int $minutesToExpire = null): string
    {
        $userModel = Config::get('auth.providers.users.model');
        $user = $userModel::find($userId);
        if (!$user) {
            throw new \InvalidArgumentException("User with ID {$userId} not found.");
        }

        $token = Str::random(60);
        $expiration = $minutesToExpire ?? $this->defaultExpirationMinutes;
        Cache::put($this->cachePrefix . $token, $userId, now()->addMinutes($expiration));
        return $token;
    }

    /**
     * Attempt to log in the user using the one-time token.
     *
     * @param string $token The one-time login token.
     * @return \Illuminate\Contracts\Auth\Authenticatable|null The authenticated user or null on failure.
     */
    public function loginWithToken(string $token): ?\Illuminate\Contracts\Auth\Authenticatable
    {
        $cacheKey = $this->cachePrefix . $token;
        $userId = Cache::get($cacheKey);

        if (!$userId) {
            return null; // Token not found or expired
        }

        // Remove the token from cache to ensure it's used only once
        Cache::forget($cacheKey);
        $userModel = Config::get('auth.providers.users.model');
        $user = $userModel::find($userId);

        if ($user) {
            Auth::login($user);
            return $user;
        }

        return null;
    }

    /**
     * Generate a full one-time login URL.
     *
     * @param int $userId User ID.
     * @param int|null $minutesToExpire Token expiration in minutes.
     * @param string $routeName The name of the login route (default: 'login.via.token').
     * @return string The full login URL.
     */
    public function generateLoginUrl(int $userId, ?int $minutesToExpire = null, string $routeName = 'login.via.token'): string
    {
        $token = $this->generateToken($userId, $minutesToExpire);
        return route($routeName, ['token' => $token]);
    }
}

