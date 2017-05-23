<?php
/**
 * 空控制器&空方法，当不存在控制器文件时，默认执行的控制器和方法
 * @authors nkuwilson (nkuwilson@qq.com)
 * @date    2016-01-29 09:45:55
 * @version v1.0
 */
namespace Admin\Controller;
use Think\Controller;
class EmptyController extends Controller {
    
    /**
     * 默认列表页
     * @return [type] [description]
     */
    public function index(){
        echo "doing......";
		// $this->display('List/default');
    }

    public function _empty()
    {
        /*$model_name = CONTROLLER_NAME;
        echo $model_name;*/
        $this->error('错误的操作');
    }
}