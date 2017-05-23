<?php
namespace Phone\Controller;
use Think\Controller;
//微信支付类
class MsWxpayController extends Controller {
	//获取access_token过程中的跳转uri，通过跳转将code传入jsapi支付页面
	public function js_api_call() {
        require_once(__DIR__ . '/JsApiPay.php');
        vendor('matewxpay.WxPayApi');

        $tools = new \JsApiPay();
       // $openId = $tools->GetOpenid();
        $openId = I('get.openid');
        $hao = I('get.member_id');
        $level_id = I('get.level_id');
        //session('level_id',$level_id);
       //  echo $hao."<br/>";
      	// echo $openId."<br/>";
       //  echo $level_id."<br/>";
       $mm = M('member');
       $mmaapp['id'] = I('get.member_id');
       $member = $mm->where($mmaapp)->find();
       $m = M('level');
       $map['id'] = $level_id;
       $level = $m->where($map)->find();
       $n = M('course');
       $mapp['id'] = $level['course_id'];
       $course = $n->where($mapp)->find();
		$fee = I('get.price')*100 - $member['red_packet']*100;
        //$fee = 1;
        if($fee<=0){
            $fee=88888800;
        }else{
            $fp = fopen(__DIR__ . "/debug3.txt", "a+");

            fwrite($fp, "kejia". $course['price'] . " *** " . $member['red_packet'] . " " .$fee . " "  ."\r\n");

            fclose($fp);
            $mnm = M('member_level');
            $mmap['level_id'] = I('get.level_id');
            $mmap['member_id'] = I('get.member_id');
            $mmap['success_or'] = 0;
            $member_level = $mnm->where($mmap)->order('id desc')->limit(1)->find();


            $mmm = M('small');
            $small_map['member_level_id'] = $member_level['id'];
            $small_select = $mmm->where($small_map)->order('id desc')->limit(1)->find();

            if($small_select['member_level_id'] != $member_level['id']){
                $data['member_id'] = I('get.member_id');
                $data['level_id'] = I('get.level_id');
                $data['fee'] = $member['red_packet'];
                $data['openid'] = $openId;
                $data['member_level_id'] = $member_level['id'];
                $data['time'] = time();
                $mmm->data($data)->add();
            }
        }
        
        $haoo =$hao."***".$level_id."***".I('get.price')."***".$openId."***".$fee."***".$member_level['id'];
		$input = new \WxPayUnifiedOrder();
        $input->SetBody("EB在线英语");
        $input->SetAttach($haoo);
        //$input->SetAttach("aaaaaaaaa");
        $input->SetOut_trade_no(\WxPayConfig::MCHID.date("YmdHis"));
        $input->SetTotal_fee($fee);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("test");
        $input->SetNotify_url("http://www.ebfree.com/huatong/index.php/Phone/MsWxpay/js_notify");
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $order = \WxPayApi::unifiedOrder($input);

		//var_dump($order);
        $jsApiParameters = $tools->GetJsApiParameters($order);

		//var_dump($jsApiParameters);

        //获取共享收货地址js函数参数
        $editAddress = $tools->GetEditAddressParameters();

        /* 领取优惠券start */
        $jssdk = new \Vendor\Wechat\Jssdk(C('WECHAT_CONFIG'));
        // $access_token = S('WX_ACCESS_TOKEN');
        if (!$access_token) {
            $access_token = $jssdk->getToken();
            S('WX_ACCESS_TOKEN', $access_token, 7000);
        }
        $jssdk->access_token = $access_token;
        $signPackage = $jssdk->GetSignPackage();
        // dump($signPackage);
        $this->assign('signPackage',$signPackage);
        /* 领取优惠券end */
        $this->assign('member_id',I('get.member_id'));
        $this->assign('level_id',I('get.level_id'));
        $this->assign('member',$member);
        $this->assign('open_id', $openId);
        $this->assign('paraVal', $jsApiParameters);
        $this->assign('course', $course['title']);
        $this->assign('price',$fee/100);
        $this->assign('pricee',$course['price']);
        $this->assign('kouchu',I('get.price'));


        $this->assign('total',I('get.total'));
        $this->assign('price_ke',I('get.price_ke'));
        // if($fee == 88888888){
        //     $mn = M('member_level');
        //     $mma['member_id'] = I('get.member_id');
        //     $mma['level_id'] = I('get.level_id');
        //     $mma['success_or'] != '2';
        //     $daaa['user_pay'] = I('get.price');
        //     $daaa['success_or'] = 1;
        //     if($mn->where($mma)->order('id desc')->limit(1)->data($daaa)->save()){
        //         $member = $mm->where($mmaapp)->find();
        //         $dad['red_packet'] = $member['red_packet'] - I('get.price');
        //         if($mm->where($mmaapp)->data($dad)->save()){
        //             echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
        //             echo  "<script>alert('购买成功！');window.location.href='https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx7e4fb28fe1443248&redirect_uri=http://www.ebfree.com/huatong/index.php/Phone/Honey/honey&response_type=code&scope=snsapi_base&state=123#wechat_redirect'</script>";
        //         }
        //     }
             
            
        // }else{
        //     $this->display('zhifu');
        // }
        $this->display('zhifu');
        
	}


