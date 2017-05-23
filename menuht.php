<?php

header("Content-type: text/html; charset=utf-8");  
header("Content-type: text/html; charset=gb2312");  


$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx7e4fb28fe1443248&secret=521b51fa1c211e00e68fcdd55732b822
";

$content = file_get_contents($url);

$info = json_decode($content);

print_r($info);

echo "token get ovre <br>";

echo $info->access_token;

 $ACCESS_TOKEN=$info->access_token;
  
 $data = '{
     "button":[
    {
           "name":"我的蜂巢", 
           "type":"view",
           "url":"https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx7e4fb28fe1443248&redirect_uri=http://serv2.matesofts.com/huatong/index.php/Phone/Honey/honey&response_type=code&scope=snsapi_base&state=123#wechat_redirect"
         
       },

       
            
            ]


      "button":[
    {
           "name":"主页", 
           "type":"view",
           "url":"https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx7e4fb28fe1443248&redirect_uri=http://serv2.matesofts.com/huatong/index.php/Phone/index&response_type=code&scope=snsapi_base&state=123#wechat_redirect"
         
       },

       
            
            ]
       

     


         
 }';

 $ch = curl_init(); 
 
 curl_setopt($ch, CURLOPT_URL, "https://api.weixin.qq.com/cgi-bin/menu/create?access_token={$ACCESS_TOKEN}"); 
 curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
 curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
 curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
 curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
 curl_setopt($ch, CURLOPT_AUTOREFERER, 1); 
 curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
 $tmpInfo = curl_exec($ch); 
 if (curl_errno($ch)) {  
echo 'Errno'.curl_error($ch);
 }
 
 curl_close($ch); 
 var_dump($tmpInfo);  
 

?>