<?php
namespace Phone\Controller;
use Think\Controller;
class HoneyController extends Controller {
    public function honey(){
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
        // $code = $_GET['code'];//获取code
        // $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=wx7e4fb28fe1443248&secret=521b51fa1c211e00e68fcdd55732b822&code=".$code."&grant_type=authorization_code";
        // $haha = $this->reque_get($url);
        // //echo "code".$code."<br/>";
        // $jsondecode = json_decode($haha);
        // $array = get_object_vars($jsondecode);//转换成数组
        // $openid = $array['openid'];//微信openid

        $appid="wx7e4fb28fe1443248";
        $appsecrect="521b51fa1c211e00e68fcdd55732b822";
        $accessToken = $this->getToken($appid,$appsecrect);
        //echo $accessToken;
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$accessToken."&openid=".$openid;
        $userinfo_json=json_decode($this->curl_get($url),true);
        $wx_nickname = $userinfo_json["nickname"];
        $wx_img = $userinfo_json["headimgurl"];
         //dump($userinfo_json);
        // echo $wx_img;
         //die('ok');
        $mm = M('member');
        $weixin['open_id'] = $openid;
        $member = $mm->where($weixin)->select();

        $nn = M('member_level');
        $yong['member_id'] = $member[0]['id'];
        //$yong['level_id'] = I("get.levelid");
        $member_level1 = $nn->where($yong)->where("success_or = 1")->select();
        //dump($member_level1);
        $task_num = 0;

		//$fp = fopen(__DIR__ . "/honey.txt", "a+");

        $cntui = count($member_level1);

		$memidui = $yong['member_id'];
        // fwrite($fp, "how man ke $cntui memid $memidui \r\n");

        // fclose($fp);

        
    	for($xx=0;$xx<$cntui;$xx++){

			//$fp = fopen(__DIR__ . "/honey.txt", "a+");

            //fwrite($fp, "loop $xx \r\n");

            //fclose($fp);

	        $zzz = M('answer');
	        $kouyu = $zzz->where($yong)->where('type = 1')->order('id asc')->select();
	        $nowtime = time();
	        $baomingtime = strtotime(date("Y-m-d ", $member_level1[$xx]['time'])) + 3600*24;//报名完当天晚上凌晨的时间戳
	        // echo $baomingtime;
	        // die("KO");
	        $geshu = floor(($nowtime - $baomingtime) / 86400);//多少个两天(172800秒)
	        if($geshu < 0){
	            $geshu = 0;
	        }
	        $tianshu = ($geshu+1)*2;

            //$fp = fopen(__DIR__ . "/honey.txt", "a+");

            //fwrite($fp, "level loop ". "loop " . $xx . " " . $member[0]['id'] . " " . $member_level1[$xx]['level_id'] . " " . $mission_start_num . " *** " . count($mission_start) . " " .$tianshu . " " . $baomingtime . "baoming " . $baomingtime . " geshu " . $geshu . "tianshu " . $tianshu . "\r\n");

            //fclose($fp);

	        // echo $tianshu;
	        // die("LL");
	        $m = M('level');
	        //$levelid = I("get.levelid");
	        $map['id'] = $member_level1[$xx]['level_id'];
	          //echo  $levelid;
	        //  die('OK');
	        $level = $m->where($map)->select();
	        $n = M('course');
	        $course_name['id'] = $level[0]['course_id'];
	        $course = $n->where($course_name)->select();
	        $n = D('MissionAnswer');
	        //$mapp['level_id'] = $levelid;
			//vip
			$mapp['level_id'] = $member_level1[$xx]['level_id'];;
	        $mapp['member_id'] = $member_level1[$xx]['member_id'];
	        $m = M('member_level');
	        $member_level = $m->where($mapp)->select();
	        $mission_start11 = $n->where($mapp)->where("type = 0")->group('mission_id')->order('mission_id asc')->select();//查询总共多少个任务
	        //dump($mission_start11);
           
           

            //
	        if($tianshu != 0){

	            $mission_start = $n->where($mapp)->where("type = 0")->group('mission_id')->limit($tianshu)->order('mission_id asc')->select();//解锁的
                //dump($mission_start);
	            /*****判断如果超两天没有回答start******/
	            if(count($mission_start) % 2 == 0){
	                $mission_start_num = count($mission_start)-2;
	            }else{
	                $mission_start_num = count($mission_start)-1;
	            }
                //$fp = fopen(__DIR__ . "/honey.txt", "a+");

                //fwrite($fp, "ok pay ". "loop " . $xx . $member[0]['id'] . $member_level1[$xx]['level_id'] . $mission_start_num . " *** " . count($mission_start) . " " .$tianshu . " " . $baomingtime ."\r\n");

                //fclose($fp);
	            $data['success'] = '3';
	            $cc = 0;
	            for($i=0;$i<$mission_start_num;$i++){
	                if($mission_start[$i]['success'] == NULL){
	                    $mission_start[$i]['success'] = 3;

						

                        //$fp = fopen(__DIR__ . "/honey1.txt", "a+");

                //fwrite($fp, "ok pay ". $member[0]['id'] . " " . $member_level1[$xx]['level_id'] . " " . $mission_start_num . " *** " . count($mission_start) . " " .$tianshu . " " . $baomingtime ."\r\n");

                //fclose($fp);
	                    $nn = M('answer');
		        		$map_answer['id'] = $mission_start[$i]['id'];

						//$fp = fopen(__DIR__ . "/honey.txt", "a+");

						$mapiddd = $map_answer['id'];

                        //fwrite($fp, "will set an id  $mapiddd \r\n");

                         //fclose($fp);
		        		$nn->data($data)->where($map_answer)->save();
		        		$cc++;
	                }
	            }
	            /*if($cc){
	                $nn = M('answer');
	            $nn->data($data)->where($mapp)->where("type = 0")->group('mission_id')->where('success is Null')->limit($cc)->save();
	            }*/
	             
	            //全部任务都解锁后判断有没有没回答的
	            $cha = time() - $baomingtime;
	            $zou = floor(count($mission_start11)/2);
	            if(($zou+1)*86400 < $cha ){//count($mission_start)
	                for($i=0;$i<count($mission_start11);$i++){
	                    if($mission_start[$i]['success'] == NULL){
	                         $mission_start[$i]['success'] = 3;
                             //$fp = fopen(__DIR__ . "/honey2.txt", "a+");

                //fwrite($fp, "ok pay ". $member[0]['id'] . " " . $member_level1[$xx]['level_id'] . " " . $mission_start_num . " *** " . count($mission_start) . " " .$tianshu . " " . $baomingtime ."\r\n");

                //fclose($fp);
	                        // $kk=1;
	                        $nn = M('answer');
		        			$map_answer['id'] = $mission_start[$i]['id'];


							//$fp = fopen(__DIR__ . "/honey.txt", "a+");

						$mapiddd = $map_answer['id'];

                        //fwrite($fp, "will set an id  $mapiddd \r\n");

                         //fclose($fp);


	        				$nn->data($data)->where($map_answer)->save();
	                    }
	                }
	            }
	            // if($kk == 1){
	            //     $nn->data($data)->where($mapp)->where("type = 0")->group('mission_id')->where('success is Null')->limit($cc)->save();
	            // }
	            /*****判断如果超两天没有回答end******/
	        }
	        //$mission_no = $n->where($mapp)->where("type = 0")->group('mission_id')->limit($tianshu,100)->order('mission_id asc')->select();//未解锁的
	            // dump($mission_start);
	            // die("OK");
	        // dump($mission_no);
	        // die("j");
	        // $level[$xx]["parent_step"] = htmlspecialchars_decode($level[$xx]["parent_step"]);
	        // $level[$xx]["parent_aim"] = htmlspecialchars_decode($level[$xx]["parent_aim"]);
	        // if(count($mission_start) == 0){
	        //     $this->assign('change',1);
	        // }else{
	        //     if(cookie('start_num')){
	        //         $start_num = count($mission_start);
	        //         if(cookie('start_num') == $start_num){
	        //             $this->assign('change',1);
	        //         }else{
	        //             $this->assign('change',2);
	        //         }
	        //     }else{
	        //         $start_num = count($mission_start);
	        //         cookie('start_num',$start_num,259200);
	        //         $this->assign('change',2);
	        //     }  
	        // }
	        $task_num = $task_num + count($mission_start);
            //echo $task_num."<br/>";
    	}

    	if($task_num == cookie('start_num')){
    		$this->assign('change',1);
    	}else{
    		$this->assign('change',2);
    	}
        cookie('start_num',$task_num,259200);
        //echo cookie('start_num');
        //die();
        //cookie('start_num',null);
        // echo count($mission_start)."<br/>";
        // echo cookie('start_num');
        // die();


        // $this->assign("kouyu",$kouyu);
        // $this->assign("course",$course);
        // $this->assign("level",$level);
        // $this->assign("mission_start",$mission_start);
        // $this->assign("mission_no",$mission_no);
        

        /*************************级别的判断  start**************************/
        $m = M('member');
        $mapxx['open_id'] = $openid;
        $memberxx = $m->where($mapxx)->limit(1)->find();
        $map1xx['referee_phone'] = $memberxx['phone'];
        $zhucexx = $m->where($map1xx)->select();//一级注册
        //dump($zhuce);
        $onexx = count($zhucexx);
        //遍历一级盟友
        $ta = 0;
        for($i=0;$i<$onexx;$i++){
            $n = M('member_level');
            $map2xx['member_id'] = $zhucexx[$i]['id'];
            $map2xx['success_or'] = 1;
            $onememberxx = $n->where($map2xx)->select();
            //遍历该一级盟友报名成功的课程
            for ($jy = 0; $jy < count($onememberxx); $jy++)
            {
                $baomingtime = strtotime(date("Y-m-d ", $onememberxx[$jy]['time'])) + 3600*24 + 3600*24*1;//报名完当天晚上凌晨之后三天的时间戳
                if(time() > $baomingtime){
                    $ta++;//报名课程(没有退课的)的总数
                }

            } 
        }
        /*************************级别的判断  end**************************/
        
         
        $this->assign("ta",$ta);
        $this->assign("member_id",$member[0]['id']);
        $this->assign("openid",$openid);
        $this->assign("wx_nickname",$wx_nickname);
        $this->assign("wx_img",$wx_img);
		$this->display();
    }

