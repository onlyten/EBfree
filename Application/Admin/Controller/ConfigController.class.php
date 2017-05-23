<?php
/**
 * 系统配置管理
 * @authors nkuwilson (nkuwilson@qq.com)
 * @date    2016-03-20 23:37:00
 * @version v1.0
 */
namespace Admin\Controller;
use Think\Controller;
class ConfigController extends CommonController {
    
    public function index()
    {
    	$this->display();
    }

    public function add()
    {
    	$this->display();
    }

    public function add_update()
    {
    	if (I('post.name')) {
    		$data['name'] = I('post.name');
    	}
        if (I('post.value')) {
            $data['value'] = I('post.value');
        }
    	if (I('post.memo')) {
    		$data['memo'] = I('post.memo');
    	}
    	$m = M('config');
    	if ($m->data($data)->add()) {
            $this->redirect('config/index', array('msg_text' => '配置项新增成功','msg_class_name' => 'success'), 0);
    	}else{
            $this->redirect('config/index', array('msg_text' => '配置项新增失败','msg_class_name' => 'danger'), 0);
    	}    	
    }
}