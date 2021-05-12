<?php
namespace App\Models;
use App\Models\BaseModel;

class Summary extends BaseModel
{
    public $SummaryKey;
    public $ChildUserKey;
    public $ChildUserName;
    public $Balance;
    public $Active;
    public $CreateBy;
    public $UpdateBy;
    public $CreateDate;
    public $UpdateDate;
}