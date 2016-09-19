<?php
namespace Admin\Controller;
use Think\Controller;
class UserController extends Controller {
    public function list(){
        $m=M();
        $result=$m->query("SELECT * FROM users");
        dump($result);
        // $this->display();

    }
}