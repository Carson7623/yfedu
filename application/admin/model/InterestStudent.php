<?php

namespace app\admin\model;

use think\Model;


class InterestStudent extends Model
{

    

    

    // 表名
    protected $table = 'interest_student';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];
    

    







    public function interest()
    {
        return $this->belongsTo('Interest', 'interest_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function student()
    {
        return $this->belongsTo('Student', 'student_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
