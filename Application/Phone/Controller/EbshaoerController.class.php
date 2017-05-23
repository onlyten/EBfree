<?php
namespace Phone\Controller;
use Think\Controller;
class EbshaoerController extends Controller {
    public function ebshaoer(){
        $openid = I('get.openid');
        $m = M('course');
        $map['type'] = '1';
        $shaoer = $m->where($map)->select();
        //dump($shaoer);
        //die("OK"); 
        $this->assign("openid",$openid);
        $this->assign("shaoer",$shaoer);
		$this->display();
    }

    public function sekecheng(){
        $openid = I('get.openid');
        $id=I("get.sid");
        $m=M("course");
        $d=M("level");
        $course=$m->where("id=".$id)->select();
        $levels=$d->where("course_id=".$id)->order('id asc')->select();
        //dump($levels);
        //die("OK");
    	$url = __SELF__;
    	$this->assign("url",$url);
        $this->assign("course",$course);
        $this->assign("levels",$levels);
        $this->assign("openid",$openid);
        $this->display();
    }

    public function gai()
    {
		//Haiti add
		$m_level_sname = "s" . I('get.member_id') . I('get.level_id');

		$val_s_payed = session($m_level_sname);

	    $m_level_sname = "st" . I('get.member_id') . I('get.level_id');

		$val_s_time = session($m_level_sname);

		$model_ml = M('member_level');
        $map_m_ml['member_id'] = I('get.member_id');
        $map_m_ml['level_id'] = I('get.level_id');
        $map_m_ml['success_or'] = 1;
		$map_m_ml['time'] = $val_s_time;

		$mem_level_list = $model_ml->where($map_m_ml)->select();

		if(count($mem_level_list) == 1)
		{
            header('location: https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx7e4fb28fe1443248&redirect_uri=http://www.ebfree.com/huatong/index.php/Phone/Honey/honey&response_type=code&scope=snsapi_base&state=123#wechat_redirect'); 

			return;
		}
		//Haiti add end
        
        $n = M('member_level');
        $repeat_select['member_id'] = I('get.member_id');
        $repeat_select['level_id'] = I('get.level_id');
        $repeat_select['success_or'] = 1;
        $repeat = $n->where($repeat_select)->order('id desc')->limit(1)->find();
        if(count($repeat) != 0){
            if($repeat['time'] > (time() - 259200)){
                echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';            
                echo  "<script>alert('请勿重复购买！');window.location.href='https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx7e4fb28fe1443248&redirect_uri=http://www.ebfree.com/huatong/index.php/Phone/Honey/honey&response_type=code&scope=snsapi_base&state=123#wechat_redirect'</script>"; 
            }
        }else{
            $mapp['member_id'] = I('get.member_id');
            $mapp['level_id'] = I('get.level_id');
            $mapp['success_or'] != 2;
            $dataa['user_pay'] = I('get.jine');
            $dataa['success_or'] = 1;
            $n->where($mapp)->lock(true)->data($dataa)->order('id desc')->limit(1)->save();
            $m = M('member');
            $map['id'] = I('get.member_id');
            $member = $m->where($map)->find();
            if(I('get.jine')<0){
                $data['red_packet'] = $member['red_packet'];
            }else{
                $data['red_packet'] = $member['red_packet'] - I('get.jine');
                if($data['red_packet'] < 0){
                    echo "课程价格--->".I('get.price_ke')."<br/>";
                    echo "优惠券--->".I('get.total')."<br/>";
                    echo "红包--->".$member['red_packet']."<br/>";
                    echo "金额--->".I('get.jine')."<br/>";
                    die("123456");
                }
                $monn = M('detail');
                $detail_data['money'] = I('get.jine');
                $detail_data['remarks'] = "购买课程";
                $detail_data['time'] = time();
                $detail_data['member_id'] = I('get.member_id');
                $monn->data($detail_data)->add();
                $fp = fopen(__DIR__ . "/debug2.txt", "a+");
                $test = time();

                fwrite($fp,  $test . " k pay ". $member['red_packet'] . " *** " . I('get.jine') . " " .I('get.member_id') . " " . I('get.level_id') ."\r\n");

                fclose($fp);
                $m->where($map)->lock(true)->data($data)->save();
            }

            //session($m_level_sname, "1");
            
            // echo I('get.jine')."<br/>";
            // dump($data);
            // die();
            // if($m->where($map)->data($data)->save()){
                echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';            
                
                echo  "<script>alert('购买成功！');window.location.href='https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx7e4fb28fe1443248&redirect_uri=http://www.ebfree.com/huatong/index.php/Phone/Honey/honey&response_type=code&scope=snsapi_base&state=123#wechat_redirect'</script>"; 
            // }
            }
    }

