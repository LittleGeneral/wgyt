<?php
 /*
    *   商品分类控制器
    */
namespace Admin\Controller;
use Think\Controller;
class CategoryController extends CommonController {

    // 商品分类列表
    public function index(){
        $category = M('category');
        $where['name']  = array('like',"%{$_REQUEST['keyword']}%");
        $where['status'] = 0;
        $count = $category ->where($where)->count();
        $Page = new \Think\Page($count,8);
        $categories = $category ->where($where)->order('concat(path,id)')->limit($Page->firstRow.','.$Page->listRows)->select();
        $show = $Page -> show();
        $this -> assign('categories',$categories);
        //计算path中逗号的数量
        foreach($categories as $v){
            $category = substr_count($v['path'],',');
            $mark[$v['id']] = str_repeat('&nbsp;',$category * 3).str_repeat('---',$category).' ';
        }
        $this->assign('mark',$mark);
        $this->assign('page',$show);
        $this->display();
    }

    /**
     * 添加商品分类
     */
    public function add()
    {   $pid = isset($_GET['id']) ? $_GET["id"] : '0';
        $path = isset($_GET['path']) ? $_GET["path"] : '0,';
        $name = isset($_GET['name']) ? $_GET["name"] : '根目录';
        $this -> assign('id',$pid);
        $this -> assign('path',$path);
        $this -> assign('name',$name);
        $this->display();
    }


    //添加分类商品
    public function create(){
        $m = M('category');
        $m -> pid = $_POST['pid'];
        $m -> name = $_POST['name'];
        $m -> path = $_POST['path'];
        $count = $m -> add();
        if($count>0){
            $this -> success('添加成功!',index,2);
        }else{
            $this -> error('添加失败!',index,3);
        }
    }

    /**
     * 插入商品分类数据
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
        //实例化category表
        $category=M('category');
        if($category->create()) {
            // $result = $category->data($data)->add();
            $result = $category->add();
            if ($result) {
                $this->redirect('Carousel/index');
            } else {
                $this->redirect('Carousel/add');
            }
        }
     }

     // 启用商品分类
     public function enable()
     {
        $id = I('get.id');
        $category = M('category');
        $data['is_enable']=1;
            $obj = $category->create($data);
            if(!$obj){
                $this->error($category->getError());
            }else{
                $result = $category->where("id = '$id'")->data($data)->save();
                if ($result) {
                    $this->redirect('Carousel/index');
                }else{
                    $this->error('启用失败!');
                }
            }
     }

     // 停用商品分类
     public function disable()
     {
        $id = I('get.id');
        $category = M('category');
        $data['is_enable']=0;
            $obj = $category->create($data);
            if(!$obj){
                $this->error($category->getError());
            }else{
                $result = $category->where("id = '$id'")->data($data)->save();
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
        $category=M('category');
        //查询要删除的信息
        $data=$category->find($id);
        if(!empty($data['img'])){
            $img=$data['img'];
            // $thumb=$data['thumb'];
        }
        //删除该条数据
        $result=$category->delete($id);
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
        $category=M('category');
        //查询要删除的信息
        $data=$category->find($id);
        if(!empty($data['img'])){
            $img=$data['img'];
            // $thumb=$data['thumb'];
        }
        //删除该条数据
        $result=$category->delete($id);
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

     //修改商品分类信息
    public function modify($id)
    {
         $category=M('category');
         $categorys=$category->where("id = '$id'")->find();
         $this->assign('categorys',$categorys);
         $this->display();

    }

   //更新商品分类信息
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
            //实例化category表
            $category=M('category');
            $obj = $category->create();
            if(!$obj){
                $this->error($category->getError());
            }else{
                $result = $category->where("id = '$id'")->data($data)->save();
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