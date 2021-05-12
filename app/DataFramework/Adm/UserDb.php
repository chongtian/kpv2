<?php

namespace  App\DataFramework\Adm;

use App\Models\Adm\User;
use App\BusinessFramework\Adm\AuthHelper;
use App\Models\Adm\TokenInfo;

class UserDb
{
    public static function getTokenInfo($username, $password)
    {
        $tokenInfo = AuthHelper::getNewTokenInfo();
        $user = app('db')->table('user_info')->where([['USER_ID','=',$username],['PASSWORD','=',$password]])->first();
        if ($user == null) {
            $tokenInfo->IsAuthorized = false;
        } else {
            $tokenInfo->IsAuthorized=true;
            $tokenInfo->Username=$user->USER_ID;
            $tokenInfo->UserId=$user->USER_KEY;
            $tokenInfo->FullName=$user->USER_NAME;
            $tokenInfo->Access=$user->ACCESS;
        }
        return $tokenInfo;
    }
}
