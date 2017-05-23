<?php
return array(
	// 默认主题包
	'DEFAULT_THEME'    =>    'default',
	// 模板替换规则
	'TMPL_PARSE_STRING' => array(
		'__STYLE__' => __ROOT__ . '/Public/Admin',
		'__MATRIX__' => __ROOT__ . '/Public/Admin/matrix',
	),
);