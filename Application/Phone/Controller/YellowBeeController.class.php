<?php
namespace Phone\Controller;
use Think\Controller;

//黄蜂计划
//王超
//20160415
//
class YellowBeeController extends Controller {

	//黄蜂计划首页
    public function index(){
    	
    	//获取用户openid
		$code = $_GET['code'];//获取code
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=wx7e4fb28fe1443248&secret=521b51fa1c211e00e68fcdd55732b822&code=".$code."&grant_type=authorization_code";
        $haha = $this->reque_get($url);
        //echo "code".$code."<br/>";
        $jsondecode = json_decode($haha);
        $array = get_object_vars($jsondecode);//转换成数组

        $openid = $array['openid'];//微信openid
        if(cookie('openid')){
            $openid = cookie('openid');
        }
        //$openid=123456;

        //设置用户微信openid cookie
        cookie('openid',$openid,86400);
        
        $this->display();
    }

    //黄蜂计划（分页）
    public function YellowBee(){
    	if($this->isReg()){
    		//
    		$this->redirect('Honey/advertise');
    	}
    	else{
    		$this->display();
    	}

    }

    //大黄蜂计划
    public function BigYellowBee(){
        $code = $_GET['code'];//获取code
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=wx7e4fb28fe1443248&secret=521b51fa1c211e00e68fcdd55732b822&code=".$code."&grant_type=authorization_code";
        $haha = $this->reque_get($url);
        //echo "code".$code."<br/>";
        $jsondecode = json_decode($haha);
        $array = get_object_vars($jsondecode);//转换成数组

        $openid = $array['openid'];//微信openid
        if(cookie('openid')){
            $openid = cookie('openid');
        }
        //$openid=123456;

        //设置用户微信openid cookie
        cookie('openid',$openid,86400);

    	if($this->isReg()){
    		$this->display();
    	}
    	else{
    		$this->redirect('Register/register');
    	}
    }

    //注册大黄蜂计划
    public function JoinBigYellowBee(){
    	$this->display();
    }

    public function BigUpdate(){

        //激活码验证
        $code_c=I('post.code');
        $code_s=session('VERIFY_CODE');
        $flag=($code_s==$code_c);
        if($flag){
            $data['name']=I('post.name');
            $data['phone']=I('post.phone');
            $data['advan_course']=I('post.advan_course');
            $data['stu_num']=I('post.stu_num');

            $openid=cookie('openid');
            $MemberMap=M('member');
            $member_query['open_id']=$openid;
            $MemberInfo=$MemberMap->where($member_query)->find();

            $data['member_id']=$MemberInfo['id'];

            $BigBeeMap=M('big_bee');
            if($BigBeeMap->data($data)->add()){
                echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
                echo '<div style="text-align:center;margin:12px 12px 12px 12px;"><p><font size="5px">提交成功！</p></div>';
            }
            else{
                echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
                echo '<div style="text-align:center;margin:12px 12px 12px 12px;"><p><font size="5px">提交失败！请再试一次！</p></div>';
            }
        }
        else{
            echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
            echo '<div style="text-align:center;margin:12px 12px 12px 12px;"><p><font size="5px">短信验证码错误！</p></div>';
        }
    }

    //判断用户是否注册
	private function isReg(){
		$openid=cookie('openid');

        //dump($openid);
        //die('good afternoon!');

		$MemberMap=M('member');
		$member_query['open_id']=$openid;
		if(count($MemberMap->where($member_query)->select())>0)
			return true;
		else
			return false;

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