    public function youhui(){
        $d = D('Coupon');
        $map['open_id'] = I('get.open_id');
        $coupon = $d->where($map)->select();
        foreach ($coupon as $key => $value) {
            if ($value['status'] == 2) {
                $coupon_consume[] = $value;//已使用
            }else if ((int)$value['begin_timestamp'] > time()) {
                $coupon_before[] = $value;//未开始
            }else if ((int)$value['end_timestamp'] < time()) {
                $coupon_after[] = $value;//已过期
            }else{
                $coupon_valid[] = $value;//可使用
            }
        }
        // dump($coupon);
        $openid = I('get.open_id');
        $appid="wx7e4fb28fe1443248";
        $appsecrect="521b51fa1c211e00e68fcdd55732b822";
        $accessToken = $this->getToken($appid,$appsecrect);
        //echo $accessToken;
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$accessToken."&openid=".$openid;
        $userinfo_json=json_decode($this->curl_get($url),true);
        $wx_nickname = $userinfo_json["nickname"];
        $wx_img = $userinfo_json["headimgurl"];
        $choose = I('get.choose');//choose  如果从我的蜂巢进来，choose值为2，如果从报名课程过来选取的话，choose值为空
        $this->assign('choose',$choose);
        $this->assign('wx_img',$wx_img);
        $this->assign('wx_nickname',$wx_nickname);
        $this->assign('coupon_consume',$coupon_consume);
        $this->assign('coupon_before',$coupon_before);
        $this->assign('coupon_after',$coupon_after);
        $this->assign('coupon_valid',$coupon_valid);
        $this->display();
    }

//查看卡券 王超 20160426
    public function getSignpackage(){

    }

        //查看卡券 王超 20160427
    public function coupon_open()
    {
        $card_id=I('get.card_id');
        $code=I('get.code');

        $jssdk = new \Vendor\Wechat\Jssdk(C('WECHAT_CONFIG'));

        $access_token = S('WX_ACCESS_TOKEN');
        if (!$access_token) {
            $access_token = $jssdk->getToken();
            S('WX_ACCESS_TOKEN', $access_token, 7000);
        }
        $jssdk->access_token = $access_token;
        
        $jsapiTicket = $jssdk->getJsapiTicket();
        $url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $timestamp = time();
        $nonceStr = "Good_Morning";

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

        $signature = sha1($string);

        $signPackage = array(
            "appId"     => "wx7e4fb28fe1443248",
            "nonceStr"  => $nonceStr,
            "timestamp" => $timestamp,
            "url"       => $url,
            "signature" => $signature,
            "rawString" => $string
        );

        //dump($string);
        //die('Good_Morning');

        $this->assign('signPackage',$signPackage);
        $this->assign('card_id',$card_id);
        $this->assign('code',$code);
        $this->display();

    }

    public function dashang(){
    	if(I('get.serial_id')){//影院打赏
    		$m = M('member');
    		$map['open_id'] = I('get.openid');
    		$member = $m->where($map)->limit(1)->find();
    		$this->assign('serial_id',I('get.serial_id'));
    		$this->assign('member_id',$member['id']);
    		$this->display();
    	}else if(I('get.mission_id')){//课程打赏
    		$this->assign('member_id',I('get.member_id'));
	        $this->assign('mission_id',I('get.mission_id'));
	    	$this->display();
    	}else{
    		echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
        	echo  "<script>alert('参数错误，请重试！');window.location.href='https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx7e4fb28fe1443248&redirect_uri=http://www.ebfree.com/huatong/index.php/Phone/Index/index&response_type=code&scope=snsapi_base&state=123#wechat_redirect'</script>";
    	}
    }
    public function dashang_update(){
   		$post = "http://www.ebfree.com/huatong/index.php/Phone/MsWxpay/js_api_cal?money=".I('post.money')."&member_id=".I('post.member_id')."&mission_id=".I('post.mission_id')."&serial_id=".I('post.serial_id');
     	header ( "location:$post" );
    }
     
    public function rule(){
    	$openid = I('get.openid');
        $m = M('course');
        $map['type'] = '1';
        $mapp['type'] = '2';
        $shaoer = $m->where($map)->select();
        $adult = $m->where($mapp)->select();
        $this->assign("adult",$adult);
        $this->assign("openid",$openid);
        $this->assign("shaoer",$shaoer);
    	$this->display();
    }

    public function task_one(){//列出所有课程（已报名的靠前）
    	$openid = I('get.openid');
        $m = D('LevelMember');
        $map['open_id'] = $openid;
        $map['success_or'] = '1';
        $course_ok = $m->where($map)->field('course_id')->select();
        $course_okok = $m->where($map)->select();//已经缴费的课程
        //dump($course_okok);
        /*任务完成一个月后课程不再显示start*/
        for($i=0;$i<count($course_okok);$i++){
            $baomingtime = strtotime(date("Y-m-d ", $course_okok[$i]['time'])) + 3600*24;//报名完当天晚上凌晨的时间戳
            $m = M('mission');
            $mapp['level_id'] = $course_okok[$i]['level_id'];
            $mission_count = $m->where($mapp)->select();
            $mission_shu = count($mission_count);//购买完后多少天结束课程（有多少个任务多少天结束）
            $endtime = $baomingtime + $mission_shu*86400 + 30*86400;//任务完成后一个月的时间戳
            if($endtime >time()){
                $course_oko[] = $course_okok[$i];
            }
        }
        /*任务完成一个月后课程不再显示end*/
        
        for($i=0;$i<count($course_ok);$i++){
        	$arr1[] = $course_ok[$i]['course_id'];
        }

        $n = M('course');
        $course = $n->field('id')->select();//全部课程
        for($i=0;$i<count($course);$i++){
        	$arr2[] = $course[$i]['id'];
        }
        
        if(count($arr1)){//如果一门课程都没报名的话，加以判断
            $res = array_diff($arr2,$arr1);
        }else{
            $res = $arr2;
        }
        
        $maap['id'] = array('in',$res);
        $course_hui = $n->where($maap)->select();//未报名的课程
        $pan = count($course_oko) % 2;//判断已报名课程的单双数
        $this->assign("pan",$pan);
        $this->assign("course_hui",$course_hui);
        $this->assign("course_okok",$course_oko);
        $this->assign("openid",$openid);
        $this->display();
    }

