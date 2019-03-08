<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\JWTException;

class CustomJWTAuth extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard)
    {
        if (!$token = $this->auth->setRequest($request)->getToken()) {
            return response()->json(['message' => '未提供 Token, 请重新登录',], 401);
        }
        try {
            $this->auth->authenticate($token);
        } catch (TokenExpiredException $e) {
            return response()->json(['message' => 'Token has expired'], 401);
        } catch (JWTException $e) {
            return response()->json(['message' => '无效的 Token, 请重新登录'], 401);
        }
        $user = auth($guard)->user();
        if (!$user) {
            return response()->json(['message' => '没有找到该用户'], 401);
        }

        return $next($request);
    }
}
