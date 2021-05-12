<?php
namespace  App\DataFramework\Event;

use App\Helper\CommonHelper;
use App\Models\Event;

class EventHydrator
{
    public static function get_entity_from_db_result($data)
    {
        if ($data == null) {
            return null;
        }

        $array = (array) $data;
        $array = array_change_key_case($array);
        $entity = new Event();
        $entity->LogKey = CommonHelper::get_array_value($array, 'log_key');
        $entity->ChildUserKey = CommonHelper::get_array_value($array, 'child_user_key');
        $entity->CodeKey = CommonHelper::get_array_value($array, 'code_key');
        $entity->EventDesc = CommonHelper::get_array_value($array, 'event_desc');
        $entity->EventPoint = CommonHelper::get_array_value($array, 'event_point');
        $entity->Memo = CommonHelper::get_array_value($array, 'memo');
        $entity->Active = CommonHelper::get_array_value($array, 'active') === 'A'? true : false;
        $entity->CreateBy = CommonHelper::get_array_value($array, 'create_by');
        $entity->UpdateBy = CommonHelper::get_array_value($array, 'update_by');
        $entity->CreateDate = CommonHelper::get_array_value($array, 'create_date');
        $entity->UpdateDate = CommonHelper::get_array_value($array, 'update_date');
        return $entity;
    }

    public static function get_entity_from_json_array($array)
    {
        if ($array == null) {
            return null;
        }
        $entity = new Event();
        $array = array_change_key_case($array);
        $entity->LogKey = CommonHelper::get_array_value($array, 'logkey');
        $entity->ChildUserKey = CommonHelper::get_array_value($array, 'childuserkey');
        $entity->CodeKey = CommonHelper::get_array_value($array, 'codekey');
        $entity->EventPoint = CommonHelper::get_array_value($array, 'eventpoint');
        $entity->Memo = CommonHelper::get_array_value($array, 'memo');
        $entity->CreateBy = CommonHelper::get_array_value($array, 'createby');
        $entity->UpdateBy = CommonHelper::get_array_value($array, 'updateby');
        $entity->CreateDate = CommonHelper::format_date_time(CommonHelper::get_array_value($array, 'createdate'));
        $entity->UpdateDate = CommonHelper::format_date_time(CommonHelper::get_array_value($array, 'updatedate'));
        $entity->Active = CommonHelper::get_array_value($array, 'active') === true;
        return $entity;
    }

}
