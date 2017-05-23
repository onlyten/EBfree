<?php
/**
 * 任务管理控制器
 * @authors nkuwilson (nkuwilson@qq.com)
 * @date    2016-02-25 20:52:35
 * @version v1.0
 */
namespace Admin\Controller;
use Think\Controller;
class MissionController extends CommonController {
    
    /**
     * 任务管理列表
     * @return [type] [description]
     */
    public function index()
    {
        /*搜索条件start $map用于存储查询的条件，$user_map用于存储用户原始输入的查询条件*/
            if (I('post.title')) {
                $map['title'] = array('like','%'.I('post.title').'%');
                $user_map['title'] = I('post.title');
            }
            if (I('post.course_id') && I('post.course_id') !="*" ) {
                $map['course_id'] = I('post.course_id');
                $user_map['course_id'] = I('post.course_id');
            }
            if (I('post.level_id') && I('post.level_id') !="*" ) {
                $map['level_id'] = I('post.level_id');
                $user_map['level_id'] = I('post.level_id');
            }
            /* 课程管理->查看等级 get请求 */
            if (I('get.level_id')) {
                $map['level_id'] = I('get.level_id');
                $user_map['level_id'] = I('get.level_id');
            }
            /*说明点击提交发送过来的请求*/
            if (IS_POST) {
                /*保存条件搜索对应的条件*/
                cookie('mission_map',ch_array_str($map)); 
                cookie('mission_user_map',ch_array_str($user_map));                  
            }elseif(!I('get.page_num')){
                /*非提交查询条件，非跳转页面而来的请求，即点击课程等级管理->查看任务的请求，将cookie暂存的搜搜索条件置空*/
                cookie('mission_map',null); 
                cookie('mission_user_map',null);
                cookie('mission_map',ch_array_str($map)); 
                cookie('mission_user_map',ch_array_str($user_map)); 
            }
        /*搜索条件end*/
        
    	$m = D('Mission');
        /*分页start*/
        if (I('get.page_num')) {
            $page_num = I('get.page_num');
            /*条件搜索，点击下一页时，条件不变*/
            if (cookie('mission_map')) {
                cookie('mission_map',str_replace('\\','',cookie('mission_map'))); 
                cookie('mission_user_map',str_replace('\\','',cookie('mission_user_map'))); 
                $map = ch_str_array(cookie('mission_map'),true);
                $user_map = ch_str_array(cookie('mission_user_map'),true);
            }           
        }else{
            $page_num = 1;
        }
        trace($page_num,'page_num');
        $this->assign('page_num',$page_num);
        $level_count = $m->where($map)->count();
        $each_page = C('EACH_PAGE');
        trace($each_page,'each_page');
        $page_sum = ceil($level_count/$each_page);
        trace($page_sum,'page_sum');
        $this->assign('page_sum',$page_sum);
        /*分页end*/

        $mission = $m->where($map)->page($page_num,$each_page)->order('id desc')->select();
        $this->assign('mission',$mission);

        /*搜索条件start*/
        $m = M('course');
        $course = $m->select();
        $this->assign('course',$course);
        $n = M('level');
        $level = $n->select();
        $this->assign('level',$level);
        /*搜索条件start*/

        $this->assign('user_map',$user_map);
        $this->assign('map',$map);

    	$this->display();
    }

	/**
	 * 新增任务记录
	 */
    public function add()
    {
    	$m = M('course');
    	$course = $m->select();
    	$this->assign('course',$course);
    	$n = M('level');
    	$level = $n->order('id asc')->select();
        $this->assign('level',$level);

    	/*读取lib_data start*/
    	$p = M('lib_data');
		$lib_id = 3;
		$answer = $p->where('lib_id = '.$lib_id)->select();
		$this->assign('answer',$answer);
        /*读取lib_data end*/
    	
    	$this->display();
    }

	/**
	 * 新增任务记录插入数据库
	 */
    public function add_update()
    {
    	if (I('post.title')) {
    		$data['title'] = I('post.title');
    	}
    	if (I('post.level_id')) {
    		$data['level_id'] = I('post.level_id');
    	}
    	if (I('post.video')) {
    		$data['video'] = I('post.video');
    	}
    	if (I('post.question')) {
    		$data['question'] = I('post.question');
    	}
    	if (I('post.answer_a_img')) {
    		$data['answer_a_img'] = I('post.answer_a_img');
    	}
    	if (I('post.answer_b_img')) {
    		$data['answer_b_img'] = I('post.answer_b_img');
    	}
    	if (I('post.answer_c_img')) {
    		$data['answer_c_img'] = I('post.answer_c_img');
    	}
    	if (I('post.answer_d_img')) {
    		$data['answer_d_img'] = I('post.answer_d_img');
    	}
    	if (I('post.answer')) {
    		$data['answer'] = I('post.answer');
    	}
    	if (I('post.words')) {
            $data['words'] = I('post.words','',false);
        }
        if (I('post.sentences')) {
            $data['sentences'] = I('post.sentences','',false);
        }
        $m = M('mission');
        if ($m->data($data)->add()) {
            $this->redirect('mission/index', array('msg_text' => '课程任务新增成功','msg_class_name' => 'success'), 0);
        }else{
            $this->redirect('mission/index', array('msg_text' => '课程任务新增失败','msg_class_name' => 'danger'), 0);
        }
    }

