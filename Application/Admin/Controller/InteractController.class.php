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
    
    /* 微信操作句柄 */
    private $wechat;
    /* 接收的消息体 */
    private $data;

    public function _initialize() {
        // 初始化微信
        $this->wechat = new \Vendor\Wechat\Wechat(C('WECHAT_CONFIG'));
        // $access_token = S('WX_ACCESS_TOKEN');
        if (!$access_token) {
            $access_token = $this->wechat->getToken();
            S('WX_ACCESS_TOKEN', $access_token, 7000);
        }
        dump($access_token);
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

    /**
     * 时间处理方法
     * @authors nkuwilson (nkuwilson@qq.com)
     * @date   2016-04-16
     * @param  [type]     $event [description]
     * @return [type]            [description]
     */
    public function event($event) {
        switch ($event) {
            case 'subscribe': // 关注事件
                self::subscribe();
                break;
            case 'unsubscribe': // 取消关注
                self::unsubscribe();
                break;
            case 'CLICK': // 点击菜单
                $this->keyword($this->data['eventkey']);
                break;
            case 'VIEW': // 跳转菜单
                break;
            case 'LOCATION': // 上报位置
                break;
            case 'user_get_card': //用户领取卡券 data中的键值需要小写
                S('WEI_CHAO', $this->data['tousername'], 7000);
                $m = M('user_coupon');
                $id = $m->where('code='.$this->data['usercardcode'])->getField('id');
                if (!$id) {                    
                    $card['card_id'] = $this->data['cardid'];
                    $card['time'] = $this->data['createtime'];
                    $card['code'] = $this->data['usercardcode'];
                    $card['open_id'] = $this->data['fromusername'];
                    $m->data($card)->add();
                }
                break;
            case 'user_consume_card': //用户领取卡券 data中的键值需要小写
                $this->wechat->response('核销卡券成功');
                break;
            default:
                $this->wechat->response('无法识别');
                break;
        }
    }
}