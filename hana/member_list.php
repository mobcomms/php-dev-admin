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
	SELECT 
		COUNT(*) AS cnt
	FROM (
		SELECT * FROM ckd_user_info WHERE 1 = 1 {$where}
		UNION ALL
		SELECT * FROM ckd_user_info_sdk WHERE 1 = 1 {$where}
	) AS combined;
";
//$fn->debug($sql);
$total = $NDO->getData($sql);
include_once __page__;
$now = ret(INPUT_GET, 'np');
$paging->np = $now;
$paging->ps = 50;
$PG = $paging->init($total['cnt']);
$PG_Param = "&key=".$key."&keyword=".$keyword;

$sql = "
(
	SELECT *
	FROM ckd_user_info m
	WHERE 1=1 {$where}
	ORDER BY user_idx DESC
	LIMIT {$PG->first}, {$PG->size}
)
UNION ALL
(
	SELECT *
	FROM ckd_user_info_sdk m
	WHERE 1=1 {$where}
	ORDER BY user_idx DESC
	LIMIT {$PG->first}, {$PG->size}
)
ORDER BY user_idx DESC
LIMIT {$PG->first}, {$PG->size};
";
//$fn->debug($sql);
$ret = $NDO->fetch_array($sql);


//적립 제한 셋팅 불러오기
$sql = "
	SELECT cfg_seq,cfg_nm, cfg_val
	FROM ckd_cfg_info  
	WHERE cfg_nm IN ('new_user_save_point_limit','old_user_save_point_limit')
";
$result = $NDO->fetch_array($sql);
$new_user_limit = $result[0]['cfg_val'];
$old_user_limit = $result[1]['cfg_val'];
?>

<div class="contentpanel">

	<div class="panel panel-default" style="width:1500px">
		<div class="panel-heading">
			<div class="panel-btns">
				<!--a href="" class="panel-close">&times;</a-->
				<a href="" class="minimize">&minus;</a>
			</div><!-- panel-btns -->
			<h4 class="panel-title">키보드 사용자 </h4>
		</div><!-- panel-heading -->

		<div class="panel-body">

			<form name="scform" method="get" action="">

				<div class="row">
					<div class="pull-left">
						<select class="form-control pull-left border-input" name="key" style="width:150px;margin-left:10px;height: 40px;">
							<option value="user_uuid" <?=($key=='user_uuid')?'selected':''?>> 아이디 </option>
							<option value="user_adid" <?=($key=='user_adid')?'selected':''?>> adid </option>
						</select>
						<input type="text" class="form-control pull-left border-input" name="keyword" style="width:300px;margin-left:10px;height: 40px;" value="<?=$keyword?>" placeholder="검색어" autocomplete="off" />
						<button class="btn btn-success" style="height: 40px;">검 색</button>
					</div>
					<div class="pull-right" style="display: inline-block;margin-right: 30px;">
						[현재 적립 제한] 신규 : <?=$new_user_limit?>P / 신규제외 : <?=$old_user_limit?>P
						<button type="button" class="btn btn-success" style="margin-left:10px;height: 40px;" onclick="centerOpenWindow('save_point_limit.php', 'save_point_limit', '450', '284', '', 'N');">적립 제한</button>
					</div>
				</div><!-- row -->
			</form>

			<div class="row col-xs-12 col-md-12">
				<div class="table-responsive" >
					<table class="table table-hover mb30 member_table" id="" style="border: 1px solid #b0b0b0;">
						<thead>
						<tr>
							<th class="col-md-2">keyboard_id</th>
							<th class="col-md-2">adid</th>
							<th class="col-md-1">운영체제</th>
							<th class="col-md-1">생성일자</th>
							<th class="col-md-1">현재 적립 대기 포인트</th>
							<th class="col-md-1">하나 적립 포인트 내역</th>
							<th class="col-md-1">키보드 일일 사용적립 내역</th>
							<th class="col-md-1">오퍼월 적립 내역</th>
						</tr>
						</thead>
						<tbody>
						<?php if(empty($ret)){ ?>
							<tr><td colspan="8">검색 결과가 없습니다.</td></tr>
						<?php }else { foreach($ret AS $row){?>
							<tr>
								<td><?=$row['user_uuid']?></td>
								<td><?=$row['user_adid']?></td>
								<td><?=$row['user_app_os'] == "A"?"Android":"iOS"?></td>
								<td><?=$row['reg_dttm']?></td>
								<td><?=$row['user_give_point']?></td>
								<td><a href="member_info.php?uuid=<?=urlencode($row['user_uuid'])?>" target="_blank">새탭으로 열기</a></td>
								<td><a href="member_info_ckd.php?uuid=<?=urlencode($row['user_uuid'])?>" target="_blank">새탭으로 열기</a></td>
								<td><a href="member_info_ppz.php?uuid=<?=urlencode($row['user_uuid'])?>" target="_blank">새탭으로 열기</a></td>
							</tr>
						<?php $PG->first_num--;}}?>
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
