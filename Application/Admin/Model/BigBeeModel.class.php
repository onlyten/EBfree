<?php
//王超 20160405
namespace Admin\Model;
use Think\Model\ViewModel;
class BigBeeModel extends ViewModel {
   public $viewFields = array(
   		'member'=>array('id'=>'member_id','wx_nickname'),
   		'big_bee'=>array('id','member_id','name','phone','advan_course','stu_num','_on'=>'big_bee.member_id=member.id')
   );
 }
 ?>