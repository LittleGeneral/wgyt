<?php
 /*
    *   收藏控制器
    */
namespace Admin\Controller;
use Think\Controller;
class CollectionController extends CommonController {
    //收藏列表
    public function index(){
        $collection = M('collection');
        $collections = $collection->alias('c')
                ->join('LEFT JOIN users u ON u.id = c.uid')
                ->join('LEFT JOIN goods g ON g.id = c.gid')
        		->field('c.id,c.createtime,c.status,u.cname,g.name,g.price,g.group_price,g.group_price,g.count,g.img,g.title,g.lasttime')
        		->order('createtime asc')
        		->select();
        // dump($collections);die();
        $this->assign('collections',$collections);
        $this->display();
	}


     // 上架
     public function shelve()
     {
        $id = I('get.id');
        $collection = M('collection');
        $data['status']=1;
            $obj = $collection->create($data);
            if(!$obj){
                $this->error($collection->getError());
            }else{
                $result = $collection->where("id = '$id'")->data($data)->save();
                if ($result) {
                    $this->redirect('Collection/index');
                }else{
                    $this->error('启用失败!');
                }
            }
     }

     // 下架
     public function offShelve()
     {
        $id = I('get.id');
        $collection = M('collection');
        $data['status']=0;
            $obj = $collection->create($data);
            if(!$obj){
                $this->error($collection->getError());
            }else{
                $result = $collection->where("id = '$id'")->data($data)->save();
                if ($result) {
                    $this->redirect('Collection/index');
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
        $collection=M('collection');
        $data=$collection->find($id);
        $result=$collection->delete($id);
        if($result){
            $this->redirect('Collection/index');
        }else{
            $this->redirect('Collection/index');
        }
    }

   	 /**
     * ajax异步删除
     */
     public function doDel(){
        $id=I('get.id');
        $collection=M('collection');
        $data=$collection->find($id);
        $result=$collection->delete($id);
        if($result){
            $this->ajaxReturn(1);
        }else{
            $this->ajaxReturn(0);
        }
    }


}