<?php
namespace Phone\Controller;
use Think\Controller;
class HoneyController extends Controller {
    public function honey(){
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
        //echo $accessToken;
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$accessToken."&openid=".$openid;
        $userinfo_json=json_decode($this->curl_get($url),true);
        $wx_nickname = $userinfo_json["nickname"];
        $wx_img = $userinfo_json["headimgurl"];
         //dump($userinfo_json);
        // echo $wx_img;
         //die('ok');
        $this->assign("openid",$openid);
        $this->assign("wx_nickname",$wx_nickname);
        $this->assign("wx_img",$wx_img);
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
    	$geshu = floor(($nowtime - $baomingtime) / 3600);//多少个两天(172800秒)
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
        	$data['success'] = 3;
        	$cc = 0;
        	for($i=0;$i<$mission_start_num;$i++){
        		if($mission_start[$i]['success'] == NULL){
        			//$mission_start[$i]['success'] = 3;
        			$cc++;
        		}
        	}
        	if($cc){
        		$nn = M('answer');
        	$nn->data($data)->where($mapp)->where("type = 0")->group('mission_id')->where('success is Null')->limit($cc)->save();
        	}
        	 
        	//全部任务都解锁后判断有没有没回答的
        	$cha = time() - $baomingtime;
        	$zou = floor(count($mission_start11)/2);
        	if(($zou+1)*3600 < $cha ){//count($mission_start)
        		for($i=0;$i<count($mission_start);$i++){
        			if($mission_start[$i]['success'] == NULL){
        				$mission_start[$i]['success'] = 3;
        				$kk=1;
        			}
        		}
        	}
        	if($kk == 1){
        		$nn->data($data)->where($mapp)->where("type = 0")->group('mission_id')->where('success is Null')->limit($cc)->save();
        	}
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
        	echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
        	echo  "<script>alert('请勿重复作答！');window.location.href='https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx7e4fb28fe1443248&redirect_uri=http://serv2.matesofts.com/huatong/index.php/Phone/Honey/honey&response_type=code&scope=snsapi_base&state=123#wechat_redirect'</script>";
        }else{
        	if(I('post.answer') == $mission[0]['answer'] ){
        		$data['success'] = 1;
                $data['time_finish'] = time();
        		$n->where($mapp)->where('type = 0')->save($data);
        		echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
        		echo  "<script>alert('恭喜你回答正确！');window.location.href='https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx7e4fb28fe1443248&redirect_uri=http://serv2.matesofts.com/huatong/index.php/Phone/Honey/honey&response_type=code&scope=snsapi_base&state=123#wechat_redirect'</script>";
        	}else{
        		$data['success'] = 0;
                $data['time_finish'] = time();
        		$n->where($mapp)->where('type = 0')->save($data);
        		echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
        		echo  "<script>alert('很抱歉，回答错误！');window.location.href='https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx7e4fb28fe1443248&redirect_uri=http://serv2.matesofts.com/huatong/index.php/Phone/Honey/honey&response_type=code&scope=snsapi_base&state=123#wechat_redirect'</script>";
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
       for($xx=0;$xx<2;$xx++){
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
	    	$geshu = floor(($nowtime - $baomingtime) / 3600);//多少个两天(172800秒)
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
	     		//dump($mission_start);   	/*****判断如果超两天没有回答start******/
	        	if(count($mission_start) % 2 == 0){
	        		$mission_start_num = count($mission_start)-2;
	        	}else{
	        		$mission_start_num = count($mission_start)-1;
	        	}
	        	$data['success'] = 3;
	        	$cc = 0;
	        	for($i=0;$i<$mission_start_num;$i++){
	        		if($mission_start[$i]['success'] == NULL){
	        			//$mission_start[$i]['success'] = 3;
	        			$cc++;
	        		}
	        	}
	        	if($cc){
	        		$nn = M('answer');
	        	$nn->data($data)->where($mapp)->where("type = 0")->group('mission_id')->where('success is Null')->limit($cc)->save();
	        	}
	        	//全部任务都解锁后判断有没有没回答的
	        	$cha = time() - $baomingtime;
	        	$zou = floor(count($mission_start11)/2);
	        	if(($zou+1)*3600 < $cha ){//count($mission_start)
	        		for($i=0;$i<count($mission_start);$i++){
	        			if($mission_start[$i]['success'] == NULL){
	        				$mission_start[$i]['success'] = 3;
	        				$kk=1;
	        			}
	        			$allover[] = $mission_start[$i]['level_id'];
	        		}
	        	}
	        	if($kk == 1){
	        		$nn = M('answer');
	        		$data['success'] = 3;
	        		$nn->data($data)->where($mapp)->where("type = 0")->group('mission_id')->where('success is Null')->limit($cc)->save();
	        	}
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
	       			$daaa['user_pay'] = 0;
	       			$cc->where($mama)->where('success_or = 1')->data($daaa)->save();
	       		}
	       		//dump($answ);
	       	}

        $this->assign("member",$member);
        $this->assign("openid",$openid);
        $this->assign("wx_nickname",$wx_nickname);
        $this->assign("wx_img",$wx_img);
    	$this->display();
    }


