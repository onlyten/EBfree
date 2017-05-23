<?php
/**
 * 口语表视图模型
 * @authors nkuwilson (nkuwilson@qq.com)
 * @date    2016-02-28 21:45:53
 * @version v1.0
 */
namespace Admin\Model;
use Think\Model\ViewModel;
class OralModel extends ViewModel {

    public $viewFields = array(
        'oral'=>array('id','title','_type'=>'LEFT'),
        'level'=>array('title'=>'level_title', '_on'=>'oral.level_id=level.id','_type'=>'LEFT'),
        'course'=>array('title'=>'course_title', '_on'=>'level.course_id=course.id'),
    );

}