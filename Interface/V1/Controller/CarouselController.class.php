<?php
/**
 * 轮播图接口API
 */
namespace V1\Controller;

use Common\Controller\ApiController;

class CarouselController extends ApiController{
    public function index(){
        $this->myApiPrint('无效接口');
    }


    //轮播图列表接口
    public function lists(){

        $pageNum = I('get.pageNum',1);
        $pageCount = I('get.pageCount',5);
        $carousel = M('carousel');
        $list = $carousel
                ->page($pageNum,$pageCount)
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