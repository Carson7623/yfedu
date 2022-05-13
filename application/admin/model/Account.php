<?php

namespace app\admin\model;

use think\Model;


class Account extends Model
{

    

    

    // 表名
    protected $table = 'account';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'in_out_text'
    ];
    

    
    public function getInOutList()
    {
        return ['入账' => __('入账'), '出账' => __('出账')];
    }


    public function getInOutTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['in_out']) ? $data['in_out'] : '');
        $list = $this->getInOutList();
        return isset($list[$value]) ? $list[$value] : '';
    }




    public function accounttype()
    {
        return $this->belongsTo('Accounttype', 'accounttype_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function accountuser()
    {
        return $this->belongsTo('Accountuser', 'accountuser_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
