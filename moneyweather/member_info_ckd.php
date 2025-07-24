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

//적립정보
$sql = "
	SELECT count(*) cnt FROM ocb_user_point_today
	WHERE user_uuid='{$_GET['uuid']}' 
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
	FROM ocb_user_point_today 
	WHERE user_uuid='{$_GET['uuid']}' 
	ORDER BY reg_dttm DESC
	LIMIT {$PG->first}, {$PG->size}
";
//pre($sql);
$point_list = $NDO->fetch_array($sql);

$sql = "
	SELECT sum(point) AS point
	FROM ocb_user_point_today 
	WHERE user_uuid='{$_GET['uuid']}' 
";
//pre($sql);
$temp = $NDO->getData($sql);
$total_point = $temp['point'];

//적립 제한 셋팅 불러오기
$sql = "
	SELECT cfg_seq,cfg_nm, cfg_val
	FROM ocb_cfg_info  
	WHERE cfg_seq IN (6,7)
";
$result = $NDO->fetch_array($sql);
foreach ($result as $row) {
	switch ($row['cfg_seq']) {
		case 6 : $new_user_limit = $row['cfg_val']; break;
		case 7 : $old_user_limit = $row['cfg_val']; break;
	}
}
?>

<div class="contentpanel">

	<div class="panel panel-default" style="width:900px">
		<div class="panel-heading">
			<div class="panel-btns">
				<!--a href="" class="panel-close">&times;</a-->
				<a href="" class="minimize">&minus;</a>
			</div><!-- panel-btns -->
			<h4 class="panel-title">키보드 사용자 (키보드 사용 적립정보) </h4>
		</div><!-- panel-heading -->

		<div class="panel-body">
			<div class="row col-xs-12 col-md-12">
				<div class="table-responsive">
					<div style="float: left;line-height: 40px;">키보드 사용 적립정보 (<?=$_GET['uuid']?>)</div>
					<div style="display: inline-block;" class="pull-right">
						[현재 적립 제한] 신규 : <?=$new_user_limit?>P / 신규제외 : <?=$old_user_limit?>P
						<button type="button" class="btn btn-success" style="margin-left:10px;" onclick="centerOpenWindow('save_point_limit.php', 'save_point_limit', '450', '284', '', 'N');">적립 제한</button>
					</div>
					<table class="table table-hover mb30 member_table" id="" style="border: 1px solid #b0b0b0;margin-top:0;">
						<thead>
						<tr>
							<th>적립 날짜</th>
							<th>처음 적립 요청 일시</th>
							<th>마지막 적립 요청 일시</th>
							<th>사용 적립 포인트</th>
						</tr>
						</thead>
						<tbody>
						<?php if(is_array($point_list)){ ?>
							<tr>
								<td>합계</td>
								<td>-</td>
								<td>-</td>
								<td><?=number_format(empty($total_point)?0:$total_point)?></td>
							</tr>

							<?php foreach($point_list AS $row){ ?>
								<tr>
									<td><?=$row['stats_dttm']?></td>
									<td><?=$row['reg_dttm']?></td>
									<td><?=$row['alt_dttm']?></td>
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
