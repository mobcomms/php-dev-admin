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
define('_title_', '매출 통계');
define('_Menu_', 'use');
define('_subMenu_', 'use');

include_once __head__;

$startDate=isset($_REQUEST['startDate']) ? $_REQUEST['startDate'] : date("Y-m-d",strtotime("-1 month"));
$endDate=isset($_REQUEST['endDate']) ? $_REQUEST['endDate'] : date("Y-m-d");
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

$sdate=str_replace("-","",$startDate);
$edate=str_replace("-","",$endDate);

$sql="
SELECT
    CDMS.stats_dttm,
    SUM(CDMS.activity_num) AS activity_num,
    SUM(CDMS.use_cnt) AS use_cnt,
    SUM(CDMS.use_time) AS use_time,
    SUM(CDMS.use_tot_cnt) AS use_tot_cnt,
    SUM(CASE WHEN service_tp_code = '01' THEN exhs_amt ELSE 0 END) AS mw_exhs1,
    SUM(CASE WHEN service_tp_code = '02' THEN exhs_amt ELSE 0 END) AS mw_exhs2,
    SUM(CASE WHEN service_tp_code = '03' THEN exhs_amt ELSE 0 END) AS mw_exhs3
FROM moneyweather.ckd_day_app_stats CDMS
LEFT JOIN moneyweather.ckd_day_ad_stats CDS ON CDS.stats_dttm = CDMS.stats_dttm
WHERE CDMS.stats_dttm BETWEEN {$sdate} AND {$edate}
GROUP BY CDMS.stats_dttm
ORDER BY CDMS.stats_dttm DESC
";
//pre($sql);
$ret = $NDO->fetch_array($sql);
//pre($ret);

function date_color_code($day){
	$yoil = array("#fff3f3","","","","","","#f1fcff");
	return ($yoil[date('w', strtotime($day))]);
}

$html='';
$TOTAL = [];
$M_TOTAL = [];
$make_array1 = array("accumulate","activity_num","use_time","accumulate","activity_num","use_time","use_cnt","use_tot_cnt","useTotCntAvg","ctr");
$make_array2 = array("mw_exhs1","mw_exhs2","mw_exhs3");

$make_array = array_merge ($make_array1, $make_array2);
foreach($make_array as $item){
	$TOTAL[$item] = 0;
}

foreach($ret as $key => $row){

	$commission = 1;

	$offerwall_commission = $commission;

	//모비위드
	$mw_commission = $commission;

	//합계
	$TOTAL['mw_exhs1'] += $row['mw_exhs1'];
	$TOTAL['mw_exhs2'] += $row['mw_exhs2'];
	$TOTAL['mw_exhs3'] += $row['mw_exhs3'];

	if($type=="3M" || $type=="6M"){
		//합계
		$month = substr($row['stats_dttm'],0,6);
		if(!isset($M_TOTAL[$month])){
			$make_array = array_merge ($make_array1, $make_array2);
			foreach($make_array as $item){
				$M_TOTAL[$month][$item] = 0;
			}
		}

		$M_TOTAL[$month]['mw_exhs1'] += round($row['mw_exhs1']);
		$M_TOTAL[$month]['mw_exhs2'] += round($row['mw_exhs2']);
		$M_TOTAL[$month]['mw_exhs3'] += round($row['mw_exhs3']);

	}

	$total_sales = $row['mw_exhs1'] + $row['mw_exhs2'] + $row['mw_exhs3'];

	$total_settlement = $total_sales * 0.8;
	$total_profit = $total_sales * 0.2;

	$use_cnt = empty($row['use_cnt'])?0:number_format($row['use_cnt']);

	$html .= "<tr class='{$fn->dateColor($row['stats_dttm'])}'>";
	$html .= "<td>{$row['stats_dttm']}</td>";

	$color_sales = $total_sales<0?'red':'blue';
	$color_settlement = $total_settlement<0?'red':'blue';
	$color_profit = $total_profit<0?'red':'blue';
	$html .= "<!-- 총 매출 --><td style='color:".$color_sales.";font-weight:700;'>".number_format($total_sales)."</td>";
	$html .= "<!-- 총 정산금 --><td style='color:".$color_settlement.";font-weight:700;'>".number_format($total_settlement)."</td>";
	if($_SESSION['Adm']['id'] == "mango"){
		$html .= "<!-- 총 수익 --><td style='color:".$color_profit.";font-weight:700;'>".number_format($total_profit)."</td>";
	}

	$color1 = $row['mw_exhs1']<0?'red':'blue';
	$color2 = $row['mw_exhs2']<0?'red':'blue';
	$color3 = $row['mw_exhs3']<0?'red':'blue';
	$html .= "
		<!-- 미션존 사다리 -->
		<td class='show_banner_aos' style='color:".$color1.";font-weight:700;'>".number_format($row['mw_exhs1'])."</td>
		<!-- 미션존 룰렛 -->
		<td class='show_banner_ios' style='color:".$color2.";font-weight:700;'>".number_format($row['mw_exhs2'])."</td>
		<!-- 미션존 복권 -->
		<td class='show_banner_aos' style='color:".$color3.";font-weight:700;'>".number_format($row['mw_exhs3'])."</td>
	";

}//foreach

