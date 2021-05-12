<?php

namespace  App\DataFramework\Event;

use App\Models\Adm\DataSet;
use App\Models\Event;
use App\Models\Child;
use App\Models\Summary;
use App\DataFramework\Event\EventHydrator;
use App\DataFramework\CommonDb;

class EventDb
{
    public static function list(int $child_user_key, $keyword, int $page = 0, int $size = 20)
    {
        $tableName = 'v_event';
        if ($child_user_key > 0){
            $where = " CHILD_USER_KEY = :p1 AND ACTIVE = 'A' ";
        } else {
            $where = " CHILD_USER_KEY > :p1 AND ACTIVE = 'A' ";
        }
        
        $orderBy = ' CREATE_DATE desc';
        $dataSet = CommonDb::query_with_raw($tableName, $where, [$child_user_key], $orderBy, $page, $size);
        $entities = [];
        if ($dataSet->Data) {
            foreach ($dataSet->Data as $data) {
                $entity = EventHydrator::get_entity_from_db_result($data);
                $entities[] = $entity;
            }
        }
        $dataSet->Data = $entities;
        return $dataSet;
    }

    public static function get(int $Id, int $child_user_key)
    {
        if($child_user_key>0){
            $data = $result = app('db')->table('event_log')->where(
                [
                    ['LOG_KEY', '=', $Id],
                    ['CHILD_USER_KEY', '=', $child_user_key]
                ]
            )->first();
        } else{
            $data = $result = app('db')->table('event_log')->where(
                [
                    ['LOG_KEY', '=', $Id]
                ]
            )->first();
        }
        
        if ($data) {
            $entity = EventHydrator::get_entity_from_db_result($data);
            return $entity;
        }
        return null;
    }

    public static function list_kids(int $kid_access)
    {
        $kids = app('db')->table('user_info')->select('USER_KEY', 'USER_NAME')->where([['ACCESS', $kid_access], ['ACTIVE', 'A']])->get();
        $entities = []; 
        if ($kids) {
            foreach ($kids as $kid) {
                $entity = new Child();
                $entity->ChildUserKey = $kid->USER_KEY;
                $entity->ChildUserName = $kid->USER_NAME;
                $entities[] = $entity;
            }
        }
        return $entities;
    }

    public static function list_summary(int $child_user_key)
    {
        if ($child_user_key > 0){
            $summaries = app('db')->table('summary')->where([['CHILD_USER_KEY', $child_user_key], ['ACTIVE', 'A']])->get();
        } else {
            $summaries = app('db')->table('summary')->where('ACTIVE', 'A')->get();
        }
        
        $entities = []; 
        if ($summaries) {
            foreach ($summaries as $summary) {
                $entity = new Summary();
                $entity->SummaryKey = $summary->SUMMARY_KEY;
                $entity->ChildUserKey = $summary->CHILD_USER_KEY;
                $entity->ChildUserName = $summary->CHILD_USER_NAME;
                $entity->Balance = $summary->BALANCE;
                $entities[] = $entity;
            }
        }
        return $entities;
    }


    public static function create(Event $entity)
    {
        $Id = app('db')->table('event_log')->insertGetId(
            [
                'CHILD_USER_KEY' => $entity->ChildUserKey,
                'CODE_KEY' => $entity->CodeKey,
                'EVENT_POINT' => $entity->EventPoint,
                'MEMO' => $entity->Memo,
                'ACTIVE' => $entity->Active === true ? 'A' : 'I',
                'CREATE_BY' => $entity->CreateBy,
                'UPDATE_BY' => $entity->UpdateBy,
                'CREATE_DATE' => $entity->CreateDate,
                'UPDATE_DATE' => $entity->UpdateDate,
            ]
        );
        $entity->LogKey = $Id;
        $entity->IsSuccessful = $Id > 0;

        // update field SORT of the Code
        $affected = app('db')->statement('UPDATE event_code SET SORT = SORT + 1 WHERE CODE_KEY = ?', [$entity->CodeKey]);

        // update summary table
        $affected = app('db')->statement('UPDATE summary SET BALANCE = BALANCE + :p1 WHERE CHILD_USER_KEY = :p2', [$entity->EventPoint, $entity->ChildUserKey]);

        return $entity;
    }

    public static function update(Event $entity)
    {
        $record = app('db')->table('event_log')->select('CODE_KEY', 'EVENT_POINT', 'CHILD_USER_KEY')->where('LOG_KEY', $entity->LogKey)->first();
        $oldCodeKey = $record->CODE_KEY;
        $oldEventPoint = $record->EVENT_POINT;
        $childUserKey = $record->CHILD_USER_KEY;
        $difference = $entity->EventPoint - $oldEventPoint;

        $affected = app('db')->table('event_log')->where('LOG_KEY', $entity->LogKey)
              ->update(
                [
                    'CHILD_USER_KEY' => $childUserKey,
                    'CODE_KEY' => $entity->CodeKey,
                    'EVENT_POINT' => $entity->EventPoint,
                    'MEMO' => $entity->Memo,
                    'ACTIVE' => $entity->Active === true ? 'A' : 'I',
                    'UPDATE_BY' => $entity->UpdateBy,
                    'UPDATE_DATE' => $entity->UpdateDate,
                ]
              );
        
        $entity->IsSuccessful = $affected >= 0;

        if ($oldCodeKey != $entity->CodeKey){
            $affected = app('db')->statement('UPDATE event_code SET SORT = SORT - 1 WHERE CODE_KEY = ?', [$oldCodeKey]);
            $affected = app('db')->statement('UPDATE event_code SET SORT = SORT + 1 WHERE CODE_KEY = ?', [$entity->CodeKey]);
        }

        // update summary table
        if ($difference != 0){
            $affected = app('db')->statement('UPDATE summary SET BALANCE = BALANCE + :p1 WHERE CHILD_USER_KEY = :p2', [$difference, $childUserKey]);
        }        

        return $entity;
    }

    public static function delete(int $Id)
    {
        $record = app('db')->table('event_log')->select('CODE_KEY', 'EVENT_POINT', 'CHILD_USER_KEY')->where('LOG_KEY', $Id)->first();
        $codeKey = $record->CODE_KEY;
        $eventPoint = $record->EVENT_POINT;
        $childUserKey = $record->CHILD_USER_KEY;

        // update field SORT of the Code
        $affected = app('db')->statement('UPDATE event_code SET SORT = SORT - 1 WHERE CODE_KEY = ?', [$codeKey]);

        // update summary table
        $affected = app('db')->statement('UPDATE summary SET BALANCE = BALANCE - :p1 WHERE CHILD_USER_KEY = :p2', [$eventPoint, $childUserKey]);

        return app('db')->table('event_log')->where('LOG_KEY', $Id)->delete() == 1;
    }

    public static function exist(int $id)
    {
        return app('db')->table('event_log')->where('LOG_KEY', $id)->exists();
    }
}
