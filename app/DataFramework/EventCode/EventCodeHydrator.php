<?php
namespace  App\DataFramework\EventCode;

use App\Helper\CommonHelper;
use App\Models\EventCode;

class EventCodeHydrator
{
    public static function get_entity_from_db_result($data)
    {
        if ($data == null) {
            return null;
        }

        $array = (array) $data;
        $array = array_change_key_case($array);
        $entity = new EventCode();
        $entity->CodeKey = CommonHelper::get_array_value($array, 'code_key');
        $entity->EventCode = CommonHelper::get_array_value($array, 'event_code');
        $entity->EventDesc = CommonHelper::get_array_value($array, 'event_desc');
        $entity->EventPoint = CommonHelper::get_array_value($array, 'event_point');
        $entity->Sort = CommonHelper::get_array_value($array, 'sort');
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
        $entity = new EventCode();
        $array = array_change_key_case($array);
        $entity->CodeKey = CommonHelper::get_array_value($array, 'codekey');
        $entity->EventCode = CommonHelper::get_array_value($array, 'eventcode');
        $entity->EventDesc = CommonHelper::get_array_value($array, 'eventdesc');
        $entity->EventPoint = CommonHelper::get_array_value($array, 'eventpoint');
        $entity->Sort = CommonHelper::get_array_value($array, 'sort');
        $entity->CreateBy = CommonHelper::get_array_value($array, 'createby');
        $entity->UpdateBy = CommonHelper::get_array_value($array, 'updateby');
        $entity->CreateDate = CommonHelper::format_date_time(CommonHelper::get_array_value($array, 'createdate'));
        $entity->UpdateDate = CommonHelper::format_date_time(CommonHelper::get_array_value($array, 'updatedate'));
        $entity->Active = CommonHelper::get_array_value($array, 'active') === true;
        return $entity;
    }

}
