<?php
namespace App\BusinessFramework;

use App\Helper\CommonHelper;
use App\BusinessFramework\Adm\Constants;
use App\Models\EventCode;
use App\DataFramework\EventCode\EventCodeDb;
use App\DataFramework\EventCode\EventCodeHydrator;

class EventCodeService
{
    public function __construct()
    {
    }

    public function list(bool $active_only, $keyword, int $page, int $size)
    {
        $data = EventCodeDb::list($active_only, $keyword, $page, $size);
        return $data;
    }

    public function get(int $id)
    {
        $entity = EventCodeDb::get($id);
        return $entity;
    }

    public function create($request_data, int $oper_id)
    {
        $entity = EventCodeHydrator::get_entity_from_json_array($request_data);
        $entity->CreateBy = $oper_id;
        $entity->UpdateBy = $oper_id;
        $this->validate($entity);
        if ($entity->IsSuccessful) {
            $entity = EventCodeDb::create($entity);
        }
        return $entity;
    }

    public function update(array $request_data, int $id, int $oper_id)
    {
        $entity = EventCodeHydrator::get_entity_from_json_array($request_data);
        if (!EventCodeDb::exist($entity->CodeKey) || $entity->CodeKey != $id) {
            $entity->IsSuccessful = false;
            $entity->ReturnCode = 404;
        } else {
            $entity->UpdateBy = $oper_id;
            $this->validate($entity);
            if ($entity->IsSuccessful) {
                $entity = EventCodeDb::update($entity);
            }
        }
        return $entity;
    }

    public function delete(int $id)
    {
        $entity = new EventCode(); 
        $entity->CodeKey = $id;
        if (!EventCodeDb::exist($id)) {
            $entity->IsSuccessful = false;
            $entity->ReturnCode = 404;
        } else if (EventCodeDb::is_used($id)){
            $entity->IsSuccessful = false;
            $entity->ReturnCode = Constants::E_RECORD_IN_USED;
            $entity->Message = Constants::MSG_RECORD_IN_USED;
        } else {
            $entity->IsSuccessful = EventCodeDb::delete($id);
        }
        return $entity;
    }

    public function is_exist(int $id)
    {
        return EventCodeDb::exist($id);
    }

    private function validate(EventCode $entity)
    {
        $isValid = true;

        if ($entity->EventCode == null) {
            $isValid = false;
            $entity->Message = 'Event Code is required.';
        }
        if ($entity->EventDesc == null) {
            $isValid = false;
            $entity->Message .= 'Event Description is required.';
        }
        if(EventCodeDb::is_duplicate($entity->CodeKey, $entity->EventCode)){
            $isValid = false;
            $entity->ReturnCode = Constants::E_DUPLICATE_RECORD;
            $entity->Message = Constants::MSG_DUPLICATE_RECORD;
        }
        $entity->IsSuccessful = $isValid;
    }
    
}
