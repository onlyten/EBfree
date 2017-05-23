<?php
namespace Phone\Model;
use Think\Model\ViewModel;
class MengMemberModel extends ViewModel {
   public $viewFields = array(
     'member_level'=>array('id'=>'member_level_id','success_or','referee_member_id','member_id','level_id','time','class_id','coupon_pay','coupon_sn','account_pay','user_pay','pay_type','foreign_time','reserve_phone','title','gender','age','school'),
     'member'=>array('id'=>'member_id','open_id','register_time','red_packet', 'phone', 'referee_phone', 'password', 'wx_nickname', 'wx_img','_on'=>'member_level.member_id=member.id'),
   );
 }
