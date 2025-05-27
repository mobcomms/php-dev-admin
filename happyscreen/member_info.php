<?php
/**********************
 *
 *    회원관리 페이지 (회원정보 리스트)
 *
 **********************/

include "./var.php";
include_once __func__;
include_once __pdoDB__;    ## DB Instance 생성

## 환경설정
define('_title_', '회원관리');
define('_Menu_', 'manage');
define('_subMenu_', 'info');

include_once __head__; ## html 헤더 출력

//유저정보
$sql = "
	SELECT * FROM ckd_user_info 
	WHERE user_uuid='{$_GET['uuid']}' 
";
//$fn->debug($sql);
$user_info = $NDO->getData($sql);

//적립정보
$sql = "
	select count(*) as cnt
	from ckd_user_point as m
	where user_uuid='{$_GET['uuid']}' 
";
//$fn->debug($sql);
$total = $NDO->getData($sql);

include_once __page__;
$now = ret(INPUT_GET, 'np');
$paging->np = $now;
$paging->ps = 50;
$PG = $paging->init($total['cnt']);
$PG_Param = "&uuid=".$_GET['uuid'];

$sql = "
	SELECT *
	FROM ckd_user_point 
	WHERE user_uuid='{$_GET['uuid']}' 
	ORDER BY point_seq DESC
	LIMIT {$PG->first}, {$PG->size}
";
//$fn->debug($sql);
$point_list = $NDO->fetch_array($sql);

$sql = "
	SELECT sum(point) AS point
	FROM ckd_user_point 
	WHERE user_uuid='{$_GET['uuid']}' 
";
//$fn->debug($sql);
$temp = $NDO->getData($sql);
$total_point = $temp['point'];
?>
<script>
	// 화면 중앙에 새창 열기
	function centerOpenWindow(theURL, winName, width, height, fstate, scrollbars) {
		var features = "width=" + width ;
		features += ",height=" + height ;
		var state = "";

		var scrollbars = scrollbars || "no";

		if (fstate == "") {		// 옵션
			state = features + ", left=" + (screen.width-width)/2 + ",top=" + (screen.height-height)/2 + ",scrollbars="+ scrollbars;
		} else {
			state = fstate + ", " + features + ", left=" + (screen.width-width)/2 + ",top=" + (screen.height-height)/2 + ",scrollbars="+ scrollbars;
		}
		var win = window.open(theURL,winName,state);
		win.focus();
	}
</script>
<div class="contentpanel">

	<div class="panel panel-default" style="width:900px">
		<div class="panel-heading">
			<div class="panel-btns">
				<!--a href="" class="panel-close">&times;</a-->
				<a href="" class="minimize">&minus;</a>
			</div><!-- panel-btns -->
			<h4 class="panel-title">키보드 사용자 </h4>
		</div><!-- panel-heading -->

		<div class="panel-body">
			<div class="row col-xs-12 col-md-12">
				<div class="table-responsive">
					사용자정보
					<table class="table table-hover mb30 member_table" id="" style="border: 1px solid #b0b0b0;margin-top:0;">
						<tbody>
						<tr>
							<td>keyboard_id</td>
							<td><?=urldecode($user_info['user_uuid'])?></td>
							<td>생성일자</td>
							<td><?=$user_info['reg_dttm']?></td>
						</tr>
						<tr>
							<td>키보드 종류</td>
							<td><?=$user_info['user_app_type']?></td>
							<td>최종접속일자</td>
							<td><?=$user_info['alt_dttm']?></td>
						</tr>
						</tbody>
					</table>
				</div><!-- table-responsive -->
			</div><!-- row -->
		</div>

		<div class="panel-body">
			<div class="row col-xs-12 col-md-12">
				<div class="table-responsive">
					<div style="float: left;line-height: 40px;">적립정보</div>
					<div style="display: inline-block;float: right;">
						<button type="button" class="btn btn-success" style="margin-left:10px;" onclick="centerOpenWindow('save_point_admin.php?uuid=<?=urlencode($user_info['user_uuid'])?>', 'save_point_admin', '500', '451', '', 'N');">포인트 지급</button>
					</div>
					<table class="table table-hover mb30 member_table" id="" style="border: 1px solid #b0b0b0;margin-top:0;">
						<thead>
						<tr>
							<th>적립 요청 번호</th>
							<th>적립 요청 일시</th>
							<th>적립 요청 포인트</th>
						</tr>
						</thead>
						<tbody>
						<?php if(is_array($point_list)){ ?>
							<tr>
								<td>합계</td>
								<td>-</td>
								<td><?=number_format(empty($total_point)?0:$total_point)?></td>
							</tr>

							<?php foreach($point_list AS $row){ ?>
								<tr>
									<td><?=$row['transaction_id']?></td>
									<td><?=$row['reg_dttm']?></td>
									<td><?=number_format($row['point'])?></td>
								</tr>
							<?php
								$PG->first_num--;
							}
						}else{
						?>
							<tr>
								<td colspan="6">적립 내용이 없습니다.</td>
							</tr>
						<?php
							}
						?>
						</tbody>
					</table>
				</div><!-- table-responsive -->
			</div><!-- row -->

			<div class="row" style="width:800px">
				<?=$paging->paging_new($PG,$PG_Param);?>
			</div><!-- row -->

		</div><!-- panel-body -->
	</div><!-- panel -->

</div><!-- contentpanel -->
<?php
include __foot__;
?>
