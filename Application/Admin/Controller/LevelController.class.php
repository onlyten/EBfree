<?php
/**
 * 课程等级控制器
 * @authors nkuwilson (nkuwilson@qq.com)
 * @date    2016-02-19 09:07:03
 * @version v1.0
 */
namespace Admin\Controller;
use Think\Controller;
class LevelController extends CommonController {
    
    /**
     * 课程等级管理列表
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
        /* 课程管理->查看等级 get请求 */
        if (I('get.course_id')) {
            $map['course_id'] = I('get.course_id');
            $user_map['course_id'] = I('get.course_id');
        }
        /*说明点击提交发送过来的请求*/
        if (I('post.title') || I('post.course_id')) {
            /*保存条件搜索对应的条件*/
            cookie('level_map',ch_array_str($map)); 
            cookie('level_user_map',ch_array_str($user_map));                  
        }elseif(!I('get.page_num')){
            /*非提交查询条件，非跳转页面而来的请求，即点击课程管理->查看等级的请求，将cookie暂存的搜搜索条件置空*/
            cookie('level_map',null); 
            cookie('level_user_map',null);
            cookie('level_map',ch_array_str($map)); 
            cookie('level_user_map',ch_array_str($user_map)); 
        }
        /*搜索条件end*/
        $m = D('level');
        /*分页start*/
        if (I('get.page_num')) {
            $page_num = I('get.page_num');
            /*条件搜索，点击下一页时，条件不变*/
            if (cookie('level_map')) {
                cookie('level_map',str_replace('\\','',cookie('level_map'))); 
                cookie('level_user_map',str_replace('\\','',cookie('level_user_map'))); 
                $map = ch_str_array(cookie('level_map'),true);
                $user_map = ch_str_array(cookie('level_user_map'),true);
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
        $level = $m->relation(true)->where($map)->page($page_num,$each_page)->order('id desc')->select();
        $this->assign('level',$level);
        $this->assign('user_map',$user_map);
        $this->assign('map',$map);
        $m = M('course');
        $course = $m->select();
        $this->assign('course',$course);
    	$this->display();
    }

    /**
     * 添加课程等级
     */
    public function add()
    {
        $m = M('course');
        $course = $m->select();
        $this->assign('course',$course);
    	$this->display();
    }

    /**
     * 新增课程等级插入数据库
     */
    public function add_update()
    {
        if (I('post.title')) {
            $data['title'] = I('post.title');
        }
        if (I('post.course_id')) {
            $data['course_id'] = I('post.course_id');
        }
        if (I('post.title_img')) {
            $data['title_img'] = I('post.title_img');
        }
        if (I('post.parent_aim')) {
            $data['parent_aim'] = I('post.parent_aim','',false);
        }
        if (I('post.parent_step')) {
            $data['parent_step'] = I('post.parent_step','',false);
        }
        $m = M('level');
        if ($m->data($data)->add()) {
            $this->redirect('level/index', array('msg_text' => '课程等级记录新增成功','msg_class_name' => 'success'), 0);
        }else{
            $this->redirect('level/index', array('msg_text' => '课程等级记录新增失败','msg_class_name' => 'danger'), 0);
        }
    }

    /**
     * 修改课程等级
     * @return [type] [description]
     */
    public function edit()
    {
        if (I('get.level_id')) {
            $map['id'] = I('get.level_id');
        }else{
            $this->error('level_id参数错误', U('level/index'));
        }
        $this->assign('level_id',I('get.level_id'));
        $m = M('level');
        $level = $m->where($map)->find();
        $this->assign('level',$level);
        $m = M('course');
        $course = $m->select();
        $this->assign('course',$course);
        $this->display();
    }

    /**
     * 修改课程等级记录更新数据库
     * @return [type] [description]
     */
    public function edit_update()
    {
        if (I('get.level_id')) {
            $map['id'] = I('get.level_id');
        }else{
            $this->error('level_id', U('course/index'));
        }
        if (I('post.title')) {
            $data['title'] = I('post.title');
        }
        if (I('post.course_id')) {
            $data['course_id'] = I('post.course_id');
        }
        if (I('post.title_img')) {
            $data['title_img'] = I('post.title_img');
        }
        if (I('post.parent_aim')) {
            $data['parent_aim'] = I('post.parent_aim','',false);
        }
        if (I('post.parent_step')) {
            $data['parent_step'] = I('post.parent_step','',false);
        }
        $m = M('level');
        if ($m->where($map)->save($data)) {
            $this->redirect('level/index', array('msg_text' => '课程等级记录修改成功','msg_class_name' => 'success'), 0);
        }else{
            $this->redirect('level/index', array('msg_text' => '课程等级记录新增失败','msg_class_name' => 'danger'), 0);
        } 
    }

    /**
     * 批量删除课程记录
     * @param string $value [description]
     */
    public function delete_batch()
    {
        if (I('post.level_id')) {
            $level_id = json_decode(stripslashes(htmlspecialchars_decode(I('post.level_id'))),true);
            $map['id'] = array('in',$level_id);
            $m = M('level');
            if ($m->where($map)->delete()) {
                $gritter['msg_text'] = "课程等级记录批量删除成功";
                $gritter['msg_class_name'] = "success";
            }else{
                $gritter['msg_text'] = "课程等级记录批量删除失败";
                $gritter['msg_class_name'] = "danger";
            } 
        }else{
            $gritter['msg_text'] = "参数错误，请重试";
            $gritter['msg_class_name'] = "danger";
        }
        $this->ajaxReturn($gritter);
    }


}