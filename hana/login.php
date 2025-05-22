<?php
include "./var.php";
include_once __func__;

if(!empty($_SESSION['Adm']['id'])){
    unset($_SESSION['Adm']);
    session_destroy();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/Class/GoogleAuthenticator.php';
$ga = new PHPGangsta_GoogleAuthenticator();
$secret = $ga->createSecret(); // 시크릿키 생성
$secret = "OLJXGSAG2A72VG53";

## OTP 플레이 스토어 링크
//$play_link = "https://chart.googleapis.com/chart?chs=165x165&chld=M|0&cht=qr&chl=".urlencode('https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=ko&gl=US');

## OTP 비밀번호 공유
//$qrCodeUrl = $ga->getQRCodeGoogleUrl("CashKeyboard", $secret);


$token = empty($_REQUEST['token'])?"":($_REQUEST['token']);
$company_code = empty($_REQUEST['company_code'])?"":($_REQUEST['company_code']);

//pre($token);
if(empty($token)){
    $data = file_get_contents('php://input');
    $data = json_decode($data,true);
    $token = empty($data['token'])?"":($data['token']);
}
use Firebase\JWT;
if(!empty($token)){
    include_once "../Class/Class.jwt.php";


//발행시간
//$iat = time();
//$nbf = time()-10;
//$exp = time()+86400;
//
//$payload = array(
//    "clientId" => "anick",
//    "iat" => $iat,
//    "nbf" => $nbf,
//    "exp" => $exp,
//);
//$jwt = JWT::encode($payload, $jwt_key, 'HS512');
//pre($jwt);

    try {
        JWT::$leeway = 600; // $leeway in seconds
        $decoded = JWT::decode($token, $jwt_key, array('HS512'));
    } catch (Exception $e) {
        $_JsonData['result_code'] = "Error";
        $_JsonData['err_code'] = 50;
        $_JsonData['result_message'] = $e->getMessage();
        $_JsonData['data'] = null;
        $_JsonData['datas'] = [];
        $_jecData = json_encode($_JsonData, true);
        exit($_jecData);
    }

    switch ($decoded->clientId){
        case "okcashbag" :
            $user_uuid ="okadm";
            $pass_word = "ok!@#rhksflwk**";
            $domain = "https://okcashbag.cashkeyboard.co.kr";
            $refpage = "/okcashbag/login.php";
        break;
        case "hana" :
            $user_uuid ="hana";
            $pass_word = "hana@admin";
            $domain = "https://hana-admin.commsad.com";
            $refpage = "/hana/login.php";
        break;
        case "shinhancard" :
            $user_uuid ="shinhan";
            $pass_word = "shinhan@admin";
            $domain = "https://shinhancard-admin.commsad.com";
            $refpage = "/shinhancard/login.php";
        break;
        case "paybooc" :
            $user_uuid ="paybooc";
            $pass_word = "paybooc@admin";
            $domain = "https://paybooc-admin.commsad.com";
            $refpage = "/paybooc/login.php";
        break;
        case "happyscreen" :
            $user_uuid ="happy";
            $pass_word = "happy@admin";
            $domain = "https://happyscreen-admin.commsad.com";
            $refpage = "/happyscreen/login.php";
        break;
        case "hanapay" :
            $user_uuid ="hanapay";
            $pass_word = "hanapay@admin!";
            $domain = "https://hanapay-admin.commsad.com";
            $refpage = "/hanapay/login.php";
        break;
        case "valuewalk" :
            $user_uuid ="valuewalk";
            $pass_word = "valuewalk@admin";
            $domain = "https://valuewalk-admin.commsad.com";
            $refpage = "/valuewalk/login.php";
        break;
        case "finnq" :
            $user_uuid ="finnq";
            $pass_word = "finnq@admin";
            $domain = "https://finnq-admin.commsad.com";
            $refpage = "/finnq/login.php";
        break;
        case "moneyweather" :
            $user_uuid ="moneyweather";
            $pass_word = "moneyweather@admin";
            $domain = "https://moneyweather-admin.commsad.com";
            $refpage = "/moneyweather/login.php";
        break;
        case "benepia" :
            $user_uuid ="benepia";
            $pass_word = "benepia@admin";
            $domain = "https://benepia-admin.commsad.com";
            $refpage = "/benepia/login.php";
        break;
        case "anick" :
            $user_uuid ="mango";
            $pass_word = "anick1214!";
            if(empty($company_code) || $company_code == "okcashbag"){
                $domain = "https://okcashbag.cashkeyboard.co.kr";
                $refpage = "/okcashbag/login.php";
            }else{
                $domain = "https://{$company_code}-admin.commsad.com";
                $refpage = "/{$company_code}/login.php";
            }
            break;
        default:
            $_JsonData['result_code'] = "Error";
            $_JsonData['err_code'] = 99;
            $_JsonData['result_message'] = "clientId check!";
            $_JsonData['data'] = null;
            $_JsonData['datas'] = [];
            $_jecData = json_encode($_JsonData, true);
            exit($_jecData);
    }
?>
    <script src="./js/jquery-1.10.2.min.js"></script>
    <form action="<?=$domain?>/process.php" method="post" id="login_jwt">
        <input type="hidden" name="refpage" value="<?=$refpage;?>" />
        <input type="hidden" placeholder="아이디" required="" name="id" id="username" value="<?=$user_uuid?>" />
        <input type="hidden" placeholder="비밀번호" required="" name="pw" id="password" value="<?=$pass_word?>" />
    </form>
    <script>
        $(document).ready(function(){
            if($("#username").val() !="" && $("#password").val() !=""){
                $("#login_jwt").submit();
            }
        })
    </script>
<?php exit; } ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>hanamoney</title>
<link rel="shortcut icon" href="./images/favicon.png" type="image/png">
<link rel="stylesheet" href="./css/style_login.css">
</head>

<body>
	<div class="container">
		<section id="content">
			<form action="/process.php" method="post">
				<input type="hidden" name="refpage" value="<?=__self__;?>" />
				<h1>hanamoney</h1>
				<div>
					<input type="text" placeholder="아이디" required="" name="id" id="username" />
				</div>
				<div>
					<input type="password" placeholder="비밀번호" required="" name="pw" id="password" />
				</div>
				<div>
					<?php if(!empty($play_link)){?>
					<div style="width: 50%;float: left;">
						<span>OTP 앱설치</span>
						<img src="login_qr.php?images=<?php echo urlencode($play_link); ?>" alt="" />
					</div>
					<?php }?>
					<?php if(!empty($qrCodeUrl)){?>
					<div style="width: 50%;float: left;">
						<span>OTP 인증번호</span>
						<img src="login_qr.php?images=<?php echo urlencode($qrCodeUrl); ?>" alt="" />
					</div>
					<?php }?>
				</div>

				<!-- div>
					<input type="hidden" name="secret" value="<?=$secret?>">
					<input type="text" class="password" placeholder="인증번호" required="" name="verifyCode" id="" autocomplete="off">
				</div -->

				<div>
					<input type="submit" value="로그인" />
					<!--a href="javascript:;" onclick="javascript:INQ();">Lost your password?</a-->
				</div>
			</form><!-- form -->
		</section><!-- content -->
	</div><!-- container -->
</body>
</html>
