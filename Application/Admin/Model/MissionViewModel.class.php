<?php
/**
 * 任务表视图模型模型
 * @authors nkuwilson (nkuwilson@qq.com)
 * @date    2016-02-28 22:03:37
 * @version v1.0
 */
namespace Admin\Model;
use Think\Model\ViewModel;
class MissionViewModel extends ViewModel {

    public $viewFields = array(
        'mission'=>array('id','title','video','words','sentences','question','answer_a_img','answer_b_img','answer_c_img','answer_d_img','answer','_type'=>'LEFT'),
        'level'=>array('title'=>'level_title', '_on'=>'mission.level_id=level.id','_type'=>'LEFT'),
        'course'=>array('title'=>'course_title', '_on'=>'level.course_id=course.id'),
    );

}