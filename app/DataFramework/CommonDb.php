<?php

namespace  App\DataFramework;

use Closure;
use App\Models\Adm\DataSet;

class CommonDb
{
    /**
     * return records from database with support of pagination
     * @param string $tableName The name of the table or view
     * @param Closure $where A function to return where statement for Lumen query builder
     * @param string $orderColumn The name of the column used in Order By
     * @param string $order The way to order the records, desc|asc
     * @param string $keyword The keyword to filter records
     * @param int $page The current page, starting from 1
     * @param int $size The number of records in each page
     * @return DataSet
     */
    public static function search(string $tableName, Closure $where, string $orderColumn, string $order, string $keyword = null, int $page = 0, int $size = 20)
    {
        $wheres = [$where];
        $keywords = [$keyword];
        $result = self::query($tableName, $wheres, $keywords, $orderColumn, $order, $page, $size);
        return $result;
    }

    /**
     * return records from database with support of pagination
     * @param string $tableName The name of the table or view
     * @param array $wheres An array of Closures, each of which is a function to return where statement for Lumen query builder
     * @param array $keywords An array including all keywords to filter records. Each keyword will be feeded to the where closure in order
     * @param string $orderColumn The name of the column used in Order By
     * @param string $order The way to order the records, desc|asc
     * @param int $page The current page, starting from 1
     * @param int $size The number of records in each page
     * @return DataSet
     */
    public static function query(string $tableName, $wheres, $keywords, string $orderColumn, string $order, int $page = 0, int $size = 20)
    {
        $result = new DataSet();
        $result->Keyword = implode(',', $keywords);
        $result->CurrentPage = $page;
        $result->PageSize = $size;
        
        $query = app('db')->table($tableName);
        
        foreach ($wheres as $i => $where) {
            $query->when($keywords[$i], $where);
        }
        
        $countOfRecords = $query->count();
        $totalPages = ceil($countOfRecords / $size);
        $result->Count = $countOfRecords;
        $result->MaxNumOfPage = $totalPages;

        if ($countOfRecords > 0) {
            $offset = 0;
            if ($page < 1) {
                $offset = 0;
            } elseif ($page > $totalPages) {
                $offset = ($totalPages - 1) * $size;
            } else {
                $offset = ($page - 1) * $size;
            }

            $query->orderBy($orderColumn, $order)->offset($offset)->limit($size);
            $data = $query->get();
            $result->Data = $data;
        }

        return $result;
    }

    public static function query_with_raw(string $tableName, string $rawWhere, $keywords, string $rawOrder, int $page = 0, int $size = 20)
    {
        $result = new DataSet();
        $result->CurrentPage = $page;
        $result->PageSize = $size;
        
        $query = app('db')->table($tableName)->whereRaw($rawWhere, $keywords); 
        
        $countOfRecords = $query->count();
        $totalPages = ceil($countOfRecords / $size);
        $result->Count = $countOfRecords;
        $result->MaxNumOfPage = $totalPages;

        if ($countOfRecords > 0) {
            $offset = 0;
            if ($page < 1) {
                $offset = 0;
            } elseif ($page > $totalPages) {
                $offset = ($totalPages - 1) * $size;
            } else {
                $offset = ($page ) * $size;
            }

            $query->orderByRaw($rawOrder)->offset($offset)->limit($size);
            $data = $query->get();
            $result->Data = $data;
        }

        return $result;
    }
}
