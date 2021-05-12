<?php
namespace App\Http\Middleware;

use Closure;
use Exception;
use App\Models\Adm\AuthInfo;
use App\Models\Adm\NserpResponse;
use Firebase\JWT\ExpiredException;
use Illuminate\Http\Request;
use App\Modules\Adm\AuthHelper;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SendResponseMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($response instanceof BinaryFileResponse) {
            return $response;
        }
        
        $nserpResponse = new NserpResponse();
        $auth = $request->auth;
        $data = $response->getOriginalContent();
        if ($auth == null) {
            $auth = new AuthInfo();
            $auth->IsAuthorized = false;
            $auth->Message = "Unauthorized request";
            $data = null;
        }
        $nserpResponse->setAuthInfo($auth);
        $nserpResponse->setData($data);
        return response()->json($nserpResponse->GetResponse());
    }
}
