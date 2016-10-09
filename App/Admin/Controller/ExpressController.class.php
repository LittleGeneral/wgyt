<?php
 /*
    *   承运控制器
    */
namespace Admin\Controller;
use Think\Controller;
class ExpressController extends CommonController {

    // 承运列表
    public function index()
    {
        $express = M('express');
        $expresses = $express->alias('e')
                    ->join('LEFT JOIN carrier c ON e.id = c.carrier_id')
                    // ->field('c.id,c.createtime,c.status,u.cname,g.name,g.price,g.group_price,g.group_price,g.count,g.img,g.title,g.lasttime')
                    ->select();
        $this->assign('expresses',$expresses);
        $this->display();
    }

    /**
     * 添加承运
     */
    public function add()
    {
        $this->display("add");
    }

    /**
     * 插入承运数据
     */
    public function insert()
    {
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
                    $file = $image->open('./Public/Admin/Uploads/'.$filename);
                    //生成缩略图片
                    $image->thumb(100,100)->save('./Public/Admin/Uploads/'.$img['img']['savepath'].'t_'.$img['img']['savename']);
                    $_POST['img']=$filename;  //保存为原图
                }
            }
        }
        //实例化express表
        $express=M('express');
        if($express->create()) {
            $result = $express->add();
            if ($result) {
                $this->redirect('Carousel/index');
            } else {
                $this->redirect('Carousel/add');
            }
        }
     }

     // 启用承运
     public function enable()
     {
        $id = I('get.id');
        $express = M('express');
        $data['is_enable']=1;
            $obj = $express->create($data);
            if(!$obj){
                $this->error($express->getError());
            }else{
                $result = $express->where("id = '$id'")->data($data)->save();
                if ($result) {
                    $this->redirect('Carousel/index');
                }else{
                    $this->error('启用失败!');
                }
            }
     }

     // 停用承运
     public function disable()
     {
        $id = I('get.id');
        $express = M('express');
        $data['is_enable']=0;
            $obj = $express->create($data);
            if(!$obj){
                $this->error($express->getError());
            }else{
                $result = $express->where("id = '$id'")->data($data)->save();
                if ($result) {
                    $this->redirect('Carousel/index');
                }else{
                    $this->error('启用失败!');
                }
            }
     }
    /**
     * 删除操作
     */
    public function del(){
        $id=I('get.id');
        $express=M('express');
        //查询要删除的信息
        $data=$express->find($id);
        if(!empty($data['img'])){
            $img=$data['img'];
            // $thumb=$data['thumb'];
        }
        //删除该条数据
        $result=$express->delete($id);
        if($result){
            $unsimg="./Public/Admin/Uploads/".$img;
            // $unsthumb="./Public/Admin/Uploads/".$thumb;
            unlink($unsimg);
            // unlink($unsthumb);
            $this->redirect('Carousel/index');
        }else{
            $this->redirect('Carousel/index');
        }
    }

     /**
     * ajax异步删除
     */
     public function doDel(){
        $id=I('get.id');
        $express=M('express');
        //查询要删除的信息
        $data=$express->find($id);
        if(!empty($data['img'])){
            $img=$data['img'];
            // $thumb=$data['thumb'];
        }
        //删除该条数据
        $result=$express->delete($id);
        if($result){
            $unsimg="./Public/Admin/Uploads/".$img;
            // $unsthumb="./Public/Admin/Uploads/".$thumb;
            unlink($unsimg);
            unlink($unsthumb);
            $this->ajaxReturn(1);
        }else{
            $this->ajaxReturn(0);
        }
    }

     //修改承运信息
    public function modify($id)
    {
         $express=M('express');
         $expresses=$express->where("id = '$id'")->find();
         $this->assign('expresses',$expresses);
         $this->display();

    }

   //更新承运信息
    public function update($id)
    {
        if (IS_POST) {
             $data['title'] = I('post.title');
             $data['url'] = I('post.url');
             $data['is_enable'] = I('post.is_enable');
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
            //实例化express表
            $express=M('express');
            $obj = $express->create();
            if(!$obj){
                $this->error($express->getError());
            }else{
                $result = $express->where("id = '$id'")->data($data)->save();
                if ($result) {
                    $this->success('修改成功!',U('Carousel/index'));
                }else{
                    $this->error('修改失败!');
                }
            }
        }else{
            $this->error('请使用post方式传输');
        }
    }


}