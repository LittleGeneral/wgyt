<?php
namespace Admin\Controller;
use Think\Controller;
/**
 * 团购商品控制器类
 */
class GoodsController extends CommonController {
	/**
	 * 团购商品首页
	 * @DateTime 2016-09-20T17:07:13+0800
	 * @return   [type]                   [description]
	 */
	public function index(){
        $model = M('goods');
        $goods = $model->select();
        // dump($goods);die();
        $this->assign('goods',$goods);
        $this->display();
	}

	/**
	 * 添加商品
	 * @DateTime 2016-09-21T10:30:29+0800
	 */
	public function add()
    {
        $this->display("add");
    }

   	/**
   	 * 插入商品数据
   	 * @DateTime 2016-09-21T10:37:30+0800
   	 * @return   [type]                   [description]
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
                    $image->open('./Public/Admin/Uploads/'.$filename);

                    //生成缩略图片
                    $image->thumb(100,100)->save('./Public/Admin/Uploads/'.$img['img']['savepath'].'t_'.$img['img']['savename']);
                    $_POST['img']=$filename;  //保存为原图
                    // $_POST['img']=$img['img']['savepath'].'t_'.$img['img']['savename'];  //保存为缩略图
                }
            }
        }

        //实例化goods表
        $model=M('goods');
        if($model->create()) {
            $res = $model->add();
            if ($res) {
                $this->redirect('Goods/index');
            } else {
                $this->redirect('Goods/add');
            }
        }

   	 }


    /**
     * 删除操作
     * @DateTime 2016-09-18T14:50:31+0800
     * @return   [type]                   [description]
     */
    public function del(){
        $id=I('get.id');
        $model=M('goods');
        //查询要删除的信息
        $data=$model->find($id);
        if(!empty($data['img'])){
            $img=$data['img'];
            // $thumb=$data['thumb'];
        }
        //删除该条数据
        $res=$model->delete($id);
        if($res){
            $unsimg="./Public/Admin/Uploads/".$img;
            // $unsthumb="./Public/Admin/Uploads/".$thumb;
            unlink($unsimg);
            // unlink($unsthumb);
            $this->redirect('Goods/index');
        }else{
            $this->redirect('Goods/index');
        }
    }

   	 /**
     * ajax异步删除
     */
     public function doDel(){
        $id=I('get.id');
        $model=M('goods');
        //查询要删除的信息
        $data=$model->find($id);
        if(!empty($data['img'])){
            $img=$data['img'];
            // $thumb=$data['thumb'];
        }
        //删除该条数据
        $res=$model->delete($id);
        if($res){
            $unsimg="./Public/Admin/Uploads/".$img;
            // $unsthumb="./Public/Admin/Uploads/".$thumb;
            unlink($unsimg);
            unlink($unsthumb);
            $this->ajaxReturn(1);
        }else{
            $this->ajaxReturn(0);
        }
    }

     //修改商品信息
    public function modify($id)
    {
         $good=M('goods');
         // $id=I('get.id');
         // $id=(int)$_GET['id'];
         $goods=$good->where("id = '$id'")->find();
         $this->assign('goods',$goods);
         $this->display();

    }



   //更新商品信息
    public function update($id)
    {
        if (IS_POST) {
             $data['name'] = I('post.name');
             $data['price'] = I('post.price');
             $data['group_price'] = I('post.group_price');
             $data['count'] = I('post.count');

            // dump($id);die();
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
            $obj = $goods->create();
            if(!$obj){
                $this->error($goods->getError());
            }else{
                $result = $goods->where("id = '$id'")->data($data)->save();
                if ($result) {
                    $this->success('修改成功!',U('Goods/index'));
                }else{
                    $this->error('修改失败!');
                }
            }
        }else{
            $this->error('请使用post方式传输');
        }

    }

}





