<?php
namespace Admin\Controller;
use Think\Controller;
class UserController extends CommonController {
    public function list(){
        $m=M();
        $result=$m->query("SELECT * FROM users");
        dump($result);
        // $this->display();

    }
}