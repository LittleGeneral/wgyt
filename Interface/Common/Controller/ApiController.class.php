<?php
/**
 * Api公共类
 * Interface引用api模式，没有display等view的渲染和页面模版输出
 */
namespace Common\Controller;
// use Common\Common\Response;
use Think\Controller;

class ApiController extends Controller
{
    protected $userID = null;

    public function _initialize()
    {
        //验证token
        $token = I('request.token');
        $cname = I('request.cname');
        if($cname)
        {
            $vToken = myDes_decode($token,$cname);
            $arrStr = explode('|',$vToken);
            if($cname && $arrStr[0] === $cname) $this->userID = $arrStr[1];
            else $this->myApiPrint('user name error !');
        }else{
            $this->myApiPrint('user name error!');
        }
    }

    // app初始化 版本是否需要更新
    public function init() {
        $this->check();//检测版本信息
        // 获取版本升级信息
        $versionUpgrade = $this->getversionUpgrade($this->app['id']);
        if($versionUpgrade) {
            if($versionUpgrade['type'] && $this->params['version_id'] < $versionUpgrade['version_id']) {
                $versionUpgrade['is_upload'] = $versionUpgrade['type'];
            }else {
                $versionUpgrade['is_upload'] = 0;
            }
            // $this->show(200, '版本升级信息获取成功', $versionUpgrade);
            return $versionUpgrade;
        } else {
            return false;
            // $this->show(400, '版本升级信息获取失败');
        }
    }

    protected $params;
    protected $app;
    public function check() {
        $this->params['app_id'] = $appId = isset($_POST['app_id']) ? $_POST['app_id'] : '';
        $this->params['version_id'] = $versionId = isset($_POST['version_id']) ? $_POST['version_id'] : '';
        $this->params['version_mini'] = $versionMini = isset($_POST['version_mini']) ? $_POST['version_mini'] : '';
        $this->params['did'] = $did = isset($_POST['did']) ? $_POST['did'] : '';
        $this->params['encrypt_did'] = $encryptDid = isset($_POST['encrypt_did']) ? $_POST['encrypt_did'] : '';

        // $this->params['app_id'] = $appId = isset($_GET['app_id']) ? $_GET['app_id'] : '';
        // $this->params['version_id'] = $versionId = isset($_GET['version_id']) ? $_GET['version_id'] : '';
        // $this->params['version_mini'] = $versionMini = isset($_GET['version_mini']) ? $_GET['version_mini'] : '';
        // $this->params['did'] = $did = isset($_GET['did']) ? $_GET['did'] : '';
        // $this->params['encrypt_did'] = $encryptDid = isset($_GET['encrypt_did']) ? $_GET['encrypt_did'] : '';

        if(!is_numeric($appId) || !is_numeric($versionId)) {
            $this->show(401, '参数不合法');
            // return Response::show(401, '参数不合法');
        }
        // 判断APP是否需要加密
        $this->app = $this->getApp($appId);
        if(!$this->app) {
            $this->show(402, 'app_id不存在');
            // return Response::show(402, 'app_id不存在');
        }
        if($this->app['is_encryption'] && $encryptDid != md5($did . $this ->app['key'])) {
            $this->show(403, '没有该权限');
            // return Response::show(403, '没有该权限');
        }
    }

    // 获取app信息
    public function getApp($id) {
        $model = M('app');
        $where = "id = '$id'";
        $appData = $model->where($where)->find();
        return $appData;
        // $this->show(200,'success',$app1);
    }

    // 获取app版本升级信息
    public function getversionUpgrade($appId) {
         $model = M('version_upgrade');
        $where = "app_id = '$appId' and status = 1";
        $appData = $model->where($where)->find();
        return $appData;
        // $this->show(200,'success',$appData);
    }

    /**
     * 根据图片大小组装相应图片
     * @param string $imageUrl
     * @param string $size
     */
    public function setImage($imageUrl, $size) {
        if(!$imageUrl) {
            return '';
        }
        if(!$size) {
            return $imageUrl;
        }

        $type = substr($imageUrl, strrpos($imageUrl, '.'));
        if(!$type) {
            return '';
        }
        $path = substr($imageUrl, 0, strrpos($imageUrl, '.'));

        return $path . '_' . $size . $type;
    }

    /*
     *检查用户账号
     *@return 混合模型
     * */
    public function checkUserAccount($user,$t=0){
//        if(!preg_match("/1[3578]{1}\d{9}$/",$user)){
//            $this->myApiPrint('手机号码格式不对');
//        }
        $where['stu_user'] = $user;
        $owner = M('student');
        $resn = $owner->where($where)->find();
        if($t==1&&!$resn){
            $this->myApiPrint('非法请求，账号不存在');
        }
        return $resn;
    }

    /**
     * 图片二进制存入物理地址
     * @param $data base64加密图片流字符串
     * @param $uid  用户ID
     * @param $type 图片类型，默认gif
     * @return 数组，booler 与 返回值
     */
    public function myStream2Img($data,$uid,$type='.gif',$name=''){
        $name = (empty($name))?time().$type:$name.$type;
        $dir = '/Public/Upload/'.$uid.'/';
        $s2i = new \Common\Org\Stream2Image($name,$dir);// 实例化上传类
        $re = $s2i->stream2Image(base64_decode($data));
        //@file_put_contents('a1_dir.log',$dir.$name.PHP_EOL,FILE_APPEND);
        if(true === $re){@chmod('./'.$dir,0777);@chmod('./'.$dir.$name,0777); return $dir.$name;}
        else {
            $result = array(
                'code' => 300,
                'msg' => $s2i->print_errInfo,
                'result' => ''
            );
            $this->ajaxReturn($result);exit;
        }
    }

