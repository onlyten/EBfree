<?php
return array(
	//'配置项'=>'配置值'


	/* 模板相关配置 */
    'TMPL_PARSE_STRING' => array(
        '__STYLE__' 		=> '/huatong/Public/',
        '__CSS__'    => '/huatong/Public/' . MODULE_NAME . '/css',
        '__JS__'    => '/huatong/Public/' . MODULE_NAME . '/js',
		'__INDEX__'    => '/index.php',
		'__PICURL__'    => 'http://www.ebfree.com/weimei/Public/upload/img',
		'__WXPHONE__' => '400-4000-000',//手机号
		'__URL__' => 'http://www.ebfree.com',
		'__IMG__' => 'http://www.ebfree.com/huatong/Public/images',
		'__PHONE__' =>'123456789,'
		
    ),


    // 微信接口处理地址
	'WECHAT_CONFIG' => array(
	    'appid'  => 'wx7e4fb28fe1443248',
	    'secret' => '521b51fa1c211e00e68fcdd55732b822',
	    'token'  => 'nkuwilson',
	    'aeskey' => 'wnz1zCXwwilson22vunVP6MRC6n4oBcq',
	    'mch_id' => '1XXX9XXXX',
	    'pem'    => 'aXXXXXXient',
	    'paykey' => '5b4XXXXXXXXXf6d2',
	),


);
