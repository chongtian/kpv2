<?php
namespace App\Http\Controllers\Adm;

use Validator;
use App\Models\Adm\TokenInfo;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Firebase\JWT\ExpiredException;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Routing\Controller;
use App\BusinessFramework\Adm\AuthHelper;
use App\Models\Adm\CgResponse;
use App\Http\Controllers\BaseController;

class AuthController extends Controller
{
    
    /**
     * The request instance.
     *
     * @var \App\Modules\Adm\AuthHelper
     */
    private $authHelper;

    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct()
    {
        $this->authHelper = new AuthHelper();
    }

    /**
     * Authenticate a user and return the token if the provided credentials are correct.
     *
     * @return mixed
     */
    public function login(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $username = $data['username'];
        $password = $data['password'];
        $tokenInfo = $this->authHelper->getTokenInfo($username, $password);
        if ($tokenInfo->IsAuthorized == false) {
            // Bad Request response
            $tokenInfo->Message = 'Credential is wrong.';
            $tokenInfo->ErrorCode = 400;
        } else {
            $tokenInfo->Token = $this->authHelper->generateToken($tokenInfo);
        }

        $r = new CgResponse();
        $r->TokenInfo = $tokenInfo;
        return response()->json($r);
    }
}
