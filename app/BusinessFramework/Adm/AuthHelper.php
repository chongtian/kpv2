<?php
namespace App\BusinessFramework\Adm;

use App\Models\Adm\TokenInfo;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Illuminate\Support\Facades\Hash;
use App\DataFramework\Adm\UserDb;

class AuthHelper
{

/**
     * Create a new token.
     *
     * @param  TokenInfo  $user
     * @return string
     */
    public static function generateToken(TokenInfo $tokenInfo)
    {
        $payload = [
            'iss' => "kid_points", // Issuer of the token
            'userid' => $tokenInfo->UserId,
            'username' => $tokenInfo->Username,
            'fullname' => $tokenInfo->FullName,
            'access' => $tokenInfo->Access,
            'iat' => time(), // Time when JWT was issued.
            'exp' => time() + 60*60 // Expiration time
        ];
        
        return JWT::encode($payload, env('JWT_SECRET'));
    }

    /**
     *
     * @param string $username
     * @param string $password
     * @return TokenInfo
     */
    public function getTokenInfo($username, $password)
    {
        return UserDb::getTokenInfo($username, $password);
    }

    /**
     * @param string $token JWT token, nullable
     * @return TokenInfo
     */
    public static function verifyToken(string $token)
    {
        $tokenInfo = new TokenInfo();

        if (!$token) {
            // Unauthorized response if token not there
            $tokenInfo->IsAuthorized = false;
            $tokenInfo->Message ='Token not provided.';
            $tokenInfo->ErrorCode = 400;
        } else {
            try {
                $credentials = JWT::decode($token, env('JWT_SECRET'), ['HS256']);
                $tokenInfo->Username = $credentials->username;
                $tokenInfo->FullName = $credentials->fullname;
                $tokenInfo->UserId = $credentials->userid;
                $tokenInfo->Access =  $credentials->access;
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

        if ($tokenInfo->IsAuthorized != false) {
            $tokenInfo->token = AuthHelper::generateToken($tokenInfo);
        }

        return $tokenInfo;
    }

    public static function getNewTokenInfo(){
        $tokenInfo = new TokenInfo();
        $tokenInfo->AppName = env('APP_NAME');
        $tokenInfo->Version = env('APP_VERSION');
        return $tokenInfo;
    }
}
