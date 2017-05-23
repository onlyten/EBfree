<?php
/**
 * 课程等级关联模型
 * @authors nkuwilson (nkuwilson@qq.com)
 * @date    2016-02-21 10:06:47
 * @version v1.0
 */
namespace Phone\Model;
use Think\Model\ViewModel;
class CouponModel extends ViewModel {
    
    public $viewFields = array(
        'user_coupon'=>array('id','card_id','time','code','open_id','status','_type'=>'LEFT'),
        'coupon'=>array('id'=>'coupon_id','title'=>'coupon_title','begin_timestamp','pic_url','end_timestamp','reduce_cost','reduce_cost','_on'=>'user_coupon.card_id=coupon.card_id','_type'=>'LEFT'),
        'course'=>array('title'=>'course_title', '_on'=>'coupon.course_id=course.id'),
    );
}