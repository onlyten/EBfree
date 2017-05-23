<?php
/**
 * 课程关联模型
 * @authors nkuwilson (nkuwilson@qq.com)
 * @date    2016-02-03 14:17:47
 * @version v1.0
 */
namespace Admin\Model;
use Think\Model\RelationModel;
class CourseModel extends RelationModel {
    
    protected $_link = array(
         'lib_data_type'  =>  array(
             'mapping_type' => self::BELONGS_TO,
             'class_name' => 'lib_data',
             'mapping_name' => 'lib_data',
             'foreign_key' => 'type',
             'mapping_fields' => 'title',
             'as_fields' => 'title:type',
         ),
         'lib_data_foreign'  =>  array(
             'mapping_type' => self::BELONGS_TO,
             'class_name' => 'lib_data',
             'mapping_name' => 'lib_data',
             'foreign_key' => 'foreign',
             'mapping_fields' => 'title',
             'as_fields' => 'title:foreign',
         ),
         
    );
}