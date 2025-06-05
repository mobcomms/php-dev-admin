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
define('_title_', '머니박스 통계');
define('_Menu_', 'manage');
define('_subMenu_', 'use1');

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

$ad_type = empty($_REQUEST['ad_type']) ?"":$_REQUEST['ad_type'];
$os_type = empty($_REQUEST['os_type']) ?"":$_REQUEST['os_type'];

// 통계 데이터
$sql="
	SELECT
		CDMS.stats_dttm
		,SUM(activity_num) AS activity_num
		,SUM(use_cnt) AS use_cnt
		,SUM(use_time) AS use_time
		,SUM(use_tot_cnt) AS use_tot_cnt
";
switch($os_type){
	case "1" :
		$sql .= "
			,IFNULL(CDS11.click_num, 0) + IFNULL(CDS12.click_num, 0) AS mw_click1
			,IFNULL(CDS1.exhs_amt, 0) + IFNULL(CDS2.exhs_amt, 0) AS mw_exhs1
			,0 AS mw_click2
			,0 AS mw_exhs2

			,IFNULL(CSTATS11.click_num, 0) + IFNULL(CSTATS12.click_num, 0) AS coupang_click_num1
			,IFNULL(CSTATS1.order_commission, 0) + IFNULL(CSTATS1.cancel_commission, 0) + IFNULL(CSTATS2.order_commission, 0) + IFNULL(CSTATS2.cancel_commission, 0)  AS coupang_order1
			,0 AS coupang_click_num2
			,0 AS coupang_order2
		";
	break;
	case "2" :
		$sql .= "
			,0 AS mw_click1
			,0 AS mw_exhs1
			,IFNULL(CDS13.click_num, 0) + IFNULL(CDS14.click_num, 0) AS mw_click2
			,IFNULL(CDS3.exhs_amt, 0) + IFNULL(CDS4.exhs_amt, 0) AS mw_exhs2

			,0 AS coupang_click_num1
			,0 AS coupang_order1
			,IFNULL(CSTATS13.click_num, 0) + IFNULL(CSTATS14.click_num, 0) AS coupang_click_num2
			,IFNULL(CSTATS3.order_commission, 0) + IFNULL(CSTATS3.cancel_commission, 0) + IFNULL(CSTATS4.order_commission, 0) + IFNULL(CSTATS4.cancel_commission, 0)  AS coupang_order2
		";
	break;
	default :
		$sql .= "
			,IFNULL(CDS11.click_num, 0) + IFNULL(CDS12.click_num, 0) AS mw_click1
			,IFNULL(CDS1.exhs_amt, 0) + IFNULL(CDS2.exhs_amt, 0) AS mw_exhs1
			,IFNULL(CDS13.click_num, 0) + IFNULL(CDS14.click_num, 0) AS mw_click2
			,IFNULL(CDS3.exhs_amt, 0) + IFNULL(CDS4.exhs_amt, 0) AS mw_exhs2
	
			,IFNULL(CSTATS11.click_num, 0) + IFNULL(CSTATS12.click_num, 0) AS coupang_click_num1
			,IFNULL(CSTATS1.order_commission, 0) + IFNULL(CSTATS1.cancel_commission, 0) + IFNULL(CSTATS2.order_commission, 0) + IFNULL(CSTATS2.cancel_commission, 0)AS coupang_order1
			,IFNULL(CSTATS13.click_num, 0) + IFNULL(CSTATS14.click_num, 0) AS coupang_click_num2
			,IFNULL(CSTATS3.order_commission, 0) + IFNULL(CSTATS3.cancel_commission, 0) + IFNULL(CSTATS4.order_commission, 0) + IFNULL(CSTATS4.cancel_commission, 0)AS coupang_order2
		";
	break;
}

