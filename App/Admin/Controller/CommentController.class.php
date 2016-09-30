<?php
 /*
    *   评论控制器
    */
namespace Admin\Controller;
use Think\Controller;
class CommentController extends CommonController {
    //评论列表
    public function index(){
        $comment = M('comments');
        $comments = $comment->alias('c')
                ->join('LEFT JOIN users u ON u.id = c.uid')
                ->join('LEFT JOIN goods g ON g.id = c.gid')
        		->field('c.id,c.comments,c.star,c.time,u.cname,g.name')
        		// ->order('createtime desc')
        		->select();
        // dump($comments);die();
        $this->assign('comments',$comments);
        $this->display();
	}

    /**
     * 删除操作
     */
    public function del(){
        $id=I('get.id');
        $comment=M('comments');
        $data=$comment->find($id);
        $result=$comment->delete($id);
        if($result){
            $this->redirect('Comment/index');
        }else{
            $this->redirect('Comment/index');
        }
    }

   	 /**
     * ajax异步删除
     */
     public function doDel(){
        $id=I('get.id');
        $comment=M('comments');
        $data=$comment->find($id);
        $result=$comment->delete($id);
        if($result){
            $this->ajaxReturn(1);
        }else{
            $this->ajaxReturn(0);
        }
    }


}