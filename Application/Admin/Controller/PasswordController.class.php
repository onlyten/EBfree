<?php
/**
 * 用户账号密码控制器
 * @authors nkuwilson (nkuwilson@qq.com)
 * @date    2016-02-20 09:21:18
 * @version v1.0
 */
namespace Admin\Controller;
use Think\Controller;
class PasswordController extends CommonController {
    
    /**
     * 修改密码
     * @return [type] [description]
     */
    public function edit()
    {
    	$this->display();
    }
}