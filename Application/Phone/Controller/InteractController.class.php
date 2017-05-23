<?php
/**
 * 与微信端用户交互，接收消息、发送消息
 * @authors nkuwilson (nkuwilson@qq.com)
 * @date    2016-04-14 22:28:03
 * @version v1.0
 */

namespace Admin\Controller;
use Think\Controller;
class InteractController extends Controller {
    
    public function _initialize() {
        // 初始化微信
        $this->wechat = new \Vendor\Wechat\Wechat(C('WECHAT_CONFIG'));
        $access_token = S('WX_ACCESS_TOKEN');
        if (!$access_token) {
            $access_token = $this->wechat->getToken();
            S('WX_ACCESS_TOKEN', $access_token, 7000);
        }
        $this->wechat->access_token = $access_token;
        $this->wechat->encode = I('get.encrypt_type', '');
        $this->wechat->valid();
        $this->data = $this->wechat->request();
    }

	/**
	 * 消息分流
	 * @authors nkuwilson (nkuwilson@qq.com)
	 * @date   2016-04-14
	 * @return [type]     [description]
	 */
    public function index() {
        if (isset($this->data['msgtype'])) {
            switch ($this->data['msgtype']) {
                case 'text':
                    $ret = $this->keyword($this->data['content']);
                    break;
                case 'event':
                    $this->event($this->data['event']);
                    break;
                default:
                    $this->wechat->response('暂时不支持的消息类型！');
                    break;
            }
        } else {
            $this->wechat->response('微信号暂时无法提供服务！');
        }
    }
}