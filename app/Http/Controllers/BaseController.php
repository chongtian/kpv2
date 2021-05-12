<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;
use App\BusinessFramework\Adm\AuthHelper;
use App\Models\Adm\TokenInfo;
use App\Models\Adm\CgResponse;

class BaseController extends Controller
{
    const ACCESS_EVERYONE = 0x0;
    const ACCESS_CHILD = 0x1;
    const ACCESS_PARENT = 0x2;
    const ACCESS_ADM = 0x4;
    
    protected $userId;
    protected $tokenInfo;
    protected $res;

    public function __construct()
    {
        $this->middleware('jwt.auth');
        // $this->middleware('send_response');
        $this->res = new CgResponse();
    }
    
    /**
     * Verify token against the required access
     * This method assign an AuthInfo instance to $request->auth
     * @param Request $request
     * @param int $requiredAccess
     * @return bool
     */
    protected function verifyAccess(Request $request, int $requiredAccess)
    {
        $this->tokenInfo = $request->auth;
        
        if ((int) $this->tokenInfo->UserId > 0) {
            $this->userId = (int) $this->tokenInfo->UserId;
        } else {
            $this->userId = 0;
        }

        if (((int) $this->tokenInfo->Access & $requiredAccess) != $requiredAccess) {
            $this->tokenInfo->IsAuthorized = false;
            $this->tokenInfo->ErrorCode = 403;
            $this->tokenInfo->Message ='User does not have required access.';
        }

        $request->auth = $this->tokenInfo;
        $this->res->TokenInfo = $this->tokenInfo;
        return $this->tokenInfo->IsAuthorized;
    }

    protected function setResponseData($data, $count = 0){
        $this->res->Data = $data;
        $this->res->Count = $count;
    }

    protected function getResponseData(){
        $this->res->TokenInfo = $this->tokenInfo;
        return $this->res;
    }

    protected function get_json_array(Request $request){
        if ($request->isJson()) {
            return $request->json()->all();
        } else {
            $this->res->TokenInfo->ErrorCode = 400;
            return null;
        }
    }
   
}
