<?php
namespace Phone\Model;
use Think\Model\ViewModel;
class MissionAnswerModel extends ViewModel {
   public $viewFields = array(
     'answer'=>array('id','member_id','level_id','success','type','kouyu_name','mission_id','time_finish'),
     'mission'=>array('id'=>'mission_id','title','_on'=>'answer.mission_id=mission.id'),
      'member_level'=>array('id'=>'member_level_id','success_or','time','_on'=>'answer.level_id=member_level.level_id','_on'=>'answer.member_id=member_level.member_id'),
   );
 }
