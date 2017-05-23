<?php
/**
 * 公用控制器，检测用户是否登陆，是否有权限
 * @authors nkuwilson (nkuwilson@qq.com)
 * @date    2016-02-19 11:22:56
 * @version v1.0
 */
namespace Admin\Controller;
use Think\Controller;
class CommonController extends Controller {
    
    /**
     * 检测用户是否登陆，是否有权限
     * @return [type] [description]
     */
    function _initialize(){
        if (is_null(session('uid'))) {
			$this->redirect('Login/index');
		}else{
			$username = session('username');
			$this->assign('username',$username);
		}

        /*$msg_text,gritter插件提示消息内容*/
        if (I('get.msg_text')) {
            $msg_text = I('get.msg_text');
            $this->assign('msg_text',$msg_text);
            $msg_class_name = I('get.msg_class_name');
            $this->assign('msg_class_name',$msg_class_name);
        }
    }

    /**
     * ajax异步上传图片
     * @param  string $value [description]
     * @return [type]        [description]
     */
    public function img_upload()
    {
        $uploadDir = C('UPLOAD_DIR');
        // Set the allowed file extensions
        $fileTypes = array('jpg', 'jpeg', 'gif', 'png'); // Allowed file extensions

        $verifyToken = md5('unique_salt' . $_POST['timestamp']);

        if (!empty($_FILES) && $_POST['token'] == $verifyToken) {
            $tempFile   = $_FILES['Filedata']['tmp_name'];
            $uploadDir  = $_SERVER['DOCUMENT_ROOT'] . $uploadDir;
            // $targetFile = $uploadDir . $_FILES['Filedata']['name'];  $_FILES['Filedata']['name']为原图片文件名
            $filename = gen_random(15);
            $ext = substr($_FILES['Filedata']['name'], strpos($_FILES['Filedata']['name'], '.'), strlen($_FILES['Filedata']['name']) - 1); //拿到后缀
            $targetFile = $uploadDir . $filename . $ext;

            // Validate the filetype
            $fileParts = pathinfo($_FILES['Filedata']['name']);
            if (in_array(strtolower($fileParts['extension']), $fileTypes)) {

                // Save the file
                move_uploaded_file($tempFile, $targetFile);
                echo C('UPLOAD_DIR').$filename . $ext;

            } else {

                // The file type wasn't allowed
                echo 'Invalid file type.';

            }
        }
        
    }

}