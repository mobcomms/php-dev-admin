<?php
/**********************
 *
 *    회원관리 페이지 (회원정보 리스트)
 *
 **********************/

include "./var.php";
include_once __func__;
include_once __pdoDB__;    ## DB Instance 생성

error_reporting(E_ALL & ~E_NOTICE);
ini_set("display_errors", 1);

## 환경설정
define('_title_', '회원관리');
define('_Menu_', 'user');
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
	select
		count(*) as cnt
	from
		user as m
	where 1=1 {$where}"
;
//pre($sql);
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
	FROM user m
	WHERE 1=1 {$where}
	ORDER BY user_seq DESC
	LIMIT {$PG->first}, {$PG->size};
";
//pre($sql);
$ret = $NDO->fetch_array($sql);

?>

<div class="contentpanel">

	<div class="panel panel-default" style="width:1500px">
		<div class="panel-heading">
			<div class="panel-btns">
				<!--a href="" class="panel-close">&times;</a-->
				<a href="" class="minimize">&minus;</a>
			</div><!-- panel-btns -->
			<h4 class="panel-title">보너스적립 사용자 </h4>
		</div><!-- panel-heading -->

		<div class="panel-body">

			<form name="scform" method="get" action="">

				<div class="row">
					<div class="pull-left">
						<select class="form-control pull-left border-input" name="key" style="width:150px;margin-left:10px;height: 40px;">
							<option value="user_key" <?=($key=='user_key')?'selected':''?>> 유저식별값 </option>
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
							<th>NO.</th>
							<th>유저식별값</th>
							<th>광고ID</th>
							<th>최초등록일</th>
							<th>최종접속일자</th>
							<th>상세</th>
						</tr>
						</thead>
						<tbody>
						<?php foreach($ret AS $row){?>
							<tr>
								<td><?=number_format($PG->first_num)?></td>
								<td><?=$row['user_key']?></td>
								<td><?=$row['adid']?></td>
								<td><?=$row['reg_date']?></td>
								<td><?=$row['mod_date']?></td>
								<td><a href="member_info.php?uuid=<?=urlencode($row['user_key'])?>" target="_blank">상세보기</a></td>
							</tr>
						<?php $PG->first_num--; }
						if(empty($ret)){ ?>
								<td colspan="6">검색된 사용자가 없습니다.</td>
						<?php } ?>
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
