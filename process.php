<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

$uri = $_SERVER['HTTP_REFERER']; // 현재 URL 경로 가져오기
$parts = explode("/", $uri); // '/' 기준으로 분리
$company_code = $parts[3]; // `hana` 부분 추출

include $company_code."/var.php";
include __fn__;

if(
	REFER($_POST['refpage'], 'id_chk') != true &&
	REFER($_POST['refpage'], 'logout') != true &&
	REFER($_POST['refpage'], '/login.html') != true &&
	REFER($_POST['refpage'], "/".$company_code."/login.php") != true
) {
	Alert("페이지 접근 권한이 없습니다");
	Hist();
	exit;
}

Switch($_POST['refpage']) {

	default:	## 이외 리다이렉트
		Alert("권한이 없습니다");
		Loca($company_code."/login.php");
		exit;

	break;


	## 광고주 로그아웃
	Case 'logout':

		$_SESSION['Adm']['idx'] = "";
		$_SESSION['Adm']['id'] = "";
		$_SESSION['Adm']['name'] = "";
		$_SESSION['Adm']['level'] = "";

		unset($_SESSION['Adm']);
		session_destroy();
		Alert("감사합니다.\\n\\n로그아웃 되었습니다.");
		exit(Loca($company_code."/login.php"));
	break;

	Case "/".$company_code."/login.php":	## 관리자 로그인
		foreach($_POST as $key => $val) $R[$key] = ${'k_'.$key} = trim(str_replace("( select| union| insert| update| delete| drop|\"|\'|#|\/\*|\*\/|\\\|\;)", "", $val));

		$sql = "
			SELECT user_no, user_id, user_ncnm, level 
			FROM ckd_admin
			WHERE user_id=:user_id AND user_pwd=CONCAT('*', UPPER(SHA1(UNHEX(SHA1(:user_pwd)))))
		";
		$res = $NDO->getData($sql,array(":user_id"=>$k_id,":user_pwd"=>$k_pw));

		###### 구글 OTP 인증 ######
		//require_once $_SERVER['DOCUMENT_ROOT'] . '/Class/GoogleAuthenticator.php';
		//$ga = new PHPGangsta_GoogleAuthenticator();
		//$result = $ga->verifyCode($_POST['secret'], $_POST['verifyCode'], 0);
		$result = true;

		if(!empty($res) && $result) {

			$_SESSION['Adm']['idx'] = $res['user_no'];
			$_SESSION['Adm']['id'] = $res['user_id'];
			$_SESSION['Adm']['name'] = $res['user_ncnm'];
			$_SESSION['Adm']['level'] = $res['level'];

			exit(Loca("/".$company_code."/index.php"));
		} else {
			exit(Hist("접속 정보를 다시 확인해 주세요"));
		}

	break;

}
