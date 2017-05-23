<?php
ini_set('date.timezone','Asia/Shanghai');
error_reporting(E_ERROR);

vendor('matewxpay.WxPayApi');
vendor('matewxpay.WxPayNotify');



class PayNotifyCallBack extends WxPayNotify
{
	//查询订单
	public function Queryorder($transaction_id)
	{
		$input = new WxPayOrderQuery();
		$input->SetTransaction_id($transaction_id);
		$result = WxPayApi::orderQuery($input);
		//echo "hello";
		//var_dump($result);

		
		if(array_key_exists("return_code", $result)
			&& array_key_exists("result_code", $result)
			&& $result["return_code"] == "SUCCESS"
			&& $result["result_code"] == "SUCCESS")
		{
			return true;
		}
		return false;
	}
	
	//重写回调处理函数
	public function NotifyProcess($data, &$msg)
	{	
		

		//$link = mysql_connect('qdm102668679.my3w.com','qdm102668679','Msd2014793') or die('连接失败！');
		//mysql_select_db('qdm102668679_db',$link)or die('数据库链接失败!');
		//$sql = "update aishang_dingdan set successor='1' where dingdanhao='".$data["attach"]."'";
		//$res = mysql_query($sql,$link);
		
		//mysql_close($link);


		$fp = fopen(__DIR__ . "/debug.txt", "a+");

		fwrite($fp, "ookk?");

		fclose($fp);

		$notfiyOutput = array();
		
		if(!array_key_exists("transaction_id", $data)){
			$msg = "输入参数不正确";
			 $fp = fopen(__DIR__ . "/debug.txt", "a+");
				fwrite($fp, "ok".$data["transaction_id"]."\r\n");
				fclose($fp);
			return false;
		}
		//查询订单，判断订单真实性
		if(!$this->Queryorder($data["transaction_id"])){
			$msg = "订单查询失败";
			return false;
		}

	    //$fp = fopen(__DIR__ . "/debug.txt", "a+");

		//fwrite($fp, "ok".$data["transaction_id"]."***".$data["attach"].$sql."\r\n");

		//fclose($fp);
		$str = $data["attach"];
		$fe = explode("***",$str);
		$member_id = $fe[0];
		$level_id = $fe[1];
		$user_pay = $fe[2];
		$openid = $fe[3];
		$money = $fe[4];
		$member_level_id = $fe[5];
		




		
		$link = mysql_connect('qdm102668679.my3w.com','qdm102668679','Msd2014793') or die('连接失败！');
		mysql_select_db('qdm102668679_db',$link)or die('数据库链接失败!');
		//$sql_1=" LOCK TABLES huatong_small WRITE ";
		//$sql_2=" LOCK TABLES huatong_small READ ";

		//mysql_query($sql_1);
		//mysql_query($sql_2);
		// $fp = fopen(__DIR__ . "/debug8.txt", "a+");

		// fwrite($fp, "test". $user_pay . $member_id . $level_id ."\r\n");

		// fclose($fp);

		$sql = "update huatong_member_level set success_or='1',user_pay=".$user_pay." where id='".$member_level_id."' and success_or = '0' and user_pay = '0' ORDER BY id DESC LIMIT 1";
		//$ssql = "update huatong_member set red_packet='0' where id='".$member_id."' and open_id='".$openid."' ORDER BY id DESC LIMIT 1";
		$sqll = "select *  from huatong_member_level where id='".$member_level_id."' and success_or = '0' ORDER BY id DESC LIMIT 1";
		$ress = mysql_query($sqll,$link);
		//$rress = mysql_query($ssql,$link);
		$test = mysql_fetch_row($ress);
		if($test){
			$res = mysql_query($sql,$link);
		}

		$ssql = "select * from huatong_small where member_id='".$member_id."' and openid='".$openid."' and level_id='".$level_id."' and member_level_id='".$member_level_id."' ORDER BY id DESC LIMIT 1";
		$resres = mysql_query($ssql,$link);
		$row = mysql_fetch_assoc($resres);
		if($row['success'] == '1'){
			$sql1 = "select *  from huatong_member where id='".$member_id."' LIMIT 1";
			$res1 = mysql_query($sql1,$link);
			
			$count = mysql_affected_rows($link);
			if($count == 0){
				$fp = fopen(__DIR__ . "/debug1.txt", "a+");

		        fwrite($fp, "select "."\r\n". $sql1 . "\r\n");

                $errmsg = mysql_error();
		        fwrite($fp, "mysql error  ". $errymsg . "\r\n");

		        fclose($fp);

		        die();
			}
			$row1 = mysql_fetch_assoc($res1);

			if ($row1 == false)
			{
                $fp = fopen(__DIR__ . "/debug1.txt", "a+");
		        fwrite($fp, "fetch error  "."\r\n". $sql1 . "\r\n");

                $errmsg = mysql_error();
		        fwrite($fp, "mysql error  ". $errymsg . "\r\n");

		        fclose($fp);

		        die();
			}
			$sql2 = "update huatong_member set red_packet='".$row1['red_packet']."' - '".$row['fee']."' where id='".$member_id."' ";
			//$sql2 = "update huatong_member set red_packet = 0 where id='".$member_id."' ";
			$res2 = mysql_query($sql2,$link);
			$testime = time();
			$moneyy = $row['fee'];
			$mingxi = iconv("utf-8","gb2312","购买课程(微信)");
			$sql4 = "insert into huatong_detail (money,time,remarks,member_id)  values ('$moneyy','$testime','$mingxi','$member_id')";
			$res4 = mysql_query($sql4,$link);
			$nowtime = time();
                $fp = fopen(__DIR__ . "/debug1.txt", "a+");

		        fwrite($fp, "okkkkkk pay "."\r\n". $nowtime . "***" . $member_level_id . "***" . $row1['red_packet'] . " *** " . $row['fee'] . "***" .$member_id . "***" . $level_id ."\r\n");

		        fclose($fp);

			$sql3 = "update huatong_small set success='2' where member_id='".$member_id."' and openid='".$openid."' and level_id='".$level_id."' and member_level_id='".$member_level_id."' ORDER BY id DESC LIMIT 1";
			$res3 = mysql_query($sql3,$link);
		}


		$sqql = "update huatong_dashang set success='1' where member_id='".$member_id."' and mission_id='".$level_id."' ORDER BY id DESC LIMIT 1";
		$rese = mysql_query($sqql,$link);

		$sqqll = "update huatong_dashang set success='1' where member_id='".$member_id."' and serial_id='".$level_id."' ORDER BY id DESC LIMIT 1";
		$resee = mysql_query($sqqll,$link);

		mysql_query('UNLOCK TABLES');
		mysql_close($link);
		// $fp = fopen(__DIR__ . "/debug.txt", "a+");

		// fwrite($fp, "ok".$data["transaction_id"]."***".$data["attach"].$sql."\r\n");

		// fclose($fp);
		return true;
	}
}


