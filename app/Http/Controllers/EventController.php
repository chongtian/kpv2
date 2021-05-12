<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Models\Adm\DataSet;
use App\BusinessFramework\EventService;

class EventController extends BaseController
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
        $this->service = new EventService();
    }

    public function list(Request $request)
    {
        if (parent::verifyAccess($request, parent::ACCESS_CHILD)) {

            $child_user_key = $this->userId;
            if((int) $this->tokenInfo->Access > parent::ACCESS_CHILD){
                $child_user_key = 0;
            }
            
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

            $dataSet = $this->service->list($keyword, $page, $size, $child_user_key);
            parent::setResponseData($dataSet->Data, $dataSet->Count);
        }
        return response()->json(parent::getResponseData());
    }

    public function get(Request $request, int $id)
    {
        if (parent::verifyAccess($request, parent::ACCESS_CHILD)) {
            $child_user_key = $this->userId;
            if((int) $this->tokenInfo->Access > parent::ACCESS_CHILD){
                $child_user_key = 0;
            }
            $entity = $this->service->get($id, $child_user_key);
            if ($entity != null) {
                parent::setResponseData($entity, 1);
            } else {
                $this->tokenInfo->ErrorCode = 404;
                parent::setResponseData(null, 0);
            }
        }
        return response()->json(parent::getResponseData());
    }

    public function list_kids(Request $request)
    {
        if (parent::verifyAccess($request, parent::ACCESS_PARENT)) {
            $dataSet = $this->service->list_kids(parent::ACCESS_CHILD);
            parent::setResponseData($dataSet, count($dataSet));
        }
        return response()->json(parent::getResponseData());
    }

    public function list_summary(Request $request)
    {
        if (parent::verifyAccess($request, parent::ACCESS_CHILD)) {
            $child_user_key = $this->userId;
            if((int) $this->tokenInfo->Access > parent::ACCESS_CHILD){
                $child_user_key = 0;
            }
            $dataSet = $this->service->list_summary($child_user_key);
            parent::setResponseData($dataSet, count($dataSet));
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