    public function edit()
    {
    	if (I('get.mission_id')) {
            $map['id'] = I('get.mission_id');
        }else{
            $this->error('mission_id参数错误', U('mission/index'));
        }
        $m = M('course');
        $course = $m->select();
        $this->assign('course',$course);
        $n = M('level');
        $level = $n->select();
        $this->assign('level',$level);
        $this->assign('mission_id',I('get.mission_id'));
        $p = M('mission');
        $mission = $p->where($map)->find();
        $course_id = $n->where('id='.$mission['level_id'])->getField('course_id');
        $mission['course_id'] = $course_id;
        $this->assign('mission',$mission);
        /*读取lib_data start*/
        $p = M('lib_data');
        $lib_id = 3;
        $answer = $p->where('lib_id = '.$lib_id)->select();
        $this->assign('answer',$answer);
        /*读取lib_data end*/
        $this->display();
    }

    /**
     * 修改任务记录插入数据库
     * @authors nkuwilson (nkuwilson@qq.com)
     * @date   2016-03-21
     */
    public function edit_update()
    {
        if (I('get.mission_id')) {
            $map['id'] = I('get.mission_id');
        }else{
            $this->error('mission_id参数错误', U('mission/index'));
        }
        if (I('post.title')) {
            $data['title'] = I('post.title');
        }
        if (I('post.level_id')) {
            $data['level_id'] = I('post.level_id');
        }
        if (I('post.video')) {
            $data['video'] = html_entity_decode(I('post.video'));
        }
        if (I('post.question')) {
            $data['question'] = I('post.question');
        }
        if (I('post.answer_a_img')) {
            $data['answer_a_img'] = I('post.answer_a_img');
        }
        if (I('post.answer_b_img')) {
            $data['answer_b_img'] = I('post.answer_b_img');
        }
        if (I('post.answer_c_img')) {
            $data['answer_c_img'] = I('post.answer_c_img');
        }
        if (I('post.answer_d_img')) {
            $data['answer_d_img'] = I('post.answer_d_img');
        }
        if (I('post.answer')) {
            $data['answer'] = I('post.answer');
        }
        if (I('post.words')) {
            $data['words'] = I('post.words','',false);
        }
        if (I('post.sentences')) {
            $data['sentences'] = I('post.sentences','',false);
        }
        $m = M('mission');
        if ($m->where($map)->save($data)) {
            $this->redirect('mission/index', array('msg_text' => '任务记录修改成功','msg_class_name' => 'success'), 0);
        }else{
            $this->redirect('mission/index', array('msg_text' => '任务记录新增失败','msg_class_name' => 'danger'), 0);
        } 
    }

    /**
     * 删除任务记录
     * @authors nkuwilson (nkuwilson@qq.com)
     * @date   2016-03-21
     * @return [type]     [description]
     */
    public function delete()
    {
        if (I('get.mission_id')) {
            $map['id'] = I('get.mission_id');
            $m = M('mission');
            if ($m->where($map)->limit('1')->delete()) {
                $this->redirect('mission/index', array('msg_text' => '任务记录删除成功','msg_class_name' => 'success'), 0);
            }else{
                $this->redirect('mission/index', array('msg_text' => '任务记录删除失败','msg_class_name' => 'danger'), 0);
            } 
        }else{
            $this->redirect('mission/index', array('msg_text' => '参数错误，请重试','msg_class_name' => 'danger'), 0);
        }
    }

    /**
     * 批量删除任务记录
     * @authors nkuwilson (nkuwilson@qq.com)
     * @date   2016-03-21
     * @return [type]     [description]
     */
    public function delete_batch()
    {
        if (I('post.mission_id')) {
            $mission_id = json_decode(stripslashes(htmlspecialchars_decode(I('post.mission_id'))),true);
            $map['id'] = array('in',$mission_id);
            $m = M('mission');
            if ($m->where($map)->delete()) {
                $gritter['msg_text'] = "任务记录批量删除成功";
                $gritter['msg_class_name'] = "success";
            }else{
                $gritter['msg_text'] = "任务记录批量删除失败";
                $gritter['msg_class_name'] = "danger";
            } 
        }else{
            $gritter['msg_text'] = "参数错误，请重试";
            $gritter['msg_class_name'] = "danger";
        }
        $this->ajaxReturn($gritter);
    }
}