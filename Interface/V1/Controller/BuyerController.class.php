<?php
/**
 * 买家接口API
 */
namespace V1\Controller;

use Common\Controller\ApiController;

class BuyerController extends ApiController{

     public function index(){
        $this->myApiPrint('无效接口');
    }


    //买家物流详情接口
    public function express_details(){

        $pageNum = I('get.pageNum',1);
        $pageCount = I('get.pageCount',5);
        $express = M('express');
        $list = $express->alias('e')
                    ->join('LEFT JOIN carrier c ON e.id = c.carrier_id')
                    ->field('e.id,e.carry_company,e.carry_num,e.carry_time,e.goods_position,e.official_phone_num,c.carrier_name,c.carrier_phone_num,c.carrier_img,c.carrier_nickname')
                    ->select();
        $comment = M('comments');
        if ($list){
            $this->myApiPrint('success',200,$list);
        }else if($list == null){
            $this->myApiPrint('暂无数据',202);
        }else{
            $this->myApiPrint('系统繁忙，请稍后再试',300);
        }
	}


























}