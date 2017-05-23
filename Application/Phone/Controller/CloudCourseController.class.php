<?php
//亲子云课堂 王超 20160405
namespace Phone\Controller;
use Think\Controller;
class CloudCourseController extends Controller {
	public function CloudCourse(){
		//获取用户openid
		$code = $_GET['code'];//获取code
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=wx7e4fb28fe1443248&secret=521b51fa1c211e00e68fcdd55732b822&code=".$code."&grant_type=authorization_code";
        $haha = $this->reque_get($url);
        //echo "code".$code."<br/>";
        $jsondecode = json_decode($haha);
        $array = get_object_vars($jsondecode);//转换成数组

        $openid = $array['openid'];//微信openid
        //设置用户微信openid cookie
        if(count(cookie('openid'))==0){
        	cookie('openid',$openid);
        }
		//设置最近播放cookie
		if(count(cookie('BrctRecentPlayList'))==0){
			cookie('BrctRecentPlayList',array(),86400);//有效期一天
		}
		if(count(cookie('MovieRecentPlayList'))==0){
			cookie('MovieRecentPlayList',array(),86400);//有效期一天
		}

		$this->display();
	}

	//判断用户是否注册
	private function isReg(){
		$openid=cookie('openid');

		$MemberMap=M('member');
		$member_query['open_id']=$openid;
		if(count($MemberMap->where($member_query)->select())>0)
			return true;
		else
			return false;

	}

	//云课堂-展示图片广告
	public function CloudCourse_3(){
		if($this->isReg()){
			$CloudCourseMap=M('cloud_course_serial');
			$SerialQuery['type']=2;//type=2为云课堂图片
			$serial_infos=$CloudCourseMap->where($SerialQuery)->order('title desc')->select();
			$this->assign('serial_infos',$serial_infos);
			$this->display();
		}
		else{
			//跳转到注册页面
			echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
			echo "<script>alert('尚未注册，请先注册！')</script>";
			$reg_url="https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx7e4fb28fe1443248&redirect_uri=http://www.ebfree.com/huatong/index.php/Phone/Register/register&response_type=code&scope=snsapi_base&state=123&connect_redirect=1#wechat_redirect";
			echo "<script>window.location.href='$reg_url'</script>";
		}	
	}

	public function BroadcastList(){
		//读取cookie
		$RecentPlayCount=count(cookie('BrctRecentPlayList'));

		$BroadcastSerialMap=M('cloud_course_serial');
		$SerialQuery['type']=0;//type=0为电台音频
		$serial_infos=$BroadcastSerialMap->where($SerialQuery)->select();
		$this->assign('RecentPlayCount',$RecentPlayCount);
		$this->assign('serial_infos',$serial_infos);
		$this->display();
	}

	public function MovieList(){
		//读取cookie
		$RecentPlayCount=count(cookie('MovieRecentPlayList'));

		$MovieSerialMap=M('cloud_course_serial');
		$SerialQuery['type']=1;//type=1为影院视频
		$serial_infos=$MovieSerialMap->where($SerialQuery)->select();
		$this->assign('RecentPlayCount',$RecentPlayCount);
		$this->assign('serial_infos',$serial_infos);
		$this->display();
	}

	public function BrctRecentPlayList(){
		$BroadcastSerialMap=M('cloud_course_serial');

		//读取cookies
		$my_cookies=cookie('BrctRecentPlayList');
		$RecentPlayList=array();
		foreach($my_cookies as $serial_id){
			$SerialQuery['id']=$serial_id;
			$serial_info=$BroadcastSerialMap->where($SerialQuery)->find();
			$RecentPlayList[]=array('serial_id'=>$serial_id,'serial_img'=>$serial_info['img'],'serial_title'=>$serial_info['title']);
		}
		$this->assign('RecentPlayList',$RecentPlayList);
		$this->display();

	}

