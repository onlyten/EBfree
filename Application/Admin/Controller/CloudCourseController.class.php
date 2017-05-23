<?php
/*
*云课堂管理
*@author: 王超
*@date: 20160412
*/
namespace Admin\Controller;
use Think\Controller;
class CloudCourseController extends CommonController {
	
	//蜜蜂电台管理
	public function BroadcastManager(){
		/*搜索条件start $map用于存储查询的条件，$user_map用于存储用户原始输入的查询条件*/
    	if (I('post.title')) {
    		$map['title'] = array('like','%'.I('post.title').'%');
    		$user_map['title'] = I('post.title');
    	}
    	/*说明点击提交发送过来的请求*/
    	if (I('post.title') || I('post.type') || I('post.foreign')) {
    		/*保存条件搜索对应的条件*/
    		cookie('course_map',ch_array_str($map)); 
    		cookie('course_user_map',ch_array_str($user_map));   		   		
    	}elseif(!I('get.page_num')){
    		/*非提交查询条件，非跳转页面而来的请求，即点击课程管理的请求，将cookie暂存的搜搜索条件置空*/
    		cookie('course_map',null); 
    		cookie('course_user_map',null);  	
    	}
		/*搜索条件end*/
    	$m = M('cloud_course_serial');
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
    	$course_count = $m->where($map)->count();
    	$each_page = C('EACH_PAGE');
    	trace($each_page,'each_page');
    	$page_sum = ceil($course_count/$each_page);
    	trace($page_sum,'page_sum');
    	$this->assign('page_sum',$page_sum);
		/*分页end*/
		$map['type']=0;
    	trace($map,'map');
    	$course = $m->where($map)->page($page_num,$each_page)->order('id desc')->select();
    	$this->assign('user_map',$user_map);
    	$this->assign('map',$map);
    	trace($m->_sql(),'sql');
    	$this->assign('course',$course);
    	$this->display();
	}

	//蜜蜂影院管理
	public function MovieManager(){
		/*搜索条件start $map用于存储查询的条件，$user_map用于存储用户原始输入的查询条件*/
    	if (I('post.title')) {
    		$map['title'] = array('like','%'.I('post.title').'%');
    		$user_map['title'] = I('post.title');
    	}
    	/*说明点击提交发送过来的请求*/
    	if (I('post.title') || I('post.type') || I('post.foreign')) {
    		/*保存条件搜索对应的条件*/
    		cookie('course_map',ch_array_str($map)); 
    		cookie('course_user_map',ch_array_str($user_map));   		   		
    	}elseif(!I('get.page_num')){
    		/*非提交查询条件，非跳转页面而来的请求，即点击课程管理的请求，将cookie暂存的搜搜索条件置空*/
    		cookie('course_map',null); 
    		cookie('course_user_map',null);  	
    	}
		/*搜索条件end*/
    	$m = M('cloud_course_serial');
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
    	$course_count = $m->where($map)->count();
    	$each_page = C('EACH_PAGE');
    	trace($each_page,'each_page');
    	$page_sum = ceil($course_count/$each_page);
    	trace($page_sum,'page_sum');
    	$this->assign('page_sum',$page_sum);
		/*分页end*/
		$map['type']=1;
    	trace($map,'map');
    	$course = $m->where($map)->page($page_num,$each_page)->order('id desc')->select();
    	$this->assign('user_map',$user_map);
    	$this->assign('map',$map);
    	trace($m->_sql(),'sql');
    	$this->assign('course',$course);
    	$this->display();
	}

