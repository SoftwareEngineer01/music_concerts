<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;

class apiProtectedRoute extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (\Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                $status     = 401;
                $message    = 'This token is invalid. Please Login';
                return response()->json(compact('status','message'),401);
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                // Si el token caduca, se actualizará y se agregará a los encabezados
                try
                {
                    $refreshed = JWTAuth::refresh(JWTAuth::getToken());
                    $user = JWTAuth::setToken($refreshed)->toUser();
                    $request->headers->set('Authorization','Bearer '.$refreshed);
                }catch (JWTException $e){
                    return response()->json([
                        'code'   => 103,
                        'message' => 'Token cannot be refreshed, please Login again'
                    ]);
                }
            }else{
                $message = 'Authorization Token not found';
                return response()->json(compact('message'), 404);
            }
        }
        return $next($request);
    }

        // try {
        //     $user = JWTAuth::parseToken()->authenticate();
        // } catch (\Exception $e) {
        //     if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
        //         return response()->json(['status' => 'Token is Invalid']);
        //     }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
        //         return response()->json(['status' => 'Token is Expired']);
        //     }else{
        //         return response()->json(['status' => 'Authorization Token not found']);
        //     }
        // }
        // return $next($request);

}