    /**
     * 公共错误返回
     * @param $msg 需要打印的错误信息
     * @param $code 默认打印300信息
     * $pageNum = I('get.pageNum',1);
        $pageCount = I('get.pageCount',5);
     */
    public function myApiPrint($msg='',$code=300,$data=''){
        $result = array(
            // 'pageNum'=>1,
            // 'pageCount'=>5,
            'code' => $code,
            'msg' => $msg,
            'result' => $data
        );
        echo json_encode($result,JSON_UNESCAPED_UNICODE);exit;
        // $this->ajaxReturn($result);exit;
    }

    /*
     * 生成定长22位的订单码
     * */
    public function MyOrderNo22(){
        $code  =date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
        $code .= randCodeM(22-strlen($code),1);
        return $code;
    }

    /*
     * 检查请求有效性与参数
     * @return 混合模型
     * */
    public function checkRequestTime($time){
        if(($time) > 0){
            //300秒/5分钟的有效请求
            $v =time()-$time;
            if($v<300 && $v>0) return true;
            else return false;
        }else return false;
    }

    /*
     * 检查数组是否为空
     * */
    public function arrIsEmpty($arr)
    {
        foreach($arr as $v)
        {
            if(empty($v)) return true;
        }
    }

    //安全性接口，验签操作
    public function mySign($para_temp){

        //除去待签名参数数组中的空值和签名参数
        $para_filter = $this->myParaFilter($para_temp);

        //对待签名参数数组排序
        $para_sort = $this->myArgSort($para_filter);

        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $prestr = $this->myParaLinkstring($para_sort);
        return $prestr;
    }

    //安全性接口，验签操作
    public function myParaFilter($para) {
        $para_filter = array();
        while (list ($key, $val) = each ($para)) {
            if($key === "sign" || $key === "sign_pass" || $val === "")continue;
            else	$para_filter[$key] = $para[$key];
        }
        return $para_filter;
    }

    /**
     * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
     * @param $para 需要拼接的数组
     * return 拼接完成以后的字符串
     */
    function myParaLinkstring($para) {
        $arg  = "";
        while (list ($key, $val) = each ($para)) {
            $arg.=$key."=".$val."&";
        }
        //去掉最后一个&字符
        $arg = substr($arg,0,count($arg)-2);

        //如果存在转义字符，那么去掉转义
        if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}

        return $arg;
    }

    /**
     * 对数组排序
     * @param $para 排序前的数组
     * return 排序后的数组
     */
    function myArgSort($para) {
        ksort($para);
        reset($para);
        return $para;
    }


    const JSON = "json";
    /**
    * 按综合方式输出通信数据
    * @param integer $code 状态码
    * @param string $message 提示信息
    * @param array $data 数据
    * @param string $judy_type(array) 数据类型
    * return string
    */
    public static function show($code, $message = '', $data = array(), $type = self::JSON) {
        if(!is_numeric($code)) {
            return '';
        }

        $type = isset($_GET['format']) ? $_GET['format'] : self::JSON;

        $result = array(
            'code' => $code,
            'message' => $message,
            'data' => $data,
        );

        if($type == 'json') {
            self::json($code, $message, $data);
            exit;
        } elseif($type == 'array') {
            var_dump($result);
        } elseif($type == 'xml') {
            self::xmlEncode($code, $message, $data);
            exit;
        } else {
            // TODO
        }
    }

    /**
    * 按json方式输出通信数据
    * @param integer $code 状态码
    * @param string $message 提示信息
    * @param array $data 数据
    * return string
    */
    public static function json($code, $message = '', $data = array()) {

        if(!is_numeric($code)) {
            return '';
        }

        $result = array(
            'code' => $code,
            'message' => $message,
            'data' => $data
        );

        // echo json_encode($result);
        echo json_encode($result,JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
    * 按xml方式输出通信数据
    * @param integer $code 状态码
    * @param string $message 提示信息
    * @param array $data 数据
    * return string
    */
    public static function xmlEncode($code, $message, $data = array()) {
        if(!is_numeric($code)) {
            return '';
        }

        $result = array(
            'code' => $code,
            'message' => $message,
            'data' => $data,
        );

        header("Content-Type:text/xml");
        $xml = "<?xml version='1.0' encoding='UTF-8'?>\n";
        $xml .= "<root>\n";

        $xml .= self::xmlToEncode($result);

        $xml .= "</root>";
        echo $xml;
    }

    // 将xml数据解析
    public static function xmlToEncode($data) {
        $xml = $attr = "";
        foreach($data as $key => $value) {
            if(is_numeric($key)) {
                $attr = " id='{$key}'";
                $key = "item";
            }
            $xml .= "<{$key}{$attr}>";
            $xml .= is_array($value) ? self::xmlToEncode($value) : $value;
            $xml .= "</{$key}>\n";
        }
        return $xml;
    }
}