if(empty($html)){
	$html="<tr><td colspan='31'>데이터가 없습니다.</td></tr>";
}

//총수익
$total_sales_all = $TOTAL['mw_exhs1']+$TOTAL['mw_exhs2']+$TOTAL['mw_exhs3'];
$total_settlement_all = $total_sales_all * 0.8;
$total_profit_all = $total_sales_all * 0.2;

?>
<style>
	a.tooltips {outline:none; }
	a.tooltips strong {line-height:30px;}
	a.tooltips:hover {text-decoration:none;}
	a.tooltips span {
		z-index:10;display:none; padding:14px 20px;
		margin-top:30px; margin-left:-160px;
		width:350px; line-height:16px;
	}
	a.tooltips:hover span{
		display:inline; position:absolute;
		border:2px solid #FFF;  color:#EEE;
		background-color: black;
	}
	.callout {z-index:20;position:absolute;border:0;top:-14px;left:120px;}

	/*CSS3 extras*/
	a.tooltips span
	{
		border-radius:2px;
		box-shadow: 0px 0px 8px 4px #666;
		/*opacity: 0.8;*/
	}

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
							<div class="row" style="margin-left:0">
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
											<a class="btn btn-default <?=($type=='30')?'active':''?>" href="?type=30">최근30일</a>
											<a class="btn btn-default <?=($type=='M')?'active':''?>" href="?type=M">이번달</a>
											<a class="btn btn-default <?=($type=='B1')?'active':''?>" href="?type=B1">전월</a>
											<a class="btn btn-default <?=($type=='B2')?'active':''?>" href="?type=B2">전전월</a>
											<a class="btn btn-default <?=($type=='3M')?'active':''?>" href="?type=3M">3개월</a>
											<a class="btn btn-default <?=($type=='6M')?'active':''?>" href="?type=6M">6개월</a>
										</span>
									<button class="btn btn-success" style="margin-left:10px;">검 색</button>
								</div>
								<?php if($_SESSION['Adm']['id'] == "mango"){ ?>
								<div style="display: inline-block;" class="pull-left">
									<button type="button" class="btn btn-danger" style="margin-left:10px;">데이터 갱신(새창)</button>
								</div>
								<?php } ?>
							</div>

							<div style="display: inline-block;" class="pull-right">
								<button class="btn btn-success" style="margin-left:10px;" id="ExcelDown" onclick="return false">Excel 다운</button>
							</div>

						</div><!-- header -->
					</div><!-- card -->
				</div><!-- row -->
			</form>
			<div class="row member_table .col-xs-12 .col-md-12" style="margin-top:8px">

				<div class="table-responsive">
					<table class="table table-hover mb30" style="border:1px solid #b0b0b0;">
						<thead>
						<tr>
							<th rowspan="2">날짜</th>
							<th rowspan="2" style="color:blue;">총 매출</th>
							<th rowspan="2" style="color:blue;">매체 수수료</th>