$sql.="
		,IFNULL(CDS7.eprs_num, 0) AS offerwall_participation
		,IFNULL(CDS7.click_num, 0) AS offerwall_click_num
		,IFNULL(CDS7.exhs_amt, 0) AS offerwall_exhs_amt

	FROM ckd_day_app_stats CDMS
	LEFT JOIN ckd_day_ad_stats CDS1 ON CDS1.stats_dttm = CDMS.stats_dttm AND CDS1.service_tp_code='01'
	LEFT JOIN ckd_day_ad_stats CDS2 ON CDS2.stats_dttm = CDMS.stats_dttm AND CDS2.service_tp_code='02'
	LEFT JOIN ckd_day_ad_stats CDS3 ON CDS3.stats_dttm = CDMS.stats_dttm AND CDS3.service_tp_code='03'
	LEFT JOIN ckd_day_ad_stats CDS4 ON CDS4.stats_dttm = CDMS.stats_dttm AND CDS4.service_tp_code='04'

	LEFT JOIN ckd_day_ad_stats CDS7 ON CDS7.stats_dttm = CDMS.stats_dttm AND CDS7.service_tp_code='07'

	LEFT JOIN ckd_day_ad_stats CDS11 ON CDS11.stats_dttm = CDMS.stats_dttm AND CDS11.service_tp_code='11'
	LEFT JOIN ckd_day_ad_stats CDS12 ON CDS12.stats_dttm = CDMS.stats_dttm AND CDS12.service_tp_code='12'
	LEFT JOIN ckd_day_ad_stats CDS13 ON CDS13.stats_dttm = CDMS.stats_dttm AND CDS13.service_tp_code='13'
	LEFT JOIN ckd_day_ad_stats CDS14 ON CDS14.stats_dttm = CDMS.stats_dttm AND CDS14.service_tp_code='14'

	LEFT JOIN ckd_day_coupang_stats CSTATS1 ON CSTATS1.stats_dttm = CDMS.stats_dttm AND CSTATS1.service_tp_code='01'
	LEFT JOIN ckd_day_coupang_stats CSTATS2 ON CSTATS2.stats_dttm = CDMS.stats_dttm AND CSTATS2.service_tp_code='02'
	LEFT JOIN ckd_day_coupang_stats CSTATS3 ON CSTATS3.stats_dttm = CDMS.stats_dttm AND CSTATS3.service_tp_code='03'
	LEFT JOIN ckd_day_coupang_stats CSTATS4 ON CSTATS4.stats_dttm = CDMS.stats_dttm AND CSTATS4.service_tp_code='04'

	LEFT JOIN ckd_day_coupang_stats CSTATS11 ON CSTATS11.stats_dttm = CDMS.stats_dttm AND CSTATS11.service_tp_code='11'
	LEFT JOIN ckd_day_coupang_stats CSTATS12 ON CSTATS12.stats_dttm = CDMS.stats_dttm AND CSTATS12.service_tp_code='12'
	LEFT JOIN ckd_day_coupang_stats CSTATS13 ON CSTATS13.stats_dttm = CDMS.stats_dttm AND CSTATS13.service_tp_code='13'
	LEFT JOIN ckd_day_coupang_stats CSTATS14 ON CSTATS14.stats_dttm = CDMS.stats_dttm AND CSTATS14.service_tp_code='14'

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
$make_array3 = array("news_eprs_num","news_click_num","news_exhs_amt","noti_click_num","mobfeed_noti","reward_mobon_eprs_num","reward_mobon_click_num","reward_mobon","reward_mobon_ori");
$make_array4 = array("reward_coupang_click_num","reward_coupang_income","reward_coupang_ori");
$make_array5 = array("reward_news_eprs_num","reward_news_click_num","reward_news_income","mobimixer_eprs_num","mobimixer_click_num","mobimixer_income","criteo_eprs_num","criteo_click_num","criteo_income","offerwall_participation","offerwall_click_num","offerwall_exhs_amt");
$make_array6 = array("mw_eprs1","mw_click1","mw_exhs1","mw_eprs2","mw_click2","mw_exhs2","offerwall_exhs_amt_ori","mw_eprs_sdk","mw_click_sdk","mw_exhs_sdk","mw_ctr_sdk");

