<?php

namespace Admin\Controller;
use Think\Controller;

/**
 * 首页控制器
 * @package Admin\Controller
 */
class IndexController extends CommonController {
    /**
     * 系统首页
     */
    public function index(){
        // 获取当前账户的登录信息
        $info = M('admin')->where(array('id' => parent::$userid))->find();
        $this->display();
    }
}