    public function task(){//我的任务
    	$openid = I('get.openid');
    	$mm = M('member');
    	$weixin['open_id'] = $openid;
    	$member = $mm->where($weixin)->select();

    	$nn = M('member_level');
    	$yong['member_id'] = $member[0]['id'];
    	$yong['level_id'] = I("get.levelid");
    	$member_level = $nn->where($yong)->where("success_or = 1")->select();
    	$zzz = M('answer');
    	$kouyu = $zzz->where($yong)->where('type = 1')->order('id asc')->select();
    	$nowtime = time();
    	$baomingtime = strtotime(date("Y-m-d ", $member_level[0]['time'])) + 3600*24;//报名完当天晚上凌晨的时间戳
    	// echo $baomingtime;
    	// die("KO");
    	$geshu = floor(($nowtime - $baomingtime) / 86400);//多少个两天(172800秒)
    	if($geshu < 0){
    		$geshu = 0;
    	}
    	$tianshu = ($geshu+1)*2;
    	// echo $tianshu;
    	// die("LL");
        $m = M('level');
        $levelid = I("get.levelid");
        $map['id'] = $levelid;
          //echo  $levelid;
        //  die('OK');
        $level = $m->where($map)->select();
        $n = M('course');
        $course_name['id'] = $level[0]['course_id'];
        $course = $n->where($course_name)->select();
        $n = D('MissionAnswer');
        $mapp['level_id'] = $levelid;
        $mapp['member_id'] = $member[0]['id'];
        $m = M('member_level');
        $member_level = $m->where($mapp)->select();
        $mission_start11 = $n->where($mapp)->where("type = 0")->group('mission_id')->order('mission_id asc')->select();//查询总共多少个任务
        //dump($mission_start11);
        if($tianshu != 0){
        	$mission_start = $n->where($mapp)->where("type = 0")->group('mission_id')->limit($tianshu)->order('mission_id asc')->select();//解锁的
        	/*****判断如果超两天没有回答start******/
        	if(count($mission_start) % 2 == 0){
        		$mission_start_num = count($mission_start)-2;
        	}else{
        		$mission_start_num = count($mission_start)-1;
        	}
        	$data['success'] = '3';
        	$cc = 0;
        	for($i=0;$i<$mission_start_num;$i++){
        		if($mission_start[$i]['success'] == NULL){
        			$mission_start[$i]['success'] = 3;
        			$nn = M('answer');
	        		$map_answer['id'] = $mission_start[$i]['id'];
	        		$nn->data($data)->where($map_answer)->save();
	        		$cc++;
        		}
        	}
        	/*if($cc){
        		$nn = M('answer');
        	$nn->data($data)->where($mapp)->where("type = 0")->group('mission_id')->where('success is Null')->order('mission_id asc')->limit($cc)->save();
        	}*/
        	 
        	//全部任务都解锁后判断有没有没回答的
        	$cha = time() - $baomingtime;
        	$zou = floor(count($mission_start11)/2);
        	if(($zou+1)*86400 < $cha ){//count($mission_start)
        		for($i=0;$i<count($mission_start11);$i++){
        			if($mission_start[$i]['success'] == NULL){
        				 $mission_start[$i]['success'] = 3;
        				// $kk=1;
        				$nn = M('answer');
	        			$map_answer['id'] = $mission_start[$i]['id'];
        				$nn->data($data)->where($map_answer)->save();

        			}
        		}
        	}
        	/*if($kk == 1){
        		$nn->data($data)->where($mapp)->where("type = 0")->group('mission_id')->where('success is Null')->limit($cc)->save();
        	}*/
        	/*****判断如果超两天没有回答end******/
        }
        $mission_no = $n->where($mapp)->where("type = 0")->group('mission_id')->limit($tianshu,100)->order('mission_id asc')->select();//未解锁的
            // dump($mission_start);
            // die("OK");
        // dump($mission_no);
        // die("j");
        $level[0]["parent_step"] = htmlspecialchars_decode($level[0]["parent_step"]);
        $level[0]["parent_aim"] = htmlspecialchars_decode($level[0]["parent_aim"]);

        $this->assign("kouyu",$kouyu);
        $this->assign("course",$course);
        $this->assign("level",$level);
        $this->assign("mission_start",$mission_start);
        $this->assign("mission_no",$mission_no);
        $this->assign("member_id",$member[0]['id']);
        //  dump($mission_start);
        // die();
        // dump($level);
        // die('OK');
        $this->display();
    }

    public function task_detail(){//我的任务
    	$member_id = I('get.member_id');
        $m = M('mission');
        $mission_id = I("get.mission_id");
        $map['id'] = $mission_id;
         // echo  $mission_id;
        $mission = $m->where($map)->select();
        // $mission[0]["words"] = htmlspecialchars_decode($mission[0]["words"]);
        // $mission[0]["sentences"] = htmlspecialchars_decode($mission[0]["sentences"]);
        if(I('get.jie')){
        	$this->assign("jie",I('get.jie'));
        }
        $n = M('member');
        $mapp['id'] = $member_id;
        $op = $n->where($mapp)->limit(1)->find();
        $this->assign("openid",$op['open_id']);
        $this->assign("mission",$mission);
        $this->assign("member_id",$member_id);
        $this->display();
    }

    public function task_detail_update(){//我的任务
        //dump(I('post.'));
        $mission_id = I('post.mission_id');
        $m = M('mission');
        $map['id'] = $mission_id;
        $mission = $m->where($map)->limit(1)->select();
        //dump($mission);
        //
        $n = M('answer');
        $mapp['member_id'] = I('post.member_id');
        $mapp['mission_id'] = I('post.mission_id');
        $answer = $n->where($mapp)->select();
        if($answer[0]['success'] != ""){

			//Haiti modify

        	echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
			/*
        	echo  "<script>alert('请勿重复作答！');window.location.href='https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx7e4fb28fe1443248&redirect_uri=http://www.ebfree.com/huatong/index.php/Phone/Honey/honey&response_type=code&scope=snsapi_base&state=123#wechat_redirect'</script>"; */

			echo  "<script>window.location.href='https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx7e4fb28fe1443248&redirect_uri=http://www.ebfree.com/huatong/index.php/Phone/Honey/honey&response_type=code&scope=snsapi_base&state=123#wechat_redirect'</script>";

        }else{
        	if(I('post.answer') == $mission[0]['answer'] ){
        		$data['success'] = 1;
                $data['time_finish'] = time();
        		$n->where($mapp)->where('type = 0')->save($data);
        		echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
        		echo  "<script>alert('恭喜你回答正确！');window.location.href='https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx7e4fb28fe1443248&redirect_uri=http://www.ebfree.com/huatong/index.php/Phone/Honey/honey&response_type=code&scope=snsapi_base&state=123#wechat_redirect'</script>";
        	}else{
        		$data['success'] = 0;
                $data['time_finish'] = time();
        		$n->where($mapp)->where('type = 0')->save($data);
        		echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
        		echo  "<script>alert('很抱歉，回答错误！');window.location.href='https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx7e4fb28fe1443248&redirect_uri=http://www.ebfree.com/huatong/index.php/Phone/Honey/honey&response_type=code&scope=snsapi_base&state=123#wechat_redirect'</script>";
        	}
        }
    }

