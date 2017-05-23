<?php
/**
 * Admin 函数
 * @authors nkuwilson (nkuwilson@qq.com)
 * @date    2016-02-05 19:13:44
 * @version v1.0
 */

/**
 * 含有中文字符的数组转换为json字符串
 * @param  string $array [description]
 * @return [type]        [description]
 */
function ch_array_str($array='')
{	
	ch_array_urlencode($array);
	$str = json_encode($array);
	return $str;
}

/**
 * 含有中文字符的数组进行urlencode编码，这里利用引用，改变调用函数中参数变量的值
 * @param  string &$array [description]
 * @return [type]         [description]
 */
function ch_array_urlencode(&$array='')
{
	foreach ($array as $key => &$value) {
		if(is_array($value)){
			ch_array_urlencode($value);
		}else{
			$value = urlencode($value);
		}		
	}
}

/**
 * json字符串转换为含有中文字符的数组
 * @param  string $str [description]
 * @return [type]      [description]
 */
function ch_str_array($str='')
{
	$array = json_decode($str,true);
	ch_array_urldecode($array);
	return $array;
}

/**
 * 含有中文字符的数组进行urldecode编码，这里利用引用，改变调用函数中参数变量的值
 * @param  string &$array [description]
 * @return [type]         [description]
 */
function ch_array_urldecode(&$array='')
{
	foreach ($array as $key => &$value) {
		if(is_array($value)){
			ch_array_urldecode($value);
		}else{
			$value = urldecode($value);
		}		
	}
}

?>