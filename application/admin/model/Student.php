<?php

namespace app\admin\model;

use think\Model;


class Student extends Model
{

    

    

    // 表名
    protected $table = 'student';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'sex_text',
        'is_interest_text',
        'is_late_text',
        'is_vip_text',
        'is_meet_text',
        'is_eat_text'
    ];
    

    
    public function getSexList()
    {
        return ['男' => __('男'), '女' => __('女')];
    }

    public function getIsInterestList()
    {
        return ['是' => __('是'), '否' => __('否')];
    }

    public function getIsLateList()
    {
        return ['是' => __('是'), '否' => __('否')];
    }

    public function getIsVipList()
    {
        return ['是' => __('是'), '否' => __('否')];
    }

    public function getIsMeetList()
    {
        return ['是' => __('是'), '否' => __('否')];
    }

    public function getIsEatList()
    {
        return ['是' => __('是'), '否' => __('否')];
    }

    public function getIsMoneyList()
    {
        return ['是' => __('是'), '否' => __('否')];
    }


    public function getSexTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['sex']) ? $data['sex'] : '');
        $list = $this->getSexList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsInterestTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_interest']) ? $data['is_interest'] : '');
        $list = $this->getIsInterestList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsLateTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_late']) ? $data['is_late'] : '');
        $list = $this->getIsLateList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsVipTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_vip']) ? $data['is_vip'] : '');
        $list = $this->getIsVipList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function getIsMoneyTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_money']) ? $data['is_money'] : '');
        $list = $this->getIsMoneyList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsMeetTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_meet']) ? $data['is_meet'] : '');
        $list = $this->getIsMeetList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsEatTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_eat']) ? $data['is_eat'] : '');
        $list = $this->getIsEatList();
        return isset($list[$value]) ? $list[$value] : '';
    }




    public function school()
    {
        return $this->belongsTo('School', 'school_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function grade()
    {
        return $this->belongsTo('Grade', 'grade_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function team()
    {
        return $this->belongsTo('Team', 'team_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function teacher()
    {
        return $this->belongsTo('Teacher', 'teacher_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function meet()
    {
        return $this->belongsTo('Meet', 'meet_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function interest()
    {
        return $this->belongsTo('Interest', 'interest_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
