<?php
/*
*关于我们管理
*@author: 王超
*@date: 20160415
*/
namespace Admin\Controller;
use Think\Controller;
class HomepageController extends CommonController {
	
	public function home(){
        $about_us_map=M('homepage');

        $maxId=$about_us_map->max('id');
        $info=$about_us_map->find($maxId);
        
        $this->assign('info',$info);
        $this->display();

    }
	
    public function info_update(){
        if(I('post.imga')){
            $info['imga']=I('post.imga');
        }
        if(I('post.imgb')){
            $info['imgb']=I('post.imgb');
        }
        if(I('post.imgc')){
            $info['imgc']=I('post.imgc');
        }
        if(I('post.imgd')){
            $info['imgd']=I('post.imgd');
        }
        if(I('post.linka')){
            $info['linka']=I('post.linka');
        }
        if(I('post.linkb')){
            $info['linkb']=I('post.linkb');
        }
        if(I('post.linkc')){
            $info['linkc']=I('post.linkc');
        }
        if(I('post.linkd')){
            $info['linkd']=I('post.linkd');
        }

        $homepage=M('homepage');
        $homepage->data($info)->add();
        $this->redirect('Homepage/home');
    }

}
?>