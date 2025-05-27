<?php
include "./var.php";
include_once __func__;

if(!empty($_SESSION['Adm']['id'])){
	header('Location: /paybooc/index.php');
	exit;
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/Class/GoogleAuthenticator.php';
$ga = new PHPGangsta_GoogleAuthenticator();
$secret = $ga->createSecret(); // 시크릿키 생성
$secret = "OLJXGSAG2A72VG53";

## OTP 플레이 스토어 링크
//$play_link = "https://chart.googleapis.com/chart?chs=165x165&chld=M|0&cht=qr&chl=".urlencode('https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=ko&gl=US');

## OTP 비밀번호 공유
//$qrCodeUrl = $ga->getQRCodeGoogleUrl("CashKeyboard", $secret);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>AD SDK(paybooc)</title>
<link rel="shortcut icon" href="./images/favicon.png" type="image/png">
<link rel="stylesheet" href="./css/style_login.css">
</head>

<body>
	<div class="container">
		<section id="content">
			<form action="/process.php" method="post">
				<input type="hidden" name="refpage" value="<?=__self__;?>" />
				<h1>AD SDK(paybooc)</h1>
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
