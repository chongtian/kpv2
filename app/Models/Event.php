<?php
namespace App\Models;
use App\Models\BaseModel;

class Event extends BaseModel
{
    public $LogKey;
    public $ChildUserKey;
    public $CodeKey;
    public $EventDesc;
    public $EventPoint;
    public $Memo;
    public $Active;
    public $CreateBy;
    public $UpdateBy;
    public $CreateDate;
    public $UpdateDate;
}
