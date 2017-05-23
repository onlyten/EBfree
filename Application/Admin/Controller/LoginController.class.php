<?php
/**
 * 后台登陆
 * @authors nkuwilson (nkuwilson@qq.com)
 * @date    2016-02-19 10:27:34
 * @version v1.0
 */
namespace Admin\Controller;
use Think\Controller;
class LoginController extends Controller {
    
    /**
     * 登陆界面
     * @return [type] [description]
     */
    public function index()
    {
    	$this->display();
    }

    /**
	 * 验证用户登陆名和密码是否匹配
	 * @param  string $value [description]
	 * @return [type]        [description]
	 */
    public function check()
    {
    	if (I('post.username') && I('post.password')) {
    		$username = I('post.username');
			$password = I('post.password');
			if ($username == C('USER_NAME') && $password == C('PASS_WORD')) {
				session('uid','0');	
				session('username',$username);			
				$this->redirect('course/index');
			}else{
				$this->error('用户名或密码错误，请重试',U('login/index'));
			}
    	}
    }

	/**
	 * 注销登陆
	 * @return [type] [description]
	 */
    public function logout()
	{
		session('uid',null);
		$this->redirect('login/index');
	}
}