    public function red_packet(){ //我的红包
    	$openid = I('get.openid');
    	$m = M('member');
    	$map['open_id'] = $openid;
    	$member = $m->where($map)->find();
    	//dump($member);
        $appid="wx7e4fb28fe1443248";
        $appsecrect="521b51fa1c211e00e68fcdd55732b822";
        $accessToken = $this->getToken($appid,$appsecrect);
        //echo $accessToken;
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$accessToken."&openid=".$openid;
        $userinfo_json=json_decode($this->curl_get($url),true);
        $wx_nickname = $userinfo_json["nickname"];
        $wx_img = $userinfo_json["headimgurl"];

        $n = M('member_level');
        // $mapp['member_id='] = $member["id"];
        // $mapp['success_or'] = '1';
        //dump($mapp);
        //$member_level = $n->where($mapp)->select();
        $member_lev = $n->where("member_id=%d and success_or='%s'",array($member["id"],'1'))->select();
        // dump($member_lev);
        // echo count($member_lev)."<br/>";
       for($xx=0;$xx<count($member_lev);$xx++){
       		$zzz = M('answer');
	        $yong['member_id'] = $member_lev[$xx]["member_id"];
	    	$yong['level_id'] = $member_lev[$xx]["level_id"];
	    	//echo $member_lev[$xx]["level_id"];
	    	$kouyu = $zzz->where($yong)->where('type = 1')->order('id asc')->select();
	    	//dump($kouyu);
	    	$nowtime = time();
	    	$baomingtime = strtotime(date("Y-m-d ", $member_lev[$xx]['time'])) + 3600*24;//报名完当天晚上凌晨的时间戳
	    	// echo $baomingtime;
	    	// die("KO");
	    	$geshu = floor(($nowtime - $baomingtime) / 86400);//多少个两天(172800秒)
	    	if($geshu < 0){
	    		$geshu = 0;
	    	}
	    	$tianshu = ($geshu+1)*2;
	    	// echo $tianshu;
	    	// die("LL");
	        $m = M('level');
	        $levelid = $member_lev[$xx]["level_id"];
	        $map['id'] = $levelid;
	          //echo  $levelid;
	        //  die('OK');
	        $level = $m->where($map)->select();
	        $n = M('course');
	        $course_name['id'] = $level[0]['course_id'];
	        $course = $n->where($course_name)->select();
	        $n = D('MissionAnswer');
	        $mapp['level_id'] = $levelid;
	        $mapp['member_id'] = $member_lev[$xx]["member_id"];
	        $m = M('member_level');
	        $member_level = $m->where($mapp)->select();
	        $mission_start11 = $n->where($mapp)->where("type = 0")->group('mission_id')->order('mission_id asc')->select();//查询总共多少个任务
	        //dump($mission_start11);
	        if($tianshu != 0){
	        	$mission_start = $n->where($mapp)->where("type = 0")->group('mission_id')->limit($tianshu)->order('mission_id asc')->select();//解锁的
	     		 /*****判断如果超两天没有回答start******/
	        	if(count($mission_start) % 2 == 0){
	        		$mission_start_num = count($mission_start)-2;
	        	}else{
	        		$mission_start_num = count($mission_start)-1;
	        	}
	        	$data['success'] = '3';
	        	$cc = 0;
	        	for($i=0;$i<$mission_start_num;$i++){
	        		if($mission_start[$i]['success'] == NULL){
	        			$mission_start[$i]['success'] = 3;
	        			$nn = M('answer');
	        			$map_answer['id'] = $mission_start[$i]['id'];
	        			$nn->data($data)->where($map_answer)->save();
	        			$cc++;
	        		}
	        	}
	        	/*if($cc){
	        		$nn = M('answer');
	        	$nn->data($data)->where($mapp)->where("type = 0")->group('mission_id')->where('success is Null')->limit($cc)->save();
	        	}*/
	        	//全部任务都解锁后判断有没有没回答的
	        	$cha = time() - $baomingtime;
	        	$zou = floor(count($mission_start11)/2);
	        	if(($zou+1)*86400 < $cha ){//count($mission_start)
	        		for($i=0;$i<count($mission_start11);$i++){
	        			if($mission_start[$i]['success'] == NULL){
	        				$mission_start[$i]['success'] = 3;
	        				// $kk=1;
	        				$nn = M('answer');
	        				$map_answer['id'] = $mission_start[$i]['id'];
        					$nn->data($data)->where($map_answer)->save();
	        			}
	        			$allover[] = $mission_start[$i]['level_id'];
	        		}
	        	}
	        	/*if($kk == 1){
	        		$nn = M('answer');
	        		$data['success'] = 3;
	        		$nn->data($data)->where($mapp)->where("type = 0")->group('mission_id')->where('success is Null')->limit($cc)->save();
	        	}*/
	        	/*****判断如果超两天没有回答end******/
	        }
	        
	       }
	       //统计哪几门课程已经完成$bb
	       $aa = array_unique($allover);
	       for($i=0;$i<count($allover);$i++){
	       	if($aa[$i]){
	       		$bb[]=$aa[$i];
	       	}
	       }
	       //
	       //找到要退款的课程$abc 并退款
	       	for($i=0;$i<count($bb);$i++){
	       		$jj = 0;
	       		$m = M('answer');
	       		$mama['level_id'] = $bb[$i];
	       		$mama['member_id'] = $member_lev[0]["member_id"];
	       		$answ = $m->where($mama)->select();
	       		for($j=0;$j<count($answ);$j++){
	       			if(($answ[$j]['success'] == '0') || ($answ[$j]['success'] == '3')){
	       				$jj++;
	       			}
	       		}
	       		if($jj<2){//错误个数小于2个
	       			$cc = M('member_level');
	       			$memb_le = $cc->where($mama)->where('success_or = 1')->select();
	       			$mc = M('member');
	       			$mem = $mc->where('id='.$member_lev[0]["member_id"])->select();
	       			$daa['red_packet'] = $mem[0]['red_packet'] + $memb_le[0]['user_pay'];
	       			$mc->where('id='.$member_lev[0]["member_id"])->data($daa)->save();
                    //插入明细
                    $monn = M('detail');
                    $detail_data['money'] = $memb_le[0]['user_pay'];
                    $detail_data['remarks'] = "押金退回";
                    $detail_data['time'] = time();
                    $detail_data['member_id'] = $member_lev[0]["member_id"];
                    $detail_data['member_level_id'] = $member_lev[$xx]["id"];
                    $detail_data['red_total'] = $mem[0]['red_packet'];
                    $monn->data($detail_data)->add();
	       			$daaa['user_pay'] = 0;
	       			$cc->where($mama)->where('success_or = 1')->data($daaa)->save();
	       		}
	       		//dump($answ);
	       	}




        $openid = I('get.openid');
        $m = M('member');
        $mapx['open_id'] = $openid;
        $member = $m->where($mapx)->limit(1)->find();
        $map1['referee_phone'] = $member['phone'];
        $zhuce = $m->where($map1)->select();//一级注册
        //dump($zhuce);
        /********************盟友start************************/

        $one = count($zhuce);

        $a = 0;
		$aa = array();
		$b = 0;
		$bb = array();
		$c = 0;
		$cc = array();

		$add20a = 0;
		$add20b = 0;
		$add20c = 0;

		$t20a = 0;
		$t20b = 0;
		$t20c = 0;

		// echo "yiji mengyou <br>";
		// dump($zhuce);
		// echo "yiji over <br>";

		//遍历一级盟友
        for($i=0;$i<$one;$i++){
        	$n = M('member_level');
        	$map2['member_id'] = $zhuce[$i]['id'];
        	$map2['success_or'] = 1;
        	$onemember = $n->where($map2)->select();
        	
        	// echo "loop $i <br>";
        	// dump($onemember);
        	// echo "loop $i ofver <br>";
            $flag = 0;
            //遍历该一级盟友报名成功的课程，计算是否给20
			for ($jy = 0; $jy < count($onemember); $jy++)
		    {
			    $baomingtime = strtotime(date("Y-m-d ", $onemember[$jy]['time'])) + 3600*24 + 3600*24*1;//报名完当天晚上凌晨之后三天的时间戳
                if(time() > $baomingtime){
            	    $aaa = explode(",",$onemember[$jy]['referee_member_id']);
            	    //dump($aaa);
                    if(in_array($member['id'], $aaa)){

                    }else{
                        $onemap['id'] = $onemember[$jy]['id'];
                        $onedate['referee_member_id'] = $onemember[$jy]['referee_member_id'] . "," .$member['id'];
                        $n->where($onemap)->data($onedate)->save();
                        $aa[] = $onemember[$jy]; //该一级盟友需要增加20的课程
						$add20a++;
                    }

					$flag = 1;
					$t20a++;
                }

			} //遍历该一级盟友报名成功的课程，计算是否给20结束

			if ($flag == 1)
			{
                $a++;
			}
			$flag = 0;
			$map2grade['referee_phone'] = $zhuce[$i]['phone'];
            $zhuce2grade = $m->where($map2grade)->select();//改一级注册会员的二级注册会员
            // echo "erji mengyou <br>";
            // dump($zhuce2grade);
            // echo "erji menguyou over <br>";
            //遍历该一级盟友的二级盟友
			for ($ty = 0; $ty < count($zhuce2grade); $ty++)
			{
                $n = M('member_level');
        	    $map2['member_id'] = $zhuce2grade[$ty]['id'];
        	    $map2['success_or'] = 1;
        	    $course2 = $n->where($map2)->select();
        	    // echo "loop ty $ty <br>";
        	    // dump($course2);
        	    // echo "loop ty $ty over <br>";
                $flag = 0;

                //遍历二级盟友报名成功的课程，计算是否给20
			    for ($py = 0; $py < count($course2); $py++)
		        {
			        $baomingtime = strtotime(date("Y-m-d ", $course2[$py]['time'])) + 3600*24 + 3600*24*1;//报名完当天晚上凌晨之后三天的时间戳
                    if(time() > $baomingtime){
            	        $aaa = explode(",",$course2[$py]['referee_member_id']);
            	        //dump($aaa);
                        if(in_array($member['id'], $aaa)){

                        }else{
                            $onemap['id'] = $course2[$py]['id'];
                            $onedate['referee_member_id'] = $course2[$py]['referee_member_id'] . "," .$member['id'];
                            $n->where($onemap)->data($onedate)->save();
                            $bb[] = $course2[$py]; //该二级盟友需要增加20的课程
							$add20b++;
                        }

						$flag = 1;
						$t20b++;
                    }

			     }
				 //遍历二级盟友报名成功的课程，计算是否给20结束

				 if ($flag == 1)
				 {
					 $b++;
				 }
				 $flag = 0;
				 $map3grade['referee_phone'] = $zhuce2grade[$ty]['phone'];
                 $zhuce3grade = $m->where($map3grade)->select();//改二级注册会员的三级注册会员
                 // echo "sanji meng <br>";
                 // dump($zhuce3grade);
                 // echo "sanji meng over <br>";
                 //遍历该二级盟友的三级盟友
				 for ($my = 0; $my < count($zhuce3grade); $my++)
			     {
                     $n = M('member_level');
        	         $map2['member_id'] = $zhuce3grade[$my]['id'];
        	         $map2['success_or'] = 1;
        	         $course3 = $n->where($map2)->select();
        	         // echo "loop my $my <br>";
        	         // dump($course3);
        	         // echo "loop my $my over <br>";
				     $flag = 0;

                     //遍历该三级盟友报名成功的课程，计算是否给20
			         for ($oy = 0; $oy < count($course3); $oy++)
		             {
			             $baomingtime = strtotime(date("Y-m-d ", $course3[$oy]['time'])) + 3600*24 + 3600*24*1;//报名完当天晚上凌晨之后三天的时间戳
                         if(time() > $baomingtime){
            	             $aaa = explode(",",$course3[$oy]['referee_member_id']);
            	             //dump($aaa);
                             if(in_array($member['id'], $aaa)){

                             }else{
                                $onemap['id'] = $course3[$oy]['id'];
                                $onedate['referee_member_id'] = $course3[$oy]['referee_member_id'] . "," .$member['id'];
                                $n->where($onemap)->data($onedate)->save();
                                $cc[] = $course3[$oy]; //该三级盟友需要增加20的课程
								$add20c++;
                             }

							 $flag = 1;
							 $t20c++;
					     }
                     } //遍历该三级盟友报名成功的课程，计算是否给20结束

					 if ($flag == 1)
					 {
						 $c++;
					 }

			     } //遍历该二级盟友的三级盟友结束
                 
              }//遍历该一级盟友的二级盟友结束

           }//遍历一级盟友结束
       

   //          echo " $a  $b $c <br>";
			// echo " $t20a  $t20b $t20c <br>";
			// echo " $add20a  $add20b  $add20c <br>";

			// die();
		
        $mengyou = $a + $b + $c;//一二三级盟友人数总和
        /********************盟友end*************************/
        $tui_money = count($zhuce);
        $xue_money = ($t20a + $t20b + $t20c)* 20;
        $all_money = $tui_money + $xue_money;
        $allm = M('member');
        $allmap['id'] = $member['id'];

		//dump($allmap);

        $alldata['red_packet'] = $member['red_packet'] + ($add20a + $add20b + $add20c)*20;
        // dump($alldata);
        // die();
		//dump($alldata);
        $allm->where($allmap)->data($alldata)->save();

        //插入明细
        $all_money = ($add20a + $add20b + $add20c)*20;
        if($all_money != 0){
            $monn = M('detail');
            $detail_data['money'] = $all_money;
            $detail_data['remarks'] = "学费奖金";
            $detail_data['time'] = time();
            $detail_data['member_id'] = $member['id'];
            $monn->data($detail_data)->add();
        }


        $endmember = $allm->where($allmap)->limit(1)->find();
			
  
        //die();

        $this->assign("red_packet",$endmember['red_packet']);
        $this->assign("member",$member);
        $this->assign("openid",$openid);
        $this->assign("wx_nickname",$wx_nickname);
        $this->assign("wx_img",$wx_img);
    	$this->display();
    }


