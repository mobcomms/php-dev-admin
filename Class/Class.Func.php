<?php

class __fn__
{

	function __fn2__()
	{
		return true;
	}

	public function alert($msg, $ret = false)
	{
		$return = '<script>alert("' . $msg . '");</script>';
		if ($ret == false) {
			echo $return;
		} else {
			return $return;
		}
	}

	public function loca($url, $msg = false, $ret = false)
	{
		if (!empty($msg)) {
			$this->alert($msg);
		}
		$return = '<script language="javascript">document.location.href="' . $url . '";</script>';
		if ($ret == false) {
			echo $return;
		} else {
			return $return;
		}
	}

	public function replace($url, $msg = false, $ret = false)
	{
		if(!empty($msg)) {
			$this->alert($msg);
		}
		$return =  '<script language="javascript">document.location.replace("'.$url.'");</script>';
		if ($ret == false) {
			echo $return;
		} else {
			return $return;
		}
	}

	public function hist($msg = false, $step = false, $ret = false)
	{
		if (!empty($msg)) {
			$this->alert($msg);
		}
		$go = ($step != false) ? $step : '-1';
		$return = '<script>history.go("' . $go . '");</script>';
		if ($ret == false) {
			echo $return;
		} else {
			return $return;
		}
	}

	public function closePOP($msg = false, $ret = false, $opener = false)
	{
		if (!empty($msg)) {
			$this->alert($msg);
		}
		if (!empty($opener)) {
			if ($opener === 'reload') {
				echo '<script>opener.document.location.reload();</script>';
			} else {
				echo '<script>' . $opener . '</script>';
			}
		}
		$return = '<script>window.close();</script>';
		if ($ret == false) {
			echo $return;
		} else {
			return $return;
		}
	}

	public function slash($input, $tf = false, $ret = false)
	{
		$return = $input;
		if ($tf == false) {
			$return = htmlspecialchars($return);
			$return = addslashes($return);
		} else if ($ret == true) {
			$return = stripslashes($return);
			$return = htmlspecialchars_decode($return);
		}
		if ($ret == false) {
			return $return;
		} else {
			echo $return;
		}
	}

	public function base($input, $tf = false, $ret = false)
	{
		$return = $input;
		$return = ($tf == false) ? base64_encode(urlencode(base64_encode($return))) : base64_decode(urldecode(base64_decode($return)));
		if ($ret == false) {
			return $return;
		} else {
			echo $return;
		}
	}

	public function cutStr($str, $len, $suf = '...')
	{
		if (strlen($str) <= $len) {
			return $str;
		}
		$cpos = $len - 1;
		$count_2B = 0;
		$lastchar = $str[$cpos];
		while (ord($lastchar) > 127 && $cpos >= 0) {
			$count_2B++;
			$cpos--;
			$lastchar = $str[$cpos];
		}
		if ($count_2B % 2) {
			$len--;
		}
		return substr($str, 0, $len) . $suf;
	}

	public function refer($ref, $val)
	{
		return ($ref != $val) ? false : true;
	}

	public function preg($org, $div)
	{
		return (preg_match('/' . $org . '/', $div) != false) ? true : false;
	}

	public function chk_admin($sess, $admin)
	{
		if (empty($_SESSION[$admin]['idx']) || empty($_SESSION[$admin]['id'])) {
            if(empty($company_code)){
                $uri = $_SERVER['SCRIPT_NAME']; // 현재 URL 경로 가져오기
                $parts = explode("/", $uri); // '/' 기준으로 분리
                $company_code = $parts[1]; // `hana` 부분 추출
            }else{
                $uri = $_SERVER['HTTP_REFERER']; // 현재 URL 경로 가져오기
                $parts = explode("/", $uri); // '/' 기준으로 분리
                $company_code = $parts[3]; // `hana` 부분 추출
            }
			exit($this->loca("/{$company_code}/login.php", "로그인 정보가 없습니다.\\n\\n다시 로그인 해 주세요"));
		}else{
			return $_SESSION[$admin];
		}
	}

	public function chk_admin_pop($sess, $admin)
	{
		if (empty($sess[$admin]['idx']) || empty($sess[$admin]['id'])) {
			exit($this->closePOP("로그인 정보가 없습니다.\\n\\n다시 로그인 해 주세요"));
		}
		return $sess[$admin];
	}

	public function select($object, $option, $ret = false)
	{
		$return = ($object == $option) ? ' selected' : '';
		if ($ret == false) {
			return $return;
		} else {
			echo $return;
		}
	}

