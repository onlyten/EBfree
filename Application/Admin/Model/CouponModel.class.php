<?php
/**
 * 优惠券模型
 * @authors nkuwilson (nkuwilson@qq.com)
 * @date    2016-04-04 19:05:38
 * @version v1.0
 */

namespace Admin\Model;
use Think\Model\RelationModel;
class CouponModel extends RelationModel {

	/* 微信操作句柄 */
    protected $wechat;
    /* 接收的消息体 */
    protected $data;
    
    /* 自动完成 */
    protected $rules = array(
		array('logo_url','logo_server_upload',3,'callback') , // 将服务器上的商户logo上传至微信端
		array('color','color_transfer',3,'callback') , // 将服务器上的商户logo上传至微信端
		array('begin_timestamp','strtotime',3,'function') , // 日期转换为时间戳
		array('end_timestamp','strtotime',3,'function') , // 日期转换为时间戳
		array('can_share','can_share_bool',3,'callback') , // 日期转换为时间戳
		array('can_give_friend','can_give_friend_bool',3,'callback') , // 日期转换为时间戳
    );


	/**
	 * 将服务器上的商户logo上传至微信端
	 * @authors nkuwilson (nkuwilson@qq.com)
	 * @date   2016-04-04
	 * @param  [type]     $logo_url [本地服务器url]
	 * @return [type]               [微信端url]
	 */
    public function logo_server_upload($logo_url)
    {
    	$this->wechat = new \Vendor\Wechat\Wechat(C('WECHAT_CONFIG'));
    	$access_token = S('WX_ACCESS_TOKEN');
        if (!$access_token) {
            $access_token = $this->wechat->getToken();
            S('WX_ACCESS_TOKEN', $access_token, 3600);
        }
        $this->wechat->access_token = $access_token;

    	// getcwd获取当前目录绝对路径 curl上传文件可能和php版本有关 dirname(__FILE__)也可获取绝对路径
    	$file = getcwd()."/..".$logo_url;
    	$this->data = $this->wechat->logo_upload($file);
    	if ($this->data) {
    		return $this->data['url'];
    	}else {
			return false;
		}
    	
    }

	/**
	 * 用户所选颜色值转换为微信卡券颜色编码
	 * @authors nkuwilson (nkuwilson@qq.com)
	 * @date   2016-04-04
	 * @param  [type]     $color [用户选择的颜色值]
	 * @return [type]            [微信卡券颜色编码]
	 */
    public function color_transfer($color)
    {
    	return $this->wechat->coupon_color_code($color);
    }

	/**
	 * lib_data转换为bool类型
	 * @authors nkuwilson (nkuwilson@qq.com)
	 * @date   2016-04-04
	 * @param  [type]     $can_share [lib_data表id字段]
	 * @return [type]                [bool值]
	 */
    public function can_share_bool($can_share)
    {
    	switch ($can_share) {
    		case 9:
    			return true;
    			break;
    		case 10:
    			return false;
    			break;
    		default:
    			return false;
    			break;
    	}
    }

	/**
	 * lib_data转换为bool类型
	 * @authors nkuwilson (nkuwilson@qq.com)
	 * @date   2016-04-04
	 * @param  [type]     $can_give_friend [lib_data表id字段]
	 * @return [type]                      [bool值]
	 */
    public function can_give_friend_bool($can_give_friend)
    {
    	switch ($can_give_friend) {
    		case 11:
    			return true;
    			break;
    		case 12:
    			return false;
    			break;
    		default:
    			return false;
    			break;
    	}
    }


    
}