<?php
/*
*黄蜂计划管理
*@author: 王超
*@date: 20160419
*/
namespace Admin\Controller;
use Think\Controller;
class YellowBeeController extends CommonController {
	
    //显示大黄蜂计划注册人员信息
    public function YellowBee(){
        /*搜索条件start $map用于存储查询的条件，$user_map用于存储用户原始输入的查询条件*/
        if (I('post.name')) {
            $map['name'] = array('like','%'.I('post.name').'%');
            $user_map['name'] = I('post.name');
        }
        if (I('post.wx_nickname')) {
            $map['wx_nickname'] = array('like','%'.I('post.wx_nickname').'%');
            $user_map['wx_nickname'] = I('post.wx_nickname');
        }
        if (I('post.advan_course')) {
            $map['advan_course'] = array('like','%'.I('post.advan_course').'%');
            $user_map['advan_course'] = I('post.advan_course');
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
        $m = D('BigBee');
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
        trace($map,'map');

        $people = $m->where($map)->page($page_num,$each_page)->order('id desc')->select();

        $this->assign('user_map',$user_map);
        $this->assign('map',$map);
        trace($m->_sql(),'sql');
        $this->assign('people',$people);
        $this->display();
    }


    //大黄蜂计划注册人员信息编辑页面
    public function edit(){
        $id=I('get.id');

        $BigBeeMap=D('BigBee');

        $person_info=$BigBeeMap->find($id);

        $this->assign('person_info',$person_info);

        $this->display();

    }

    //编辑更新
    public function update(){
        $data['id']=I('get.id');
        $data['name']=I('post.name');
        $data['phone']=I('post.phone');
        $data['advan_course']=I('post.advan_course');
        $data['stu_num']=I('post.stu_num');

        $BigBeeMap=M('big_bee');

        if($BigBeeMap->save($data)){
            $this->redirect('YellowBee/YellowBee', array('msg_text' => '修改成功！','msg_class_name' => 'success'), 0);
        }
        else{
            $this->redirect('YellowBee/YellowBee', array('msg_text' => '修改失败！','msg_class_name' => 'success'), 0);
        }

    }

    //删除
    public function delete(){
        $data['id']=I('get.id');

        $BigBeeMap=M('big_bee');

        if($BigBeeMap->where($data)->limit(1)->delete()){
            $this->redirect('YellowBee/YellowBee', array('msg_text' => '删除成功！','msg_class_name' => 'success'), 0);
        }
        else{
            $this->redirect('YellowBee/YellowBee', array('msg_text' => '删除失败！','msg_class_name' => 'success'), 0);
        }



    }

    //批量删除
    public function delete_batch(){

        if (I('post.course_id')) {
            $course_id = json_decode(stripslashes(htmlspecialchars_decode(I('post.course_id'))),true);
            $map['id'] = array('in',$course_id);

            $BigBeeMap=M('big_bee');

            if ($BigBeeMap->where($map)->delete()) {
                $gritter['msg_text'] = "批量删除成功";
                $gritter['msg_class_name'] = "success";
            }else{
                $gritter['msg_text'] = "批量删除失败";
                $gritter['msg_class_name'] = "danger";
            } 
        }else{
            $gritter['msg_text'] = "参数错误，请重试";
            $gritter['msg_class_name'] = "danger";
        }
        $this->ajaxReturn($gritter);

    }
}
?>