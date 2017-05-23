<?php
/**
 * 课程管理
 * @authors nkuwilson (nkuwilson@qq.com)
 * @date    2016-02-01 10:46:25
 * @version v1.0
 */
namespace Admin\Controller;
use Think\Controller;
class UserController extends CommonController {

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
		$m = D('LevelMember');
		$map['success_or'] = '1';
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
        $member_level = $m->where($map)->page($page_num,$each_page)->order('member_level_id desc')->select();
    	$this->assign('member_level',$member_level);
    	$this->display();
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
            $m = M('member_level');
            if ($m->where($map)->limit('1')->delete()) {
                $this->redirect('User/index', array('msg_text' => '删除成功','msg_class_name' => 'success'), 0);
            }else{
                $this->redirect('User/index', array('msg_text' => '删除失败','msg_class_name' => 'danger'), 0);
            } 
        }else{
            $this->redirect('User/index', array('msg_text' => '参数错误，请重试','msg_class_name' => 'danger'), 0);
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
            $m = M('member_level');
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


    public function baoming_output(){
      
        
      $m = D('LevelMember');
      $map['success_or'] = '1';
      $kecheng = $m->where($map)->select();
      
      foreach ($kecheng as $key => $value) {
          $data[$key]['key'] = $key+1;
          $data[$key]['wx_nickname'] = $value['wx_nickname'];
          $data[$key]['level_title'] = $value['level_title'];  
          if($value['type'] == 1){
            $data[$key]['type'] = "少儿";
          }     
          if($value['type'] == 2){
            $data[$key]['type'] = "成人";
          } 
          $data[$key]['user_pay'] = $value['user_pay'];
      }
             $headArr[]='序号';
             $headArr[]='成员';
             $headArr[]='等级';
             $headArr[]='类型';
             $headArr[]='支付金额';
             

      $filename = "报名列表";
      $this->excel_export($filename,$headArr,$data);

}
    //excel导出
    public function excel_export($fileName,$headArr,$data)
    {
        //导入PHPExcel类库
          //Vendor('Excel.PHPExcel'); //导入thinkphp第三方类库
        ob_end_clean();
        import("Vendor.excel.PHPExcel");
        import("Vendor.excel.PHPExcel.Writer.Excel5");
        import("Vendor.excel.PHPExcel.IOFactory.php");

        $date = date("Y_m_d",time());
        $fileName .= "_{$date}.xls";

        //创建PHPExcel对象，注意，不能少了\
        $objPHPExcel = new \PHPExcel();
        $objProps = $objPHPExcel->getProperties();

         //设置表头
        $key = ord("A");
        //print_r($headArr);exit;
        foreach($headArr as $v){
            $colum = chr($key);
            $objPHPExcel->setActiveSheetIndex(0) ->setCellValue($colum.'1', $v);
            $objPHPExcel->setActiveSheetIndex(0) ->setCellValue($colum.'1', $v);
            $key += 1;
        }

        $column = 2;
        $objActSheet = $objPHPExcel->getActiveSheet();

        //print_r($data);exit;
        foreach($data as $key => $rows){ //行写入
            $span = ord("A");
            foreach($rows as $keyName=>$value){// 列写入
                $j = chr($span);
                $objActSheet->setCellValue($j.$column, $value);
                $span++;
            }
            $column++;
        }

        $fileName = iconv("utf-8", "gb2312", $fileName);
        //重命名表
        //$objPHPExcel->getActiveSheet()->setTitle('test');
        //设置活动单指数到第一个表,所以Excel打开这是第一个表
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"$fileName\"");
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output'); //文件通过浏览器下载
        exit;

    }

}