	//云课堂管理
	public function CloudcourseManager(){
		/*搜索条件start $map用于存储查询的条件，$user_map用于存储用户原始输入的查询条件*/
    	if (I('post.title')) {
    		$map['title'] = array('like','%'.I('post.title').'%');
    		$user_map['title'] = I('post.title');
    	}
    	/*说明点击提交发送过来的请求*/
    	if (I('post.title') || I('post.type') || I('post.foreign')) {
    		/*保存条件搜索对应的条件*/
    		cookie('course_map',ch_array_str($map)); 
    		cookie('course_user_map',ch_array_str($user_map));   		   		
    	}elseif(!I('get.page_num')){
    		/*非提交查询条件，非跳转页面而来的请求，即点击课程管理的请求，将cookie暂存的搜搜索条件置空*/
    		cookie('course_map',null); 
    		cookie('course_user_map',null);  	
    	}
		/*搜索条件end*/
    	$m = M('cloud_course_serial');
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
    	$course_count = $m->where($map)->count();
    	$each_page = C('EACH_PAGE');
    	trace($each_page,'each_page');
    	$page_sum = ceil($course_count/$each_page);
    	trace($page_sum,'page_sum');
    	$this->assign('page_sum',$page_sum);
		/*分页end*/
		$map['type']=2;
    	trace($map,'map');
    	$course = $m->where($map)->page($page_num,$each_page)->order('id desc')->select();
    	$this->assign('user_map',$user_map);
    	$this->assign('map',$map);
    	trace($m->_sql(),'sql');
    	$this->assign('course',$course);
    	$this->display();
	}

	//添加音/视频系列
	public function add_media(){
		$media_type=I('get.type');
		$this->assign('type',$media_type);
		$this->display();
	}

	public function add_update(){
		$type=I('get.type');
		$serial_data['type']=$type;
		if (I('post.title')) {
    		$serial_data['title'] = I('post.title');
    	}
    	if (I('post.banner_img')) {
    		$serial_data['img'] = I('post.banner_img');
    	}
    	
    	$serial_map=M('cloud_course_serial');
    	$episode_map=M('cloud_course_episode');
    	$serial_id=$serial_map->data($serial_data)->add();
    	if($serial_id){
    		$success_flag=true;
    	}
    	else{
    		$success_flag=false;
    	}
    	if(($success_flag)&&($type!='2')){
    		$success_flag=true;
    		$link_num=(int)I('post.media_link_num');
    		for($i=1;$i<=$link_num;$i++){
    			$episode_data['serial_id']=$serial_id;
    			$episode_data['episode_id']=$i;
    			$episode_data['link']=html_entity_decode(I('post.media_link'.$i));
    			if(!$episode_map->data($episode_data)->add()){
    				$success_flag=false;
    				break;
    			}
    		}
    		//dump($link_num);
    		//die('Good Morning!');

    	}
    	if ($success_flag) {
    		if($type=='0'){
    			$this->redirect('CloudCourse/BroadcastManager', array('msg_text' => '系列记录新增成功','msg_class_name' => 'success'), 0);
    		}
            if($type=='1'){
            	$this->redirect('CloudCourse/MovieManager', array('msg_text' => '系列记录新增成功','msg_class_name' => 'success'), 0);
            }
            if($type=='2'){
            	$this->redirect('CloudCourse/CloudcourseManager', array('msg_text' => '系列记录新增成功','msg_class_name' => 'success'), 0);
            }
    	}else{
            if($type=='0'){
    			$this->redirect('CloudCourse/BroadcastManager', array('msg_text' => '系列记录新增失败','msg_class_name' => 'success'), 0);
    		}
            if($type=='1'){
            	$this->redirect('CloudCourse/MovieManager', array('msg_text' => '系列记录新增失败','msg_class_name' => 'success'), 0);
            }
            if($type=='2'){
            	$this->redirect('CloudCourse/CloudcourseManager', array('msg_text' => '系列记录新增失败','msg_class_name' => 'success'), 0);
            }
    	} 

	}

	//编辑音视频系列
	public function edit_media(){
		$serial_id=(int)I('get.serial_id');

		//dump($serial_id);
		//die('Good Afternoon!');

		$serial_map=M('cloud_course_serial');
		$episode_map=M('cloud_course_episode');

		$serial_info=$serial_map->find($serial_id);
		$episode_query['serial_id']=$serial_id;
		$episode_infos=$episode_map->where($episode_query)->select();

		$media_link_num=count($episode_infos);

		//dump($episode_infos);
		//die('Good Morning!');

		$this->assign('type',$serial_info['type']);
		$this->assign('serial_info',$serial_info);
		$this->assign('episode_infos',$episode_infos);
		$this->assign('media_link_num',$media_link_num);
		
		$this->display();
	}

