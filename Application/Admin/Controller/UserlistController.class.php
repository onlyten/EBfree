<?php
/**
 * 课程等级控制器
 * @authors nkuwilson (nkuwilson@qq.com)
 * @date    2016-02-19 09:07:03
 * @version v1.0
 */
namespace Admin\Controller;
use Think\Controller;
class UserlistController extends CommonController {
    
    /**
     * 课程等级管理列表
     * @return [type] [description]
     */
    public function index()
    {                  
        /*搜索条件start $map用于存储查询的条件，$user_map用于存储用户原始输入的查询条件*/
        if (I('post.title')) {
            $map['wx_nickname'] = array('like','%'.I('post.title').'%');
            $user_map['wx_nickname'] = I('post.title');
        }
        if (I('post.phone')) {
            $map['phone'] = array('like','%'.I('post.phone').'%');
            $user_map['phone'] = I('post.phone');
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
        $m = M('member');
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
        $level = $m->where($map)->page($page_num,$each_page)->order('id desc')->select();
        $this->assign('level',$level);
        $this->assign('user_map',$user_map);
        $this->assign('map',$map);
        $m = M('course');
        $course = $m->select();
        $this->assign('course',$course);
    	$this->display();
    }
    public function user_output(){
      
        
      $d = M('member');
      $member = $d->select();
      
      foreach ($member as $key => $value) {
          $data[$key]['key'] = $key+1;
          $data[$key]['wx_nickname'] = $value['wx_nickname'];
          $data[$key]['phone'] = $value['phone'];  
          $data[$key]['register_time'] =date('Y_m_d H:m:s', $value['register_time']);      
          $data[$key]['city'] = $value['city'];
          $data[$key]['red_packet'] = $value['red_packet'];
      }
             $headArr[]='序号';
             $headArr[]='微信昵称';
             $headArr[]='会员电话';
             $headArr[]='注册时间';
             $headArr[]='所在城市';
             $headArr[]='红包余额';
             

      $filename = "会员列表";
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