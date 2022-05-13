<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use think\Db;
/**
 * 学生信息
 *
 * @icon fa fa-circle-o
 */
class Student extends Backend
{
    
    /**
     * Student模型对象
     * @var \app\admin\model\Student
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Student;
        $this->view->assign("sexList", $this->model->getSexList());
        $this->view->assign("isInterestList", $this->model->getIsInterestList());
        $this->view->assign("isLateList", $this->model->getIsLateList());
        $this->view->assign("isVipList", $this->model->getIsVipList());
        $this->view->assign("isMeetList", $this->model->getIsMeetList());
        $this->view->assign("isEatList", $this->model->getIsEatList());
        $this->view->assign("isMoneyList", $this->model->getIsMoneyList());
    }

    public function import()
    {
        parent::import();
    }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    

    /**
     * 查看
     */
    public function index()
    {
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $list = $this->model
                    ->with(['school','grade','team','teacher','meet','interest'])
                    ->where($where)
                    ->order($sort, $order)
                    ->paginate($limit);

            foreach ($list as $row) {
                
                $row->getRelation('school')->visible(['name']);
				$row->getRelation('grade')->visible(['name']);
				$row->getRelation('team')->visible(['name']);
				$row->getRelation('teacher')->visible(['name']);
				$row->getRelation('meet')->visible(['name']);
				$row->getRelation('interest')->visible(['name']);
            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }

    public function score_show($ids = null){
        $this->view->assign("id",$ids); 
        return $this->view->fetch();
    }

    public function score_save($ids = null){
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            $params['exam_name'] = $params["exam_name"];
            $params['student_id'] = $params["id"];
            $params['chinese'] = $params["chinese"];
            $params['math'] = $params["math"];
            $params['english'] = $params["english"];
            $params['pride'] = $params["pride"];
            $params['remark'] = $params["remark"];
            unset($params["id"]);
            Db::name('exam_log')->insert($params);

        }
        $this->success();
    }

    public function score_list($ids = null) {
        $result = Db::name("exam_log")->where("student_id", $ids)->order("id DESC")->select();
        $this->view->assign("result", $result);
        return $this->view->fetch();
    }

    public function del_exam_log($ids = null){
        if ($this->request->isPost()) {
            $params = $this->request->post("exam_log_id");
            Db::name("exam_log")->where("id", $params)->delete();
            $this->success();
        }
    }
}
