<?php
namespace Admin\Controller;
use Think\Controller;
/**
 * 团购商品详情控制器类
 */
class GoodsDetailController extends CommonController {
	/**
	 * 团购商品详情首页
	 */
	public function index($id){
        $detail = M('goods_detail');
        $goods_detail = $detail->alias('d')
                        ->join('LEFT JOIN goods g ON g.id = d.id')
                        ->where("d.id = '$id'")
                        ->find();
        $this->assign('goods_detail',$goods_detail);
        $this->display();
	}

     //修改商品详情信息
    public function modify($id)
    {
         $good=M('goods_detail');
         // $id=I('get.id');
         // $id=(int)$_GET['id'];
         $goods_detail=$good->where("id = '$id'")->find();
         $this->assign('goods_detail',$goods_detail);
         $this->display();

    }

   //更新商品详情信息
    public function update($id)
    {
        if (IS_POST) {
             $data['name'] = I('post.name');
             $data['price'] = I('post.price');
             $data['group_price'] = I('post.group_price');
             $data['count'] = I('post.count');

            // dump($id);die();
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
            //实例化goods_detail表
            $goods_detail=M('goods_detail');
            $obj = $goods_detail->create();
            if(!$obj){
                $this->error($goods_detail->getError());
            }else{
                $result = $goods_detail->where("id = '$id'")->data($data)->save();
                if ($result) {
                    $this->success('修改成功!',U('Goods/index'));
                }else{
                    $this->error('修改失败!');
                }
            }
        }else{
            $this->error('请使用post方式传输');
        }

    }

}





