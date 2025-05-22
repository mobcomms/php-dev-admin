<?php
/**********************
 *
 *    광고 호출 실패 조회
 *
 **********************/
include "./var.php";
include_once __func__;
include_once __pdoDB__;    ## DB Instance 생성

## 환경설정
define('_title_', '광고 호출 실패 일자별 통계');
define('_Menu_', 'manage');
define('_subMenu_', 'fail_status');

include_once __head__;

$startDate = !empty($_REQUEST['startDate']) ? $_REQUEST['startDate'] : date("Y-m-d",strtotime("-1 month"));
$endDate = !empty($_REQUEST['endDate']) ? $_REQUEST['endDate'] : date("Y-m-d");
$os_type = empty($_REQUEST['os_type']) ?"":$_REQUEST['os_type'];

if(empty($_REQUEST['type'])){
	if(empty($_REQUEST['startDate'])){
		$type = 30;
	}else{
		$type = "";
	}
}else{
	$type = $_REQUEST['type'];
}

$today = date("Y-m-d");
$today1 = date("Y-m-01");

//$today = "2022-12-31";
//$today1 = "2022-12-01";;

switch($type){
	case '30'://최근 30일
		$startDate = date("Y-m-d",strtotime($today." -30 day"));
		$endDate = $today;
	break;
	case 'M'://이번달
		$startDate = $today1;
		$endDate = $today;
	break;
	case 'B1'://전월
		$startDate = date("Y-m-01", strtotime($today1." -1 month")); //지난달 1일
		$endDate = date("Y-m-t", strtotime($today1." -1 month")); //지난달 말일
	break;
	case 'B2'://전전월
		$startDate = date("Y-m-01", strtotime($today1." -2 month"));
		$endDate = date("Y-m-t", strtotime($today1." -2 month"));
	break;
	case '3M'://3개월
		$startDate = date("Y-m-01", strtotime($today1." -2 month"));
		$endDate = $today;
	break;
	case '6M'://6개월
		$startDate = date("Y-m-01", strtotime($today1." -5 month"));
		$endDate = $today;
	break;
}

$sdate = str_replace("-","",$startDate);
$edate = str_replace("-","",$endDate);

$param = "";
$param .= "&type={$type}&startDate={$sdate}&startDate={$edate}";

switch($os_type){
	case "A" : $add_query=" AND os_type = 'A'";
		$sdate = str_replace("-","",$startDate);
		$edate = str_replace("-","",$endDate);
	break;
	case "I" : $add_query=" AND os_type = 'I'";
	//if($startDate < 20240103) $startDate = "2024-01-03";
		$sdate = str_replace("-","",$startDate);
		$edate = str_replace("-","",$endDate);
	break;
	default : $add_query=""; break;
}


$sql="
	SELECT count(distinct stats_dttm) cnt FROM ckd_ad_fail
	WHERE stats_dttm BETWEEN {$sdate} AND {$edate} {$add_query}
";
//pre($sql);
$total = $NDO->getData($sql);

include_once __page__;
$now = ret(INPUT_GET, 'np');
$paging->np = $now;
$paging->ps = 100;
$PG = $paging->init($total['cnt']);
//통계 데이터
$sql="
	SELECT stats_dttm, count(*) cnt FROM ckd_ad_fail AS a
	LEFT JOIN ckd_user_info b ON a.userKey = b.user_uuid
	WHERE a.stats_dttm BETWEEN {$sdate} AND {$edate} {$add_query}
	group by stats_dttm
	ORDER BY idx DESC
	LIMIT {$PG->first}, {$PG->size}
";
//pre($sql);
$ret = $NDO->fetch_array($sql);
//pre($ret);
?>

<div class="contentpanel">
	<div class="panel panel-default">
		<div class="panel-heading">
			<div class="panel-btns">
				<!--a href="" class="panel-close">&times;</a-->
				<a href="" class="minimize">&minus;</a>
			</div><!-- panel-btns -->
			<h4 class="panel-title"><?=_title_?></h4>
		</div><!-- panel-heading -->

		<div class="panel-body">

			<form name="scform" method="get" action="">
				<div class="row">
					<div class="card">
						<div class="header">
							<div class="input-group call">
								<input type="text" class="form-control pull-left border-input databox" placeholder="yyyy-mm-dd" id="sdate" value="<?=$startDate?>" name="startDate" >
								<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
							</div>
							<span class="pull-left space-in"> ~ </span>
							<div class="input-group call">
								<input type="text" class="form-control pull-left border-input databox" placeholder="yyyy-mm-dd" id="edate" value="<?=$endDate?>" name="endDate" >
								<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
							</div>

							<div class="pull-left">
								<span>
									<a class="btn btn-default <?=($type=='30')?'active':''?>" href="?type=30">최근30일</a>
									<a class="btn btn-default <?=($type=='M')?'active':''?>" href="?type=M">이번달</a>
									<a class="btn btn-default <?=($type=='B1')?'active':''?>" href="?type=B1">전월</a>
									<a class="btn btn-default <?=($type=='B2')?'active':''?>" href="?type=B2">전전월</a>
									<a class="btn btn-default <?=($type=='3M')?'active':''?>" href="?type=3M">3개월</a>
									<a class="btn btn-default <?=($type=='6M')?'active':''?>" href="?type=6M">6개월</a>
								</span>
								<button class="btn btn-success" style="margin-left:10px;">검 색</button>
							</div>
						</div><!-- header -->
					</div><!-- card -->
				</div><!-- row -->
			</form>

			<div class="row member_table .col-xs-12 .col-md-12" style="margin-top:8px">
				<div class="table-responsive table-wrap">
					<table class="table table-hover mb30" style="border:1px solid #b0b0b0;" id="jb-table">
						<thead>
						<tr>
							<th style="width:300px">날짜</th>
							<th>합계</th>
						<tr>
						</thead>
						<tbody>
						<?php
							if(!empty($ret)) {
							foreach ($ret as $row){
						?>
						<tr>
							<td><?=$row['stats_dttm']?></td>
							<td><?=number_format($row['cnt']);?></td>
						</tr>
						<?php }}else{ ?>
						<tr><td colspan="16">데이터가 없습니다.</td></tr>
						<?php } ?>

						</tbody>
					</table>

				</div><!-- table-responsive -->
			</div><!-- row -->
			<div class="row">
				<?=$paging->paging_new($PG,$param);?>
			</div><!-- row -->
		</div><!-- panel-body -->
	</div><!-- panel -->
</div><!-- contentpanel -->

<script>
	$("section").css({"min-width":"1000px"});
</script>

<?php
include __foot__;
?>