     public function sekecheng_update()
    {
		 

    	$url = I('post.url');
    	session('url',$url);
        $openid = I('post.openid');
        $m = M('member');
        $map['open_id'] = $openid;
        $member = $m->where($map)->select();
        if(count($member) == 0){
            echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
        	echo  "<script>alert('请先注册！');window.location.href='https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx7e4fb28fe1443248&redirect_uri=http://www.ebfree.com/huatong/index.php/Phone/Register/register&response_type=code&scope=snsapi_base&state=123#wechat_redirect'</script>";
            // $uurrll = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx7e4fb28fe1443248&redirect_uri=http://www.ebfree.com/huatong/index.php/Phone/Register/register&response_type=code&scope=snsapi_base&state=123#wechat_redirect?url=".$url;
            // header ( "location:$uurrll" );
            //echo $uurrll;
        }else{
        	$map1['member_id'] = $member[0]['id'];
        	$map1['level_id'] = I('post.level_id');
        	$map1['success_or'] = 1;
        	$n = M('member_level');
        	$member_level_success = $n->where($map1)->select();
        	$success_count = count($member_level_success);
        	if($success_count >= 1){
        		echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
        		echo  "<script>alert('您已购买过该课程，请勿重复购买！');window.location.href='https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx7e4fb28fe1443248&redirect_uri=http://www.ebfree.com/huatong/index.php/Phone/Honey/honey&response_type=code&scope=snsapi_base&state=123#wechat_redirect'</script>";
        	}else{
        		$data['member_id'] = $member[0]['id'];
	            $data['level_id'] = I('post.level_id');
	            $data['reserve_phone'] = I('post.txtphone');
	            $data['time'] = time();

	            //Haiti add
	            $m_level_sname = "st" . $data['member_id'] . $data['level_id'];
	            session($m_level_sname, $data['time']);


	            $n = M('member_level');
	            $mm = M('answer');
	            $nn = M('mission');
	            $mapp['level_id'] = I('post.level_id');
	            $mission = $nn->where($mapp)->order('id asc')->select();
	            $mmaapp['member_id'] = $member[0]['id'];
	            $testest['member_id'] = $member[0]['id'];
	            $testest['level_id'] = I('post.level_id');
	            //$answer = $mm->where($mapp)->where($mmaapp)->select();
	            $answer = $mm->where($testest)->select();
	            if(count($answer) == 0){
	                for($i=0;$i<count($mission);$i++){
	                    $dataa['level_id'] = I('post.level_id');
	                    $dataa['member_id'] = $member[0]['id'];
	                    $dataa['mission_id'] = $mission[$i]['id'];
	                    $mm->data($dataa)->add();
	                }
	            }
	            if(count($answer) != 0){
	                $geng['level_id'] = I('post.level_id');
	                $geng['member_id'] = $member[0]['id'];
	                $dataaaa['success'] = null;
	                $dataaaa['time_finish'] = null;
	                $mm->where($geng)->data($dataaaa)->save();
	            }

	            //写入四门口语任务（先判断是否是vip课程）
	            $m = M('level');
	            $level = $m->where('id='.I('post.level_id'))->select();
	            $nm = M('course');
	            $course = $nm->where('id='.$level[0]['course_id'])->select();
	            $kouyu[0] = "外教口语1VS1";
	            $kouyu[1] = "外教口语2VS1";
	            $kouyu[2] = "外教口语3VS1";
	            $kouyu[3] = "外教口语4VS1";
	            $kouyuyu = $mm->where($mapp)->where($mmaapp)->where('type = 1')->select();
	            if(count($kouyuyu) == 0){
	                if($course[0]['foreign'] == 4){
	                    for($i=0;$i<4;$i++){
	                        $ddata['level_id'] = I('post.level_id');
	                        $ddata['member_id'] = $member[0]['id'];
	                        $ddata['mission_id'] = $mission[$i]['id'];
	                        $ddata['type'] = 1;
	                        $ddata['kouyu_name'] = $kouyu[$i];
	                        $mm->data($ddata)->add();
	                    }
	                }
	            }

	            if($n->data($data)->add()){
	                $post = "http://www.ebfree.com/huatong/index.php/Phone/MsWxpay/js_api_call?member_id=" . $member[0]['id'] ."&openid=" .$openid. "&level_id=" .I('post.level_id'). "&price=" .$course[0]['price'];
	                cookie('urlurl',$post,3600);
	                header ( "location:$post" );
	            	//header('Location: https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx7e4fb28fe1443248&redirect_uri=http://www.ebfree.com/huatong/index.php/Phone/Honey/honey&response_type=code&scope=snsapi_base&state=123#wechat_redirect');
	            	//$this->redirect('https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx7e4fb28fe1443248&redirect_uri=http://www.ebfree.com/huatong/index.php/Phone/Honey/task_one&response_type=code&scope=snsapi_base&state=123#wechat_redirect');
	            }
	            /*echo "提交信息如下：<br/>";
	            dump(I('post.'));
	            echo "成员id：<br/>".$member[0]['id'];*/
	        }
        }
        
    }
}