    public function red_packet_detail(){ //红包详情
    	$openid = I('get.openid');
        $m = M('member');
        $mapx['open_id'] = $openid;
        $member = $m->where($mapx)->limit(1)->find();
        $map1['referee_phone'] = $member['phone'];
        $zhuce = $m->where($map1)->select();//一级注册
        //dump($zhuce);
        /********************盟友start************************/

        $one = count($zhuce);

        $a = 0;
		$aa = array();
		$b = 0;
		$bb = array();
		$c = 0;
		$cc = array();

		$add20a = 0;
		$add20b = 0;
		$add20c = 0;

		$t20a = 0;
		$t20b = 0;
		$t20c = 0;

		//遍历一级盟友
        for($i=0;$i<$one;$i++){
        	$n = M('member_level');
        	$map2['member_id'] = $zhuce[$i]['id'];
        	$map2['success_or'] = 1;
        	$onemember = $n->where($map2)->select();
        	

            $flag = 0;
            //遍历该一级盟友报名成功的课程，计算是否给20
			for ($jy = 0; $jy < count($onemember); $jy++)
		    {
			    $baomingtime = strtotime(date("Y-m-d ", $onemember[$jy]['time'])) + 3600*24 + 3600*24*1;//报名完当天晚上凌晨之后三天的时间戳
                if(time() > $baomingtime){
            	    $aaa = explode(",",$onemember[$jy]['referee_member_id']);
            	    //dump($aaa);
                    if(in_array($member['id'], $aaa)){

                    }else{
                        $onemap['id'] = $onemember[$jy]['id'];
                        $onedate['referee_member_id'] = $onemember[$jy]['referee_member_id'] . "," .$member['id'];
                        $n->where($onemap)->data($onedate)->save();
                        $aa[] = $onemember[$jy]; //该一级盟友需要增加20的课程
						$add20a++;
                    }

					$flag = 1;
					$t20a++;
                }

			} //遍历该一级盟友报名成功的课程，计算是否给20结束

			if ($flag == 1)
			{
                $a++;
			}

			$map2grade['referee_phone'] = $zhuce[$i]['phone'];
            $zhuce2grade = $m->where($map2grade)->select();//改一级注册会员的二级注册会员

            //遍历该一级盟友的二级盟友
			for ($ty = 0; $ty < count($zhuce2grade); $ty++)
			{
                $n = M('member_level');
        	    $map2['member_id'] = $zhuce2grade[$ty]['id'];
        	    $map2['success_or'] = 1;
        	    $course2 = $n->where($map2)->select();
        	    
                $flag = 0;

                //遍历二级盟友报名成功的课程，计算是否给20
			    for ($py = 0; $py < count($course2); $py++)
		        {
			        $baomingtime = strtotime(date("Y-m-d ", $course2[$py]['time'])) + 3600*24 + 3600*24*1;//报名完当天晚上凌晨之后三天的时间戳
                    if(time() > $baomingtime){
            	        $aaa = explode(",",$course2[$py]['referee_member_id']);
            	        //dump($aaa);
                        if(in_array($member['id'], $aaa)){

                        }else{
                            $onemap['id'] = $course2[$py]['id'];
                            $onedate['referee_member_id'] = $course2[$py]['referee_member_id'] . "," .$member['id'];
                            $n->where($onemap)->data($onedate)->save();
                            $bb[] = $course2[$py]; //该二级盟友需要增加20的课程
							$add20b++;
                        }

						$flag = 1;
						$t20b++;
                    }

			     }
				 //遍历二级盟友报名成功的课程，计算是否给20结束

				 if ($flag == 1)
				 {
					 $b++;
				 }

				 $map3grade['referee_phone'] = $zhuce2grade[$ty]['phone'];
                 $zhuce3grade = $m->where($map3grade)->select();//改二级注册会员的三级注册会员

                 //遍历该二级盟友的三级盟友
				 for ($my = 0; $my < count($zhuce3grade); $my++)
			     {
                     $n = M('member_level');
        	         $map2['member_id'] = $zhuce3grade[$my]['id'];
        	         $map2['success_or'] = 1;
        	         $course3 = $n->where($map2)->select();
        	         
				     $flag = 0;

                     //遍历该三级盟友报名成功的课程，计算是否给20
			         for ($oy = 0; $oy < count($course3); $oy++)
		             {
			             $baomingtime = strtotime(date("Y-m-d ", $course3[$oy]['time'])) + 3600*24 + 3600*24*1;//报名完当天晚上凌晨之后三天的时间戳
                         if(time() > $baomingtime){
            	             $aaa = explode(",",$course3[$oy]['referee_member_id']);
            	             //dump($aaa);
                             if(in_array($member['id'], $aaa)){

                             }else{
                                $onemap['id'] = $course3[$oy]['id'];
                                $onedate['referee_member_id'] = $course3[$oy]['referee_member_id'] . "," .$member['id'];
                                $n->where($onemap)->data($onedate)->save();
                                $cc[] = $course3[$oy]; //该三级盟友需要增加20的课程
								$add20c++;
                             }

							 $flag = 1;
							 $t20c++;
					     }
                     } //遍历该三级盟友报名成功的课程，计算是否给20结束

					 if ($flag == 1)
					 {
						 $c++;
					 }

			     } //遍历该二级盟友的三级盟友结束
                 
              }//遍历该一级盟友的二级盟友结束

           }//遍历一级盟友结束
       

   //          echo " $a  $b $c <br>";
			// echo " $t20a  $t20b $t20c <br>";
			// echo " $add20a  $add20b  $add20c <br>";

			// die();
		
        $mengyou = $a + $b + $c;//一二三级盟友人数总和
        /********************盟友end*************************/
        $tui_money = count($zhuce);
        $xue_money = ($t20a + $t20b + $t20c)* 20;
        $all_money = $tui_money + $xue_money;
        $allm = M('member');
        $allmap['id'] = $member['id'];

        $alldata['red_packet'] = $member['red_packet'] + ($add20a + $add20b + $add20c)*20;
        // dump($alldata);
        // die();
        $allm->where($allmap)->data($alldata)->save();

        //插入明细
        $all_money1 = ($add20a + $add20b + $add20c)*20;
        if($all_money != 0){
            $monn = M('detail');
            $detail_data['money'] = $all_money1;
            $detail_data['remarks'] = "学费奖金";
            $detail_data['time'] = time();
            $detail_data['member_id'] = $member['id'];
            $monn->data($detail_data)->add();
        }



        $endmember = $allm->where($allmap)->limit(1)->find();

        /**********************红包明细   start**********************/
        $m = M('detail');
        $detail_map['member_id'] = $member['id'];
        $detail_map['money'] = array('neq',0);
        $detail_mingxi = $m->where($detail_map)->select();
        /**********************红包明细   end**********************/
        $this->assign("detail_mingxi",$detail_mingxi);
        $this->assign("tui_money",$tui_money);
        $this->assign("all_money",$all_money);
        $this->assign("xue_money",$xue_money);
        $this->assign("mengyou",$mengyou);
        $this->assign("mengyou1",$a);
        $this->assign("mengyou2",$b);
        $this->assign("mengyou3",$c);
        $this->assign("openid",$openid);
        $this->assign("wx_nickname",$member['wx_nickname']);
        $this->assign("wx_img",$member['wx_img']);
        $this->assign("red_packet",$endmember['red_packet']);
        //die();
    	$this->display();



















        
    }

