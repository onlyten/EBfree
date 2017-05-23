<?php
//王超 20160405
namespace Phone\Model;
use Think\Model\ViewModel;
class CloudCourseModel extends ViewModel {
   public $viewFields = array(
   	 'cloud_course_episode'=>array('id','serial_id','episode_id','link'=>'episode_link','length'=>'episode_length'),
   	 'cloud_course_serial'=>array('id'=>'serial_id','img'=>'serial_img','title'=>'serial_title','type'=>'serial_type','_on'=>'cloud_course_episode.serial_id=cloud_course_serial.id')
   );
 }
 ?>