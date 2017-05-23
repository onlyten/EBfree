<?php
/**
 * 前端优惠券领取
 * @authors nkuwilson (nkuwilson@qq.com)
 * @date    2016-04-07 21:13:48
 * @version v1.0
 */

namespace Phone\Controller;
use Think\Controller;
class CouponController extends Controller {
    
    /**
     * 卡券列表
     * @authors nkuwilson (nkuwilson@qq.com)
     * @date   2016-04-18
     * @return [type]     [description]
     */
    public function index()
    {
        $m = M('coupon');
        $coupon = $m->select();

        foreach ($coupon as $key => $value) {
            if ((int)$value['begin_timestamp'] > time()) {
                continue;
            }else if ((int)$value['end_timestamp'] < time()) {
                continue;
            }else{
                // dump($value);
                $coupon_valid[] = $value;
            }
        }
    	
    	//dump($signPackage);
        $this->assign('coupon_valid',$coupon_valid);
    	$this->display();
    }


    /**
     * 领取卡券
     * @authors nkuwilson (nkuwilson@qq.com)
     * @date   2016-04-18
     * @return [type]     [description]
     */
    public function coupon_get()
    {

        $jssdk = new \Vendor\Wechat\Jssdk(C('WECHAT_CONFIG'));
        $access_token = S('WX_ACCESS_TOKEN');
        if (!$access_token) {
            $access_token = $jssdk->getToken();
            S('WX_ACCESS_TOKEN', $access_token, 7000);
        }
        $jssdk->access_token = $access_token;

        $id = I('get.id');
        $m = M('coupon');
        $coupon = $m->where('id = '.$id)->find();
        $signPackage = $jssdk->GetSignPackage($coupon['card_id']);
        $this->assign('signPackage',$signPackage);
        $this->display();

    }


    /**
     * 用户选择使用的卡券
     * @authors nkuwilson (nkuwilson@qq.com)
     * @date   2016-04-15
     * @return [type]     [description]
     */
    public function coupon_list($open_id)
    {
        $d = D('Coupon');
        $map['open_id'] = $open_id;
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
        $this->assign('coupon_consume',$coupon_consume);
        $this->assign('coupon_before',$coupon_before);
        $this->assign('coupon_after',$coupon_after);
        $this->assign('coupon_valid',$coupon_valid);
        $this->display();
    }

    /**
     * 处理用户选择的卡券
     * @authors nkuwilson (nkuwilson@qq.com)
     * @date   2016-04-15
     * @param  string     $value [description]
     * @return [type]            [description]
     */
    public function user_coupon_consume()
    {
        $d = D('Coupon');
        $map['code']  = array('in',I('post.coupon'));
        $youhui = $d->where($map)->select();
        $total = 0;
        for($i=0;$i<count($youhui);$i++){
            $total = $total + $youhui[$i]['reduce_cost'];
        }
        $urlurl = cookie('urlurl');
        cookie('urlurl',null);
        $arr = explode('&price=',$urlurl);
        $brr = $arr[1] - $total/100;
        $newurl = $arr[0]."&price=".$brr."&total=".$total."&price_ke=".$arr[1];
        header ( "location:$newurl" );
        //$this->display();
        if (IS_POST) {

            $wechat = new \Vendor\Wechat\Wechat(C('WECHAT_CONFIG'));
            $access_token = S('WX_ACCESS_TOKEN');
            if (!$access_token) {
                $access_token = $wechat->getToken();
                S('WX_ACCESS_TOKEN', $access_token, 7000);
            }
            $wechat->access_token = $access_token;
            dump($access_token);

            $coupon = I('post.coupon');
            $map['code']  = array('in',$coupon);
            $m = M('user_coupon');
            if ($m->where($map)->setField('status',2)) {
                //改变status为已使用

                if ($wechat->coupon_consume($coupon)) {
                    exit("用户卡券微信端已核销，数据库状态更改成功");
                }else{
                    exit("用户卡券微信端核销失败");
                }

            }else{
                exit("用户卡券数据库状态更改失败");
            }           
            
        }
    }

}
?>