<?php
function ret($key, $val, $ret = false) {
	$return = filter_input($key, $val);
	if(empty($return))  return 0;
	$return = preg_replace('/(<|>|union |select |create |rename |truncate |load |alter |delete |update |insert |drop |alter |not |distinct |concat |load_file |regexp |--|<script>|<\/script>|INFORMATION_SCHEMA |sql|order |from |table |group |having |\"|\'|#|\/\*|\*\/|\\\)/i', ' ', $return);
	if($ret == false) { return $return; } else { echo $return; }
}

if(!function_exists('pre') ){
	function pre(){//데이타를 보기좋게 출력한다.
		$varsN=func_num_args();
		for( $n=0; $n<$varsN; $n++ ){
			echo "<pre>".print_r(func_get_arg($n),true)."</pre>";
		}
	}
}

define('__root__', $_SERVER['DOCUMENT_ROOT']);
define('__pdoDB__', __root__.'/Class/Class.PDO.DB.php');
define('__fn__', __root__.'/Class/Func.FN.php');
define('__func__', __root__.'/Class/Class.Func.php');
define('__database__', __root__.'/Class/Class.Database.php');
define('__sessions__', __root__.'/Class/Class.Sessions.php');
define('__head__', __root__.'/okcashbag/lib/header.php');
define('__head_pop__', './lib/header_pop.php');
define('__left_menu__', './lib/left_menu.php');
define('__foot__', './lib/footer.php');
define('__self__', $_SERVER['PHP_SELF']);
define('__host__', 'okcashbag.cashkeyboard.co.kr');	## 도메인
define('__company__', 'Cash Keyboard');
define('__page__', __root__.'/Class/Class.Page.php');
define('__ver_js__', '?v='.date('YmdHis'));
define('__ver_css__', '?v='.date('YmdHis'));
define('_rep_', '');

include_once __pdoDB__;
include_once __database__;
include_once __sessions__;

$session = new Session();	//Start a new PHP MySQL session

header("Content-Type: text/html; charset=UTF-8");
//extract($_SESSION);
