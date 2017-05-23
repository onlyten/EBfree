<?php
/**
 * 课程管理
 * @authors nkuwilson (nkuwilson@qq.com)
 * @date    2016-02-01 10:46:25
 * @version v1.0
 */
namespace Admin\Controller;
use Think\Controller;
class CourseController extends CommonController {

    /**
     * 课程管理列表
     * @return [type] [description]
     */
    public function index()
    {
    	/*搜索条件start $map用于存储查询的条件，$user_map用于存储用户原始输入的查询条件*/
    	if (I('post.title')) {
    		$map['title'] = array('like','%'.I('post.title').'%');
    		$user_map['title'] = I('post.title');
    	}
    	if (I('post.type') && I('post.type') !="*" ) {
    		$map['type'] = I('post.type');
    		$user_map['type'] = I('post.type');
    	}
    	if (I('post.foreign') && I('post.foreign') !="*") {
    		$map['foreign'] = I('post.foreign');
    		$user_map['foreign'] = I('post.foreign');
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
    	$m = D('course');
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
		/*读取lib_data start*/
		$n = M('lib_data');
		$lib_id = 1;
		$type = $n->where('lib_id = '.$lib_id)->select();
		$this->assign('type',$type);
		$lib_id = 2;
		$foreign = $n->where('lib_id = '.$lib_id)->select();
		$this->assign('foreign',$foreign);
		/*读取lib_data end*/
    	trace($map,'map');
    	$course = $m->relation(true)->where($map)->page($page_num,$each_page)->order('id desc')->select();
    	$this->assign('user_map',$user_map);
    	$this->assign('map',$map);
    	trace($m->_sql(),'sql');
    	$this->assign('course',$course);
    	$this->display();
    }

	/**
	 * 新增课程
	 */
    public function add()
    {
        /*读取lib_data start*/
    	$n = M('lib_data');
		$lib_id = 1;
		$type = $n->where('lib_id = '.$lib_id)->select();
		$this->assign('type',$type);
		$lib_id = 2;
		$foreign = $n->where('lib_id = '.$lib_id)->select();
		$this->assign('foreign',$foreign);
        /*读取lib_data end*/
    	$this->display();
    }

	/**
	 * 新增课程信息插入数据库
	 */
    public function add_update()
    {
    	if (I('post.title')) {
    		$data['title'] = I('post.title');
    	}
        if (I('post.weight')) {
            $data['weight'] = I('post.weight');
        }
    	if (I('post.banner_img')) {
    		$data['banner_img'] = I('post.banner_img');
    	}
    	if (I('post.type')) {
    		$data['type'] = I('post.type');
    	}
    	if (I('post.foreign')) {
    		$data['foreign'] = I('post.foreign');
    	}
    	if (I('post.video')) {
    		$data['video'] = I('post.video');
    	}
    	if (I('post.detail_img')) {
    		$data['detail_img'] = I('post.detail_img');
    	}
        if (I('post.price')) {
            $data['price'] = I('post.price');
        }
    	if (I('post.level_img')) {
    		$data['level_img'] = I('post.level_img');
    	}
    	$m = M('course');
    	if ($m->data($data)->add()) {
            $this->redirect('course/index', array('msg_text' => '课程记录新增成功','msg_class_name' => 'success'), 0);
    	}else{
            $this->redirect('course/index', array('msg_text' => '课程记录新增失败','msg_class_name' => 'danger'), 0);
    	}    	
    }
	

    /**
     * 修改课程
     * @return [type] [description]
     */
    public function edit()
    {
        if (I('get.course_id')) {
            $map['id'] = I('get.course_id');
        }else{
            $this->error('course_id参数错误', U('course/index'));
        }
        $this->assign('course_id',I('get.course_id'));
        $m = M('course');
        $course = $m->where($map)->find();
        $this->assign('course',$course);
        /*读取lib_data start*/
        $n = M('lib_data');
        $lib_id = 1;
        $type = $n->where('lib_id = '.$lib_id)->select();
        $this->assign('type',$type);
        $lib_id = 2;
        $foreign = $n->where('lib_id = '.$lib_id)->select();
        $this->assign('foreign',$foreign);
        /*读取lib_data end*/
        $this->display();
    }

    public function edit_update()
    {
        if (I('get.course_id')) {
            $map['id'] = I('get.course_id');
        }else{
            $this->error('course_id参数错误', U('course/index'));
        }
        if (I('post.title')) {
            $data['title'] = I('post.title');
        }
        if (I('post.weight')) {
            $data['weight'] = I('post.weight');
        }
        if (I('post.banner_img')) {
            $data['banner_img'] = I('post.banner_img');
        }
        if (I('post.type')) {
            $data['type'] = I('post.type');
        }
        if (I('post.price')) {
            $data['price'] = I('post.price');
        }
        if (I('post.foreign')) {
            $data['foreign'] = I('post.foreign');
        }
        if (I('post.video')) {
            $data['video'] = html_entity_decode(I('post.video'));
        }
        if (I('post.detail_img')) {
            $data['detail_img'] = I('post.detail_img');
        }
        if (I('post.level_img')) {
            $data['level_img'] = I('post.level_img');
        }
        $m = M('course');
        if ($m->where($map)->save($data)) {
            $this->redirect('course/index', array('msg_text' => '课程记录修改成功','msg_class_name' => 'success'), 0);
        }else{
            $this->redirect('course/index', array('msg_text' => '课程记录新增失败','msg_class_name' => 'danger'), 0);
        } 
    }

    /**
     * 删除课程记录
     * @param  string $value [description]
     * @return [type]        [description]
     */
    public function delete()
    {
        if (I('get.course_id')) {
            $map['id'] = I('get.course_id');
            $m = M('course');
            if ($m->where($map)->limit('1')->delete()) {
                $this->redirect('course/index', array('msg_text' => '课程记录删除成功','msg_class_name' => 'success'), 0);
            }else{
                $this->redirect('course/index', array('msg_text' => '课程记录删除失败','msg_class_name' => 'danger'), 0);
            } 
        }else{
            $this->redirect('course/index', array('msg_text' => '参数错误，请重试','msg_class_name' => 'danger'), 0);
        }
    }

    /**
     * 批量删除课程记录
     * @param string $value [description]
     */
    public function delete_batch()
    {
        if (I('post.course_id')) {
            $course_id = json_decode(stripslashes(htmlspecialchars_decode(I('post.course_id'))),true);
            $map['id'] = array('in',$course_id);
            $m = M('course');
            if ($m->where($map)->delete()) {
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

}