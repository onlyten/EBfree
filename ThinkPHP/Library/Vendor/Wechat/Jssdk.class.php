<?php
/**
 * 卡券签名类库，基于微信前端的开发工具
 * 卡券签名和JSSDK的签名完全独立，两者的算法和意义完全不同，请不要混淆
 * @authors nkuwilson (nkuwilson@qq.com)
 * @date    2016-04-10 20:46:28
 * @version v1.0
 */

namespace Vendor\Wechat;
use Vendor\Wechat\Wechat;
class Jssdk extends Wechat {
    

	/**
	 * 获取cardExt数组
	 * @authors nkuwilson (nkuwilson@qq.com)
	 * @date   2016-04-11
	 * @param  string     $value [description]
	 */
    public function GetSignPackage($card_id)
    {
        $api_ticket = $this->getapiTicket();
        if ($api_ticket) {
	        $signPackage = array(
				'api_ticket' => $api_ticket,
				'card_id'	 => $card_id,
				'timestamp'	 => NOW_TIME,
				'nonce_str'	 => self::_getRandomStr()
	    	);
	    	$signPackage['signature'] = $this->orderSHA1($signPackage);
	    	return $signPackage;	
        }else{
        	return false;
        }
			

    }

	/**
	 * 获取卡券特有的api_ticket
	 * @authors nkuwilson (nkuwilson@qq.com)
	 * @date   2016-04-11
	 * @return [type]     [description]
	 */
    public function getapiTicket() {

    	$api_ticket = S('API_TICKET');
        if (!$api_ticket) {
            $params = array(
				'access_token'  => $this->access_token,
				'type'          => 'wx_card'
			);
			$jsonStr = $this->http(self::JSAPI_TICKET_URL, $params);
			$jsonArr = $this->parseJson($jsonStr);
			if ($jsonArr) {
	            S('API_TICKET', $api_ticket, 3600);
				return $this->result['ticket'];
			}else {
				return false;
			}
        }else{
        	return $api_ticket;
        }
        
	}






}