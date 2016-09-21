<?php
namespace Admin\Controller;
use Think\Controller;
/**
 * 登陆注册控制器
 */
class LoginController extends Controller {
    public function index(){
        $this->display();
    }

    /**
     * 登录
     * @DateTime 2016-08-19T17:45:27+0800
     * @return   [type]                   [description]
     */
    public function login()
    {
        $this->display();
    }

}