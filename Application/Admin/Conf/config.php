<?php
return array(
	// 默认主题包
	'DEFAULT_THEME'    =>    'default',
	// 模板替换规则
	'TMPL_PARSE_STRING' => array(
		'__STYLE__' => __ROOT__ . '/Public/Admin',
		'__MATRIX__' => __ROOT__ . '/Public/Admin/matrix',
	),
	// 显示页面Trace信息
	'SHOW_PAGE_TRACE' =>false, 
	// 分页，每页的记录数
	'EACH_PAGE' => 12,
	'UPLOAD_DIR' => __ROOT__ . '/Uploads/Admin/',
	'USER_NAME' => 'admin',
	'PASS_WORD' => 'admin',

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