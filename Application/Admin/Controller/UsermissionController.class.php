<?php
/**
 * 课程管理
 * @authors nkuwilson (nkuwilson@qq.com)
 * @date    2016-02-01 10:46:25
 * @version v1.0
 */
namespace Admin\Controller;
use Think\Controller;
class UsermissionController extends CommonController {

    /**
     * 课程管理列表
     * @return [type] [description]
     */
    public function index()
    {
    	/*搜索条件start $map用于存储查询的条件，$user_map用于存储用户原始输入的查询条件*/
    	if (I('post.title1')) {
    		$map['kouyu_name'] = array('like','%'.I('post.title1').'%');
    		$user_map['kouyu_name'] = I('post.title1');
    	}
    	if (I('post.title')) {
    		$map['wx_nickname'] = array('like','%'.I('post.title').'%');
    		$user_map['wx_nickname'] = I('post.title');
    	}
    	
    	/*说明点击提交发送过来的请求*/
    	if (I('post.title1') || I('post.title')) {
    		/*保存条件搜索对应的条件*/
    		cookie('course_map',ch_array_str($map)); 
    		cookie('course_user_map',ch_array_str($user_map));   		   		
    	}elseif(!I('get.page_num')){
    		/*非提交查询条件，非跳转页面而来的请求，即点击课程管理的请求，将cookie暂存的搜搜索条件置空*/
    		cookie('course_map',null); 
    		cookie('course_user_map',null);  	
    	}
		/*搜索条件end*/
    	$n = D('UserMission');
    	/*分页start*/
    	if (I('get.page_num')) {
	    	$page_num = I('get.page_num');
	    	/*条件搜索，点击下一页时，条件不变*/
	    	if (cookie('course_map')) {
		    	cookie('course_map',str_replace('\\','',cookie('course_map'))); 
		    	cookie('course_user_map',str_replace('\\','',cookie('course_user_map'))); 
	    		$map = ch_str_array(cookie('course_map'),true);
	    		$user_map = ch_str_array(cookie('course_user_map'),true);
	    	}	    	
    	}else{
    		$page_num = 1;
    	}
    	trace($page_num,'page_num');
    	$this->assign('page_num',$page_num);
    	$course_count = $n->where($map)->count();
    	$each_page = C('EACH_PAGE');
    	trace($each_page,'each_page');
    	$page_sum = ceil($course_count/$each_page);
    	trace($page_sum,'page_sum');
    	$this->assign('page_sum',$page_sum);
		/*分页end*/
		/*读取lib_data start*/
		$n = D('UserMission');
        $usermission = $n->order('id desc')->where($map)->page($page_num,$each_page)->select();
        /*根据任务的ID 查找到任务名字  并赋值给任务ID  但不存入数据库 start*/
        for($i=0;$i<count($usermission);$i++){
            if($usermission[$i]['type'] == '0'){
                $m=M('mission');
                $mma['id'] = $usermission[$i]['mission_id'];
                $mission_name = $m->where($mma)->find();
                $usermission[$i]['mission_id'] = $mission_name['title'];
            }
        }
        /*根据任务的ID 查找到任务名字  并赋值给任务ID  但不存入数据库 end*/
    	$this->assign('usermission',$usermission);
    	$this->display();
    }


    /**
     * 修改课程
     * @return [type] [description]
     */
    public function edit()
    {
        if (I('get.mission_id')) {
            $map['id'] = I('get.mission_id');
        }else{
            $this->error('mission_id参数错误', U('usermission/index'));
        }
        $this->assign('mission_id',I('get.mission_id'));
        $m = M('answer');
        $mission = $m->where($map)->find();
        //dump($mission);
        $this->assign('mission',$mission);
        $this->display();
    }

    public function edit_update()
    {
        if (I('post.mission_id')) {
            $map['id'] = I('post.mission_id');
        }else{
            $this->error('mission_id参数错误', U('usermission/index'));
        }
        $data['success'] = I('post.success');
        $data['kouyu_time'] = substr(I('post.kouyu_time'),0,16);
        $data['time_finish'] = time();
        $m = M('answer');
        if ($m->where($map)->save($data)) {
            $this->redirect('usermission/index', array('msg_text' => '口语状态修改成功','msg_class_name' => 'success'), 0);
        }else{
            $this->redirect('usermission/index', array('msg_text' => '口语状态修改失败','msg_class_name' => 'danger'), 0);
        } 
    }
}