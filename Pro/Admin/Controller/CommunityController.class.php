<?php
namespace Admin\Controller;
use Think\Controller;
   /*
	*	单个小区控制器
	*/
class CommunityController extends Controller{

    //小区管理员列表
    public function index($id){

        $estate=M('property');
        $propertyid=$estate->where("id = '$id'")->getField('propertyid');

        $community = M('users');
        $communitys=$community->where("propertyid = '$propertyid'")->select();
        // dump($communitys);die();
        $this->assign('communitys',$communitys);

        $this->display();

		}

	/**
	 * 添加管理员
	 * @DateTime 2016-08-18T15:44:52+0800
	 */
	public function add()
    {
        $this->display("add");
    }

    /**
     * 插入管理员信息
     * @DateTime 2016-08-19T10:16:58+0800
     * @return   [type]                   [description]
     */
    public function insert()
    {
         $propertyid = $_POST['propertyid'];
         $estate=M('property');
         $id = $estate->where("propertyid = '$propertyid'")->getField('id');
         $user = M('users');
        	if($user -> create()){
				if($user -> add()){
                        $this->redirect('index', array('id' => $id));
				}else{
					$this -> error('添加错误');
				}
			}else{
				$this -> error('错误');
			}
    }


   	/**
   	 * 修改
   	 * @DateTime 2016-08-19T10:30:39+0800
   	 * @param    [type]                   $id [description]
   	 * @return   [type]                       [description]
   	 */
    public function modify($id)
    {
		 $user=M('users');
		 // $id=I('get.id');
	     // $id=(int)$_GET['id'];
	     $user=$user->where("id = '$id'")->find();
	     $this->assign('user',$user);
	     $this->display();
    }

    /**
     * 更新
     * @DateTime 2016-08-19T15:46:11+0800
     * @return   [type]                   [description]
     */
    public function update($id)
    {
        $propertyid = $data['propertyid'] = I('post.propertyid');
        $data['tel'] = I('post.tel');
        $data['password'] = I('post.password');
        $estate=M('property');
        $id2 = $estate->where("propertyid = '$propertyid'")->getField('id');
		$user=M('users');
        $obj = $user->create();
        if(!$obj){
        	$this->error($user->getError());
        }else{
        	$result = $user->where("id = '$id'")->data($data)->save();
        	if ($result) {
        		$this->redirect('index', array('id' => $id2));
        	}else{
        		$this->error('修改失败!');
        	}
        }
    }

    // 删除小区管理员
    public function del($id){
        $user = M('users');
        $result = $user->where("id='{$id}'")->delete();
        if($result){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }

}
