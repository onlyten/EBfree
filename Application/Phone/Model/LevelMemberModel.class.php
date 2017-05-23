<?php
namespace Phone\Model;
use Think\Model\ViewModel;
class LevelMemberModel extends ViewModel {
   public $viewFields = array(
     'member_level'=>array('id'=>'member_level_id','success_or','member_id','level_id','time','class_id','coupon_pay','coupon_sn','account_pay','user_pay','pay_type','foreign_time','reserve_phone','title','gender','age','school'),
     'member'=>array('id'=>'member_id','open_id','register_time', 'phone', 'referee_phone', 'password', 'wx_nickname', 'wx_img','_on'=>'member_level.member_id=member.id'),
     'level'=>array('id'=>'level_id','course_id', 'title_img', 'title'=>'level_title','parent_aim', 'parent_step','_on'=>'member_level.level_id=level.id'),
     'course'=>array('id'=>'course_id','title'=>'course_title','price','banner_img','_on'=>'level.course_id=course.id'),
     // 'mission'=>array('id'=>'mission_id','title'=>'mission_title','_on'=>'level.id=mission.level_id'),
   );
 }
