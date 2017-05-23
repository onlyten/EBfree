<?php
namespace Phone\Controller;
use Think\Controller;
class AboutUsController extends Controller {

    public function aboutus(){
        $about_us_map=M('about_us');

        $maxId=$about_us_map->max('id');
        $info=$about_us_map->find($maxId);
        
        $this->assign('info',$info);
		$this->display();
    }
}
?>