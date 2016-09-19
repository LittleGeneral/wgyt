<?php
namespace Admin\Controller;
use Think\Controller;
   /*
	*	公告控制器
	*/
class NoticeController extends Controller{
	//公告列表
    public function index(){
        $datas=file_get_contents('http://db-plus.cn:8080/cgi/owifi-get.php?token=jT35IfavOApV6YqslCRegLuPpYhUWb3mqk%2B7U/2Djaur&key=notice-1');
        $datas = json_decode($datas,JSON_UNESCAPED_UNICODE);
        $datas = $datas['rows'];
        $this->assign('datas',$datas);
        $this->display();
		}


	/**
	 * 添加公告
	 * @DateTime 2016-08-18T15:44:52+0800
	 */
	public function add()
    {
        $this->display("add");
    }

    /**
     * 插入公告信息
     * @DateTime 2016-08-19T10:16:58+0800
     * @return   [type]                   [description]
     */
    public function insert()
    {

        $datas['title'] = I('post.title');
        $datas['section'] = I('post.section');
        $datas['content'] = I('post.content');

        $datas = json_encode($datas);


        // dump($datas);die();
        $insert = 'http://db-plus.cn:8080/cgi/owifi-insert.php?token=jT35IfavOApV6YqslCRegLuPpYhUWb3mqk%2B7U/2Djaur&key=notice-1';

        $datas = file_put_contents($insert['row'],$datas);

        dump($datas);die();
    }


   	/**
   	 * 修改公告小区信息
   	 * @DateTime 2016-08-19T10:30:39+0800
   	 * @param    [type]                   $id [description]
   	 * @return   [type]                       [description]
   	 */
    public function modify($id)
    {
		 $estate=M('property');
		 // $id=I('get.id');
	     // $id=(int)$_GET['id'];
	     $estates=$estate->where("id = '$id'")->find();
	     $this->assign('estates',$estates);
	     $this->display();
    }

    /**
     * 更新公告小区信息
     * @DateTime 2016-08-19T15:46:11+0800
     * @return   [type]                   [description]
     */
    public function update($id)
    {
    	 // $data['id'] = I('post.id');
    	 $data['propertyid'] = I('post.propertyid');
    	 $data['name'] = I('post.name');
    	 $data['tel'] = I('post.tel');
    	 $data['address'] = I('post.address');

    	 $upload = new \Think\Upload();// 实例化上传类
         $upload->maxSize =3145728 ;// 设置附件上传大小
         $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
         $upload->rootPath = './Public/Admin/Uploads/'; // 设置附件上传目录
         $upload->autoSub = false; //关闭自动生成目录
         // 上传文件
         $info = $upload->upload();
         // dump($info);
         if(!$info){
            // 上传错误提示错误信息
            $this->error($upload->getError());
         }else{// 上传成功
            //$this->success('上传成功！');
            foreach($info as $file){
                $fname = $file['savename'];
                // echo $file['savepath'].$fname."<br/>";
                $image = new \Think\Image();
                $image->open('./Public/Admin/Uploads/'.$fname);
                // 按照原图的比例生成一个最大为100*100的缩略图并保存为
                $image->thumb(100,100)->save('./Public/Admin/Uploads/'.$fname);
                $imgs[]=$fname;
            }
         }
        $data['img1'] = $imgs[0];
 		$data['img2'] = $imgs[1];
 		$data['img3'] = $imgs[2];

        // $id = I('post.id');
		$estate=M('property');
        $obj = $estate->create();
        if(!$obj){
        	$this->error($estate->getError());
        }else{
        	$result = $estate->where("id = '$id'")->data($data)->save();
        	if ($result) {
        		$this->success('修改成功!',U('Estate/index'));
        	}else{
        		$this->error('修改失败!');
        	}
        }
    }

    /**
     * 删除公告小区
     * @DateTime 2016-08-24T13:57:25+0800
     * @param    [type]                   $id [description]
     * @return   [type]                       [description]
     */
    public function del($id){
        $estate = M('property');
        $result = $estate->where("id='{$id}'")->delete();
        if($result){
            $this->success('删除成功',U('Estate/index'));
        }else{
            $this->error('删除失败');
        }
    }

}