    public function red_packet_tixian(){ //红包提现
        $openid = I('get.openid');
        $m = M('member');
        $map['open_id'] = $openid;
        $member = $m->where($map)->find();
        $this->assign("openid",$openid);
        $this->assign("wx_nickname",$member['wx_nickname']);
        $this->assign("wx_img",$member['wx_img']);
        $this->assign("red_packet",$member['red_packet']);
        $this->display();
    }

    public function tixian_update(){ //红包提现更新
        $openid = I('post.openid');
        $user_name = I('post.user_name');
        $bank_name = I('post.bank_name');
        $money = I('post.money');
        $card_num = I('post.card_num');
        $m = M('member');
        $map['open_id'] = $openid;
        $member = $m->where($map)->find();
        if($money < 100){
            echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
            echo  "<script>alert('不足100元，无法提现！');window.location.href='https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx7e4fb28fe1443248&redirect_uri=http://www.ebfree.com/huatong/index.php/Phone/Index/index&response_type=code&scope=snsapi_base&state=123#wechat_redirect'</script>";
        }else{
            if($money > $member['red_packet']){
                echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
                echo  "<script>alert('提现金额超过了您的红包余额！');window.location.href='https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx7e4fb28fe1443248&redirect_uri=http://www.ebfree.com/huatong/index.php/Phone/Index/index&response_type=code&scope=snsapi_base&state=123#wechat_redirect'</script>";
            }else{
                $dataa['red_packet'] = $member['red_packet'] - $money;
                $m->where($map)->data($dataa)->save();

                $n = M('tixian');
                $data['openid'] = I('post.openid');
                $data['user_name'] = $user_name;
                $data['money'] = $money;
                $data['bank_name'] = $bank_name;
                $data['card_num'] = $card_num;
                $data['time'] = date('Y-m-d H:i:s',time());
                //插入明细
                $monn = M('detail');
                $detail_data['money'] = $money;
                $detail_data['remarks'] = "红包提现";
                $detail_data['time'] = time();
                $detail_data['member_id'] = $member['id'];
                $monn->data($detail_data)->add();


                if($n->data($data)->add()){
                     echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
                    echo  "<script>alert('申请提现成功，请耐心等待！');window.location.href='https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx7e4fb28fe1443248&redirect_uri=http://www.ebfree.com/huatong/index.php/Phone/Index/index&response_type=code&scope=snsapi_base&state=123#wechat_redirect'</script>";
                }
            }
        }
    }

