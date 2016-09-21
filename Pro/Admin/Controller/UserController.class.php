<?php
namespace Admin\Controller;
use Think\Controller;
/**
 * 用户控制器
 */
class UserController extends Controller {
    public function list(){
        $m=M();
        $result=$m->query("SELECT * FROM users");
        dump($result);
        // $this->display();

    }
}