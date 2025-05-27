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
define('_title_', '포인트 적립');
define('_Menu_', 'user');
define('_subMenu_', 'user');

include_once __head__; ## html 헤더 출력

$where="";

// 검색어
$key=empty($_REQUEST['key'])?"":$_REQUEST['key'];
$keyword=empty($_REQUEST['keyword'])?"":htmlspecialchars($_REQUEST['keyword']);

// 검색어
if($keyword && $key=='user_id'){
	$where.=" AND (".$key." like '%".$keyword."%' or m.user_no='".$keyword."') ";
}else if($keyword){
	$where.=" AND ".$key." like '%".$keyword."%' ";
}

$sql = "
	select
		count(*) as cnt
	from
		ckd_user_info as m
	where 1=1 {$where}"
;
//$fn->debug($sql);
$total = $NDO->getData($sql);
include_once __page__;

$now = ret(INPUT_GET, 'np');
$paging->np = $now;
$paging->ps = 50;
$PG = $paging->init($total['cnt']);
$PG_Param = "&key=".$key."&keyword=".$keyword;

$sql = "
	SELECT *
	FROM ckd_user_info m
	WHERE 1=1 {$where}
	ORDER BY user_idx DESC
	LIMIT {$PG->first}, {$PG->size};
";
//$fn->debug($sql);
$ret = $NDO->fetch_array($sql);
//pre($ret);

?>

<div class="contentpanel">

	<div class="panel panel-default" style="width:1000px">
		<div class="panel-heading">
			<div class="panel-btns">
				<!--a href="" class="panel-close">&times;</a-->
				<a href="" class="minimize">&minus;</a>
			</div><!-- panel-btns -->
			<h4 class="panel-title"><?=_title_?> </h4>
		</div><!-- panel-heading -->

		<div class="panel-body">

			<form name="scform" method="get" action="">

				<div class="row">
					<div class="pull-left">
						<select class="form-control pull-left border-input" name="key" style="width:150px;margin-left:10px;height: 40px;">
							<option value="user_uuid" <?=($key=='user_uuid')?'selected':''?>> 아이디 </option>
						</select>
						<input type="text" class="form-control pull-left border-input" name="keyword" style="width:300px;margin-left:10px;height: 40px;" value="<?=$keyword?>" placeholder="검색어" autocomplete="off" />
						<button class="btn btn-success" style="height: 40px;">검 색</button>
					</div>
				</div><!-- row -->
			</form>

			<div class="row col-xs-12 col-md-12">
				<div class="table-responsive" >
					<table class="table table-hover mb30 member_table" id="" style="border: 1px solid #b0b0b0;">
						<thead>
						<tr>
							<th>keyboard_id</th>
							<th>운영체제</th>
							<th>생성일자</th>
							<th>적립 받은 포인트</th>
							<th>적립 포인트 내역</th>
						</tr>
						</thead>
						<tbody>
						<?php foreach($ret AS $row){


							//적립받은 포인트
							$sql = "
								SELECT user_uuid,sum(point) AS tot_point FROM ckd_game_zone_point
								WHERE user_uuid = '{$row['user_uuid']}'
								GROUP BY user_uuid
							";
							//$fn->debug($sql);
							$temp = $NDO->fetch_array($sql);
							//pre($temp);

							foreach($temp AS $key => $item){
								$user_point_data[$item['user_uuid']] = $item['tot_point'];
							}

						?>
							<tr>
								<td><?=$row['user_uuid']?></td>
								<td><?=$row['user_app_os'] == "A"?"Android":"iOS"?></td>
								<td><?=$row['reg_dttm']?></td>
								<td><?=empty($user_point_data[$row['user_uuid']])?0:$user_point_data[$row['user_uuid']]?></td>
								<td><a href="member_info.php?uuid=<?=urlencode($row['user_uuid'])?>" target="_blank">새탭으로 열기</a></td>
							</tr>
						<?php $PG->first_num--;}?>
						</tbody>
					</table>
					<div class="row">
						<?=$paging->paging_new($PG,$PG_Param);?>
					</div><!-- row -->

				</div><!-- table-responsive -->

			</div><!-- row -->

		</div><!-- panel-body -->
	</div><!-- panel -->

</div><!-- contentpanel -->

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

<?php
include __foot__;
?>
