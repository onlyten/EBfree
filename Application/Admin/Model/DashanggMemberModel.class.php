<?php
namespace Admin\Model;
use Think\Model\ViewModel;
class DashanggMemberModel extends ViewModel {
   public $viewFields = array(
     'dashang'=>array('id','member_id','mission_id','serial_id','success','money'),
     'member'=>array('id'=>'member_id','wx_nickname','phone','_on'=>'dashang.member_id=member.id'),
     'cloud_course_serial'=>array('id'=>'cloud_course_serial_id','title','_on'=>'dashang.serial_id=cloud_course_serial.id'),
   );
 }
