<?php
/**
 * 公开接口API
 */
namespace V1\Controller;

use Common\Controller\ApiController;
// use Common\Common\Response;

class IndexController extends ApiController{

    //模块初始化，重写父类方法，避免该模块进入token验证
   public function _initialize(){
        // $this->init();
        //版本是否需要更新
        // $this->upgrade();
    }

   // 首页
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


   // 用户登录
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

     // 版本升级信息
    public function upgrade()
    {
        // $update = isset($_GET['update']) ? $_GET['update'] : '1';
        $update = isset($_POST['update']) ? $_POST['update'] : '1';
        if ($update==1) {
            $upgradeData = $this->init();
            $is_upload = $upgradeData['is_upload'];
            if ($is_upload) {
                $apk_url = $upgradeData['apk_url'];
                $this->show(200, 'apk_url获取成功', $apk_url);
            }else{
                $this->show(300, 'apk_url获取失败');
            }
        }else{
            $this->index();
        }
    }


   // 测试加密方法（对称加密算法）
   public function testEncode()
   {
       $str = I('get.str');
       echo myEncode($str);
   }

    // 测试解密方法（对称加密算法）
   public function testDecode()
   {
       $str = I('get.str');
       echo myDecode($str);
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

    //测试Response类 可返回json和xml数据类型
    public function testResponse()
    {
        $msg = '测试Response类成功,兼容json和xml数据类型';
        // $msg = 'success';
        $strToken='yk-mMEAzNP3cn1G4fj4MXQ==';
        $resn['token'] = myDes_decode($strToken,$username);
        $this->show(200, $msg, $resn);
        // return Response::show(200, $msg, $resn);
        // $this->myApiPrint($msg,200,$resn);
    }

    // 测试获取app信息
    public function testGetApp($id)
    {
        $data = $this->getApp($id);
        $this->show(200,'测试获取app信息成功',$data);
    }
    // 测试获取app版本升级信息
    public function testGetversionUpgrade($appId)
    {
        $data = $this->getversionUpgrade($appId);
        $this->show(200,'测试获取app版本升级信息成功',$data);
    }

    // 测试app初始化 版本是否需要更新
    public function testInit() {
        $this->check();
        // 获取版本升级信息
        $versionUpgrade = $this->getversionUpgrade($this->app['id']);
        if($versionUpgrade) {
            if($versionUpgrade['type'] && $this->params['version_id'] < $versionUpgrade['version_id']) {
                $versionUpgrade['is_upload'] = $versionUpgrade['type'];
            }else {
                $versionUpgrade['is_upload'] = 0;
            }
            $this->show(200, '版本升级信息获取成功', $versionUpgrade);
            // return $versionUpgrade;
            // return Response::show(200, '版本升级信息获取成功', $versionUpgrade);
        } else {
            // return false;
            $this->show(400, '版本升级信息获取失败');
            // return Response::show(400, '版本升级信息获取失败');
        }
    }







}