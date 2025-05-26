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
define('_title_', '이벤트 적립 통계');
define('_Menu_', 'manage');
define('_subMenu_', 'save_spot');

include_once __head__;

$startDate=!empty($_REQUEST['startDate']) ? $_REQUEST['startDate'] : date("Y-m-d",strtotime("-1 month"));
$endDate=!empty($_REQUEST['endDate']) ? $_REQUEST['endDate'] : date("Y-m-d");
if(empty($_REQUEST['type'])) {
	if (empty($_REQUEST['startDate'])) {
		$type = 30;
	} else {
		$type = "";
	}
}
$sdate=str_replace("-","",$startDate);
$edate=str_replace("-","",$endDate);

//통계 데이터
$sql="
	SELECT
	ODS.stats_dttm
	
	,IFNULL(OUP.spot_user_cnt1, 0) AS user_cnt1
	,IFNULL(OUP.spot_sum_point1, 0) AS sum_point1
	
	,IFNULL(OUP.spot_user_cnt2, 0) AS user_cnt2
	,IFNULL(OUP.spot_sum_point2, 0) AS sum_point2
	
	,IFNULL(OUP.spot_user_cnt3, 0) AS user_cnt3
	,IFNULL(OUP.spot_sum_point3, 0) AS sum_point3
	
	,IFNULL(OUP.spot_user_cnt4, 0) AS user_cnt4
	,IFNULL(OUP.spot_sum_point4, 0) AS sum_point4
	
	,IFNULL(OUP.spot_user_cnt5, 0) AS user_cnt5
	,IFNULL(OUP.spot_sum_point5, 0) AS sum_point5
	
	,IFNULL(OUP.spot_user_cnt6, 0) AS user_cnt6
	,IFNULL(OUP.spot_sum_point6, 0) AS sum_point6

	FROM ocb_day_stats ODS
	LEFT JOIN ocb_save_point_one_day OUP ON OUP.stats_dttm = ODS.stats_dttm

	WHERE ODS.stats_dttm BETWEEN {$sdate} AND {$edate}
	ORDER BY ODS.stats_dttm DESC
";
//pre($sql);
$ret = $NDO->fetch_array($sql);

