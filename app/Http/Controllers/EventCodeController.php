<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Models\Adm\DataSet;
use App\BusinessFramework\EventCodeService;

class EventCodeController extends BaseController
{
// private $service;
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->service = new EventCodeService();
    }

    public function list_active(Request $request)
    {
        if (parent::verifyAccess($request, parent::ACCESS_CHILD)) {

            $this->list($request, true);
        }
        return response()->json(parent::getResponseData());
    }

    public function list_all(Request $request)
    {
        if (parent::verifyAccess($request, parent::ACCESS_CHILD)) {

            $this->list($request, false);
        }
        return response()->json(parent::getResponseData());
    }

    private function list(Request $request, bool $active_only){
            $keyword = null;
            if ($request->filled('keyword')) {
                $keyword = $request->input('keyword');
            }

            // the page is zero-based
            $page = 0;
            if ($request->filled('page')) {
                $page = (int) $request->input('page');
            }

            $size = 20;
            if ($request->filled('pageSize')) {
                $size = (int) $request->input('pageSize');
            }
            $dataSet = $this->service->list($active_only, $keyword, $page, $size);
            parent::setResponseData($dataSet->Data, $dataSet->Count);
    }

    public function get(Request $request, int $id)
    {
        if (parent::verifyAccess($request, parent::ACCESS_CHILD)) {
            $entity = $this->service->get($id);
            if ($entity != null) {
                parent::setResponseData($entity, 1);
            } else {
                $this->tokenInfo->ErrorCode = 404;
                parent::setResponseData(null, 0);
            }
        }
        return response()->json(parent::getResponseData());
    }

    public function create(Request $request)
    {
        if (parent::verifyAccess($request, parent::ACCESS_PARENT)) {
            $data = parent::get_json_array($request); 
            if($data){
                $entity = $this->service->create($data, $this->userId);
                parent::setResponseData($entity, 1);
            }
        }
        return response()->json(parent::getResponseData());
    }

    public function update(Request $request, int $id)
    {
        if (parent::verifyAccess($request, parent::ACCESS_PARENT)) {
            $data = parent::get_json_array($request); 
            if($data){
                $entity = $this->service->update($data, $id, $this->userId);
            }
            parent::setResponseData($entity, 1);
        }
        return response()->json(parent::getResponseData());
    }

    public function delete(Request $request, int $id)
    {
        if (parent::verifyAccess($request, parent::ACCESS_PARENT)) {
            $entity = $this->service->delete($id);
            parent::setResponseData($entity, 1);
        }
        return response()->json(parent::getResponseData());
    }
    
}
