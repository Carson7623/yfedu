<?php

namespace app\api\controller;

use app\admin\model\Patient;
use app\common\controller\Api;
// use app\common\library\Nurse as LibraryNurse;
use app\common\library\Token;
use EasyWeChatComposer\Plugin;
use think\Db;

use function GuzzleHttp\json_encode;

/**
 *报告
 */
class Report extends Api
{
    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $_token = '';
    
    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 入院基本信息
     */
    public function in_hospital()
    {
        $info = self::check_login();
        $params["name"] = $this->request->request('name');
        $params["sex"] = $this->request->request('sex');
        $params["hospital_id"] = $this->request->request('hospital_id');
        $params["hospital_number"] = $this->request->request('hospital_number');
        $params["age"] = $this->request->request('age');
        $params["look_after_name"] = $this->request->request('look_after_name');
        $params["look_after_mobile"] = $this->request->request('look_after_mobile');
        $params["look_after_relation"] = $this->request->request('look_after_relation');
        $params["nurse_id"] = $info["user_id"];
        if (empty($params["name"]) || empty($params["nurse_id"])){
            $this->error("参数错误");
        }
        $id = Db('patient')->insertGetId($params);
        if ($id) {
            $info["id"] = $id;
            $info["hospital_number"] = $params["hospital_number"];
            $this->success("登记完成", $info);
        } else {
            $this->error("入院报告错误");
        }
    }

    /**
     * 入院报告
     */
    public function hospital_report()
    {
        $info = self::check_login();
        $user_id = $info["user_id"];
        $type = $this->request->request("type");    //0.入院 1.出院
        $patient_id = $this->request->request("patient_id");
        $hospital_number = $this->request->request("hospital_number");
        $report_content = $this->request->request("report_content");
        // $report_content = '{"report_id":1,"answer":[{"number_id":1,"score":2,"remark":"\u8bc4\u4f30\u8be6\u60c5","judge":"\u65e0\u6cd5\u5224\u65ad\u8be6\u60c5"},{"number_id":2,"score":8,"remark":"\u8bc4\u4f30\u8be6\u60c5","judge":"\u65e0\u6cd5\u5224\u65ad\u8be6\u60c5"}]}';
        $report_content = htmlspecialchars_decode($report_content);
        $report_content_arr = json_decode($report_content, true);
        if (empty($report_content_arr) || empty($patient_id)) {
            $this->error("参数错误");
        }
        
        if ($type == 0) {
            $is_has = Db("report")->where("patient_id", $patient_id)->where("report_status", 0)->find();
            if (!empty($is_has)) {
                $this->error("入院报告已经填写");
            }
        } elseif ($type == 1) {
            $is_has = Db("report")->where("patient_id", $patient_id)->where("report_status", 1)->find();
            if (!empty($is_has)) {
                $this->error("入院报告已经填写");
            }
        }

        $sum = 0;
        foreach ($report_content_arr["answer"] as $k => $v){
            $sum += $v["score"] == -1 ? 0 : $v["score"];
        }

        $params["hospital_number"] = $hospital_number;
        $params["answer"] = $report_content;
        $params["score"] = $sum;
        $params["patient_id"] = $patient_id;
        $params["createtime"] = date("Y-m-d");
        $params["updatetime"] = date("Y-m-d");
        $params["nurse_id"] = $user_id;
        if ($type == 0) {
            $params["report_status"] = 0;
        } elseif ($type == 1) {
            $params["report_status"] = 1;
        }
        $insert_id = Db('report')->insertGetId($params);

        if ($type == 0) {
            $param["one_id"] = $insert_id;
            $param["in_time"] = date("Y-m-d");
            $param["in_score"] = $sum;
            $param["status"] = 0;
        }
       
        $result = db('patient')->where('id', $patient_id)->update($param);
        
        if ($result) {
            $this->success("保存成功");
        } else {
            $this->error("保存失败");
        }
    }
    
