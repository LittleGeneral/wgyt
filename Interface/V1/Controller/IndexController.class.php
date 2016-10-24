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

   public function index(){
            //如果有缓存，则读取缓存数据
        $cache_category = cache('cache_category');
        if (!empty($cache_category)) {
            $this->myApiPrint('','',$cache_category);
        }else{
           //如果没有缓存，则读取数据库当中的数据放入缓存
            $category = M('category')->select();
            $cache = cache('cache_category',$category,600);
            if ($cache) {
                $cache_category = cache('cache_category');
                $this->myApiPrint('','',$cache_category);
            }
        }
    }


   public function Login()
   {
        $id = I('post.id');
        $where['id'] = $id;
        $owner = M('users');
        $resn = $owner->where($where)->field('id,tel,cname,usertype')->find();
        if (!$resn) {
            $this->myApiPrint('帐号密码错误',300);
        }
        else{
            if (!$resn['usertype'])
                $msg = 'first login';
            else
            $msg = 'success';
            $strToken = $id.'|'.$resn['id'];
            $resn['token'] = myDes_encode($strToken,$id);  //生成token
            $this->myApiPrint($msg,200,$resn);
        }
    }

     // 测试token
     public function testToken()
    {
        $strToken='yk-mMEAzNP3cn1G4fj4MXQ==';
        $resn['token'] = myDes_decode($strToken,$username);
        $this->myApiPrint($msg,200,$resn);
    }

    //测试redis
    public function testRedis()
    {
        $redis = new \Redis();
        $redis->connect('127.0.0.1', 6379);
        $this->myApiPrint('',200,$redis);
    }
}