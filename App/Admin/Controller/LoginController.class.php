<?php
namespace Admin\Controller;
use Think\Controller;
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
       // $username =  $_POST['username'];
        // $data['username'] = I('post.username');
        // $data['password'] = I('post.password');
        // $admin = M('admin');
        // $admin=$admin->where("username = '$username'")->find();
        // dump($admin);
        // dump($username);
        // $this->assign('data', $data);
        $this->display();
    }

}