<?php
/**
 * 地址接口API
 */
namespace V1\Controller;

use Common\Controller\ApiController;

class AddressController extends ApiController{
    public function index(){
        $this->myApiPrint('无效接口');
    }

    //买家物流详情接口
    public function lists(){

        $pageNum = I('get.pageNum',1);
        $pageCount = I('get.pageCount',5);
        $address = M('address');
        $list = $address->alias('a')
                    ->join('LEFT JOIN users u ON u.id = a.user_id')
                    ->field('a.addr_id,a.code,a.is_default,a.is_district,u.cname,u.tel,u.img,u.address')
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