    /**
     * 既往报告
     */
    public function old_report()
    {
        $info = self::check_login();
        $user_id = $info["user_id"];
        $name = $this->request->request("name");
        $name_sort = $this->request->request("name_sort");  //1升序2降序
        $report_time = $this->request->request("report_time"); //1一周内2一月内3半年内4一年内 
        $report_time_sort = $this->request->request("report_time_sort");//1升序2降序
        $age = $this->request->request("age");  //1.40-49,2.50-59,3.60-69,4.70-79,5.其他
        $score = $this->request->request("score");//1:<50,2:51-80,3:81-110,4:111-150,5:>150
        $page = $this->request->request("page") ? $this->request->request("page") : 1; 
        $page_size = $this->request->request("page_size") ?  $this->request->request("page_size") : 10;
        $where = $order = "";
        if ($name != "") $where .= " AND p.name LIKE '%". $name ."%' ";
        $now = date('Y-m-d H:i:s',time());
        if (in_array($report_time, [1,2,3,4])){
            if ($report_time == 1) {
                $report_time = date("Y-m-d",strtotime("-1 week",strtotime($now)));
            } elseif ($report_time == 2) {
                $report_time = date("Y-m-d",strtotime("-1 month",strtotime($now)));
            } elseif ($report_time == 3) {
                $report_time = date("Y-m-d",strtotime("-182 days",strtotime($now)));
            } elseif ($report_time == 4) {
                $report_time = date("Y-m-d",strtotime("-1 year",strtotime($now)));
            }
            $where .= " AND r.createtime >= '" . $report_time . "' ";
        }
        if ($age == 1) {
            $where .= " AND (p.age >= 40 AND p.age <= 49) ";
        } elseif ($age == 2) {
            $where .= " AND (p.age >= 50 AND p.age <= 59) ";
        } elseif ($age == 3) {
            $where .= " AND (p.age >= 60 AND p.age <= 69) ";
        } elseif ($age == 4) {
            $where .= " AND (p.age >= 70 AND p.age <= 79) ";
        } elseif ($age == 5) {
            $where .= " AND (p.age < 40 OR p.age > 80 ) ";
        }
        if ($score == 1) {
            $where .= " AND r.score < 50 ";
        } elseif ($score == 2) {
            $where .= " AND r.score >= 51 AND r.score <= 80 ";
        } elseif ($score == 3) {
            $where .= " AND r.score >= 81 AND r.score <= 110 ";
        } elseif ($score == 4) {
            $where .= " AND r.score >= 111 AND r.score <= 151 ";
        } elseif ($score == 5) {
            $where .= " AND r.score > 150 ";
        }
        if ($name_sort == 1) {
            $order .= " ,p.name ASC ";
        } elseif ($name_sort == 2){
            $order .= " ,p.name DESC ";
        }
        if ($report_time_sort == 1) {
            $order .=  " ,r.createtime ASC ";
        } elseif ($report_time_sort == 2) {
            $order .= " ,r.createtime DESC ";
        }

        $nurse_ids = self::nurse_ids_by_hospital($info["user_id"]);
        $sql = "SELECT count(r.id) as total FROM fa_report as r LEFT JOIN fa_patient as p ON r.patient_id = p.id 
                WHERE r.nurse_id in (' ". $nurse_ids . " ') AND report_status = 0 ";
        if ($where) $sql .=  $where;
        $count = Db::query($sql);
        $total = $count[0]["total"];

        $now_page = $page_size*($page-1);
        $sql_list = "SELECT r.*,p.* FROM fa_report as r LEFT JOIN fa_patient as p ON r.patient_id = p.id 
        WHERE r.nurse_id in (' ". $nurse_ids . " ') AND report_status = 0 ";
        if ($where) $sql_list .=  $where ;
        $sql_list .= " ORDER BY r.id DESC ";
        if ($order) $sql_list .= $order;
        $sql_list .= " LIMIT " . $now_page . " , " . $page_size;
        $list = Db::query($sql_list);
       
        if ($list) {
            foreach ($list as $k => $v) {
                $list[$k]["answer"] = json_decode($v["answer"], true);
            }
        }
        $return["list"] = $list;
        $return["total"] = $total;
        $return["page"] = $page;
        if ($return) {
            $this->success("成功", $return);
        } else {
            $this->error("失败");
        }
    }

    /**
     * 首页数据统计,该机构已评估总数；
     */
    public function analyse_data(){
        $info = self::check_login();
        $user_id = $info["user_id"];
        $nurse_total = $department_total = $hospital_total = 0;
        $nurse_total = Db('report')->where("nurse_id", $user_id)->count();
        $return = [];
        $return["nurse_total"] = $nurse_total;

        $nurse_info = Db('nurse')->where("id", $user_id)->find();
        $hospital_id = $nurse_info["hospital_id"];
        $hospital_str = "";
        
        //查询该医院的医生ID
        if ($hospital_id) {
            $hospital_info = Db("nurse")->where("hospital_id", $hospital_id)->select();
            if ($hospital_info) {
                foreach ($hospital_info as $k => $v) {
                    $hospital_str .= $v["id"] . ",";
                }
                $hospital_str = substr($hospital_str, 0, strlen($hospital_str)-1); 
            }
        }
   
        $hospital_str = "SELECT count(id) as total FROM fa_report WHERE nurse_id in (".$hospital_str.")";
        $hospital_total_arr = Db::query($hospital_str);
        $hospital_total = $hospital_total_arr[0]["total"];

        $return["hospital_total"] = $hospital_total;

        $this->success("", $return);
    }

    /**
     * 检查是否登陆
     */
    public function check_login(){
        $token = $this->request->request('token');
        $info = Token::get($token);
        if ($info == false) {
            $this->error("token错误", null, -101);
        } 
        return $info;
    }

