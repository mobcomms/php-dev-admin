<?php
/**********************
 *
 *    후후 메인
 *
 **********************/

include "./var.php";
include_once __func__;
include_once __pdoDB__;    ## DB Instance 생성

## 환경설정
define('_title_', '메인');
define('_Menu_', 'main');
define('_subMenu_', 'main');

include __head__;
?>

	<div class="contentpanel">

	<div class="panel panel-default">
	<div class="panel-heading">
		<div class="panel-btns">
			<a href="" class="minimize">&minus;</a>
		</div><!-- panel-btns -->
		<h4 class="panel-title">보너스적립</h4>
	</div><!-- panel-heading -->
	<div class="panel-body">

		<div class="row">

			<div class="col-xs-12 col-md-12">
				안녕하세요 <?=$admin['name']?>님. 보너스적립 입니다.
				<br >
			</div><!-- col-md-12 -->

		</div><!-- row -->

	</div><!-- contentpanel -->

<?php
include __foot__;
?>