	public function edit_update(){
		$serial_id=(int)I('get.serial_id');
		$type=I('get.type');
		if (I('post.title')) {
    		$serial_data['title'] = I('post.title');
    	}
    	if (I('post.banner_img')) {
    		$serial_data['img'] = I('post.banner_img');
    	}

    	$serial_map=M('cloud_course_serial');
    	$episode_map=M('cloud_course_episode');

    	$serial_data['id']=$serial_id;

    	$serial_map->save($serial_data);
    	$success_flag=true;
    	if(($success_flag)&&($type!='2')){
    		$link_num=(int)I('post.media_link_num');
    		for($i=1;$i<=$link_num;$i++){
    			$episode_id=(int)I('post.episode_id'.$i);
    			$key_id=(int)I('post.key_id'.$i);

    			//dump($episode_id);
    			//dump($key_id);

    			if($episode_id>0){
    				$episode_data['serial_id']=$serial_id;
    				$episode_data['episode_id']=I('post.episode_id'.$i);
    				$episode_data['link']=html_entity_decode(I('post.media_link'.$i));
    				if($key_id){//新增链接key_id无值
    					//dump('now in save');
    					//dump($episode_data);
    					
    					$episode_data['id']=$key_id;
						$episode_map->save($episode_data);
    				}
    				else{
    					//dump('now in add');
    					
    					$episode_data['id']=NULL;
    					$episode_map->data($episode_data)->add();
    				}
    			}
    			else{//episode_id为-1时在前端已被删除
    				//dump('now in delete');

    				$episode_map->delete($key_id);
    				continue;
    			}	
    		}
    	}
    	//die('Good Evening!');
    	if($type=='0'){
    			$this->redirect('CloudCourse/BroadcastManager', array('msg_text' => '系列记录更新成功','msg_class_name' => 'success'), 0);
    		}
        if($type=='1'){
            	$this->redirect('CloudCourse/MovieManager', array('msg_text' => '系列记录更新成功','msg_class_name' => 'success'), 0);
        }
        if($type=='2'){
            	$this->redirect('CloudCourse/CloudcourseManager', array('msg_text' => '系列记录更新成功','msg_class_name' => 'success'), 0);
        }
	}

	public function delete()
    {	
    	$serial_id=I('get.serial_id');
        if ($serial_id) {

        	$serial_map=M('cloud_course_serial');
    		$episode_map=M('cloud_course_episode');

            $s_map['id'] = $serial_id;
            $e_map['serial_id']=$serial_id;

            $episode_map->where($e_map)->delete();

            if ($serial_map->where($s_map)->limit('1')->delete()) {
                $this->redirect('CloudCourse/BroadcastManager', array('msg_text' => '课程记录删除成功','msg_class_name' => 'success'), 0);
            }else{
                $this->redirect('CloudCourse/BroadcastManager', array('msg_text' => '课程记录删除失败','msg_class_name' => 'danger'), 0);
            } 
        }else{
            $this->redirect('CloudCourse/BroadcastManager', array('msg_text' => '参数错误，请重试','msg_class_name' => 'danger'), 0);
        }
    }

    public function delete_batch()
    {
        if (I('post.course_id')) {
            $course_id = json_decode(stripslashes(htmlspecialchars_decode(I('post.course_id'))),true);
            $s_map['id'] = array('in',$course_id);
            $e_map['serial_id']= array('in',$course_id);

            $serial_map=M('cloud_course_serial');
    		$episode_map=M('cloud_course_episode');

    		$episode_map->where($e_map)->delete();

            if ($serial_map->where($s_map)->delete()) {
                $gritter['msg_text'] = "课程记录批量删除成功";
                $gritter['msg_class_name'] = "success";
            }else{
                $gritter['msg_text'] = "课程记录批量删除失败";
                $gritter['msg_class_name'] = "danger";
            } 
        }else{
            $gritter['msg_text'] = "参数错误，请重试";
            $gritter['msg_class_name'] = "danger";
        }
        $this->ajaxReturn($gritter);
    }

    //新增云课堂
    public function add_course(){
    	$this->assign('type',2);
    	$this->display();
    }
    public function edit_course(){
    	$serial_id=(int)I('get.serial_id');

		//dump($serial_id);
		//die('Good Afternoon!');

		$serial_map=M('cloud_course_serial');

		$serial_info=$serial_map->find($serial_id);


		$this->assign('type',$serial_info['type']);
		$this->assign('serial_info',$serial_info);
		
		$this->display();
    }

}
?>