    public function deposit(){ //我的押金
        $openid = I('get.openid');
        $appid="wx7e4fb28fe1443248";
        $appsecrect="521b51fa1c211e00e68fcdd55732b822";
        $accessToken = $this->getToken($appid,$appsecrect);
        //echo $accessToken;
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$accessToken."&openid=".$openid;
        $userinfo_json=json_decode($this->curl_get($url),true);
        $wx_nickname = $userinfo_json["nickname"];
        $wx_img = $userinfo_json["headimgurl"];



        $mm = M('member');
        $weixin['open_id'] = $openid;
        $member = $mm->where($weixin)->select();
        $nn = M('member_level');
        $yong['member_id'] = $member[0]['id'];
        $memberlevel = $nn->where($yong)->field('user_pay,level_id,time,member_id')->where("success_or = 1")->select();
        $mmnn = D('LevelMember');
        $kecheng = $mmnn->where($yong)->field('course_id,user_pay,course_title,level_id,time,member_id')->where("success_or = 1")->select();//已报名的课程
        // dump($memberlevel);
        //  dump($kecheng);
        //  die();
        /* 自报名晚上凌晨算，三天后不能退换课 start*/
        for($x=0;$x<count($kecheng);$x++){
           $baotime = strtotime(date("Y-m-d ", $kecheng[$x]["time"])) + 3600*24; //报名完当天晚上凌晨的时间戳
           $guotime = strtotime(date("Y-m-d ", $kecheng[$x]["time"])) + 3600*24+3600*24*1; //报名完后三天晚上凌晨的时间戳
           //echo time()."<br/>".$guotime."<br/>".$baotime."<br/>";
           if(time() < $guotime){
            $keke[] = $kecheng[$x];
           }
        }
        /* 自报名晚上凌晨算，三天后不能退换课 end*/
        // dump($keke);
        // die();




        for($j=0;$j<count($memberlevel);$j++){
            $nowtime = time();
            $baomingtime = strtotime(date("Y-m-d ", $memberlevel[$j]['time'])) + 3600*24;//报名完当天晚上凌晨的时间戳
            $geshu = floor(($nowtime - $baomingtime) / 86400);//多少个两天(172800秒)
            if($geshu < 0){
            	$geshu = 0;
            }
            $tianshu = ($geshu+1)*2;
             //echo $tianshu;
             // die("LL");
            $m = M('level');
            $map['id'] = $memberlevel[$j]['level_id'];
            //  die('OK');
            $level = $m->where($map)->select();
            //dump($level);
            $n = M('course');
            $course_name['id'] = $level[0]['course_id'];
            $course = $n->where($course_name)->select();//查找出对应的任务
            //dump($course);
            $n = D('MissionAnswer');
            $mapp['level_id'] = $memberlevel[$j]['level_id'];
            $mapp['member_id'] = $memberlevel[$j]['member_id'];
            $mapp['success_or'] = 1;
            // $m = M('member_level');
            // $member_level = $m->where($mapp)->select();
            $mnm = M('answer');
            $mission_all[$j] = $mnm->where($mapp)->order('mission_id asc')->select();
            $mission_start11[$j] = $mnm->where($mapp)->where("type = 0")->order('mission_id asc')->select();//查询总共多少个任务
             // dump($mission_start11[$j]);
             // die();
            if($tianshu != 0){
                $mission_start[$j] = $n->where($mapp)->where("type = 0")->group('mission_id')->limit($tianshu)->order('mission_id asc')->select();//解锁的
                //dump($mission_start);
                /*****判断如果超两天没有回答start******/
                if(count($mission_start[$j]) % 2 == 0){
                    $mission_start_num = count($mission_start[$j])-2;
                }else{
                    $mission_start_num = count($mission_start[$j])-1;
                }
                $data['success'] = '3';
        		$cc = 0;
                for($i=0;$i<$mission_start_num;$i++){
                    if($mission_start[$j][$i]['success'] == NULL){
                        $mission_start[$j][$i]['success'] = 3;
                        $nn = M('answer');
	        			$map_answer['id'] = $mission_start[$j][$i]['id'];
	        			$nn->data($data)->where($map_answer)->save();
                        $cc++;
                    }
                }
           //      if($cc != 0){
           //      	$nn = M('answer');
           //          // $haha = $nn->where($mapp)->where("type = 0")->fetchSql(true)->where('success is Null')->limit($cc)->select();
           //          // dump($haha);
           //          // die(0);
        			// $nn->data($data)->where($mapp)->where("type = 0")->fetchSql(true)->where('success is Null')->limit($cc)->save();
           //      }
                
                //全部任务都解锁后判断有没有没回答的
                $cha = time() - $baomingtime;
                $zou = floor(count($mission_start11[$j])/2);
                if(($zou+1)*86400 < $cha ){//count($mission_start)
                    for($i=0;$i<count($mission_start11[$j]);$i++){
                        if($mission_start[$j][$i]['success'] == NULL){
                             $mission_start[$j][$i]['success'] = 3;
                            // $kk = 1;
                            $nn = M('answer');
	        				$map_answer['id'] = $mission_start[$j][$i]['id'];
        					$nn->data($data)->where($map_answer)->save();
                        }
                    }
                }
                /*if($kk == 1){
                	$nn = M('answer');
	        		$data['success'] = 3;
        			$nn->data($data)->where($mapp)->where("type = 0")->where('success is Null')->limit($cc)->save();
        		}*/
                /*****判断如果超两天没有回答end******/
            }
        }
        // dump($mission_start[0]);
        $keci = "";
        //$z = 0;
        $shijian = "";
         //dump($memberlevel);
         //dump($mission_all);
        for($i=0;$i<count($memberlevel);$i++){
        	$nb = 0;
            $z[$i] = 0;
            for($j=0;$j<count($mission_all[$i]);$j++){
            	if($mission_all[$i][$j]['success'] == 3 || $mission_all[$i][$j]['success'] == '0'){
            		$nb++;
            	}  
            }
            if($nb>1){
            	for($j=0;$j<count($mission_all[$i]);$j++){
	            if($mission_all[$i][$j]['success'] == 3 || $mission_all[$i][$j]['success'] == '0'){
	                	if($mission_all[$i][$j]['time_finish'] == ""){
	                		$shijian = $shijian."未作答"."!";
	                	}else{
	                		$shijian = $shijian.date('Y-m-d H:i:s',$mission_all[$i][$j]['time_finish'])."!";
	                	}
	                    ++$z[$i];
	                }
	            }
            }
            


            $level_id_alll[] = $mission_all[$i][0]['level_id'];
            if($z[$i] > 1){
                $m = M('level');
                $map['id'] = $mission_all[$i][0]['level_id'];
                $level_id_all[] = $mission_all[$i][0]['level_id'];
                $level = $m->where($map)->select();
                $n = M('course');
                $course_name['id'] = $level[0]['course_id'];
                $course = $n->where($course_name)->select();
                $keci = $keci.$z[$i]."!".$course[0]['title']."!".$shijian."*";
                //echo $course[0]['title']."出现".$z."次错误<br/>";
                //$z = 0;
                $shijian = "";
            }
        }
         //dump($keci);
         //die();
        // dump($z);
    
        $arr =array_filter(explode("*", $keci));//每个课程
         //dump($arr);
         for($j=0;$j<count($arr);$j++){
         	$arr2[$j] = array_filter(explode("!", $arr[$j]));
         	if($arr2[$j][0] > 1){
         		$arr3[] = $arr2[$j];
         	}
         }
         // dump($arr3);
         // die();
         if($arr3){
         	$this->assign("red",1);//警告
         }
         //dump($level_id_alll);//全部level ID
         //dump($level_id_all);//错误大于等于2的level ID
         $m = M('member');
         $openi['open_id'] = $openid;
         $member_id = $m->where($openi)->find();
        for($i=0;$i<count($level_id_all);$i++){
        	$n = M('member_level');
        	$member_level = $n->where("member_id='%s' and level_id='%s' and success_or='%s'",$member_id["id"],$level_id_all[$i],'1')->limit(1)->find();
        	$baom = strtotime(date("Y-m-d ", $member_level['time'])) + 3600*24+3600*24*1;//报名完后三天晚上凌晨的时间戳
        	// dump($member_level);
         //    die();
        	if($baom < time()){
        		$dat['user_pay'] = '0';
        		$n->where("member_id='%s' and level_id='%s' and success_or='%s'",$member_id["id"],$level_id_all[$i],'1')->limit(1)->data($dat)->save();
        	}
        	//$member_level = $n->where("member_id='%s' and level_id='%s' and success_or='%s'",$member_id["id"],$level_id_alll[$i],'1')->limit(1)->find();
            
        }
        //die();
        //$fp = fopen(__DIR__ . "/debug3.txt", "a+");

            //fwrite($fp, "begain"."\r\n");

            //fclose($fp);
         for($i=0;$i<count($level_id_alll);$i++){//计算押金总数
            $m = M('member_level');
            $map12['member_id'] = $member[0]['id'];
            $map12['level_id'] = $level_id_alll[$i];
            $map12['success_or'] = 1;
            $endend[] = $m->where($map12)->find();//含有押金的课程
           // $fp = fopen(__DIR__ . "/debug3.txt", "a+");

            //fwrite($fp, "yajinzongshu". $endend[$i]['id'] . "***" . $endend[$i]['user_pay'] . "\r\n");

            //fclose($fp);
         }
         $sum = 0;
         for($j=0;$j<count($endend);$j++){
            $sum = $sum + $endend[$j]['user_pay'];
         }
        $this->assign("yue",$sum);
        
        $this->assign("kecheng",$keke);
        $this->assign("keci",$keci);
        $this->assign("mission_all",$arr3);
        $this->assign("openid",$openid);
        $this->assign("wx_nickname",$wx_nickname);
        $this->assign("wx_img",$wx_img);
        $this->display();
    }

