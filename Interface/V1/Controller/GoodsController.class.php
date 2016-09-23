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

    public function lists()
    {
    	$list = M('goods')->select();
        if ($list){
            $this->myApiPrint('success',200,$list);
        }else if($list == null){
            $this->myApiPrint('暂无数据',202);
        }else{
            $this->myApiPrint('系统繁忙，请稍后再试',300);
        }
    }






















}