<?php

namespace App\Http\Controllers\Api\Client\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'refresh', 'recovery']]);
    }

    #[OA\Post(path: '/api/v1/auth/login', tags: ['auth'])]
    #[OA\RequestBody(ref: '#/components/requestBodies/authLoginRequest')]
    #[OA\Response(ref: '#/components/responses/authLoginResponse', response: 200)]
    public function login(): JsonResponse
    {
        $credentials = request(['login', 'password']);

        if (! $token = auth('api')->attempt($credentials, true)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json(['access_token' => $token]);
    }

    #[OA\Post(path: '/api/v1/auth/me', security: [['bearerAuth' => []]], tags: ['auth'])]
    #[OA\Response(ref: '#/components/responses/authMeResponse', response: 200)]
    public function me(): JsonResponse
    {
        $userId = auth('api')->id();

        return response()->json(['user_id' => $userId]);
    }

    #[OA\Post(path: '/api/v1/auth/logout', security: [['bearerAuth' => []]], tags: ['auth'])]
    #[OA\Response(ref: '#/components/responses/authLogoutResponse', response: 200)]
    public function logout(): JsonResponse
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    #[OA\Post(path: '/api/v1/auth/refresh', security: [['bearerAuth' => []]], tags: ['auth'])]
    #[OA\Response(ref: '#/components/responses/authRefreshResponse', response: 200)]
    public function refresh(): JsonResponse
    {
        try {
            return $this->respondWithToken(auth('api')->refresh());
        } catch (TokenBlacklistedException $e) {
            return response()->json(['message' => 'The token does not exist'], 403);
        }
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     * @return JsonResponse
     */
    protected function respondWithToken(string $token): JsonResponse
    {
        $result = [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ];

        return response()->json($result);
    }
}
