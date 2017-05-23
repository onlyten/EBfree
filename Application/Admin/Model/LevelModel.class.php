<?php
/**
 * 课程等级关联模型
 * @authors nkuwilson (nkuwilson@qq.com)
 * @date    2016-02-21 10:06:47
 * @version v1.0
 */
namespace Admin\Model;
use Think\Model\RelationModel;
class LevelModel extends RelationModel {
    
    protected $_link = array(
         'course_title'  =>  array(
             'mapping_type' => self::BELONGS_TO,
             'class_name' => 'course',
             'mapping_name' => 'course',
             'foreign_key' => 'course_id',
             'mapping_fields' => 'title',
             'as_fields' => 'title:course_id',
         ),
         
    );
}