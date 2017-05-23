<?php
namespace Phone\Controller;
use Think\Controller;
class RegisterController extends Controller {
    public function register(){
        $code = $_GET['code'];//获取code
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=wx7e4fb28fe1443248&secret=521b51fa1c211e00e68fcdd55732b822&code=".$code."&grant_type=authorization_code";
        $haha = $this->reque_get($url);
        //echo "code".$code."<br/>";
        $jsondecode = json_decode($haha);
        $array = get_object_vars($jsondecode);//转换成数组
        $openid = $array['openid'];//微信openid
        $appid="wx7e4fb28fe1443248";
        $appsecrect="521b51fa1c211e00e68fcdd55732b822";
        $accessToken = $this->getToken($appid,$appsecrect);
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$accessToken."&openid=".$openid;
        $userinfo_json=json_decode($this->curl_get($url),true);
        $wx_nickname = $userinfo_json["nickname"];
        $wx_img = $userinfo_json["headimgurl"];
        $city = $userinfo_json["province"] ." ".$userinfo_json["city"];
        $this->assign("city",$city);
        $this->assign("wx_nickname",$wx_nickname);
        $this->assign("wx_img",$wx_img);
        $this->assign("openid",$openid);
        $this->display();
    }


    public function register_update(){
    	$verify_code_s=session('VERIFY_CODE');
    	$verify_code_c=I('post.code');
    	if(I('post.openid') == ""){
    		echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
            echo  "<script>alert('注册失败，请关闭后重新注册！');</script>";
    	}else{
    		if($verify_code_s==$verify_code_c){
	    	//if(1){
	    		$url = session('url');
	        	session('url',null);
	        	$m = M('member');
	        	$data['open_id'] = I('post.openid');
	        	$data['register_time'] = time();
	        	$data['phone'] = I('post.phone');
	        	$data['referee_phone'] = I('post.user_phone');
	        	$data['password'] = I('post.password');
	        	$data['wx_nickname'] = I('post.wx_nickname');
	            $data['city'] = I('post.city');
	        	$data['wx_img'] = I('post.wx_img');
	        	$map['open_id'] = I('post.openid');
	        	$member = $m->where($map)->select();
	            if(I('post.user_phone') != ""){
	                $m = M('member');
	                $huan['phone'] = I('post.user_phone');
	                $huancha = $m->where($huan)->select();
	                if(count($huancha) == 0){
	                    echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
	                    echo  "<script>alert('推荐人手机号码无效，没有可以不填！');window.location.href='https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx7e4fb28fe1443248&redirect_uri=http://www.ebfree.com/huatong/index.php/Phone/Register/register&response_type=code&scope=snsapi_base&state=123&connect_redirect=1#wechat_redirect'</script>";
	                }else{
	                    if(count($member) == 0){
	                        /***************给推荐人一块钱 start****************/
	                        $mapp['phone'] = I('post.user_phone');
	                        $tuijian = $m->where($mapp)->find();
	                        $dataa['red_packet'] = $tuijian['red_packet'] + 1;
	                        $mnm = M('detail');
	                        $detail_data['money'] = 1;
	                        $detail_data['remarks'] = "推广奖金";
	                        $detail_data['time'] = time();
	                        $detail_data['member_id'] = $tuijian['id'];
	                        $mnm->data($detail_data)->add();
	                        // $m->where($mapp)->data($dataa)->save();
	                        /***************给推荐人一块钱 end****************/
	                        if($m->data($data)->add()){
	                            $m->where($mapp)->data($dataa)->save();
	                                // if(count($url)){
	                            //  session('url',null);
	                            //  header ( "location:$url" );
	                            // }else{
	                                echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
	                                echo  "<script>alert('注册成功！去领优惠券吧！');window.location.href='/huatong/index.php/Phone/coupon/index'</script>";
	                            // }    
	                        }
	                    }else{
	                        echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
	                        echo  "<script>alert('请勿重复注册！');</script>";
	                    }
	                }
	            }else{
	                    if(count($member) == 0){
	                    /***************给推荐人一块钱 start****************/
	                    $mapp['phone'] = I('post.user_phone');
	                    $tuijian = $m->where($mapp)->find();
	                    $dataa['red_packet'] = $tuijian['red_packet'] + 1;
	                    $mnm = M('detail');
	                    $detail_data['money'] = 1;
	                    $detail_data['remarks'] = "推广奖金";
	                    $detail_data['time'] = time();
	                    $detail_data['member_id'] = $tuijian['id'];
	                    $mnm->data($detail_data)->add();
	                    // $m->where($mapp)->data($dataa)->save();
	                    /***************给推荐人一块钱 end****************/
	                    if($m->data($data)->add()){
	                        $m->where($mapp)->data($dataa)->save();
	                            // if(count($url)){
	                        //  session('url',null);
	                        //  header ( "location:$url" );
	                        // }else{
	                            echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
	                            echo  "<script>alert('注册成功！去领优惠券吧！');window.location.href='/huatong/index.php/Phone/coupon/index'</script>";
	                        // }    
	                        }
	                    }else{
	                        echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
	                        echo  "<script>alert('请勿重复注册！');</script>";
	                    }
	            }
	    	}
	    	else{
	    		echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
	            echo  "<script>alert('短信验证码错误！');window.location.href='https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx7e4fb28fe1443248&redirect_uri=http://www.ebfree.com/huatong/index.php/Phone/Register/register&response_type=code&scope=snsapi_base&state=123&connect_redirect=1#wechat_redirect'</script>";
	            //echo  "<script>alert('短信验证码错误！');window.location.href='http://192.168.1.100/huatong/index.php/Phone/Register/register'</script>";
	    	}
    	}
        //$this->display();
    }

