<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\Api\V1\SigninRequest;
use App\Http\Transformers\UserTransformer;

class AuthorizationController extends Controller
{

    private $guard = 'api';
    public function signin(SigninRequest $request)
    {
        $credentials = $request->only('phone', 'password');
        if (!$token = \Auth::guard($this->guard)->attempt($credentials)) {
            return $this->response->errorUnauthorized('手机号或密码错误');
        }
        $tenant = \Auth::guard($this->guard)->getUser();
        return $this->response->item($tenant, new UserTransformer())
            ->setMeta([
                'access_token' => \Auth::guard($this->guard)->fromUser($tenant),
                'token_type' => 'Bearer',
                'expires_in' => \Auth::guard($this->guard)->factory()->getTTL() * 60
            ]);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth('api')->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     * 刷新token，如果开启黑名单，以前的token便会失效。
     * 值得注意的是用上面的getToken再获取一次Token并不算做刷新，两次获得的Token是并行的，即两个都可用。
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }
}