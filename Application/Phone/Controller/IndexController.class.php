<?php
namespace Phone\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        if(cookie('openid')){
            $openid = cookie('openid');
        }else{
            $code = $_GET['code'];//获取code
            $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=wx7e4fb28fe1443248&secret=521b51fa1c211e00e68fcdd55732b822&code=".$code."&grant_type=authorization_code";
            $haha = $this->reque_get($url);
            //echo "code".$code."<br/>";
            $jsondecode = json_decode($haha);
            $array = get_object_vars($jsondecode);//转换成数组
            $openid = $array['openid'];//微信openid
            cookie('openid',$openid);
        }

        $appid="wx7e4fb28fe1443248";
        $appsecrect="521b51fa1c211e00e68fcdd55732b822";
        $accessToken = $this->getToken($appid,$appsecrect);
        //echo $accessToken;
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$accessToken."&openid=".$openid;
        $userinfo_json=json_decode($this->curl_get($url),true);
        $wx_nickname = $userinfo_json["nickname"];
        $wx_img = $userinfo_json["headimgurl"];

        $m = M('member');
        $map['open_id'] = $openid;
        $member = $m->where($map)->select();

        $homepage=M('homepage');
        $maxId=$homepage->max('id');
        $info=$homepage->find($maxId);
        $this->assign('info',$info);
        
        $this->assign("member",$member);
        $this->assign("openid",$openid);
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