	public function checked($object, $option, $ret = false)
	{
		$return = ($object == $option) ? ' checked' : '';
		if ($ret == false) {
			return $return;
		} else {
			echo $return;
		}
	}

	public function checkedIn($object, $option, $ret = false)
	{
		$return = preg_match('/\|' . $option . '\|/', $object) ? ' checked' : '';
		if ($ret == false) {
			return $return;
		} else {
			echo $return;
		}
	}

	public function server()
	{
		echo 'IP : ' . filter_input(INPUT_SERVER, 'REMOTE_ADDR') . '<br /><br />';
		echo '</pre>';
	}

	public function directory($img, $dir)
	{
		$t1 = substr($img, 0, 1);
		$t2 = substr($img, 1, 1);
		$t3 = substr($img, 2, 1);
		$t4 = substr($img, 3, 1);
		if (is_dir($dir . '/' . $t1) == false) {
			mkdir($dir . '/' . $t1, 0755);
		}
		if (is_dir($dir . '/' . $t1 . '/' . $t2) == false) {
			mkdir($dir . '/' . $t1 . '/' . $t2, 0755);
		}
		if (is_dir($dir . '/' . $t1 . '/' . $t2 . '/' . $t3) == false) {
			mkdir($dir . '/' . $t1 . '/' . $t2 . '/' . $t3, 0755);
		}
		if (is_dir($dir . '/' . $t1 . '/' . $t2 . '/' . $t3 . '/' . $t4) == false) {
			mkdir($dir . '/' . $t1 . '/' . $t2 . '/' . $t3 . '/' . $t4, 0755);
		}
		return '/' . $t1 . '/' . $t2 . '/' . $t3 . '/' . $t4 . '/';
	}

	public function menu_main($input, $div, $ret = false)
	{
		$return = ($input == $div) ? ' active' : '';
		if ($ret == false) {
			echo $return;
		} else {
			return $return;
		}
	}

	public function menu_show($sm, $mm, $smd, $mmd, $sess, $link, $cmt)
	{
		$code = $mmd . '_' . $smd;
		if (preg_match('/' . $code . '/is', $sess) != false) {
			return '<dl class="' . (($sm == $smd && $mm == $mmd) ? ' active_sub' : '') . '"><a href="' . $link . '">' . $cmt . '</a></dl>';
		}
	}

	public function route($str)
	{
		$route = array("01" => "캐시키보드", "02" => "네이버", "03" => "다음카카오", "04" => "페이스북");
		return $route[$str];
	}

	public function debug($str, $opt = FALSE)
	{
		echo "<pre>";
		print_r($str);
		echo "</pre>";
		if ($opt === TRUE) exit;
	}


	public function debugSql($str, $varArry)
	{
		for ($i = 0; $i < count($varArry); $i++) {
			$str = preg_replace("/\?/", "'" . $varArry[$i] . "'", $str, 1);
		}

		echo "<pre>";
		print_r($str);
		echo "</pre>";
	}

	// 요일별 색상
	public function dateColor($date)
	{
		$dateYear = substr($date, 0, 4);
		$dateMonth = substr($date, 4, 2);
		$dateDay = substr($date, 6, 2);
		$date = $dateYear . "-" . $dateMonth . "-" . $dateDay;
		$holidayArray = array(
			$dateYear . '-01-01'
		, $dateYear . '-03-01'
		, $dateYear . '-05-05'
		, $dateYear . '-06-06'
		, $dateYear . '-08-15'
		, $dateYear . '-10-03'
		, $dateYear . '-10-09'
		, $dateYear . '-12-25'
		,'2023-05-29'
		,'2023-09-28'
		,'2023-09-29'
		,'2023-09-30'
		);

		$colorArray = array('red', '', '', '', '', '', 'blue');
		$chkData = date("w", strtotime($date));

		if(in_array($date, $holidayArray)){
			return "color_background_orange";
		}

		$chkData = (in_array($date, $holidayArray)) ? "0" : $chkData;
		$return = ($chkData == '0' || $chkData == '6') ? "color_background_" . $colorArray[$chkData] : '';
		return $return;
	}

