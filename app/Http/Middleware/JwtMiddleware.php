<?php
namespace App\Http\Middleware;

use Closure;
use Exception;
use App\Models\Adm\TokenInfo;
use App\Models\Adm\CgResponse;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Illuminate\Http\Request;
use App\BusinessFramework\Adm\AuthHelper;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $tokenInfo = new TokenInfo();
        $tokenInfo->AppName = env('APP_NAME');
        $tokenInfo->Version = env('APP_VERSION');
        
        $token = $request->header('Authorization');
        if (!$token) {
            // Unauthorized response if token not there
            $tokenInfo->IsAuthorized = false;
            $tokenInfo->Message ='Token not provided.';
            $tokenInfo->ErrorCode = 400;
        } else {
            try {
                $token = explode(' ', $token)[1];
                $credentials = JWT::decode($token, env('JWT_SECRET'), ['HS256']);
                $tokenInfo->Username = $credentials->username;
                $tokenInfo->FullName = $credentials->fullname;
                $tokenInfo->Access =  $credentials->access;
                $tokenInfo->UserId = $credentials->userid;
                $tokenInfo->IsAuthorized = true;
                $tokenInfo->Message ='';
            } catch (ExpiredException $e) {
                $tokenInfo->IsAuthorized = false;
                $tokenInfo->ErrorCode = 401;
                $tokenInfo->Message ='Provided token is expired.';
            } catch (Exception $e) {
                $tokenInfo->IsAuthorized = false;
                $tokenInfo->ErrorCode = 500;
                $tokenInfo->Message ='An error while decoding token.'.$e->getMessage();
            }
        }

        if ($tokenInfo->IsAuthorized == false) {
            $r = new CgResponse();
            $r->TokenInfo = $tokenInfo;
            return response()->json($r);
        }

        $tokenInfo->Token = AuthHelper::generateToken($tokenInfo);
        $request->auth = $tokenInfo;
        return $next($request);
    }
}
