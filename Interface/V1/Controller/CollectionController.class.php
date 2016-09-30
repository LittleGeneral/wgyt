<?php
/**
 * 收藏接口API
 */
namespace V1\Controller;

use Common\Controller\ApiController;

class CollectionController extends ApiController{
    public function index(){
        $this->myApiPrint('无效接口');
    }


	 //收藏列表接口
    public function lists(){

        $pageNum = I('get.pageNum',1);
        $pageCount = I('get.pageCount',5);
        $collection = M('collection');
        $list = $collection->alias('c')
                ->join('LEFT JOIN users u ON u.id = c.uid')
                ->join('LEFT JOIN goods g ON g.id = c.gid')
                ->field('c.id,c.createtime,c.status,u.cname,g.name,g.price,g.group_price,g.group_price,g.count,g.img,g.title,g.lasttime')
                ->order('createtime asc')
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