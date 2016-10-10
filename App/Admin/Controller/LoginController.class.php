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
     */
    public function login()
    {
        $this->display();
    }

     /**
     * 登陆验证
     * @DateTime 2016-09-22T17:48:59+0800
     */
    public function doLogin()
    {
        // $username =  $_POST['username'];
        $username = I('post.username');
        $pwd = I('post.password');
        $result = M('admin')->field('id,username,password')->find();
        if ($username == $result['username'] && $pwd ==$result['password']) {
             // 登录成功，设置session
            session('uid', $result ['id']);
            session('username', $result ['username']);
            // $_SESSION['username'] = $username;
            $this->success('登录成功', U('Index/index'),3);
            // $this->redirect('Index/index');
        }else{
            $this->error('登陆失败，请重新登陆');
        }
    }

    /**
     * 退出
     * @DateTime 2016-09-23T10:29:01+0800
     */
    public function logout() {
         // 清空所有session
        session(null);
        redirect(U('Login/login'));
    }

}