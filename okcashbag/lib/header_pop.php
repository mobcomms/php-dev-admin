<?php $admin=$fn->chk_admin($_SESSION, 'Adm'); ?>
<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="">
	<link rel="shortcut icon" href="images/favicon.png" type="image/png">
	<title>돈버는 키보드 <?=_title_?></title>
	<link href="css/style.default.css<?=__ver_css__?>" rel="stylesheet">
	<link href="css/jquery.datatables.css" rel="stylesheet">
	<link rel="stylesheet" href="css/bootstrap-timepicker.min.css" />
	<link rel="stylesheet" href="css/bootstrap-fileupload.min.css" />
	<link href="css/layout.css<?=__ver_css__?>" rel="stylesheet">
	<script src="js/jquery-1.10.2.min.js"></script>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.js"></script>
	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	<script type="text/javascript">
	google.charts.load('current', {packages: ['corechart']});
	</script>

	<script src="js/jquery-migrate-1.2.1.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/modernizr.min.js"></script>
	<script src="js/jquery.sparkline.min.js"></script>
	<script src="js/toggles.min.js"></script>
	<script src="js/retina.min.js"></script>
	<script src="js/jquery.cookies.js"></script>
	<script src="js/bootstrap-timepicker.min.js"></script>
	<script src="js/jquery-ui-1.10.3.min.js"></script>
	<script src="js/chosen.jquery.min.js"></script>
	<script src="js/custom.js<?=__ver_js__?>"></script>

</head>

<body style="overflow-x:hidden;overflow-y: auto;">

<section>