<?php if($_SESSION['Adm']['id'] == "mango"){ ?>
							<th rowspan="2" style="color:blue;">매출 총이익</th>
<?php } ?>
							<th  class="show_coupang_ios">미션존 사다리
								<a href="#" class="tooltips"><i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
									<span>
										<strong>모비위드 연동</strong><br />
										(2일전 데이터 매시갱신)
									</span>
								</a>
							</th>
							<th class="show_coupang_aos">미션존 룰렛
								<a href="#" class="tooltips"><i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
									<span>
										<strong>모비위드 연동</strong><br />
										(2일전 데이터 매시갱신)
									</span>
								</a>
							</th>
							<th class="show_coupang_aos">미션존 복권
								<a href="#" class="tooltips"><i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
									<span>
										<strong>모비위드 연동</strong><br />
										(2일전 데이터 매시갱신)
									</span>
								</a>
							</th>
						</tr>
						</thead>
						<tbody id="ocb" >
						<tr class="" style="background-color: #F2F5A9;">
							<td>합계</td>
							<td style="color:blue;font-weight:700;"><?=number_format($total_sales_all)?></td>
							<td style="color:blue;font-weight:700;"><?=number_format($total_settlement_all)?></td>
							<?php if($_SESSION['Adm']['id'] == "mango"){ ?>
							<td style="color:blue;font-weight:700;"><?=number_format($total_profit_all)?></td>
							<?php } ?>
							<!-- 미션존 사다리 -->
							<td class="show_banner_aos" style="color:<?=$TOTAL['mw_exhs1']<0?"red":"blue"?>;font-weight:700;"><?=number_format($TOTAL['mw_exhs1'])?></td>
							<!-- 미션존 룰렛  -->
							<td class="show_banner_ios" style="color:<?=$TOTAL['mw_exhs2']<0?"red":"blue"?>;font-weight:700;"><?=number_format($TOTAL['mw_exhs2'])?></td>
							<!-- 미션존 복권 -->
							<td class="show_coupang_aos" style="color:<?=$TOTAL['mw_exhs3']<0?"red":"blue"?>;font-weight:700;"><?=number_format($TOTAL['mw_exhs3'])?></td>
						</tr>
					<?php

					//3개월 6개월 합계
					################################################################################################################################################################
					################################################################################################################################################################
					################################################################################################################################################################
					################################################################################################################################################################
					################################################################################################################################################################

					if($type=="3M" || $type=="6M"){
						foreach ($M_TOTAL as $key=>$row){
							$this_date = $key."01";
							$date = date("Y-m-d",strtotime ("-1 days", strtotime($this_date)));
							$date = date("Y-m-d",strtotime ("+1 days", strtotime($date)));
							$this_month_day_cnt = date('t',strtotime($date));

							$month_total_sales = $row['mw_exhs1']+$row['mw_exhs2']+$row['mw_exhs3'];
							$month_total_settlement = $month_total_sales * 0.8;
							$month_total_profit = $month_total_sales * 0.2;

					?>
						<tr class="" style="background-color: #afe076;">
							<td><?=$key?></td>
							<td style="color:blue;font-weight:700;"><?=number_format($month_total_sales)?></td>
							<td style="color:blue;font-weight:700;"><?=number_format($month_total_settlement)?></td>
							<?php if($_SESSION['Adm']['id'] == "mango"){ ?>
							<td style="color:blue;font-weight:700;"><?=number_format($month_total_profit)?></td>
							<?php }?>
							<!-- 미션존 사다리 -->
							<td class="show_banner_aos" style="color:<?=$row['mw_exhs1']<0?"red":"blue"?>;font-weight:700;"><?=number_format($row['mw_exhs1'])?></td>
							<!-- 미션존 룰렛  -->
							<td class="show_banner_ios" style="color:<?=$row['mw_exhs2']<0?"red":"blue"?>;font-weight:700;"><?=number_format($row['mw_exhs2'])?></td>
							<!-- 미션존 복권 -->
							<td class="show_coupang_aos" style="color:<?=$row['mw_exhs3']<0?"red":"blue"?>;font-weight:700;"><?=number_format($row['mw_exhs3'])?></td>

						</tr>

					<?php }}?>

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
					<th>날짜</th>\
					<th style='color:blue;'>총 매출</th>\
					<th style='color:blue;'>총 정산금</th>\
		<?php if($_SESSION['Adm']['id'] == "mango"){ ?>
					<th style='color:blue;'>총 수익</th>\
		<?php }?>
		";
		tab_text = tab_text + "<th>미션존 사다리 </th>";
		tab_text = tab_text + "<th>미션존 룰렛</th>";
		tab_text = tab_text + "<th>미션존 복권 </th>";

		tab_text = tab_text + "</tr>\
			</thead>";
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
	fnExcelReport('ocb','매출통계');
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
$(".btn-danger").click(function(){
	centerOpenWindow('upd_mobwithad_data.php', 'save_point_limit', '450', '284', '', 'N');
});

//전체 클릭시 일반,쿠팡,오퍼월 모두 체크 해제
$("#ad_type").click(function(){
	if($(this).prop("checked")){
		$("[name='ad_type[1]']").prop("checked", false);
		$("[name='ad_type[2]']").prop("checked", false);
		$("[name='ad_type[3]']").prop("checked", false);
	}
});
//일반,쿠팡,오퍼월 모두 체크 해제시 전체 체크
$("[name='ad_type[1]'], [name='ad_type[2]'], [name='ad_type[3]']").click(function(){
	if(!$("[name='ad_type[1]']").prop("checked") && !$("[name='ad_type[2]']").prop("checked") && !$("[name='ad_type[3]']").prop("checked")){
		$("#ad_type").prop("checked", true);
	}else{
		$("#ad_type").prop("checked", false);
	}
});

	//$("section").css({"min-width":"2200px"});
</script>

<?php
include __foot__;
?>
