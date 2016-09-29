<?php
/**
 * 用户接口API
 */
namespace V1\Controller;

use Common\Controller\ApiController;

class UserController extends ApiController{
    public function index(){
        $this->myApiPrint('无效接口');
    }

    //用户列表接口
    public function lists(){

        $pageNum = I('get.pageNum',1);
        $pageCount = I('get.pageCount',5);
        $user = M('users');
        $list = $user->alias('u')
        		->join('LEFT JOIN property p ON p.propertyid = u.propertyid')
        		->field('u.id,u.tel,u.cname,u.gender,u.img,u.usertype,u.password,u.address,u.info,u.status,p.name')
                ->page($pageNum,$pageCount)
                ->order('createtime desc')
        		->select();
        if ($list){
            $this->myApiPrint('success',200,$list);
        }else if($list == null){
            $this->myApiPrint('暂无数据',202);
        }else{
            $this->myApiPrint('系统繁忙，请稍后再试',300);
        }
	}

	/**
	 * 添加用户
	 */
	public function add()
    {
        $this->display("add");
    }

   	/**
   	 * 插入用户数据
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
        //实例化users表
        $user=M('users');
        if($user->create()) {
            // $result = $user->data($data)->add();
            $result = $user->add();
            if ($result) {
                $this->redirect('User/index');
            } else {
                $this->redirect('User/add');
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
        $user=M('users');
        //查询要删除的信息
        $data=$user->find($id);
        if(!empty($data['img'])){
            $img=$data['img'];
            // $thumb=$data['thumb'];
        }
        //删除该条数据
        $result=$user->delete($id);
        if($result){
            $unsimg="./Public/Admin/Uploads/".$img;
            // $unsthumb="./Public/Admin/Uploads/".$thumb;
            unlink($unsimg);
            // unlink($unsthumb);
            $this->redirect('User/index');
        }else{
            $this->redirect('User/index');
        }
    }

   	 /**
     * ajax异步删除
     */
     public function doDel(){
        $id=I('get.id');
        $user=M('users');
        //查询要删除的信息
        $data=$user->find($id);
        if(!empty($data['img'])){
            $img=$data['img'];
            // $thumb=$data['thumb'];
        }
        //删除该条数据
        $result=$user->delete($id);
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

     //修改用户信息
    public function modify($id)
    {
         $user=M('users');
         $users=$user->where("id = '$id'")->find();
         $this->assign('users',$users);
         $this->display();

    }

   //更新用户信息
    public function update($id)
    {
        if (IS_POST) {
             $data['cname'] = I('post.cname');
             $data['usertype'] = I('post.usertype');
             $data['password'] = I('post.password');
             $data['gender'] = I('post.gender');
             $data['tel'] = I('post.tel');
             $data['address'] = I('post.address');
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
            //实例化users表
            $users=M('users');
            $obj = $users->create();
            if(!$obj){
                $this->error($users->getError());
            }else{
                $result = $users->where("id = '$id'")->data($data)->save();
                if ($result) {
                    $this->success('修改成功!',U('User/index'));
                }else{
                    $this->error('修改失败!');
                }
            }
        }else{
            $this->error('请使用post方式传输');
        }

    }

}