        public function js_api_cal() {
        require_once(__DIR__ . '/JsApiPay.php');
        vendor('matewxpay.WxPayApi');

        $tools = new \JsApiPay();
        $zouzou = M('member');
        $jiejie['id'] = I('get.member_id');
        $op = $zouzou->where($jiejie)->limit(1)->find();
        //$openId = "oA9VnxC3EioS9xxI5DDrs5mZnHlg";
        $openId = $op['open_id'];
        $fee = I('get.money')*100;
        $member_id = I('get.member_id');
        if(I('get.mission_id')){
            $mission_id = I('get.mission_id');
            $m = M('dashang');
            $data['member_id'] = $member_id;
            $data['mission_id'] = $mission_id;
            $data['money'] = I('get.money');
            $m->data($data)->add(); 
            $haoo = $member_id."***".$mission_id;
        }
        if(I('get.serial_id')){
            $serial_id = I('get.serial_id');
            $m = M('dashang');
            $data['member_id'] = $member_id;
            $data['serial_id'] = $serial_id;
            $data['money'] = I('get.money');
            $m->data($data)->add(); 
            $haoo = $member_id."***".$serial_id;
        }
        
        
        $input = new \WxPayUnifiedOrder();
        $input->SetBody("EB在线英语");
        $input->SetAttach($haoo);
        //$input->SetAttach("aaaaaaaaa");
        $input->SetOut_trade_no(\WxPayConfig::MCHID.date("YmdHis"));
        $input->SetTotal_fee($fee);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("test");
        $input->SetNotify_url("http://www.ebfree.com/huatong/index.php/Phone/MsWxpay/js_notify");
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $order = \WxPayApi::unifiedOrder($input);

        //var_dump($order);
        $jsApiParameters = $tools->GetJsApiParameters($order);

        //var_dump($jsApiParameters);

        //获取共享收货地址js函数参数
        $editAddress = $tools->GetEditAddressParameters();

        /* 领取优惠券start */
        $jssdk = new \Vendor\Wechat\Jssdk(C('WECHAT_CONFIG'));
        // $access_token = S('WX_ACCESS_TOKEN');
        if (!$access_token) {
            $access_token = $jssdk->getToken();
            S('WX_ACCESS_TOKEN', $access_token, 7000);
        }
        $jssdk->access_token = $access_token;
        $signPackage = $jssdk->GetSignPackage();
        // dump($signPackage);
        $this->assign('signPackage',$signPackage);
        /* 领取优惠券end */


        $this->assign('open_id', $openId);
        $this->assign('paraVal', $jsApiParameters);
        $this->display('js_api_callyao1');
    }


	public function js_notify() {
		$fp = fopen(__DIR__ . "/debug.txt", "a+");

		fwrite($fp, "aaaa");

		fclose($fp);


        vendor('matewxpay.WxPayApi');

		$input = new \WxPayOrderQuery();
     	$input->SetOut_trade_no('1005200182201601062533686590');
    	var_dump(\WxPayApi::orderQuery($input));

		$input = new \WxPayOrderQuery();

		$input->SetTransaction_id('1005200182201601062533686590');
	    var_dump(\WxPayApi::orderQuery($input));



        $order_sn = "20150109113322";
        if (empty($order_sn)) {
            header('location:'.__ROOT__.'/');
        }
        require_once(__DIR__ . '/MsWxpayNotify.php');
		

	

        $notify = new \PayNotifyCallBack();
        $notify->Handle(false);

		$this->display('js_api_callyao2');
		
	}


	public function js_query() {
		$fp = fopen(__DIR__ . "/debug.txt", "a+");

		fwrite($fp, "bbbb");

		fclose($fp);


        vendor('matewxpay.WxPayApi');

		$input = new \WxPayOrderQuery();
     	$input->SetOut_trade_no('1005200182201601062532706447');
    	var_dump(\WxPayApi::orderQuery($input));
    	echo "<br/>";
		$input = new \WxPayOrderQuery();

		$input->SetTransaction_id('1005200182201601062532706447');
	    var_dump(\WxPayApi::orderQuery($input));

	    echo "<br/>";
		$input = new \WxPayOrderQuery();

		$input->SetTransaction_id('1005200182201601062533686590');
	    var_dump(\WxPayApi::orderQuery($input));
	}

}
?>