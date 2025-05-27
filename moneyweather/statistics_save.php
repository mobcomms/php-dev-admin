<?php
/**********************
 *
 *    통계 페이지 / 일자별 통계
 *
 **********************/

include "./var.php";
include_once __func__;
include_once __pdoDB__;    ## DB Instance 생성

## 환경설정
define('_title_', '포인트 적립 통계');
define('_Menu_', 'reward');
define('_subMenu_', 'reward');

include_once __head__;

$startDate = !empty($_REQUEST['startDate']) ? $_REQUEST['startDate'] : date("Y-m-d",strtotime("-1 month"));
$endDate = !empty($_REQUEST['endDate']) ? $_REQUEST['endDate'] : date("Y-m-d");

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

//통계 데이터
$sql="
	SELECT
	OUP.stats_dttm

	,SUM(IFNULL(OUP.user_cnt, 0)) AS tot_user_cnt
	,SUM(IFNULL(OUP.point_cnt, 0)) AS tot_point_cnt
	,SUM(IFNULL(OUP.sum_point, 0)) AS tot_sum_point

	,SUM(IFNULL(OUP.spot_user_cnt1, 0)) AS spot_user_cnt1
	,SUM(IFNULL(OUP.spot_point_cnt1, 0)) AS spot_point_cnt1
	,SUM(IFNULL(OUP.spot_sum_point1, 0)) AS spot_sum_point1

	,SUM(IFNULL(OUP.spot_user_cnt2, 0)) AS spot_user_cnt2
	,SUM(IFNULL(OUP.spot_point_cnt2, 0)) AS spot_point_cnt2
	,SUM(IFNULL(OUP.spot_sum_point2, 0)) AS spot_sum_point2

	,SUM(IFNULL(OUP.spot_user_cnt3, 0)) AS spot_user_cnt3
	,SUM(IFNULL(OUP.spot_point_cnt3, 0)) AS spot_point_cnt3
	,SUM(IFNULL(OUP.spot_sum_point3, 0)) AS spot_sum_point3

	FROM ckd_save_point_one_day OUP

	WHERE OUP.stats_dttm BETWEEN {$sdate} AND {$edate}
	GROUP BY stats_dttm
	ORDER BY OUP.stats_dttm DESC
";
//pre($sql);
$ret = $NDO->fetch_array($sql);
//pre($ret);