    /**
     * 既往报告详情
     */
    public function old_report_detail()
    {
        $info = self::check_login();
        $id = $this->request->request("id");
        $return = [];

        $nurse_info = Db("patient")->where("id", $id)->find();
        if (empty($nurse_info)){
            $this->error("数据为空");
        }
        if ($nurse_info["look_after_relation"]) {
            $relation_info = Db("realtion")->where("id", $nurse_info["look_after_relation"])->find();
            $nurse_info["look_after_relation_name"] = $relation_info["name"];
        }
        $return["info"] = $nurse_info;
        $return["in_report"] = $return["out_report"] = [];
        if ($nurse_info["one_id"]) {
            $in_report_info = Db("report")->where("id", $nurse_info["one_id"])->find();
            $return["in_report"] = $in_report_info;
            $return["in_report"]["answer"] = json_decode($in_report_info["answer"], true);
        }
       
        $exam_info = Db("examination")->where("status", 0)->select();
        $return["exam_info"] = isset($exam_info[0]) && $exam_info[0] ? $exam_info[0] : [];
        $return["exam_info"]["content"] = json_decode($return["exam_info"]["content"], true);
        foreach ($return["exam_info"]["content"] as $k => $v) {
            if (isset($v["question"]) && $v["question"]) {
                foreach ($v["question"] as $m => $n) {
                    $temp_arr = isset($n["answer_score"]) && $n["answer_score"] ? explode("-", $n["answer_score"]) : $n["answer_score"];
                    if (!is_array($temp_arr)) {
                        $temp_arr = (array)$temp_arr;
                    }
                    if (is_array($temp_arr) && count($temp_arr) > 1) {
                        $arr = [];
                        for ($i = $temp_arr[0]; $i <= $temp_arr[1]; $i++) {
                            $arr[] = (int)$i;
                        }
                        $temp_arr = $arr;
                    }  else {
                        if (is_array($temp_arr)) {
                            $temp_arr = (int)$temp_arr[0];
                        } else {
                            $temp_arr = (int)$temp_arr;
                        }
                    }
                    $return["exam_info"]["content"][$k]["question"][$m]["answer_score_arr"] = $temp_arr;
                }
            }
        }
        $this->success("", $return);
    }

    /**
     * 入院考试题目
     */
    public function exam_list()
    {
        $info = self::check_login();
        $return = [];
        $exam_info = Db("examination")->where("status", 0)->select();
        $return = isset($exam_info[0]) && $exam_info[0] ? $exam_info[0] : [];
        $return["content"] = json_decode($return["content"], true);
        foreach ($return["content"] as $k => $v) {
            if (isset($v["question"]) && $v["question"]) {
                foreach ($v["question"] as $m => $n) {
                    $temp_arr = isset($n["answer_score"]) && $n["answer_score"] ? explode("-", $n["answer_score"]) : $n["answer_score"];
                    if (!is_array($temp_arr)) {
                        $temp_arr = (array)$temp_arr;
                    }
                    if (is_array($temp_arr) && count($temp_arr) > 1) {
                        $arr = [];
                        for ($i = $temp_arr[0]; $i <= $temp_arr[1]; $i++) {
                            $arr[] = (int)$i;
                        }
                        $temp_arr = $arr;
                    }  else {
                        if (is_array($temp_arr)) {
                            $temp_arr = (int)$temp_arr[0];
                        } else {
                            $temp_arr = (int)$temp_arr;
                        }
                    }
                    $return["content"][$k]["question"][$m]["answer_score_arr"] = $temp_arr;
                }
            }
        }
        $this->success("", $return);
    }

    /**
     * 照顾关系
     */
    public function relation_list()
    {
        $info = self::check_login();
        $relation_info = Db("realtion")->select();
        $this->success("", $relation_info);
    }

    /**
     * 付款方式列表
     */
    public function payway_list()
    {
        $info = self::check_login();
        $payway_info = Db("payway")->select();
        $this->success("", $payway_info);
    }

    /**
     * 查看某个入院的详细信息
     */
    public function patient_info_by_id()
    {
        self::check_login();
        $patient_id = $this->request->request("patient_id");
        if (empty($patient_id)){
            $this->error("参数错误");
        }
        $result = db('patient')->where('id', $patient_id)->find();
        $this->success("", $result);
    }

    /**
     * 入院列表，待出院
     */
    public function patient_list()
    {
        $info = self::check_login();
        $nurse_ids = self::nurse_ids_by_hospital($info["user_id"]);
        $name = $this->request->request("name");
        if ($name) {
            $result = db('patient')->where('status', 0)->where("name", $name)->whereIn("nurse_id", $nurse_ids, "AND")->select();
        } else {
            $result = db('patient')->where('status', 0)->whereIn("nurse_id", $nurse_ids, "AND")->select();
        }
        $this->success("", $result);
    }

    /**
     * 根据护士ID查询出本部门的所有护士ID 
     */
    public function nurse_ids_by_hospital($nurse_id){
        $info = db('nurse')->where('id', $nurse_id)->find();
        if (empty($info)) {
            return $nurse_id;
        }
        $department_id = $info["department_id"];
        $nurse_info = db('nurse')->where('department_id', $department_id)->select();
        if (empty($nurse_info)) {
            return $nurse_id;
        }
        $return = $nurse_id;
        foreach ($nurse_info as $k => $v){
            $return .= "," . $v["id"];
        }
        return $return;
    }

}