    /*课程服务协议页面*/
    function agreement(){
        $content="<p>这里应该有课程服务协议。</p><p>但是现在这里什么也没有。</p>";
        $this->assign('content',$content);
        $this->display();
    }

    function reque_get($url)
    {
        if (empty($url)) {
            return false;
        }
        //echo "<br> url $url <br>";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1800);
        $data = curl_exec($ch);
        $info = curl_getinfo($ch);
        // echo "<pre>";
        // print_r($info);
        // echo "</pre>";
        // curl_close($ch);
        // echo "<br> data $data <br>";
        return $data;
    }

     public function curl_get($url)
     {
        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在   
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);//返回文本流,
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
            //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1800);
        $data=curl_exec($ch);
        curl_close($ch);
        return $data;
    }

   //添加图形验证码 王超 20160429
    public function verify_code(){

    	ob_clean();//Magic

    	$Verify = new \Think\Verify();
		$Verify->entry();
    }

    public function verify_page(){
    	$phone=I('get.phone');
    	$this->assign('phone',$phone);
    	$this->display();
    }

    public function check_verify(){
    	$verify_code=I('post.verify_code');
    	$verify = new \Think\Verify();
        $phone=I('post.phone');
    	if($verify->check($verify_code,'')){
    		//短信验证码发送
    		//主帐号,对应开官网发者主账号下的 ACCOUNT SID
			$accountSid= 'aaf98f89544cd9d901545ab8d01f0f97';
			//主帐号令牌,对应官网开发者主账号下的 AUTH TOKEN
			$accountToken= '95eb7b34ea5a4c4c88b906c07d5847a3';
			//应用Id，在官网应用列表中点击应用，对应应用详情中的APP ID
			//在开发调试的时候，可以使用官网自动为您分配的测试Demo的APP ID
			$appId='8a48b551544cd73f01545abc8fca0f9c';
			//请求地址
			//沙盒环境（用于应用开发调试）：sandboxapp.cloopen.com
			//生产环境（用户应用上线使用）：app.cloopen.com
			$serverIP='app.cloopen.com';
			//请求端口，生产环境和沙盒环境一致
			$serverPort='8883';
			//REST版本号，在官网文档REST介绍中获得。
			$softVersion='2013-12-26';

			$rest = new \Vendor\CCPRestSms\REST($serverIP,$serverPort,$softVersion);
     		$rest->setAccount($accountSid,$accountToken);
     		$rest->setAppId($appId);

     		$verify_code=rand(100000,999999);

     		/**************************************举例说明***********************************************************************
		//*假设您用测试Demo的APP ID，则需使用默认模板ID 1，发送手机号是13800000000，传入参数为6532和5，则调用方式为           *
		//*result = sendTemplateSMS("13800000000" ,array('6532','5'),"1");													  *
		//*则13800000000手机号收到的短信内容是：【云通讯】您使用的是云通讯短信模板，您的验证码是6532，请于5分钟内正确输入     *
		//*********************************************************************************************************************/
     		$result = $rest->sendTemplateSMS($phone,array("$verify_code",''),"83287");
    		
    		if($result==NULL){
    			//dump();
    			//die("SMS WRONG!");
    		}
    		if($result->statusCode!=0) {
         		//TODO 添加错误处理逻辑
                //dump($phone);
         		//dump($result);
    			//die("SMS WRONG!");
     		}else{
         		//TODO 添加成功处理逻辑
         		session('VERIFY_CODE',$verify_code);
     		}

    		$this->display();
    	}
    	else{
    		$this->redirect('Register/verify_page',array('phone'=>$phone));
    	}
    }

    protected function getToken($appid, $appsecret)
    {
        //echo $appsecret;
        if (S($appid)) {
            $access_token = S($appid);
        } else {
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $appid . "&secret=" . $appsecret;
            $token = $this->reque_get($url);
            $token = json_decode(stripslashes($token));
            $arr = json_decode(json_encode($token), true);
            $access_token = $arr['access_token'];
            //S($appid, $access_token, 720);
        }
        return $access_token;
    }
}
?>