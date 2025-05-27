<?php
date_default_timezone_set('Asia/Seoul');

header("Content-Type: text/html; charset=UTF-8");
if(empty($_SERVER['DOCUMENT_ROOT'])){
	$document_root = "/home/valuewalk/public_html";
}else{
	$document_root = $_SERVER['DOCUMENT_ROOT'];
}
define('__root__', $document_root);
define('__pdo__', __root__.'/Class/Class.PDO.php');
define('__pdoDB__', __root__.'/Class/Class.PDO.DB.php');
define('__fn__', __root__.'/Class/Class.Func.php');

if(!function_exists('pre') ){
	function pre(){//데이타를 보기좋게 출력한다.
		$varsN=func_num_args();
		for( $n=0; $n<$varsN; $n++ ){
			echo "<pre>".print_r(func_get_arg($n),true)."</pre>";
		}
	}
}

$_DEBUG = (isset($_REQUEST['debug']) && $_REQUEST['debug']=="Y")?"Y":"N";
define('__debug__', $_DEBUG);

$_FILELOG = (date('H')!="00")?"Y":"Y";
define('__filelog__', $_FILELOG);

$_FILELOG = "Y";
define('__filelog_curl__', $_FILELOG);

$jwt_key_user = "enliple_jwt_token_user@3dnjf30dlf!";
$jwt_key_point = "enliple_jwt_token_point@3dnjf31dlf!";


############# 서버 점검 포인트 막기 #############
$time = time();
$stime = "1689175800";
$etime = "1689195600";
$point_disabled = 'false';
if($time >= $stime && $time <= $etime){
	$point_disabled = 'true';
}
