<?php
namespace Admin\Model;
use Think\Model\ViewModel;
class DashangMemberModel extends ViewModel {
   public $viewFields = array(
     'dashang'=>array('id','member_id','mission_id','serial_id','success','money'),
     'member'=>array('id'=>'member_id','wx_nickname','phone','_on'=>'dashang.member_id=member.id'),
     'mission'=>array('id'=>'mission_id','level_id','_on'=>'dashang.mission_id=mission.id'),
     'level'=>array('id'=>'level_id','course_id','_on'=>'mission.level_id=level.id'),
     'course'=>array('id'=>'course_id','title','_on'=>'level.course_id=course.id'),
     //'cloud_course_serial'=>array('id'=>'cloud_course_serial_id','title'=>'movie_totle','_on'=>'dashang.serial_id=cloud_course_serial.id'),
   );
 }
