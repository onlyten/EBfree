<?php
namespace Admin\Model;
use Think\Model\ViewModel;
class TixianMemberModel extends ViewModel {
   public $viewFields = array(
     'tixian'=>array('id','user_name','bank_name','time','openid','money','card_num'),
     'member'=>array('id'=>'member_id','open_id','wx_nickname','phone','_on'=>'tixian.openid=member.open_id'),
   );
 }