$html = "";
if(!empty($ret)) {
	foreach ($ret as $row) {
		if(empty($TOTAL['tot_point_cnt'])){
			$row+=['tot_point_cnt'=>0,'tot_user_cnt'=>0,'tot_sum_point'=>0];
			foreach($row as $key => $value){
				$TOTAL[$key] = 0;
			}
		}

		$TOTAL['tot_user_cnt'] += $row['tot_user_cnt'];
		$TOTAL['tot_point_cnt'] += $row['tot_point_cnt'];
		$TOTAL['tot_sum_point'] += $row['tot_sum_point'];

		$TOTAL['spot_user_cnt1'] += $row['spot_user_cnt1'];
		$TOTAL['spot_point_cnt1'] += $row['spot_point_cnt1'];
		$TOTAL['spot_sum_point1'] += $row['spot_sum_point1'];
		$TOTAL['spot_user_cnt2'] += $row['spot_user_cnt2'];
		$TOTAL['spot_point_cnt2'] += $row['spot_point_cnt2'];
		$TOTAL['spot_sum_point2'] += $row['spot_sum_point2'];
		$TOTAL['spot_user_cnt3'] += $row['spot_user_cnt3'];
		$TOTAL['spot_point_cnt3'] += $row['spot_point_cnt3'];
		$TOTAL['spot_sum_point3'] += $row['spot_sum_point3'];

		//3개월 6개월 합계
		################################################################################################################################################################
		################################################################################################################################################################
		################################################################################################################################################################

		if($type=="3M" || $type=="6M"){
			//합계
			$month = substr($row['stats_dttm'],0,6);
			if(empty($M_TOTAL[$month]['tot_point_cnt'])){
				$row2 = $row+['tot_point_cnt'=>0,'tot_user_cnt'=>0,'tot_sum_point'=>0];
				foreach($row2 as $key => $value){
					$M_TOTAL[$month][$key] = 0;
				}
			}
			$M_TOTAL[$month]['tot_user_cnt'] += $row['tot_user_cnt'];
			$M_TOTAL[$month]['tot_point_cnt'] += $row['tot_point_cnt'];
			$M_TOTAL[$month]['tot_sum_point'] += $row['tot_sum_point'];

			$M_TOTAL[$month]['spot_user_cnt1'] += $row['spot_user_cnt1'];
			$M_TOTAL[$month]['spot_point_cnt1'] += $row['spot_point_cnt1'];
			$M_TOTAL[$month]['spot_sum_point1'] += $row['spot_sum_point1'];
			$M_TOTAL[$month]['spot_user_cnt2'] += $row['spot_user_cnt2'];
			$M_TOTAL[$month]['spot_point_cnt2'] += $row['spot_point_cnt2'];
			$M_TOTAL[$month]['spot_sum_point2'] += $row['spot_sum_point2'];
			$M_TOTAL[$month]['spot_user_cnt3'] += $row['spot_user_cnt3'];
			$M_TOTAL[$month]['spot_point_cnt3'] += $row['spot_point_cnt3'];
			$M_TOTAL[$month]['spot_sum_point3'] += $row['spot_sum_point3'];

		}
		$dateColor = $fn->dateColor($row['stats_dttm']);
		$dateColor_sticky = $fn->dateColor($row['stats_dttm'])?$fn->dateColor($row['stats_dttm']):'color_background_grey';

		$html .= "
			<tr class='{$dateColor}'>
				<td class='{$dateColor_sticky}'>{$row['stats_dttm']}</td>
				<td>".number_format($row['tot_user_cnt'])."</td>
				<td>".number_format($row['tot_point_cnt'])."</td>
				<td>".number_format($row['tot_sum_point'])."</td>
				<td>".number_format($row['spot_user_cnt1'])."</td>
				<td>".number_format($row['spot_point_cnt1'])."</td>
				<td>".number_format($row['spot_sum_point1'])."</td>
				<td>".number_format($row['spot_user_cnt2'])."</td>
				<td>".number_format($row['spot_point_cnt2'])."</td>
				<td>".number_format($row['spot_sum_point2'])."</td>
				<td>".number_format($row['spot_user_cnt3'])."</td>
				<td>".number_format($row['spot_point_cnt3'])."</td>
				<td>".number_format($row['spot_sum_point3'])."</td>
		";
	}

}else{
	$html='<tr><td colspan="16">데이터가 없습니다.</td></tr>';
}
?>

<style>
	table#jb-table tr:nth-child(1) th:nth-child(1),
	table#jb-table td:first-child { position: sticky; left: 0;   z-index: 1; }
</style>

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
							<div style="display: inline-block;" class="pull-right">
								<button class="btn btn-success" style="margin-left:10px;" id="ExcelDown" onclick="return false">Excel 다운</button>
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
							<th rowspan="2">날짜</th>
							<th rowspan="2">전체 적립<br>유니크요청수</th>
							<th rowspan="2">전체 적립<br>요청수</th>
							<th rowspan="2">전체 적립 금액</th>

							<th colspan="3">미션존 사다리</th>
							<th colspan="3">미션존 룰렛</th>
							<th colspan="3">미션존 복권</th>

						</tr>
						<tr>
							<th>유니크요청수</th>
							<th>요청수</th>
							<th>적립금</th>
							<th>유니크요청수</th>
							<th>요청수</th>
							<th>적립금</th>
							<th>유니크요청수</th>
							<th>요청수</th>
							<th>적립금</th>
						</tr>
						</thead>
						<tbody id="excel">
						<?php if(!empty($ret)) { ?>
						<tr style="background-color: #F2F5A9;">
							<td style="background-color: #F2F5A9;">합계</td>
							<td><?=number_format($TOTAL['tot_user_cnt'])?></td>
							<td><?=number_format($TOTAL['tot_point_cnt'])?></td>
							<td><?=number_format($TOTAL['tot_sum_point'])?></td>

							<td><?=number_format($TOTAL['spot_user_cnt1'])?></td>
							<td><?=number_format($TOTAL['spot_point_cnt1'])?></td>
							<td><?=number_format($TOTAL['spot_sum_point1'])?></td>

							<td><?=number_format($TOTAL['spot_user_cnt2'])?></td>
							<td><?=number_format($TOTAL['spot_point_cnt2'])?></td>
							<td><?=number_format($TOTAL['spot_sum_point2'])?></td>

							<td><?=number_format($TOTAL['spot_user_cnt3'])?></td>
							<td><?=number_format($TOTAL['spot_point_cnt3'])?></td>
							<td><?=number_format($TOTAL['spot_sum_point3'])?></td>

						</tr>
						<?php
						}

						//3개월 6개월 합계
						################################################################################################################################################################
						################################################################################################################################################################
						################################################################################################################################################################

						if($type=="3M" || $type=="6M"){
							foreach ($M_TOTAL as $key=>$row){
						?>

								<tr style="background-color: #AFE076;">
									<td style="background-color: #AFE076;"><?=$key?></td>
									<td><?=number_format($row['tot_user_cnt'])?></td>
									<td><?=number_format($row['tot_point_cnt'])?></td>
									<td><?=number_format($row['tot_sum_point'])?></td>

									<td><?=number_format($row['spot_user_cnt1'])?></td>
									<td><?=number_format($row['spot_point_cnt1'])?></td>
									<td><?=number_format($row['spot_sum_point1'])?></td>

									<td><?=number_format($row['spot_user_cnt2'])?></td>
									<td><?=number_format($row['spot_point_cnt2'])?></td>
									<td><?=number_format($row['spot_sum_point2'])?></td>

									<td><?=number_format($row['spot_user_cnt3'])?></td>
									<td><?=number_format($row['spot_point_cnt3'])?></td>
									<td><?=number_format($row['spot_sum_point3'])?></td>
								</tr>
						<?php
							}
						}
						?>
						<?=$html?>
						</tbody>
					</table>

				</div><!-- table-responsive -->
			</div><!-- row -->
		</div><!-- panel-body -->
	</div><!-- panel -->
