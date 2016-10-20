<?php
/**
 * 公开接口API
 */
namespace V1\Controller;

use Common\Controller\ApiController;

class IndexController extends ApiController{

   public function _initialize(){
        //模块初始化，重写父类方法，避免该模块进入token验证
    }

   // 首页内容写入缓存
   public function index(){
        $va = M('category')->where('id = 1')->cache('cache_category')->select();
        $this->myApiPrint('','',$va);
    }


   public function Login()
   {
        $id = I('post.id');
        // $pwd = I('post.pwd');
        // $from = I('post.from','android');
        // $password = md5($pwd);

        // $where['password'] = $password;
        $where['id'] = $id;

        $owner = M('users');
        $resn = $owner->where($where)->field('id,tel,cname,usertype')->find();
        // dump($resn);die();
        if (!$resn) {
            $this->myApiPrint('帐号密码错误',300);
        }
        else{
            if (!$resn['usertype'])
                $msg = 'first login';
            else
            $msg = 'success';
            $strToken = $id.'|'.$resn['id'];
            $resn['token'] = myDes_encode($strToken,$id);
            $this->myApiPrint($msg,200,$resn);
        }
    }

     public function testToken()
    {
        $strToken='yk-mMEAzNP3cn1G4fj4MXQ==';
        $resn['token'] = myDes_decode($strToken,$username);
        $this->myApiPrint($msg,200,$resn);
    }


}