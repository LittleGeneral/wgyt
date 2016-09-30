<?php
/**
 * 评论接口API
 */
namespace V1\Controller;

use Common\Controller\ApiController;

class CommentController extends ApiController{
    public function index(){
        $this->myApiPrint('无效接口');
    }



    //评论列表接口
    public function lists(){

        $pageNum = I('get.pageNum',1);
        $pageCount = I('get.pageCount',5);
        $comment = M('comments');
        $list = $comment->alias('c')
                ->join('LEFT JOIN users u ON u.id = c.uid')
                ->join('LEFT JOIN goods g ON g.id = c.gid')
        		->field('c.id,c.comments,c.star,c.time,u.cname,g.name')
        		->order('time desc')
        		->select();
        if ($list){
            $this->myApiPrint('success',200,$list);
        }else if($list == null){
            $this->myApiPrint('暂无数据',202);
        }else{
            $this->myApiPrint('系统繁忙，请稍后再试',300);
        }
	}


}