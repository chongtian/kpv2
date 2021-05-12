<?php

namespace  App\DataFramework\EventCode;

use App\Models\Adm\DataSet;
use App\Models\EventCode;
use App\DataFramework\EventCode\EventCodeHydrator;
use App\DataFramework\CommonDb;

class EventCodeDb
{
    public static function list(bool $active_only, $keyword, int $page = 0, int $size = 20)
    {
        $tableName = 'event_code';
        $orderBy = ' SORT desc';
        if ($active_only){
            $where = " ACTIVE = 'A' ";
        } else {
            $where = " 1 ";
        }

        $dataSet = CommonDb::query_with_raw($tableName, $where, [], $orderBy, $page, $size);
        $entities = [];
        if ($dataSet->Data) {
            foreach ($dataSet->Data as $data) {
                $entity = EventCodeHydrator::get_entity_from_db_result($data);
                $entities[] = $entity;
            }
        }
        $dataSet->Data = $entities;
        return $dataSet;
    }

    public static function get(int $Id)
    {
        $data = $result = app('db')->table('event_code')->where(
                [
                    ['CODE_KEY', '=', $Id]
                ]
            )->first();
        
        if ($data) {
            $entity = EventCodeHydrator::get_entity_from_db_result($data);
            return $entity;
        }
        return null;
    }

    public static function create(EventCode $entity)
    {
        $Id = app('db')->table('event_code')->insertGetId(
            [
                'EVENT_CODE' => $entity->EventCode,
                'EVENT_DESC' => $entity->EventDesc,
                'EVENT_POINT' => $entity->EventPoint,
                'SORT' => 0,
                'ACTIVE' => $entity->Active === true ? 'A' : 'I',
                'CREATE_BY' => $entity->CreateBy,
                'UPDATE_BY' => $entity->UpdateBy,
                'CREATE_DATE' => $entity->CreateDate,
                'UPDATE_DATE' => $entity->UpdateDate,
            ]
        );
        $entity->CodeKey = $Id;
        $entity->IsSuccessful = $Id > 0;
        return $entity;
    }

    public static function update(EventCode $entity)
    {
        $affected = app('db')->table('event_code')->where('CODE_KEY', $entity->CodeKey)
              ->update(
                [
                    'EVENT_CODE' => $entity->EventCode,
                    'EVENT_DESC' => $entity->EventDesc,
                    'EVENT_POINT' => $entity->EventPoint,
                    'ACTIVE' => $entity->Active === true ? 'A' : 'I',
                    'UPDATE_BY' => $entity->UpdateBy,
                    'UPDATE_DATE' => $entity->UpdateDate,
                ]
              );
        
        $entity->IsSuccessful = $affected >= 0;
        return $entity;
    }

    public static function delete(int $Id)
    {
        return app('db')->table('event_code')->where('CODE_KEY', $Id)->delete() == 1;
    }

    public static function exist(int $id)
    {
        return app('db')->table('event_code')->where('CODE_KEY', $id)->exists();
    }

    public static function is_used(int $id)
    {
        return app('db')->table('event_log')->where('CODE_KEY', $id)->exists();
    }

    public static function is_duplicate(int $id, $code)
    {
        if(app('db')->table('event_code')->where('EVENT_CODE', $code)->exists()){
            $_id = app('db')->table('event_code')->select('CODE_KEY')->where('EVENT_CODE', $code)->first()->CODE_KEY;
            return $id != $_id;
        }
        return false;        
    }
}