	public function MovieRecentPlayList(){
		$MovieSerialMap=M('cloud_course_serial');

		//读取cookies
		$my_cookies=cookie('MovieRecentPlayList');
		$RecentPlayList=array();
		foreach($my_cookies as $serial_id){
			$SerialQuery['id']=$serial_id;
			$serial_info=$MovieSerialMap->where($SerialQuery)->find();
			$RecentPlayList[]=array('serial_id'=>$serial_id,'serial_img'=>$serial_info['img'],'serial_title'=>$serial_info['title']);
		}
		$this->assign('RecentPlayList',$RecentPlayList);
		$this->display();

	}

	public function BroadcastPlayer(){
		if($this->isReg()){
			$serial_id=I('get.serial_id');

			//设置cookie
			$RecentPlayList=cookie('BrctRecentPlayList');
			$RecentPlayList[]=$serial_id;
			cookie('BrctRecentPlayList',array_unique(array_reverse($RecentPlayList)),86400);//数组反序再去重，有效期一天

			$CloudCourseMap=D('CloudCourse');
			$EpisodeQuery['serial_id']=$serial_id;
			$episode_list=$CloudCourseMap->field('serial_img,serial_title,episode_id,serial_id,episode_link')->where($EpisodeQuery)->select();
			
			//系列没有音视频时的特殊处理
			if(count($episode_list)==0){
				$SerialMap=M('cloud_course_serial');
				$Serial_info=$SerialMap->find($serial_id);
				$episode_list[]=array('episode_id'=>'','serial_id'=>$serial_id,'episode_link'=>'',
					'serial_img'=>$Serial_info['img'],'serial_title'=>$Serial_info['title']);
			}

			//补足5的倍数
			$remainder_count=5-count($episode_list)%5;
			for($i=0;$i<$remainder_count;$i++){
				$episode_list[]=array('episode_id'=>'','serial_id'=>'','episode_link'=>'','serial_img'=>'','serial_title'=>'');
			}

			$this->assign('episode_list',$episode_list);
			$this->display();
		}
		else{
			//跳转到注册页面
			echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
			echo "<script>alert('尚未注册，请先注册！')</script>";
			$reg_url="https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx7e4fb28fe1443248&redirect_uri=http://www.ebfree.com/huatong/index.php/Phone/Register/register&response_type=code&scope=snsapi_base&state=123&connect_redirect=1#wechat_redirect";
			echo "<script>window.location='$reg_url'</script>";
		}	
	}

	public function MoviePlayer(){
		if($this->isReg()){
			$serial_id=I('get.serial_id');
			//设置cookie
			$RecentPlayList=cookie('MovieRecentPlayList');
			$RecentPlayList[]=$serial_id;
			cookie('MovieRecentPlayList',array_unique(array_reverse($RecentPlayList)),86400);//数组反序再去重，有效期一天

			$CloudCourseMap=D('CloudCourse');
			$EpisodeQuery['serial_id']=$serial_id;
			$episode_list=$CloudCourseMap->field('serial_img,serial_title,episode_id,serial_id,episode_link')->where($EpisodeQuery)->select();

			//系列没有音视频时的特殊处理
			if(count($episode_list)==0){
				$SerialMap=M('cloud_course_serial');
				$Serial_info=$SerialMap->find($serial_id);
				$episode_list[]=array('episode_id'=>'','serial_id'=>$serial_id,'episode_link'=>'',
					'serial_img'=>$Serial_info['img'],'serial_title'=>$Serial_info['title']);
			}

			//补足5的倍数
			$remainder_count=5-count($episode_list)%5;
			for($i=0;$i<$remainder_count;$i++){
				$episode_list[]=array('episode_id'=>'','serial_id'=>'','episode_link'=>'','serial_img'=>'','serial_title'=>'');
			}

			$openid = cookie('openid');
			$this->assign('openid',$openid);
			$this->assign('episode_list',$episode_list);
			$this->display();
		}
		else{
			//跳转到注册页面
			echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
			echo "<script>alert('尚未注册，请先注册！')</script>";
			$reg_url="https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx7e4fb28fe1443248&redirect_uri=http://www.ebfree.com/huatong/index.php/Phone/Register/register&response_type=code&scope=snsapi_base&state=123&connect_redirect=1#wechat_redirect";
			echo "<script>window.location.href='$reg_url'</script>";
		}
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