<?php
/*
*关于我们管理
*@author: 王超
*@date: 20160415
*/
namespace Admin\Controller;
use Think\Controller;
class AboutUsController extends CommonController {
	
	public function AboutUsManager(){
        $about_us_map=M('about_us');

        $maxId=$about_us_map->max('id');
        $info=$about_us_map->find($maxId);
        
        $this->assign('info',$info);
        $this->display();

    }
	
    public function info_update(){
        if(I('post.title_img')){
            $info['banner_img']=I('post.title_img');
        }
        if(I('post.intro')){
            $info['intro']=I('post.intro','',false);
        }
        if(I('post.advan_img')){
            $info['advan_img']=I('post.advan_img');
        }

        $about_us_map=M('about_us');
        $about_us_map->data($info)->add();
        $this->redirect('AboutUs/AboutUsManager');
    }

}
?>