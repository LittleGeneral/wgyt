<?php
/**
 * 商品接口API
 */
namespace V1\Controller;

use Common\Controller\ApiController;

class GoodsController extends ApiController{
    public function index(){
        $this->myApiPrint('无效接口');
    }

    // 商品列表接口
    public function lists()
    {
        $pageNum = I('get.pageNum',1);
        $list = M('goods')->order('updatetime desc')->page($pageNum,10)->select();
        if ($list){
            $this->myApiPrint('success',200,$list);
        }else if($list == null){
            $this->myApiPrint('暂无数据',202);
        }else{
            $this->myApiPrint('系统繁忙，请稍后再试',300);
        }
    }

    /**
     * 添加商品接口
     * @DateTime 2016-09-21T10:37:30+0800
     */
    public function add()
    {
        if (IS_POST) {
            $data['name']=$name=I('post.name','');
            $data['price']=$price=I('post.price','');
            $data['group_price']=$group_price=I('post.group_price','');
            $data['count']=$count=I('post.count','');

            if(!empty($_FILES['img']['name'])){
                // 设置图片上传配置信息
                $config = array(
                        'maxSize' => 3145728,   // 上传文件大小限制
                        'exts'    => array('png','gif','jpg','jpeg'),   //上传文件的后缀名
                        'rootPath'=> './Public/Admin/Uploads/', // 设置附件上传目录
                    );
                //1.实例化上传类
                $upload = new \Think\Upload($config);
                //2.上传操作
                $img = $upload->upload();  // 多文件上传
                //3.判断
                if (!$img) {
                    $this->error($upload->getError());
                } else {
                    //4.处理上传图片
                    $image = new \Think\Image();
                    if(!empty($img['img'])){
                        $filename=$img['img']['savepath'].$img['img']['savename'];
                        //打开图片
                        $image->open('./Public/Admin/Uploads/'.$filename);

                        //生成缩略图片
                        $image->thumb(100,100)->save('./Public/Admin/Uploads/'.$img['img']['savepath'].'t_'.$img['img']['savename']);
                        $data['img']=$_POST['img']=$filename;  //保存为原图
                        // $_POST['img']=$img['img']['savepath'].'t_'.$img['img']['savename'];  //保存为缩略图
                    }
                }
            }
            //实例化goods表
            $goods=M('goods');
            if (!$goods->create($data)) {
               $this->myApiPrint($goods->getError());
            }
            $res = $goods->data($data)->add();
            if ($res === false){
               $this->myApiPrint('添加失败，请稍后再试', 300);
            }else{
                $this->myApiPrint('success',200,$res);
            }
        }else{
            $this->myApiPrint('请使用post提交', 300);
        }

     }


    /**
     * 删除商品接口
     * @DateTime 2016-09-18T14:50:31+0800
     */
    public function del(){
        $id=I('post.id');
        if (!$id || $id==null) {
            $this->myApiPrint('未获取商品id参数',300);
        }
        $model=M('goods');
        //查询要删除的信息
        $data=$model->find($id);
        if (!$data) {
            $this->myApiPrint('没有该商品',300);
        }
        if(!empty($data['img'])){
            $img=$data['img'];
        }
        //删除该条数据
        if ($data) {
            $res=$model->delete($id);
            $unsimg="./Public/Admin/Uploads/".$img;
            unlink($unsimg);

            if ($res){
                $this->myApiPrint('删除成功',200);
            }else{
                $this->myApiPrint('删除失败，请稍后再试',300);
            }
        }
    }

   //更新商品信息接口
    public function update()
    {
         if (IS_POST) {
            if (!I('post.id')) {
                $this->myApiPrint('未获取商品id参数或该id不存在',300);
            }
             $data['id'] = $id = I('post.id');
             $data['name'] = I('post.name');
             $data['price'] = I('post.price');
             $data['group_price'] = I('post.group_price');
             $data['count'] = I('post.count');
            if(!empty($_FILES['img']['name'])){
                // 设置图片上传配置信息
                $config = array(
                        'maxSize' => 3145728,   // 上传文件大小限制
                        'exts'    => array('png','gif','jpg','jpeg'),   //上传文件的后缀名
                        'rootPath'=> './Public/Admin/Uploads/', // 设置附件上传目录
                    );
                //1.实例化上传类
                $upload = new \Think\Upload($config);
                //2.上传操作
                $img = $upload->upload();  // 多文件上传
                //3.判断
                if (!$img) {
                    $this->error($upload->getError());
                } else {
                    //4.处理上传图片
                    $image = new \Think\Image();
                    if(!empty($img['img'])){
                        $filename=$img['img']['savepath'].$img['img']['savename'];
                        //打开图片
                        $image->open('./Public/Admin/Uploads/'.$filename);

                        //生成缩略图片
                        $image->thumb(100,100)->save('./Public/Admin/Uploads/'.$img['img']['savepath'].'t_'.$img['img']['savename']);
                       $data['img'] = $_POST['img']=$filename;  //保存为原图
                        // $_POST['img']=$img['img']['savepath'].'t_'.$img['img']['savename'];  //保存为缩略图
                    }
                }
            }

            //实例化goods表
            $goods=M('goods');
            // dump($goods);die();
            $obj = $goods->create($data);
            if(!$obj){
                $this->myApiPrint($goods->getError());
            }else{
                $result = $goods->where("id = '$id'")->data($data)->save();
                if ($result === false){
                   $this->myApiPrint('修改失败，请重新操作', 300);
                }else{
                    $this->myApiPrint('success',200,$result);
                }
            }
        }else{
            $this->myApiPrint('请使用post提交', 300);
        }

    }







}