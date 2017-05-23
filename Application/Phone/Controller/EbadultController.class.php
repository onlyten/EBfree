<?php
namespace Phone\Controller;
use Think\Controller;
class EbadultController extends Controller {
    public function ebadult(){
        $openid = I('get.openid');
        $m = M('course');
        $map['type'] = '2';
        $adult = $m->where($map)->select();
        //dump($adult);
        //die("OK"); 
        $this->assign("openid",$openid);
        $this->assign("adult",$adult);
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

    public function test_view()
    {
    	echo "string";
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
                    $post = "http://www.ebfree.com/huatong/index.php/Phone/MsWxpay/js_api_call?member_id=" . $member[0]['id'] ."&openid=" .$openid. "&level_id=" .I('post.level_id')."&price=" .$course[0]['price'];
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