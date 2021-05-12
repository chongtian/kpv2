<?php
namespace App\Models;
use App\Models\BaseModel;

class EventCode extends BaseModel
{
    public $CodeKey;
    public $EventCode;
    public $EventDesc;
    public $EventPoint;
    public $Sort;    
    public $Active;
    public $CreateBy;
    public $UpdateBy;
    public $CreateDate;
    public $UpdateDate;
}