</div><!-- contentpanel -->

<script>
	function fnExcelReport(id, title) {
		var tab_text = '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
		tab_text = tab_text + '<head><meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8">';
		tab_text = tab_text + '<xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>'
		tab_text = tab_text + '<x:Name>Sheet1</x:Name>';
		tab_text = tab_text + '<x:WorksheetOptions><x:Panes></x:Panes></x:WorksheetOptions></x:ExcelWorksheet>';
		tab_text = tab_text + '</x:ExcelWorksheets></x:ExcelWorkbook></xml></head><body>';
		tab_text = tab_text + "\
		<table border='1px'>\
			<thead>\
			<tr>\
				<th rowspan='2'>날짜</th>\
				<th rowspan='2'>전체 적립<br>유니크요청수</th>\
				<th rowspan='2'>전체 적립<br>요청수</th>\
				<th rowspan='2'>전체 적립 금액</th>\
				<th colspan='3'>광고API - AOS</th>\
				<th colspan='3'>광고API - IOS</th>\
				<th colspan='3'>미션존 - AOS</th>\
				<th colspan='3'>미션존 - IOS</th>\
			</tr>\
			<tr>\
				<th>유니크요청수</th>\
				<th>요청수</th>\
				<th>적립금</th>\
				<th>유니크요청수</th>\
				<th>요청수</th>\
				<th>적립금</th>\
				<th>유니크요청수</th>\
				<th>요청수</th>\
				<th>적립금</th>\
				<th>유니크요청수</th>\
				<th>요청수</th>\
				<th>적립금</th>\
			</tr>\
			</thead>\
			";
		var exportTable = $('#' + id).clone();
		exportTable.find('input').each(function (index, elem) { $(elem).remove(); });
		tab_text = tab_text + exportTable.html();
		tab_text = tab_text + '</table></body></html>';
		var data_type = 'data:application/vnd.ms-excel';
		var ua = window.navigator.userAgent;
		var msie = ua.indexOf("MSIE ");
		var fileName = title + '.xls';
//Explorer 환경에서 다운로드
		if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) {
			if (window.navigator.msSaveBlob) {
				var blob = new Blob([tab_text], {
					type: "application/csv;charset=utf-8;"
				});
				navigator.msSaveBlob(blob, fileName);
			}
		} else {
			var blob2 = new Blob([tab_text], {
				type: "application/csv;charset=utf-8;"
			});
			var filename = fileName;
			var elem = window.document.createElement('a');
			elem.href = window.URL.createObjectURL(blob2);
			elem.download = filename;
			document.body.appendChild(elem);
			elem.click();
			document.body.removeChild(elem);
		}
	}

	$("#ExcelDown").click(function(){
		fnExcelReport('excel','돈씨 적립통계');
	});


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


	$("section").css({"min-width":"1500px"});

</script>

<?php
include __foot__;
?>