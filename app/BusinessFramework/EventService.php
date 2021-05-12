<?php
namespace App\BusinessFramework;

use App\Helper\CommonHelper;
use App\BusinessFramework\Adm\Constants;
use App\Models\Event;
use App\DataFramework\Event\EventDb;
use App\DataFramework\Event\EventHydrator;

class EventService
{
    public function __construct()
    {
    }

    public function list($keyword, int $page, int $size, int $child_user_key)
    {
        $data = EventDb::list($child_user_key, $keyword, $page, $size);
        return $data;
    }

    public function get(int $id, int $child_user_key)
    {
        $entity = EventDb::get($id, $child_user_key);
        return $entity;
    }

    public function list_kids(int $kid_access)
    {
        $data = EventDb::list_kids($kid_access); 
        return $data;
    }

    public function list_summary(int $child_user_key)
    {
        $data = EventDb::list_summary($child_user_key); 
        return $data;
    }

    public function create($request_data, int $oper_id)
    {
        $entity = EventHydrator::get_entity_from_json_array($request_data);
        $entity->CreateBy = $oper_id;
        $entity->UpdateBy = $oper_id;
        $this->validate($entity);
        if ($entity->IsSuccessful) {
            $entity = EventDb::create($entity);
        }
        return $entity;
    }

    public function update(array $request_data, int $id, int $oper_id)
    {
        $entity = EventHydrator::get_entity_from_json_array($request_data);
        if (!EventDb::exist($entity->LogKey) || $entity->LogKey != $id) {
            $entity->IsSuccessful = false;
            $entity->ReturnCode = 404;
        } else {
            $entity->UpdateBy = $oper_id;
            $this->validate($entity);
            if ($entity->IsSuccessful) {
                $entity = EventDb::update($entity);
            }
        }
        return $entity;
    }

    public function delete(int $id)
    {
        $entity = new Event(); 
        $entity->LogKey = $id;
        if (!EventDb::exist($id)) {
            $entity->IsSuccessful = false;
            $entity->ReturnCode = 404;
        } else {
            $entity->IsSuccessful = EventDb::delete($id);
        }
        return $entity;
    }

    public function is_exist(int $id)
    {
        return EventDb::exist($id);
    }

    private function validate(Event $entity)
    {
        $isValid = true;

        if ($entity->ChildUserKey == null) {
            $isValid = false;
            $entity->Message = 'Child User Key is required.';
        }
        if ($entity->CodeKey == null) {
            $isValid = false;
            $entity->Message = 'Event Code is required.';
        }
        if ($entity->EventPoint == null) {
            $isValid = false;
            $entity->Message .= 'EventPoint is required.';
        }
        
        $entity->IsSuccessful = $isValid;
    }
    
}
