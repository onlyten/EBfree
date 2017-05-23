<?php
/**
 * 课程管理
 * @authors nkuwilson (nkuwilson@qq.com)
 * @date    2016-03-20 20:46:25
 * @version v1.0
 */
namespace Admin\Controller;
use Think\Controller;
class CouponController extends CommonController {


    /* 微信操作句柄 */
    private $wechat;
    /* 接收的消息体 */
    private $data;


    public function index()
    {
        $m = M("coupon");
        /*分页start*/
        if (I('get.page_num')) {
            $page_num = I('get.page_num');
            /*条件搜索，点击下一页时，条件不变*/
            if (cookie('coupon_map')) {
                cookie('coupon_map',str_replace('\\','',cookie('coupon_map'))); 
                cookie('coupon_user_map',str_replace('\\','',cookie('coupon_user_map'))); 
                $map = ch_str_array(cookie('coupon_map'),true);
                $user_map = ch_str_array(cookie('coupon_user_map'),true);
            }           
        }else{
            $page_num = 1;
        }
        trace($page_num,'page_num');
        $this->assign('page_num',$page_num);
        $coupon_count = $m->where($map)->count();
        $each_page = C('EACH_PAGE');
        trace($each_page,'each_page');
        $page_sum = ceil($coupon_count/$each_page);
        trace($page_sum,'page_sum');
        $this->assign('page_sum',$page_sum);
        /*分页end*/
        $coupon = $m->where($map)->page($page_num,$each_page)->select();
        $this->assign('user_map',$user_map);
        $this->assign('map',$map);
        $this->assign('coupon',$coupon);
        $this->display();
    }

    public function add()
    {
        $m = M('course');
        $course = $m->select();
        $this->assign('course',$course);

        /* 读取lib_data */
        $m = M('lib_data');
        $lib_id = 4;
        $can_share = $m->where('lib_id = '.$lib_id)->select();
        $this->assign('can_share',$can_share);
        $lib_id = 5;
        $can_give_friend = $m->where('lib_id = '.$lib_id)->select();
        $this->assign('can_give_friend',$can_give_friend);

        $this->display();
    }

    public function add_update()
    {
        $m = M("coupon");
        if (IS_POST) {

            $data = I('post.');
            $data['can_share'] = I('post.can_share') == 9 ? true : false;
            $data['can_give_friend'] = I('post.can_give_friend') == 11 ? true : false;

            $this->wechat = new \Vendor\Wechat\Wechat(C('WECHAT_CONFIG'));
            // $access_token = S('WX_ACCESS_TOKEN');
            if (!$access_token) {
                $access_token = $this->wechat->getToken();
                S('WX_ACCESS_TOKEN', $access_token, 3600);
            }
            $this->wechat->access_token = $access_token;
            // getcwd获取当前目录绝对路径 curl上传文件可能和php版本有关 dirname(__FILE__)也可获取绝对路径
            $file = getcwd()."/..".$data['logo_url'];
            $logo_url_data = $this->wechat->logo_upload($file);
            if ($logo_url_data) {
                $data['logo_url'] = $logo_url_data['url'];
            }else {
                $this->redirect('coupon/index', array('msg_text' => '商户logo上传失败，logo_url参数错误','msg_class_name' => 'danger'), 0);
            }
            $data['color'] = $this->wechat->coupon_color_code($data['color']);
            $data['begin_timestamp'] = strtotime($data['begin_timestamp']);
            $data['end_timestamp'] = strtotime($data['end_timestamp']);
            
            $jsonStr = $this->wechat->coupon_add($data,'cash');
            if ($jsonStr) {
                $jsonArr = json_decode($jsonStr, true);
                $card_id = $jsonArr['card_id'];
                $data['card_id'] = $card_id;

                if ($m->data($data)->add()) {
                    $this->redirect('coupon/index', array('msg_text' => '优惠券新增成功','msg_class_name' => 'success'), 0);
                }else{
                    $this->redirect('coupon/index', array('msg_text' => '优惠券新增失败','msg_class_name' => 'danger'), 0);
                }

            }else{
                $this->redirect('coupon/index', array('msg_text' => '优惠券新增失败，微信参数配置错误','msg_class_name' => 'danger'), 0);
            }

        }

    }

    public function edit()
    {
        $this->display();
    }


    public function delete()
    {
        if (I('get.coupon_id')) {
            $map['id'] = I('get.coupon_id');
            $m = M('coupon');
            if ($m->where($map)->limit('1')->delete()) {
                $this->redirect('coupon/index', array('msg_text' => '优惠券记录删除成功','msg_class_name' => 'success'), 0);
            }else{
                $this->redirect('coupon/index', array('msg_text' => '优惠券记录删除失败','msg_class_name' => 'danger'), 0);
            } 
        }else{
            $this->redirect('coupon/index', array('msg_text' => '参数错误，请重试','msg_class_name' => 'danger'), 0);
        }
    }


    public function delete_batch()
    {
        if (I('post.coupon_id')) {
            $coupon_id = json_decode(stripslashes(htmlspecialchars_decode(I('post.coupon_id'))),true);
            $map['id'] = array('in',$coupon_id);
            $m = M('coupon');
            if ($m->where($map)->delete()) {
                $gritter['msg_text'] = "任务记录批量删除成功";
                $gritter['msg_class_name'] = "success";
            }else{
                $gritter['msg_text'] = "任务记录批量删除失败";
                $gritter['msg_class_name'] = "danger";
            } 
        }else{
            $gritter['msg_text'] = "参数错误，请重试";
            $gritter['msg_class_name'] = "danger";
        }
        $this->ajaxReturn($gritter);
    }


}