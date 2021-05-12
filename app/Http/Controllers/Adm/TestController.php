<?php

namespace App\Http\Controllers\Adm;

use Illuminate\Http\Request;
use App\Models\Adm\CgResponse;
use App\Http\Controllers\BaseController;

class TestController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function about(Request $request)
    {
        if (parent::verifyAccess($request, 0)==true) {
            parent::setResponseData($this->tokenInfo->FullName, $this->userId);
        }
        return response()->json(parent::getResponseData());
    }
}