$make_array = array_merge ($make_array1, $make_array3, $make_array4, $make_array5, $make_array6);
foreach($make_array as $item){
	$TOTAL[$item] = 0;
}
$total_sales_all = 0;
$total_settlement_all = 0;
$total_profit_all = 0;
foreach($ret as $key => $row){

	$commission = 1;

	#하나페이 계정 12월부터 0.8 적용
	if($admin['id'] == "hanapay"){
		$commission ="0.8";
	}

	//모비위드
	$mw_commission = $commission;

	$row['mw_exhs1'] = round($row['mw_exhs1']*$mw_commission);
	$row['mw_exhs2'] = round($row['mw_exhs2']*$mw_commission);

	$offerwall_commission = $commission;
	$offerwall_exhs_amt=$row['offerwall_exhs_amt'];
	$row['offerwall_exhs_amt']=round($row['offerwall_exhs_amt'] * $offerwall_commission);

	$commission = 0.8;

	//합계
	$TOTAL['mw_click1'] += $row['mw_click1'];
	$TOTAL['mw_exhs1'] += $row['mw_exhs1'];

	$TOTAL['mw_click2'] += $row['mw_click2'];
	$TOTAL['mw_exhs2'] += $row['mw_exhs2'];

	$TOTAL['offerwall_participation'] += $row['offerwall_participation'];
	$TOTAL['offerwall_click_num'] += $row['offerwall_click_num'];
	$TOTAL['offerwall_exhs_amt'] += $row['offerwall_exhs_amt'];
	$TOTAL['offerwall_exhs_amt_ori'] += $offerwall_exhs_amt;

	if($type=="3M" || $type=="6M"){
		//합계
		$month = substr($row['stats_dttm'],0,6);
		if(!isset($M_TOTAL[$month])){
			$make_array = array_merge ($make_array1, $make_array3, $make_array4, $make_array5, $make_array6);
			foreach($make_array as $item){
				$M_TOTAL[$month][$item] = 0;
			}
		}

		$M_TOTAL[$month]['mw_click1'] += $row['mw_click1'];
		$M_TOTAL[$month]['mw_exhs1'] += $row['mw_exhs1'];

		$M_TOTAL[$month]['mw_click2'] += $row['mw_click2'];
		$M_TOTAL[$month]['mw_exhs2'] += $row['mw_exhs2'];

		$M_TOTAL[$month]['offerwall_participation'] += $row['offerwall_participation'];
		$M_TOTAL[$month]['offerwall_click_num'] += $row['offerwall_click_num'];
		$M_TOTAL[$month]['offerwall_exhs_amt'] += $row['offerwall_exhs_amt'];
		$M_TOTAL[$month]['offerwall_exhs_amt_ori'] += $offerwall_exhs_amt;

	}
	$total_sales = round($row['mw_exhs1']+$row['mw_exhs2']+$row['offerwall_exhs_amt']);
	$total_settlement = round($total_sales * $commission);
	$total_profit = round($total_sales * (1-$commission));

	//총수익
	$total_sales_all += round($total_sales);
	$total_settlement_all += round($total_sales * $commission);
	$total_profit_all += round($total_sales * (1-$commission));

	$use_cnt = empty($row['use_cnt'])?0:number_format($row['use_cnt']);

	$html .= "<tr class='{$fn->dateColor($row['stats_dttm'])}'>";
	$html .= "<td>{$row['stats_dttm']}</td>";

		$html .= "<!-- 총 매출 --><td style='color:blue;font-weight:700;'>".number_format($total_sales)."</td>";
	if($_SESSION['Adm']['id'] == "mango"){
		$html .= "<!-- 총 정산금 --><td style='color:blue;font-weight:700;'>".number_format($total_settlement)."</td>";
		$html .= "<!-- 총 수익 --><td style='color:blue;font-weight:700;'>".number_format($total_profit)."</td>";
	}
	if($os_type != 2){
		$html .= "<!-- 일반광고(AOS) -->
			<td class='show_banner_aos'>".number_format($row['mw_click1'])."</td>
			<td class='show_banner_aos' style='color:blue;font-weight:700;'>".number_format($row['mw_exhs1'])."</td>
		";
	}
	if($os_type != 1){
		$html .= "<!-- 일반광고(iOS) -->
			<td class='show_banner_ios'>".number_format($row['mw_click2'])."</td>
			<td class='show_banner_ios' style='color:blue;font-weight:700;'>".number_format($row['mw_exhs2'])."</td>
		";
	}

		$html .= "
		<!-- 오퍼월 -->
		<td class='show_offerwall'>".number_format($row['offerwall_participation'])."</td>
		<td class='show_offerwall' style='color:blue;font-weight:700;'>".number_format($row['offerwall_exhs_amt'])."</td>

	</tr>
	";
}//foreach

