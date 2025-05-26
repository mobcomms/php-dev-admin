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
define('_title_', '게임존 유저 통계');
define('_Menu_', 'manage');
define('_subMenu_', 'game');

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
	SELECT T.stats_dttm, T.game_main_PV, T.game_main_DAU
	,IFNULL(T2.roulette_PV,0) AS roulette_PV
	,IFNULL(T2.roulette_UV,0) AS roulette_UV
	,IFNULL(T5.roulette_DAU,0) AS roulette_DAU
	,IFNULL(T8.roulette_DAU2,0) AS roulette_DAU2
	,IFNULL(T3.ladder_PV,0) AS ladder_PV
	,IFNULL(T3.ladder_UV,0) AS ladder_UV
	,IFNULL(T6.ladder_DAU,0) AS ladder_DAU
	,IFNULL(T9.ladder_DAU2,0) AS ladder_DAU2
	,IFNULL(T4.lotto_PV,0) AS lotto_PV
	,IFNULL(T4.lotto_UV,0) AS lotto_UV
	,IFNULL(T7.lotto_DAU,0) AS lotto_DAU
	,IFNULL(T10.lotto_DAU2,0) AS lotto_DAU2
	FROM(
		SELECT
		stats_dttm
		,IFNULL(SUM(game_main),0) AS game_main_PV
		,IFNULL(COUNT(game_main),0) AS game_main_DAU
		FROM ocb_game_stats WHERE game_main>0 GROUP BY stats_dttm
	) T
	LEFT JOIN (
		SELECT
		stats_dttm
		,SUM(roulette) AS roulette_PV
		,COUNT(roulette) AS roulette_UV
		FROM ocb_game_stats WHERE roulette>0 GROUP BY stats_dttm
	) T2 ON T.stats_dttm = T2.stats_dttm
	LEFT JOIN(
		SELECT
		stats_dttm
		,SUM(ladder) AS ladder_PV
		,COUNT(ladder) AS ladder_UV
		FROM ocb_game_stats WHERE ladder>0 GROUP BY stats_dttm
	) T3 ON T.stats_dttm = T3.stats_dttm
	LEFT JOIN(
		SELECT
		stats_dttm
		,SUM(lotto) AS lotto_PV
		,COUNT(lotto) AS lotto_UV
		FROM ocb_game_stats WHERE lotto>0 GROUP BY stats_dttm
	) T4 ON T.stats_dttm = T4.stats_dttm
	LEFT JOIN(
		SELECT
		stats_dttm
		,COUNT(roulette) AS roulette_DAU
		FROM ocb_game_stats WHERE roulette>0 AND roulette_ad>=1 GROUP BY stats_dttm
	) T5 ON T.stats_dttm = T5.stats_dttm
	LEFT JOIN(
		SELECT
		stats_dttm
		,COUNT(ladder) AS ladder_DAU
		FROM ocb_game_stats WHERE ladder>0 AND ladder_ad>=1 GROUP BY stats_dttm
	) T6 ON T.stats_dttm = T6.stats_dttm
	LEFT JOIN(
		SELECT
		stats_dttm
		,COUNT(lotto) AS lotto_DAU
		FROM ocb_game_stats WHERE lotto>0 AND lotto_ad>=1 GROUP BY stats_dttm
	) T7 ON T.stats_dttm = T7.stats_dttm
	LEFT JOIN(
		SELECT
		stats_dttm
		,COUNT(roulette) AS roulette_DAU2
		FROM ocb_game_stats WHERE roulette>0 AND roulette_ad>=2 GROUP BY stats_dttm
	) T8 ON T.stats_dttm = T8.stats_dttm
	LEFT JOIN(
		SELECT
		stats_dttm
		,COUNT(ladder) AS ladder_DAU2
		FROM ocb_game_stats WHERE ladder>0 AND ladder_ad>=2 GROUP BY stats_dttm
	) T9 ON T.stats_dttm = T9.stats_dttm
	LEFT JOIN(
		SELECT
		stats_dttm
		,COUNT(lotto) AS lotto_DAU2
		FROM ocb_game_stats WHERE lotto>0 AND lotto_ad>=2 GROUP BY stats_dttm
	) T10 ON T.stats_dttm = T10.stats_dttm

	WHERE T.stats_dttm BETWEEN {$sdate} AND {$edate}
	ORDER BY T.stats_dttm DESC
";
//pre($sql);
$ret = $NDO->fetch_array($sql);
//pre($ret);

//합계 변수 세팅
$html = '';
$TOTAL = [];
$make_array = array("stats_dttm","game_main_PV","game_main_DAU","roulette_PV","roulette_UV","roulette_DAU","roulette_DAU2","ladder_PV","ladder_UV","ladder_DAU","ladder_DAU2","lotto_PV","lotto_UV","lotto_DAU","lotto_DAU2");
foreach($make_array as $item){
	$TOTAL[$item] = 0;
}

