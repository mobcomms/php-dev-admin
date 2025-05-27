<?php $admin=$fn->chk_admin($_SESSION, 'Adm'); ?>
<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="">
	<link rel="shortcut icon" href="images/favicon.png" type="image/png">
	<title>해피스크린 키보드 <?=_title_?></title>
	<link href="css/style.default.css<?=__ver_css__?>" rel="stylesheet">
	<link href="css/jquery.datatables.css" rel="stylesheet">
	<link rel="stylesheet" href="css/bootstrap-timepicker.min.css" />
	<link rel="stylesheet" href="css/bootstrap-fileupload.min.css" />
	<link href="css/layout.css<?=__ver_css__?>" rel="stylesheet">
	<link href="css/custom.css<?=__ver_css__?>" rel="stylesheet">
	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

	<script src="./js/jquery-1.10.2.min.js"></script>
	<script src="./js/jquery-migrate-1.2.1.min.js"></script>
	<script src="./js/bootstrap.min.js"></script>
	<script src="./js/modernizr.min.js"></script>
	<script src="./js/jquery.sparkline.min.js"></script>
	<script src="./js/toggles.min.js"></script>
	<script src="./js/retina.min.js"></script>
	<script src="./js/jquery.cookies.js"></script>
	<script src="./js/bootstrap-timepicker.min.js"></script>
	<script src="./js/jquery-ui-1.10.3.min.js"></script>
	<script src="./js/chosen.jquery.min.js"></script>
	<script src="./js/bootstrap-fileupload.min.js"></script>
	<script src="./js/custom.js<?=__ver_js__?>"></script>

	<script type="text/javascript">
		google.charts.load('current', {packages: ['corechart']});
	</script>
</head>

<body style="overflow-x: scroll;">

<!-- Preloader -->
	<div id="preloader">
		<div id="status"><i class="fa fa-spinner fa-spin"></i></div>
	</div>

<section>

<?php include __left_menu__; ?>

<div class="mainpanel">

	<div class="headerbar">
		<a class="menutoggle"><i class="fa fa-bars"></i></a>
		<div class="main_title">
			<h1></h1>
		</div>

		<div class="header-right">
			<ul class="headermenu">
				<li>
					<div class="btn-group">
						<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
							<i class="glyphicon glyphicon-user icon_gap"></i>
							<?=$admin['name']?>님
							<span class="caret"></span>
						</button>
						<ul class="dropdown-menu dropdown-menu-usermenu pull-right">
							<!-- <li><a href="profile.html"><i class="glyphicon glyphicon-user"></i> My Profile</a></li> -->
							<li><a href="javascript:;" onclick="javascript:document.logoutform.submit();"><i class="glyphicon glyphicon-log-out"></i> Log Out</a></li>
						</ul>
					</div>
				</li>
			</ul>
		</div><!-- header-right -->

		<form name="logoutform" method="post" action="/process.php">
			<input type="hidden" name="refpage" value="logout" />
			<input type="hidden" name="type" value="Adm" />
		</form>

	</div><!-- headerbar -->