    public function red_packet_detail(){ //红包详情
    	$openid = I('get.openid');
        $appid="wx7e4fb28fe1443248";
        $appsecrect="521b51fa1c211e00e68fcdd55732b822";
        $accessToken = $this->getToken($appid,$appsecrect);
        //echo $accessToken;
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$accessToken."&openid=".$openid;
        $userinfo_json=json_decode($this->curl_get($url),true);
        $wx_nickname = $userinfo_json["nickname"];
        $wx_img = $userinfo_json["headimgurl"];
        $this->assign("openid",$openid);
        $this->assign("wx_nickname",$wx_nickname);
        $this->assign("wx_img",$wx_img);
    	$this->display();
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
        $kecheng = $mmnn->where($yong)->field('course_id,course_title,level_id,time,member_id')->where("success_or = 1")->select();//已报名的课程

        // dump($kecheng);
        /* 自报名晚上凌晨算，三天后不能退换课 start*/
        for($x=0;$x<count($kecheng);$x++){
           $baotime = strtotime(date("Y-m-d ", $kecheng[$x]["time"])) + 3600*24; //报名完当天晚上凌晨的时间戳
           $guotime = strtotime(date("Y-m-d ", $kecheng[$x]["time"])) + 3600*96; //报名完后三天晚上凌晨的时间戳
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
            $geshu = floor(($nowtime - $baomingtime) / 3600);//多少个两天(172800秒)
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
            $course = $n->where($course_name)->select();
            //dump($course);
            $n = D('MissionAnswer');
            $mapp['level_id'] = $memberlevel[$j]['level_id'];
            $mapp['member_id'] = $memberlevel[$j]['member_id'];
            $m = M('member_level');
            $member_level = $m->where($mapp)->select();
            $mnm = M('answer');
            $mission_all[$j] = $mnm->where($mapp)->order('mission_id asc')->select();
            $mission_start11[$j] = $n->where($mapp)->where("type = 0")->order('mission_id asc')->select();//查询总共多少个任务
            // dump($mission_start11[$j]);
            // die();
            if($tianshu != 0){
                $mission_start[$j] = $n->where($mapp)->where("type = 0")->limit($tianshu)->order('mission_id asc')->select();//解锁的
                /*****判断如果超两天没有回答start******/
                if(count($mission_start[$j]) % 2 == 0){
                    $mission_start_num = count($mission_start[$j])-2;
                }else{
                    $mission_start_num = count($mission_start[$j])-1;
                }
                $data['success'] = 3;
        		$cc = 0;
                for($i=0;$i<$mission_start_num;$i++){
                    if($mission_start[$j][$i]['success'] == NULL){
                        //$mission_start[$j][$i]['success'] = 3;
                        $cc++;
                    }
                }
                if($cc != 0){
                	$nn = M('answer');
        			$nn->data($data)->where($mapp)->where("type = 0")->where('success is Null')->limit($cc)->save();
                }
                
                //全部任务都解锁后判断有没有没回答的
                $cha = time() - $baomingtime;
                $zou = floor(count($mission_start11[$j])/2);
                if(($zou+1)*3600 < $cha ){//count($mission_start)
                    for($i=0;$i<count($mission_start[$j]);$i++){
                        if($mission_start[$j][$i]['success'] == NULL){
                            $mission_start[$j][$i]['success'] = 3;
                            $kk = 1;
                        }
                    }
                }
                if($kk == 1){
                	$nn = M('answer');
	        		$data['success'] = 3;
        			$nn->data($data)->where($mapp)->where("type = 0")->where('success is Null')->limit($cc)->save();
        		}
                /*****判断如果超两天没有回答end******/
            }
        }
        // dump($mission_start[0]);
        $keci = "";
        $z = 0;
        $shijian = "";
        for($i=0;$i<count($memberlevel);$i++){
            for($j=0;$j<count($mission_all[$i]);$j++){
                if($mission_all[$i][$j]['success'] == 3 || $mission_all[$i][$j]['success'] == '0'){
                	if($mission_all[$i][$j]['time_finish'] == ""){
                		$shijian = $shijian."未作答"."!";
                	}else{
                		$shijian = $shijian.date('Y-m-d H:i:s',$mission_all[$i][$j]['time_finish'])."!";
                	}
                    ++$z;
                }
            }
            	$level_id_alll[] = $mission_all[$i][0]['level_id'];
            if($z > 1){
                $m = M('level');
                $map['id'] = $mission_all[$i][0]['level_id'];
                $level_id_all[] = $mission_all[$i][0]['level_id'];
                $level = $m->where($map)->select();
                $n = M('course');
                $course_name['id'] = $level[0]['course_id'];
                $course = $n->where($course_name)->select();
                $keci = $keci.$z."!".$course[0]['title']."!".$shijian."*";
                //echo $course[0]['title']."出现".$z."次错误<br/>";
                $z = 0;
                $shijian = "";
            }
        }
        $arr =array_filter(explode("*", $keci));//每个课程
         //dump($arr);
         for($j=0;$j<count($arr);$j++){
         	$arr2[$j] = array_filter(explode("!", $arr[$j]));
         	if($arr2[$j][0] > 1){
         		$arr3[] = $arr2[$j];
         	}
         }
         if($arr3){
         	$this->assign("red",1);//警告
         }
         //dump($level_id_alll);//全部level ID
         //dump($level_id_all);//错误大于等于2的level ID
         $m = M('member');
         $openi['open_id'] = $openid;
         $member_id = $m->where($openi)->find();
         $sum = 0;
        for($i=0;$i<count($level_id_alll);$i++){
        	$n = M('member_level');
        	//echo $member_id["id"]."*/*".$level_id_all[$i]."<br/>";
        	//$member_level = $n->where('member_id='.$member_id["id"])->where('level_id='.$level_id_all[$i])->where('success_or=1')->limit(1)->find();
        	$member_level = $n->where("member_id='%s' and level_id='%s' and success_or='%s'",$member_id["id"],$level_id_alll[$i],'1')->limit(1)->find();
        	//dump($member_level);
        	//echo $member_level['time']."<br/>";
        	$baom = strtotime(date("Y-m-d ", $member_level['time'])) + 3600*96;//报名完后三天晚上凌晨的时间戳
        	//echo $baom;
        	if($baom < time()){
        		//echo "************************<br/>";
        		$dat['user_pay'] = '0';
        		$n->where("member_id='%s' and level_id='%s' and success_or='%s'",$member_id["id"],$level_id_alll[$i],'1')->limit(1)->data($dat)->save();
        	}
        	$member_level = $n->where("member_id='%s' and level_id='%s' and success_or='%s'",$member_id["id"],$level_id_alll[$i],'1')->limit(1)->find();
            //dump($member_level);
        	$sum = $sum + $member_level['user_pay'];//押金余额
        }
        //die();
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
        $keci=str_replace('+',' ',$keci);
        $arr =array_filter(explode("*", $keci));//每个课程
         for($j=0;$j<count($arr);$j++){
            $arr2[$j] = array_filter(explode("!", $arr[$j]));
            if($arr2[$j][0] > 1){
                $arr3[] = $arr2[$j];
            }
         }
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
            echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
            echo  "<script>alert('退换成功！');window.location.href='https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx7e4fb28fe1443248&redirect_uri=http://serv2.matesofts.com/huatong/index.php/Phone/Index/index&response_type=code&scope=snsapi_base&state=123#wechat_redirect'</script>";
        }else{
            echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
            echo  "<script>alert('退换失败！');window.location.href='https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx7e4fb28fe1443248&redirect_uri=http://serv2.matesofts.com/huatong/index.php/Phone/Index/index&response_type=code&scope=snsapi_base&state=123#wechat_redirect'</script>";
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
        //dump($member_info);
        //die("l");
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
                else{
                    $is_reg=0;
                    $myclass_info[]=array('member_level_id'=>$class_info['member_level_id'],
                                          'is_class_reg'=>0);
                }
            }
            else{
                if($class_info['success_or']!='1'){
                    $myclass_info[]=array('member_level_id'=>$class_info['member_level_id'],
                                          'is_class_reg'=>-1);
                }
            }
        }

        $this->assign("openid",$openid);
        $this->assign("wx_nickname",$wx_nickname);
        $this->assign("wx_img",$wx_img);


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
        $teacher_info= array('name'=>'王老师','qrcode'=>"http://192.168.1.100/huatong/Public/images/honey/teacher_qrcode/1675713710.jpg");
        $this->assign('teacher_info',$teacher_info);
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