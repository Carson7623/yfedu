<?php

namespace app\api\controller;

use app\admin\model\Nurse;
use app\common\controller\Api;
// use app\common\library\Nurse as LibraryNurse;
use app\common\library\Token;
use think\Db;
/**
 * 登陆
 */
class Login extends Api
{
    protected $noNeedLogin = ['login'];
    protected $noNeedRight = '*';
    protected $_token = '';
    
    public function _initialize()
    {
        parent::_initialize();
    }

    public function login()
    {
        $account = $this->request->request('account');
        $password = $this->request->request('password');
        if (!$account || !$password) {
            $this->error(__('Invalid parameters'));
        }
    
        $info = @Nurse::check_info($account, $password);
        if (is_array($info)) {
            $this->success('返回成功', $info);
        } else {
            $this->error($info);
        }
        
    }

    /**
     * 登出
     */
    public function login_out()
    {
        $token = $this->request->request('token');
        $info = Token::delete($token);
        if ($info) {
            $this->success("退出成功");
        } else {
            $this->error("退出失败");
        }
    }

    /**
     * 个人简介信息
     */
    public function info()
    {
        $token = $this->request->request('token');
        $info = Token::get($token);
        if ($info == false) {
            $this->error("token错误", null, -101);
        } 
        $user_id = $info["user_id"];
        $sql = "SELECT n.id,n.name as name,n.mobile,h.name as hospital_name,h.id as hospital_id 
                FROM fa_nurse as n 
                LEFT JOIN fa_hospital as h ON n.hospital_id = h.id 
                WHERE n.id = ". $user_id;
        $result = Db::query($sql);
        $this->success("", $result);

    }
}