if(empty($html)){
	$html="<tr><td colspan='31'>데이터가 없습니다.</td></tr>";
}


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
									<button type="button" class="btn btn-danger" style="margin-left:10px;">모비위드 API & 정산 갱신(새창)</button>
								</div>
								<?php } ?>
							</div>

							<div class="row" style="margin-top: 10px;margin-bottom: 10px;margin-left: 0;">
								<div class="pull-left"><input style="width:20px;height:20px;accent-color: darkcyan;" type="radio" name="os_type" value="0" <?=empty($os_type)?"checked":""?>></div>
								<div class="pull-left" style="padding-top:5px;padding-left:3px;">전체</div>
								<div class="pull-left" style="margin-left: 10px;" ><input style="width:20px;height:20px;accent-color: darkcyan;" type="radio" name="os_type" value="1" <?=($os_type=="1")?"checked":""?>></div>
								<div class="pull-left" style="padding-top:5px;padding-left:3px;">Android</div>
								<div class="pull-left" style="margin-left: 10px;"><input style="width:20px;height:20px;accent-color: darkcyan;" type="radio" name="os_type" value="2" <?=($os_type=="2")?"checked":""?>></div>
								<div class="pull-left" style="padding-top:5px;padding-left:3px;">iOS <span style="margin-left: 50px;">*오퍼월은 운영체제를 구분하지 않음</span></div>

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
							<th rowspan="3">날짜</th>
							<th rowspan="2" colspan="<?=($_SESSION['Adm']['id'] == "mango")?3:1;?>">매출통계</th>
							<th colspan="<?=empty($os_type)?"4":"2"?>">광고 SDK 성과</th>
							<th colspan="2">오퍼월 광고 성과</th>
						</tr>
						<tr>
							<th colspan="2" class="show_banner_aos">AOS
								<a href="#" class="tooltips"><i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
									<span>
										<strong>모비위드 연동</strong><br />
										(2일전 데이터 매시갱신)
									</span>
								</a>
							</th>
							<th colspan="2" class="show_banner_ios">IOS
								<a href="#" class="tooltips"><i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
									<span>
										<strong>모비위드 연동</strong><br />
										(2일전 데이터 매시갱신)
									</span>
								</a>
							</th>

							<th colspan="2" class="show_offerwall">OS 통합</th>
						</tr>
						<tr>
							<th style="color:blue;">총 매출</th>
							<?php if($_SESSION['Adm']['id'] == "mango"){ ?>
							<th style="color:blue;">매체 정산금</th>
							<th style="color:blue;">총 이익</th>
							<?php } ?>

							<th class="show_banner_aos">클릭수</th>
							<th class="show_banner_aos" style="color:blue;">매출</th>

							<th class="show_banner_ios">클릭수</th>
							<th class="show_banner_ios" style="color:blue;">매출</th>

							<th class="show_offerwall">참여수</th>
							<th class="show_offerwall" style="color:blue;">매출</th>
						</tr>
						</thead>
						<tbody id="ocb" >
						<tr class="" style="background-color: #F2F5A9;">
							<td>합계</td>
							<td style="color:blue;font-weight:700;"><?=number_format($total_sales_all)?></td>
							<?php if($_SESSION['Adm']['id'] == "mango"){ ?>
							<td style="color:blue;font-weight:700;"><?=number_format($total_settlement_all)?></td>
							<td style="color:blue;font-weight:700;"><?=number_format($total_profit_all)?></td>
							<?php }?>

							<?php if($os_type != 2){ ?>
							<!-- 일반 광고(AOS) -->
							<td class="show_banner_aos"><?=number_format($TOTAL['mw_click1'])?></td>
							<td class="show_banner_aos" style="color:blue;font-weight:700;"><?=number_format($TOTAL['mw_exhs1'])?></td>
							<?php } ?>

							<?php if($os_type != 1){ ?>
							<!-- 일반 광고(iOS)  -->
							<td class="show_banner_ios"><?=number_format($TOTAL['mw_click2'])?></td>
							<td class="show_banner_ios" style="color:blue;font-weight:700;"><?=number_format($TOTAL['mw_exhs2'])?></td>
							<?php } ?>

							<!-- 오퍼월 -->
							<td class="show_offerwall"><?=number_format($TOTAL['offerwall_participation'])?></td>
							<td class="show_offerwall" style="color:blue;font-weight:700;"><?=number_format($TOTAL['offerwall_exhs_amt'])?></td>

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

							$month_total_sales = $row['mw_exhs1']+$row['mw_exhs2']+$row['coupang_income1']+$row['coupang_income2']+$row['offerwall_exhs_amt'];
							$month_total_settlement = round($month_total_sales * $commission);
							$month_total_profit = round($month_total_sales * (1*$commission));

							?>
						<tr class="" style="background-color: #afe076;">
							<td><?=$key?></td>
							<td style="color:blue;font-weight:700;"><?=number_format($month_total_sales)?></td>
							<?php if($_SESSION['Adm']['id'] == "mango"){ ?>
							<td style="color:blue;font-weight:700;"><?=number_format($month_total_settlement)?></td>
							<td style="color:blue;font-weight:700;"><?=number_format($month_total_profit)?></td>
							<?php }?>
							<?php if($os_type != 2){ ?>
							<!-- 일반 광고(AOS) -->
							<td  class="show_banner_aos"><?=number_format($row['mw_click1'])?></td>
							<td  class="show_banner_aos" style="color:blue;font-weight:700;"><?=number_format($row['mw_exhs1'])?></td>
							<?php } ?>
							<?php if($os_type != 1){ ?>
							<!-- 일반 광고(iOS)  -->
							<td  class="show_banner_ios"><?=number_format($row['mw_click2'])?></td>
							<td  class="show_banner_ios" style="color:blue;font-weight:700;"><?=number_format($row['mw_exhs2'])?></td>
							<?php } ?>

							<!-- 오퍼월 -->
							<td class="show_offerwall"><?=number_format($row['offerwall_participation'])?></td>
							<td class="show_offerwall" style="color:blue;font-weight:700;"><?=number_format($row['offerwall_exhs_amt'])?></td>
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
				<tr>";
		tab_text = tab_text + "<th rowspan='3'>날짜</th>";
		tab_text = tab_text + "<th rowspan='2' colspan='<?=($_SESSION['Adm']['id'] == "mango")?3:1;?>'>매출통계</th>";
		tab_text = tab_text + "<th colspan='<?=empty($os_type)?"4":"2"?>'>광고 SDK 성과</th>";
		tab_text = tab_text + "<th colspan='2'>오퍼월 광고 성과</th>";
		tab_text = tab_text + "</tr>";

		tab_text = tab_text + "<tr>";
		<?php if($os_type == 1){ ?>
		tab_text = tab_text + "<th colspan='2' class='show_banner_aos'>일반 광고 (AOS)</th>";
		<?php }else if($os_type == 2){ ?>
		tab_text = tab_text + "<th colspan='2' class='show_banner_ios'>일반 광고 (IOS)</th>";
		<?php }else{ ?>
		tab_text = tab_text + "<th colspan='2' class='show_banner_aos'>일반 광고 (AOS)</th>";
		tab_text = tab_text + "<th colspan='2' class='show_banner_ios'>일반 광고 (IOS)</th>";
		<?php } ?>
		tab_text = tab_text + "<th colspan='2' class='show_offerwall'>포미션 연동 (OS공통)</th>";
		tab_text = tab_text + "</tr>";
		tab_text = tab_text + "<tr>";
		tab_text = tab_text + "<th style='color:blue;'>총 매출</th>";
		<?php if($_SESSION['Adm']['id'] == "mango"){ ?>
		tab_text = tab_text + "<th style='color:blue;'>매체 정산금</th>";
		tab_text = tab_text + "<th style='color:blue;'>총 이익</th>";
		<?php } ?>

		<?php if($os_type == 1){ ?>
		tab_text = tab_text + "<th class='show_banner_aos'>클릭수</th>";
		tab_text = tab_text + "<th class='show_banner_aos' style='color:blue;'>매출</th>";
		<?php }else if($os_type == 2){ ?>
		tab_text = tab_text + "<th class='show_banner_aos'>클릭수</th>";
		tab_text = tab_text + "<th class='show_banner_aos' style='color:blue;'>매출</th>";
		<?php }else{ ?>
		tab_text = tab_text + "<th class='show_banner_aos'>클릭수</th>";
		tab_text = tab_text + "<th class='show_banner_aos' style='color:blue;'>매출</th>";
		tab_text = tab_text + "<th class='show_banner_ios'>클릭수</th>";
		tab_text = tab_text + "<th class='show_banner_ios' style='color:blue;'>매출</th>";

		<?php } ?>
		tab_text = tab_text + "<th class='show_offerwall'>참여수</th>";
		tab_text = tab_text + "<th class='show_offerwall' style='color:blue;'>매출</th>";
		tab_text = tab_text + "</tr>";

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
	fnExcelReport('ocb','하나페이 통계');
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

$(".show_banner_aos").hide();
$(".show_banner_ios").hide();
$(".show_coupang_aos").hide();
$(".show_coupang_ios").hide();
$(".show_offerwall").hide();

<?php if($os_type == 1){ ?>
	$(".show_banner_aos").show();
	$(".show_coupang_aos").show();
	$(".show_offerwall").show();
<?php }else if($os_type == 2){ ?>
	$(".show_banner_ios").show();
	$(".show_coupang_ios").show();
	$(".show_offerwall").show();
<?php }else{ ?>
	$(".show_banner_aos").show();
	$(".show_coupang_aos").show();
	$(".show_banner_ios").show();
	$(".show_coupang_ios").show();
	$(".show_offerwall").show();
<?php } ?>

	//$("section").css({"min-width":"2200px"});
</script>

<?php
include __foot__;
?>