if(is_array($ret) && !empty($ret)) {

	foreach($make_array as $item){
		$TOTAL[$item] = 0;
	}
	foreach ($ret as $row) {
		$TOTAL['game_main_PV'] += $row['game_main_PV'];
		$TOTAL['game_main_DAU'] += $row['game_main_DAU'];

		$TOTAL['roulette_PV'] += $row['roulette_PV'];
		$TOTAL['roulette_UV'] += $row['roulette_UV'];
		$TOTAL['roulette_DAU'] += $row['roulette_DAU'];
		$TOTAL['roulette_DAU2'] += $row['roulette_DAU2'];

		$TOTAL['ladder_PV'] += $row['ladder_PV'];
		$TOTAL['ladder_UV'] += $row['ladder_UV'];
		$TOTAL['ladder_DAU'] += $row['ladder_DAU'];
		$TOTAL['ladder_DAU2'] += $row['ladder_DAU2'];

		$TOTAL['lotto_PV'] += $row['lotto_PV'];
		$TOTAL['lotto_UV'] += $row['lotto_UV'];
		$TOTAL['lotto_DAU'] += $row['lotto_DAU'];
		$TOTAL['lotto_DAU2'] += $row['lotto_DAU2'];

		$html .= "
			<tr class='".$fn->dateColor($row['stats_dttm'])."'>
				<td>{$row['stats_dttm']}</td>

				 <td>".number_format($row['game_main_DAU'])."</td>
				 <td>".number_format($row['game_main_PV'])."</td>

				 <td>".number_format($row['roulette_UV'])."</td>
				 <td>".number_format($row['roulette_PV'])."</td>
				 <td>".number_format($row['roulette_DAU'])."</td>
				 <td>".number_format($row['roulette_DAU2'])."</td>
				 
				 <td>".number_format($row['ladder_UV'])."</td>
				 <td>".number_format($row['ladder_PV'])."</td>
				 <td>".number_format($row['ladder_DAU'])."</td>
				 <td>".number_format($row['ladder_DAU2'])."</td>
				 
				 <td>".number_format($row['lotto_UV'])."</td>
				 <td>".number_format($row['lotto_PV'])."</td>
				 <td>".number_format($row['lotto_DAU'])."</td>
				 <td>".number_format($row['lotto_DAU2'])."</td>

			 </tr>
		";
	}

}else{
	$html='<tr><td colspan="15">데이터가 없습니다.</td></tr>';
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

								<div style="display: inline-block;" class="pull-left">
									<span>
										<a class="btn btn-default <?=($type=='30')?'active':''?>" href="javascript:void(0);" onclick="SetDate('30','sdate','edate','30','');">최근30일</a>
										<a class="btn btn-default <?=($type=='M')?'active':''?>" href="javascript:void(0);" onclick="SetDate('M','sdate','edate','M','');">이번달</a>
										<a class="btn btn-default <?=($type=='B1')?'active':''?>" href="javascript:void(0);" onclick="SetDate('B1','sdate','edate','B1','');">전월</a>
										<a class="btn btn-default <?=($type=='B2')?'active':''?>" href="javascript:void(0);" onclick="SetDate('B2','sdate','edate','B2','');">전전월</a>
										<a class="btn btn-default <?=($type=='3M')?'active':''?>" href="javascript:void(0);" onclick="SetDate('3M','sdate','edate','3M','');">3개월</a>
										<a class="btn btn-default <?=($type=='6M')?'active':''?>" href="javascript:void(0);" onclick="SetDate('6M','sdate','edate','6M','');">6개월</a>
									</span>
									<button class="btn btn-success" style="margin-left:10px;">검 색</button>
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
								<th rowspan="2" style="width: 100px;">날짜</th>
								<th colspan="2">돈버는 게임존</th>
								<th colspan="4">룰렛</th>
								<th colspan="4">사다리</th>
								<th colspan="4">복권</th>
							</tr>
							<tr>
								<th width="110">DAU</th>
								<th>PV</th>

								<th>UV</th>
								<th>PV</th>
								<th width="110">DAU</th>
								<th width="110">2회 참여자</th>

								<th>UV</th>
								<th>PV</th>
								<th width="110">DAU</th>
								<th width="110">2회 참여자</th>

								<th>UV</th>
								<th>PV</th>
								<th width="110">DAU</th>
								<th width="110">2회 참여자</th>
							</tr>
							</thead>
							<tbody>
							<tr style="background-color: #F2F5A9;">
								<td>합계</td>

								<td><?=number_format($TOTAL['game_main_DAU'])?></td>
								<td><?=number_format($TOTAL['game_main_PV'])?></td>

								<td><?=number_format($TOTAL['roulette_UV'])?></td>
								<td><?=number_format($TOTAL['roulette_PV'])?></td>
								<td><?=number_format($TOTAL['roulette_DAU'])?></td>
								<td><?=number_format($TOTAL['roulette_DAU2'])?></td>

								<td><?=number_format($TOTAL['ladder_UV'])?></td>
								<td><?=number_format($TOTAL['ladder_PV'])?></td>
								<td><?=number_format($TOTAL['ladder_DAU'])?></td>
								<td><?=number_format($TOTAL['ladder_DAU2'])?></td>

								<td><?=number_format($TOTAL['lotto_UV'])?></td>
								<td><?=number_format($TOTAL['lotto_PV'])?></td>
								<td><?=number_format($TOTAL['lotto_DAU'])?></td>
								<td><?=number_format($TOTAL['lotto_DAU2'])?></td>

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
		fnExcelReport('ocb','OCB_게임존_통계');
	});
	// 날짜 설정
	function report(type, sub, sdate, edate){
		sdate=(sdate) ? sdate :  '<?=date("Y-m-d")?>';
		edate=(edate) ? edate :  '<?=date("Y-m-d")?>';
		location.href='<?=$_SERVER['PHP_SELF']?>?startDate='+sdate+'&endDate='+edate+'&type='+type+'&sub='+sub;
	}
</script>