    public function deposit_detail(){//警告详情
    	$yue = I('get.yue');
        $keci = I('get.keci');
        //dump($keci);
        //die();
        $keci=str_replace('+',' ',$keci);
        $arr =array_filter(explode("*", $keci));//每个课程
         for($j=0;$j<count($arr);$j++){
            $arr2[$j] = array_filter(explode("!", $arr[$j]));
            if($arr2[$j][0] > 1){
                $arr3[] = $arr2[$j];
            }
         }
         // dump($arr3);
         // die();
        $this->assign("yue",$yue);
        $this->assign("mission_all",$arr3);
        $this->display();
    }

     public function tuihuan(){//退换课
        $level_id = I('post.course');
        $openid = I('post.openid');
        $m = M('member');
        $map['open_id'] = $openid;
        $member = $m->where($map)->limit(1)->find();//member["id"]
        $n = M('member_level');
        $mapp['member_id'] = $member["id"];
        $mapp['success_or'] = "1";
        $mapp['level_id'] = $level_id;
        $member_level = $n->where($mapp)->limit(1)->find();
        //dump($member_level);
        $dataa['red_packet'] = $member["red_packet"] + $member_level['user_pay'];
        $data['user_pay'] = "0";
        $data['success_or'] = "2";
        if($m->where($map)->limit(1)->data($dataa)->save()){
        	$n->where($mapp)->data($data)->save();
            //插入明细
            $monn = M('detail');
            $detail_data['money'] = $member_level['user_pay'];
            $detail_data['remarks'] = "退换课退回";
            $detail_data['time'] = time();
            $detail_data['member_id'] = $member["id"];
            $monn->data($detail_data)->add();
            echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
            echo  "<script>alert('退换成功！');window.location.href='https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx7e4fb28fe1443248&redirect_uri=http://www.ebfree.com/huatong/index.php/Phone/Index/index&response_type=code&scope=snsapi_base&state=123#wechat_redirect'</script>";
        }else{
            echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
            echo  "<script>alert('退换失败！');window.location.href='https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx7e4fb28fe1443248&redirect_uri=http://www.ebfree.com/huatong/index.php/Phone/Index/index&response_type=code&scope=snsapi_base&state=123#wechat_redirect'</script>";
        }

    }
    
  /*我的班级 王超 20160412 test*/
    public function my_class(){
        $openid = I('get.openid');

        $LevelMember_map=D('level_member');
        $mission_map=M('mission');
        $member_level_map=M('member_level');//用于班级更新失效信息

        $weixin_query['open_id'] = $openid;//查询获取用户信息

        $class_infos=$LevelMember_map->where($weixin_query)->select();
        // dump($class_infos);
        // die("l");
        $wx_nickname=$class_infos[0]['wx_nickname'];
        $wx_img=$class_infos[0]['wx_img'];

        $myclass_info=array();
        $is_reg=1;
        foreach ($class_infos as $i => $class_info) {
            # code..
            if(($class_info['class_id']!=-1)&&($class_info['success_or']=='1')){
                if(($class_info['class_id']!=NULL)&&($class_info['class_id']!=0)){
                    //判断课程是否失效
                    $mission_query['level_id']=$class_info['level_id'];
                    $mission_count=count($mission_map->where($mission_query)->select());
                    if($mission_count<=2*(time()-$class_info['time'])/86400){//按一天计
                       $data_update['id']=$class_info['member_level_id'];
                       $data_update['class_id']=-1;
                       $member_level_map->save($data_update);
                       continue;
                    }

                    $myclass_name=$class_info['course_title'].$class_info['class_id'].'班';
                    //查member_level表获取classmate信息
                    $classmates_query['class_id']=$class_info['class_id'];
                    $classmates_query['level_id']=$class_info['level_id'];
                    $myclassmates=$LevelMember_map->where($classmates_query)->select();
                    //数量
                    $myclass_member_number=count($myclassmates);
                    //前五个classmate头像
                    $myclassmate_head=array();
                    $j=0;
                    foreach($myclassmates as $i => $myclassmate){
                        //获取同学头像
                        if($myclassmate['member_id']!=$class_info['member_id']){
                            $myclassmate_head[$j]=$myclassmate['wx_img'];
                            $j++;
                        }
                        if($j>=5){
                            break;
                        }
                    }
                    $myclass_info[]=array('member_level_id'=>$class_info['member_level_id'],
                                          'class_name'=>$myclass_name, 
                                          'member_number'=>$myclass_member_number,
                                          'classmate_head'=>$myclassmate_head,
                                          'class_id'=>$class_info['class_id'],
                                          'is_class_reg' => 1);
                }
                else{//未缴费的不予显示 王超 20160511
                    $is_reg=0;
                    $myclass_info[]=array('member_level_id'=>$class_info['member_level_id'],
                                         'is_class_reg'=>0);
                }
            }
            else{
                // if($class_info['success_or']!='1'){
                //     $myclass_info[]=array('member_level_id'=>$class_info['member_level_id'],
                //                           'is_class_reg'=>-1);
                // }
            }
        }
        // dump($myclass_info);
        // die();
        $this->assign("openid",$openid);
        $this->assign("wx_nickname",$wx_nickname);
        $this->assign("wx_img",$wx_img);

        $this->assign("ta",I('get.ta'));
        $this->assign("myclass_info",$myclass_info);

        $this->assign("is_reg",$is_reg);

        $this->display();
    }

    /*加入新的班级 王超 20160329 test*/
    public function join_class(){
        $member_level_id=I('get.member_level_id');
        $this->assign('member_level_id',$member_level_id);
        $this->display();
    }
    /*班级信息更新 王超 20160412 test*/
    public function update_class(){
        $member_level_map=M('member_level');
        $data['id']=I('post.member_level_id');
        $data['title']=I('post.student_name');
        $data['gender']=I('post.student_gender');
        $data['age']=I('post.student_age');
        $data['school']=I('post.student_school');
        $data['reserve_phone']=I('post.student_phone');

        $member_level_info_query['id']=$data['id'];
        $member_level_info=$member_level_map->where($member_level_info_query)->find();
        $levels_info_query['level_id']=$member_level_info['level_id'];
        $levels_info=$member_level_map->where($levels_info_query)->select();
        $MaxClassId=$levels_info[0]['class_id'];
        foreach($levels_info as $level_info){
            if($MaxClassId<$level_info['class_id']){
                $MaxClassId=$level_info['class_id'];
            }
        }
        if($MaxClassId>0){
            $idcount_query['class_id']=$MaxClassId;
            $idcount_query['level_id']=$member_level_info['level_id'];
            $idcount=count($member_level_map->where($idcount_query)->select());
            if($idcount>=30){
                $data['class_id']=$MaxClassId+1;
            }
            else{
                $data['class_id']=$MaxClassId;
            }       
        }
        else{
            $data['class_id']=1;
        }

        if($member_level_map->save($data)){
            $this->redirect('Honey/after_class_jump',array('class_id'=>$data['class_id']));
        }
        else{
            $this->error();//一般情况这句不会执行，留在这里仅供调试
        }
    }
    /*班主任信息展示页面 王超 20160412*/
    public function after_class_jump(){
        $class_id=I('get.class_id');
        $teacher_info= array('qrcode1'=>"/huatong/Public/images/honey/teacher_qrcode/1675713710.jpg",
                             'qrcode2'=>"/huatong/Public/images/honey/teacher_qrcode/1675713710.jpg");
        $this->assign('teacher_info',$teacher_info);
        $this->display();
    }

    /*推广链接 王超 20160419*/
    public function advertise(){
        $img_url='/huatong/Public/images/AboutUs/advertise.jpg';
        $this->assign('img_url',$img_url);
        $this->display();
    }





    public function zhifu(){
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
?>