$data = array();
if(is_array($ret)) {

	$html = '';
	$TOTAL = [];
	$make_array = array("user_cnt1","sum_point1","user_cnt2","sum_point2","user_cnt3","sum_point3","user_cnt4","sum_point4","user_cnt5","sum_point5","user_cnt6","sum_point6");
	foreach($make_array as $item){
		$TOTAL[$item] = 0;
	}
	foreach ($ret as $row) {
		$TOTAL['user_cnt1'] += $row['user_cnt1'];
		$TOTAL['sum_point1'] += $row['sum_point1'];

		$TOTAL['user_cnt2'] += $row['user_cnt2'];
		$TOTAL['sum_point2'] += $row['sum_point2'];

		$TOTAL['user_cnt3'] += $row['user_cnt3'];
		$TOTAL['sum_point3'] += $row['sum_point3'];

		$TOTAL['user_cnt4'] += $row['user_cnt4'];
		$TOTAL['sum_point4'] += $row['sum_point4'];

		$TOTAL['user_cnt5'] += $row['user_cnt5'];
		$TOTAL['sum_point5'] += $row['sum_point5'];

		$TOTAL['user_cnt6'] += $row['user_cnt6'];
		$TOTAL['sum_point6'] += $row['sum_point6'];

		$html .= "
			<tr class='".$fn->dateColor($row['stats_dttm'])."'>
				<td>{$row['stats_dttm']}</td>

				 <td>".number_format($row['user_cnt1'])."</td>
				 <td>".number_format($row['sum_point1'])."</td>

				 <td>".number_format($row['user_cnt2'])."</td>
				 <td>".number_format($row['sum_point2'])."</td>

				 <td>".number_format($row['user_cnt3'])."</td>
				 <td>".number_format($row['sum_point3'])."</td>

				 <td>".number_format($row['user_cnt4'])."</td>
				 <td>".number_format($row['sum_point4'])."</td>

				 <td>".number_format($row['user_cnt5'])."</td>
				 <td>".number_format($row['sum_point5'])."</td>

				 <td>".number_format($row['user_cnt6'])."</td>
				 <td>".number_format($row['sum_point6'])."</td>

			 </tr>
		";
	}

}else{
	$html='<tr><td colspan="5">데이터가 없습니다.</td></tr>';
}

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
								<input type="hidden" name="rep" value="<?=_rep_;?>" />
								<div class="input-group call">
									<input type="text" class="form-control pull-left border-input databox" placeholder="yyyy-mm-dd" id="sdate" value="<?=$startDate?>" name="startDate" >
									<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
								</div>
								<span class="pull-left space-in"> ~ </span>
								<div class="input-group call">
									<input type="text" class="form-control pull-left border-input databox" placeholder="yyyy-mm-dd" id="edate" value="<?=$endDate?>" name="endDate" >
									<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
								</div>
								<button class="btn btn-success" style="margin-left:10px;">검 색</button>

								<div style="display: inline-block;" class="pull-left">
									<span>
										<a class="btn btn-default <?=($type=='30')?'active':''?>" href="javascript:void(0);" onclick="SetDate('30','sdate','edate','30','');">최근30일</a>
										<a class="btn btn-default <?=($type=='M')?'active':''?>" href="javascript:void(0);" onclick="SetDate('M','sdate','edate','M','');">이번달</a>
										<a class="btn btn-default <?=($type=='B1')?'active':''?>" href="javascript:void(0);" onclick="SetDate('B1','sdate','edate','B1','');">전월</a>
										<a class="btn btn-default <?=($type=='B2')?'active':''?>" href="javascript:void(0);" onclick="SetDate('B2','sdate','edate','B2','');">전전월</a>
										<a class="btn btn-default <?=($type=='3M')?'active':''?>" href="javascript:void(0);" onclick="SetDate('3M','sdate','edate','3M','');">3개월</a>
										<a class="btn btn-default <?=($type=='6M')?'active':''?>" href="javascript:void(0);" onclick="SetDate('6M','sdate','edate','6M','');">6개월</a>
									</span>
								</div>

								<div style="display: inline-block;margin-left: 360px;" class="pull-right">
									<button class="btn btn-success" style="margin-left:10px;" id="ExcelDown" onclick="return false">Excel 다운</button>
								</div>

							</div><!-- header -->
						</div><!-- card -->
					</div><!-- row -->
				</form>

				<div class="row member_table .col-xs-12 .col-md-12" style="margin-top:20px">
					<div class="table-responsive">
						<table class="table table-hover mb30" id="ocb"  style="border:1px solid #b0b0b0;">
							<thead>
							<tr>
								<th style="width: 100px;">날짜</th>

								<th>깜짝_키보드 사용 요청 사용자 수</th>
								<th>깜짝_키보드 사용 적립 금액</th>

								<th>깜짝_뉴스 사용자 수</th>
								<th>깜짝_뉴스 적립 금액</th>

								<th>깜짝_배너광고 사용자 수</th>
								<th>깜짝_배너광고 적립 금액</th>

								<th>깜짝_타임딜 사용자 수</th>
								<th>깜짝_타임딜 적립 금액</th>

								<th>깜짝_쇼핑검색 사용자 수</th>
								<th>깜짝_ 쇼핑검색 적립 금액</th>

								<th>깜짝_브랜드 광고 사용자 수</th>
								<th>깜짝_브랜드 광고 적립 금액</th>

							</tr>
							</thead>
							<tbody>
							<tr style="background-color: #F2F5A9;">
								<td>합계</td>

								<td><?=number_format($TOTAL['user_cnt1'])?></td>
								<td><?=number_format($TOTAL['sum_point1'])?></td>

								<td><?=number_format($TOTAL['user_cnt2'])?></td>
								<td><?=number_format($TOTAL['sum_point2'])?></td>

								<td><?=number_format($TOTAL['user_cnt3'])?></td>
								<td><?=number_format($TOTAL['sum_point3'])?></td>

								<td><?=number_format($TOTAL['user_cnt4'])?></td>
								<td><?=number_format($TOTAL['sum_point4'])?></td>

								<td><?=number_format($TOTAL['user_cnt5'])?></td>
								<td><?=number_format($TOTAL['sum_point5'])?></td>

								<td><?=number_format($TOTAL['user_cnt6'])?></td>
								<td><?=number_format($TOTAL['sum_point6'])?></td>

							</tr>
							<?=$html?>
							</tbody>
						</table>

					</div><!-- table-responsive -->
				</div><!-- row -->
			</div><!-- panel-body -->
		</div><!-- panel -->
	</div><!-- contentpanel -->

<?php
include __foot__;
?>

<script>
	function fnExcelReport(id, title) {
		var tab_text = '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
		tab_text = tab_text + '<head><meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8">';
		tab_text = tab_text + '<xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>'
		tab_text = tab_text + '<x:Name>Sheet1</x:Name>';
		tab_text = tab_text + '<x:WorksheetOptions><x:Panes></x:Panes></x:WorksheetOptions></x:ExcelWorksheet>';
		tab_text = tab_text + '</x:ExcelWorksheets></x:ExcelWorkbook></xml></head><body>';
		tab_text = tab_text + "<table border='1px'>";
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
		fnExcelReport('ocb','OCB_이벤트_적립통계');
	});
	// 날짜 설정
	function report(type, sub, sdate, edate){
		sdate=(sdate) ? sdate :  '<?=date("Y-m-d")?>';
		edate=(edate) ? edate :  '<?=date("Y-m-d")?>';
		location.href='<?=$_SERVER['PHP_SELF']?>?startDate='+sdate+'&endDate='+edate+'&type='+type+'&sub='+sub;
	}
</script>