	public function RemoveXSS($val)
	{
		// remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
		// this prevents some character re-spacing such as <java\0script>
		// note that you have to handle splits with \n, \r, and \t later since they *are*
		// allowed in some inputs
		$val = preg_replace('/([\x00-\x08][\x0b-\x0c][\x0e-\x20])/', '', $val);

		// straight replacements, the user should never need these since they're normal characters
		// this prevents like <IMG SRC=&#X40&#X61&#X76&#X61&#X73&#X63&#X72&#X69&#X70&#X74&
		// #X3A&#X61&#X6C&#X65&#X72&#X74&#X28&#X27&#X58&#X53&#X53&#X27&#X29>
		$search = 'abcdefghijklmnopqrstuvwxyz';
		$search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$search .= '1234567890!@#$%^&*()';
		$search .= '~`";:?+/={}[]-_|\'\\';
		for ($i = 0; $i < strlen($search); $i++) {
			// ;? matches the ;, which is optional
			// 0{0,7} matches any padded zeros, which are optional and go up to 8 chars

			// &#x0040 @ search for the hex values
			$val = preg_replace('/(&#[x|X]0{0,8}' . dechex(ord($search[$i])) . ';?)/i', $search[$i], $val);
			// with a ;

			// &#00064 @ 0{0,7} matches '0' zero to seven times
			$val = preg_replace('/(&#0{0,8}' . ord($search[$i]) . ';?)/', $search[$i], $val); // with a ;
		}

		return $val;
	}


	// 로그 (파일)
	public function filelog($fileName = 'Log', $logTxt = '')
	{
		$dir = '/home/cashkeyboard/log/develop/';
		$date = date("Y-m-d");
		$fn_log = $fileName . '_' . $date . '.log';
		$ff_log = (file_exists($dir . $fn_log) != false) ? 'a' : 'w';
		$fp_log = fopen($dir . $fn_log, $ff_log);
		fwrite($fp_log, date("Y-m-d H:i:s") . " - " . $logTxt . "\r\n\r\n");
		fclose($fp_log);
	}


	public function send_telegram_error($botToken, $chat_id, $message = "Error")
	{
		//BotFather 이용해서 예를 들어 abcd_bot(@abcd_bot) 만들고, 만든 Monitoring 채널에 관리자로 등록시켜줌

		//  텔레그램 봇
		if (!$botToken || $botToken == 'STARHOP') $botToken = "381516256:AAF8Z27IB_Nhe4JIw_FevlFr6oNyEkhPjfE";

		//  모니티링 채널 ID (스타샵 TEST)
		if (!$chat_id || $chat_id == "TEST") $chat_id = "-1001145711392";

		//  모니티링 채널 ID (스타샵 PHP)
		else if ($chat_id == "PHP") $chat_id = "-10011111314761";


		$message = urlencode($message);
		$bot_url = "https://api.telegram.org/bot" . $botToken . "/sendMessage?chat_id=" . $chat_id . "&text=" . $message;

		//echo $bot_url;

		$result = file_get_contents($bot_url);
		$result = json_decode($result, true);

		return $result;
	}


	// 서버간 통신 함수
	// POST
	public function curl_chk($url, $params)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

		$c_result = curl_exec($ch);
		curl_close($ch);
		return $c_result;
	}

	// GET
	public function curl_get($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$c_result = curl_exec($ch);
		curl_close($ch);
		return $c_result;
	}

	// 초단위를 시간,분,초 로 변환
	public function convertTime($sec){
		$sec = (int)$sec;
		$Hour=($sec > 360)?floor($sec / 3600)."h ":"";
		$Minute=($sec > 60)?floor($sec % 3600 / 60)."m ":"";
		$Second=($sec > 0)?ceil($sec % 60)."s ":"-";
		return $Hour.$Minute.$Second;
	}

	public function getRealClientIp() {
		if (getenv('HTTP_CLIENT_IP')) {
			$ipaddress = getenv('HTTP_CLIENT_IP');
		} else if(getenv('HTTP_X_FORWARDED_FOR')) {
			$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
		} else if(getenv('HTTP_X_FORWARDED')) {
			$ipaddress = getenv('HTTP_X_FORWARDED');
		} else if(getenv('HTTP_FORWARDED_FOR')) {
			$ipaddress = getenv('HTTP_FORWARDED_FOR');
		} else if(getenv('HTTP_FORWARDED')) {
			$ipaddress = getenv('HTTP_FORWARDED');
		} else if(getenv('REMOTE_ADDR')) {
			$ipaddress = getenv('REMOTE_ADDR');
		} else {
			$ipaddress = '알수없음';
		}
		return $ipaddress;
	}

}

if (!isset($fn)) {
	$fn = new __fn__;
}
