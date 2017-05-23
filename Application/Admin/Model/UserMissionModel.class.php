<?php
namespace Admin\Model;
use Think\Model\ViewModel;
class UserMissionModel extends ViewModel {
   public $viewFields = array(
     'answer'=>array('id','member_id','level_id','success','mission_id','type','kouyu_time','kouyu_name'),
     'member'=>array('id'=>'member_id','wx_nickname', '_on'=>'answer.member_id=member.id'),
     'level'=>array('id'=>'level_id','title'=>'level_title','_on'=>'answer.level_id=level.id'),
     //'mission'=>array('id'=>'mission_id','title'=>'mission_title','_on'=>'answer.mission_id=mission.id'),
   );
 }
