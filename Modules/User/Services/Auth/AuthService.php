<?php

namespace Modules\User\Services\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\UnauthorizedException;
use Modules\Core\Services\BaseService;
use Modules\User\Repositories\Interfaces\UserRepositoryInterface;

class AuthService extends BaseService
{
    public function __construct(
        protected UserRepositoryInterface $userRepository
    ) {
    }

    /**
     * Login
     *
     * @param array $credentials
     *
     * @return array
     */
    public function login(array $credentials): array
    {
        if (! $token = auth('api')->attempt($credentials)) {
            throw new UnauthorizedException(
                message: $this->lang("auth.login-error")
            );
        }
        
        return $this->respondWithToken($token);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return void
     */
    public function logout(): void
    {
        auth()->logout();
    }

    /**
     * Refresh a token.
     *
     * @return array
     */
    public function refresh(): array
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return array
     */
    protected function respondWithToken($token): array
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ];
    }

    
}