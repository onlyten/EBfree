<?php
/**
 * 口语管理控制器
 * @authors nkuwilson (nkuwilson@qq.com)
 * @date    2016-02-28 17:30:40
 * @version v1.0
 */
namespace Admin\Controller;
use Think\Controller;
class OralController extends CommonController {
    
    /**
     * 口语列表
     * @return [type] [description]
     */
    public function index()
    {
        $m = D('oral');
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
        $level_count = $m->where($map)->count();
        $each_page = C('EACH_PAGE');
        trace($each_page,'each_page');
        $page_sum = ceil($level_count/$each_page);
        trace($page_sum,'page_sum');
        $this->assign('page_sum',$page_sum);
        /*分页end*/
        $oral = $m->where($map)->page($page_num,$each_page)->select();
        $this->assign('oral',$oral);
        $this->display();
    }

	/**
	 * 添加口语
	 */
    public function add()
    {
    	$m = M('course');
    	$course = $m->select();
    	$this->assign('course',$course);
    	$n = M('level');
    	$level = $n->select();
    	$this->assign('level',$level);
    	$this->display();
    }

    public function add_update()
    {
    	if (I('post.title')) {
    		$data['title'] = I('post.title');
    	}
    	if (I('post.level_id')) {
    		$data['level_id'] = I('post.level_id');
    	}
        $m = M('oral');
        if ($m->data($data)->add()) {
        	$this->redirect('oral/index', array('msg_text' => '口语记录新增成功','msg_class_name' => 'success'), 0);
        }else{
            $this->redirect('oral/index', array('msg_text' => '口语记录新增失败','msg_class_name' => 'danger'), 0);
        }
    }
}