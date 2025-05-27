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
define('_title_', '키보드 사용 통계(통합)');
define('_Menu_', 'manage');
define('_subMenu_', 'use_new');

include_once __head__;

$ip = $fn->getRealClientIp();
$allow_ip = array('127.0.0.1','221.150.126.74','112.220.254.82');

//if(in_array($ip, $allow_ip) && $_SESSION['Adm']['id'] == "mango"){
//	$hidden_page = "show";
//}else{
//	$hidden_page = "hide";
//}

if($_SESSION['Adm']['id'] == "happy"){
	$hidden_page = "show";
}else{
	$hidden_page = "hide";
}

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
$check_date = "2025-02-07";
if ($check_date > $startDate) {
    $news_show = true;
} else {
    $news_show = false;
}

// 통계 데이터
$sql="
	SELECT
		CDMS.stats_dttm
		,SUM(activity_num) AS activity_num
		,SUM(use_cnt) AS use_cnt
		,SUM(use_time) AS use_time
		,SUM(use_tot_cnt) AS use_tot_cnt

		,IFNULL(CDS1.eprs_num, 0) AS mw_eprs_main
		,IFNULL(CDS1.click_num, 0) AS mw_click_main
		,IFNULL(CDS1.exhs_amt, 0) AS mw_exhs_main

		,IFNULL(CDS2.eprs_num, 0) AS mw_eprs_set
		,IFNULL(CDS2.click_num, 0) AS mw_click_set
		,IFNULL(CDS2.exhs_amt, 0) AS mw_exhs_set

		,IFNULL(CDS5.eprs_num, 0)  AS news_eprs_num
		,IFNULL(CDS5.click_num , 0)AS news_click_num
		,IFNULL(CDS5.exhs_amt, 0) AS news_exhs_amt

		,IFNULL(CDS6.eprs_num, 0) AS offerwall_participation
		,IFNULL(CDS6.click_num, 0) AS offerwall_click_num
		,IFNULL(CDS6.exhs_amt, 0) AS offerwall_exhs_amt

		,IFNULL(CSTATS1.click_num, 0) AS brand_click_num
		,IFNULL(CSTATS1.order_commission, 0) AS brand_order
		,IFNULL(CSTATS1.cancel_commission, 0) AS brand_cancel

	FROM ckd_day_app_stats CDMS

	LEFT JOIN ckd_day_ad_stats CDS1 ON CDS1.stats_dttm = CDMS.stats_dttm AND CDS1.service_tp_code='04'
	LEFT JOIN ckd_day_ad_stats CDS2 ON CDS2.stats_dttm = CDMS.stats_dttm AND CDS2.service_tp_code='05'

	LEFT JOIN ckd_day_ad_stats CDS5 ON CDS5.stats_dttm = CDMS.stats_dttm AND CDS5.service_tp_code='08'
	LEFT JOIN ckd_day_ad_stats CDS6 ON CDS6.stats_dttm = CDMS.stats_dttm AND CDS6.service_tp_code='09'

	LEFT JOIN ckd_day_coupang_stats CSTATS1 ON CSTATS1.stats_dttm = CDMS.stats_dttm AND CSTATS1.service_tp_code='01'

	WHERE CDMS.stats_dttm BETWEEN {$sdate} AND {$edate}
	GROUP BY CDMS.stats_dttm
	ORDER BY CDMS.stats_dttm DESC
";
//pre($sql);
$ret = $NDO->fetch_array($sql);

//전체 누적 설정수
$sql="
	SELECT stats_dttm,activity_num FROM ckd_day_app_stats
	WHERE stats_dttm >= '20231226' AND stats_dttm <= '{$edate}'
	ORDER BY stats_dttm ASC
";
//pre($sql);
$ret2 = $NDO->fetch_array($sql);
$accumulate = 0;
foreach($ret2 as $key => $row){
	$accumulate+=$row['activity_num'];
	$accumulate_array[$row['stats_dttm']] = $accumulate;
}
//pre($accumulate_array);

function date_color_code($day){
	$yoil = array("#fff3f3","","","","","","#f1fcff");
	return ($yoil[date('w', strtotime($day))]);
}

$html='';
$TOTAL = [];
$M_TOTAL = [];
$make_array1 = array("accumulate","activity_num","use_time","accumulate","activity_num","use_time","use_cnt","use_tot_cnt","useTotCntAvg","ctr");
$make_array2 = array("brand_click_num","brand_income");
$make_array3 = array("news_eprs_num","news_click_num","news_exhs_amt","noti_click_num","mobfeed_noti","reward_mobon_eprs_num","reward_mobon_click_num","reward_mobon","reward_mobon_ori");
$make_array4 = array("reward_coupang_click_num","reward_coupang_income","reward_coupang_ori");
$make_array5 = array("reward_news_eprs_num","reward_news_click_num","reward_news_income","mobimixer_eprs_num","mobimixer_click_num","mobimixer_income","criteo_eprs_num","criteo_click_num","criteo_income","offerwall_participation","offerwall_click_num","offerwall_exhs_amt");
$make_array6 = array("mw_eprs_main","mw_click_main","mw_exhs_main","mw_ctr_main","mw_eprs_set","mw_click_set","mw_exhs_set","mw_ctr_set","mw_exhs_main_ori","mw_exhs_set_ori","offerwall_exhs_amt_ori","brand_income_ori","news_exhs_amt_ori");

$make_array = array_merge ($make_array1, $make_array2, $make_array3, $make_array4, $make_array5, $make_array6);
foreach($make_array as $item){
	$TOTAL[$item] = 0;
}

foreach($ret as $key => $row){

	//평균 사용 시간
	$useTimeAvg = ($row['use_time'] > 0) ? round($row['use_time'] / $row['use_tot_cnt']) : 0;
	//평균 사용횟수
	$useTotCntAvg = ($row['use_cnt'] > 0) ? round($row['use_tot_cnt'] / $row['use_cnt'], 1) : 0;


	# 커미션 100 으로 보여줌
	//쿠팡 수익 계산 (주문 커미션 - 취소 커미션 ) * 0.8
	//하나머니 계정에만 정산용 0.9 적용
	$commission = 1;
	if($_SESSION['Adm']['id'] == "happy"){
		$commission = 0.7;
	}
	$brand_income_ori = ($row['brand_order'] + $row['brand_cancel']);
	$brand_income = ($row['brand_order'] + $row['brand_cancel']) * $commission ;

	$offerwall_commission = 1;
	if($_SESSION['Adm']['id'] == "happy"){
		$offerwall_commission =0.7;
	}

	$offerwall_exhs_amt=($row['offerwall_exhs_amt']);
	$row['offerwall_exhs_amt']=($row['offerwall_exhs_amt'] * $offerwall_commission);

	//모비위드 CTR
	$mw_ctr_main = ($row['mw_eprs_main'] > 0) ? number_format($row['mw_click_main'] / $row['mw_eprs_main'] * 100, 1) : 0;
	$mw_ctr_set = ($row['mw_eprs_set'] > 0) ? number_format($row['mw_click_set'] / $row['mw_eprs_set'] * 100, 1) : 0;

	//모비위드
	$mw_commission = 1;
	if($_SESSION['Adm']['id'] == "happy"){
		$mw_commission =0.7;
	}
	$mw_exhs_main = ($row['mw_exhs_main']);
	$mw_exhs_set = ($row['mw_exhs_set']);
	$row['mw_exhs_main']=($row['mw_exhs_main'] * $mw_commission);
	$row['mw_exhs_set']=($row['mw_exhs_set'] * $mw_commission);

	//뉴스
	$row['news_exhs_amt_ori'] = $row['news_exhs_amt'];
	if($_SESSION['Adm']['id'] == "happy"){
		$row['news_exhs_amt'] = ($row['news_exhs_amt'] * $commission);
	}

	//합계
	$TOTAL['accumulate'] += $accumulate_array[$row['stats_dttm']];//날짜
	$TOTAL['activity_num'] += $row['activity_num'];//누적 설정수
	$TOTAL['use_time'] += $row['use_time'];//신규 설정수
	$TOTAL['use_cnt'] += $row['use_cnt'];//평균 사용자수
	$TOTAL['use_tot_cnt'] += $row['use_tot_cnt'];//평균 사용시간
	$TOTAL['useTotCntAvg'] += $useTotCntAvg; //평균사용횟수

	//모비위드 CTR
	$TOTAL['mw_ctr_main'] += $mw_ctr_main;
	$TOTAL['mw_ctr_set'] += $mw_ctr_set;

	$TOTAL['mw_eprs_main'] += $row['mw_eprs_main'];
	$TOTAL['mw_click_main'] += $row['mw_click_main'];
	$TOTAL['mw_exhs_main'] += ($row['mw_exhs_main']);
	$TOTAL['mw_exhs_main_ori'] += ($mw_exhs_main);

	$TOTAL['mw_eprs_set'] += $row['mw_eprs_set'];
	$TOTAL['mw_click_set'] += $row['mw_click_set'];
	$TOTAL['mw_exhs_set'] += ($row['mw_exhs_set']);
	$TOTAL['mw_exhs_set_ori'] += ($mw_exhs_set);

	$TOTAL['brand_click_num'] += $row['brand_click_num'];
	$TOTAL['brand_income'] += ($brand_income);
	$TOTAL['brand_income_ori'] += ($brand_income_ori);

	$TOTAL['news_eprs_num'] += $row['news_eprs_num'];
	$TOTAL['news_click_num'] += $row['news_click_num'];
	$TOTAL['news_exhs_amt'] += $row['news_exhs_amt'];
	$TOTAL['news_exhs_amt_ori'] += $row['news_exhs_amt_ori'];

	$TOTAL['offerwall_participation'] += $row['offerwall_participation'];
	$TOTAL['offerwall_click_num'] += $row['offerwall_click_num'];
	$TOTAL['offerwall_exhs_amt'] += ($row['offerwall_exhs_amt']);
	$TOTAL['offerwall_exhs_amt_ori'] += ($offerwall_exhs_amt);

	if($type=="3M" || $type=="6M"){
		//합계
		$month = substr($row['stats_dttm'],0,6);
		if(!isset($M_TOTAL[$month])){
			$make_array = array_merge ($make_array1, $make_array2, $make_array3, $make_array4, $make_array5, $make_array6);
			foreach($make_array as $item){
				$M_TOTAL[$month][$item] = 0;
			}
		}

		$M_TOTAL[$month]['accumulate'] += $accumulate_array[$row['stats_dttm']];
		$M_TOTAL[$month]['activity_num'] += $row['activity_num'];
		$M_TOTAL[$month]['use_time'] += $row['use_time'];
		$M_TOTAL[$month]['use_cnt'] += $row['use_cnt'];
		$M_TOTAL[$month]['use_tot_cnt'] += $row['use_tot_cnt'];

		$M_TOTAL[$month]['mw_eprs_main'] += $row['mw_eprs_main'];
		$M_TOTAL[$month]['mw_click_main'] += $row['mw_click_main'];
		$M_TOTAL[$month]['mw_exhs_main'] += ($row['mw_exhs_main']);
		$M_TOTAL[$month]['mw_exhs_main_ori'] += ($mw_exhs_main);

		$M_TOTAL[$month]['mw_eprs_set'] += $row['mw_eprs_set'];
		$M_TOTAL[$month]['mw_click_set'] += $row['mw_click_set'];
		$M_TOTAL[$month]['mw_exhs_set'] += ($row['mw_exhs_set']);
		$M_TOTAL[$month]['mw_exhs_set_ori'] += ($mw_exhs_set);

		$M_TOTAL[$month]['brand_click_num'] += $row['brand_click_num'];
		$M_TOTAL[$month]['brand_income'] += ($row['brand_click_num']);
		$M_TOTAL[$month]['brand_income_ori'] += ($brand_income_ori);

		$M_TOTAL[$month]['news_eprs_num'] += $row['news_eprs_num'];
		$M_TOTAL[$month]['news_click_num'] += $row['news_click_num'];
		$M_TOTAL[$month]['news_exhs_amt'] += $row['news_exhs_amt'];
		$M_TOTAL[$month]['news_exhs_amt_ori'] += $row['news_exhs_amt_ori'];

		$M_TOTAL[$month]['offerwall_participation'] += $row['offerwall_participation'];
		$M_TOTAL[$month]['offerwall_click_num'] += $row['offerwall_click_num'];
		$M_TOTAL[$month]['offerwall_exhs_amt'] += ($row['offerwall_exhs_amt']);
		$M_TOTAL[$month]['offerwall_exhs_amt_ori'] += ($offerwall_exhs_amt);

	}

	$total_amt = $row['mw_exhs_main']+$row['mw_exhs_set']+$brand_income+$row['news_exhs_amt']+$row['offerwall_exhs_amt'];
	$total_amt_ori = $mw_exhs_main+$mw_exhs_set+$brand_income_ori+$row['news_exhs_amt_ori']+$offerwall_exhs_amt;

	$use_cnt = empty($row['use_cnt'])?0:number_format($row['use_cnt']);

	$html .= "<tr class='{$fn->dateColor($row['stats_dttm'])}'>";
	$html .= "
		<td>{$row['stats_dttm']}</td>

		<td>".number_format($accumulate_array[$row['stats_dttm']])."</td>
		<td style='font-weight:700;'>".number_format($row['activity_num'])."</td>
		<td style='color:red;'>".$use_cnt."</td> 

		<!-- 모비위드 메인 -->
		<td>".number_format($row['mw_eprs_main'])."</td>
		<td>".number_format($row['mw_click_main'])."</td>
		<td>".number_format($mw_ctr_main)."%</td>
	";
	if($hidden_page == "show"){
		$html .= "<td style='color:blue; font-weight:700;'>".number_format($row['mw_exhs_main'])."</td>";
	}
	$html .= "
		<td style='color:red; font-weight:700;'>".number_format($mw_exhs_main)."</td>

		<!-- 모비위드 설정 -->
		<td>".number_format($row['mw_eprs_set'])."</td>
		<td>".number_format($row['mw_click_set'])."</td>
		<td>".number_format($mw_ctr_set)."%</td>
	";
	if($hidden_page == "show"){
		$html .= "<td style='color:blue; font-weight:700;'>".number_format($row['mw_exhs_set'])."</td>";
	}
	$html .= "
		<td style='color:red; font-weight:700;'>".number_format($mw_exhs_set)."</td>

		<!-- 브랜드 -->
		<td>".number_format($row['brand_click_num'])."</td>
	";
	if($hidden_page == "show"){
		$html .= "<td style='color:blue; font-weight:700;'>".number_format($brand_income)."</td>";
	}
	$html .= "<td style='color:red; font-weight:700;'>".number_format($brand_income_ori)."</td>";
    if($news_show) {
        $html .= "
            <!-- 뉴스 -->
            <td>" . number_format($row['news_click_num']) . "</td>";
        if ($hidden_page == "show") {
            $html .= "<td style='color:blue; font-weight:700;'>" . number_format($row['news_exhs_amt']) . "</td>";
        }
        $html .= "<td style='color:red; font-weight:700;'>" . number_format($row['news_exhs_amt_ori']) . "</td>";
    }
    $html .= "
		<!-- 오퍼월 -->
		<td>".number_format($row['offerwall_click_num'])."</td>
		<td>".number_format($row['offerwall_participation'])."</td>
	";
	if($hidden_page == "show"){
		$html .= "<td style='color:blue; font-weight:700;'>".number_format($row['offerwall_exhs_amt'])."</td>";
	}
	$html .= "
		<td style='color:red; font-weight:700;'>".number_format($offerwall_exhs_amt)."</td>
		<!-- 총 매출(원) -->
	";
	if($_SESSION['Adm']['id'] == "happy"){
		$html .= "
			<td style='color:blue; font-weight:700;'>".number_format($total_amt)."</td>
			<td style='color:red; font-weight:700;'>".number_format($total_amt_ori)."</td>
		";
	}else{
		$html .= "
			<td style='color:red; font-weight:700;'>".number_format($total_amt)."</td>
			<td style='color:blue; font-weight:700;'>".number_format($total_amt*0.7)."</td>
			<td style='color:red; font-weight:700;'>".number_format($total_amt*0.3)."</td>
		";
	}
	$html .= "
		</tr>
	";

}//foreach

if(empty($html)){
	$html="<tr><td colspan='31'>데이터가 없습니다.</td></tr>";
}

//평균 사용자 수
if(!empty($accumulate_array)){
	$date1 = new DateTime(array_keys($accumulate_array)[0]);
	$date2 = new DateTime($endDate);
	$interval = $date1->diff($date2);
	$diff_data = (int)$interval->format('%a');

	//합계 평균 사용자 수
	$TOTAL['use_cnt'] = $TOTAL['use_cnt'] / ($diff_data+1);

	//합계 평균 사용시간
	$TOTAL['useAvg'] = ($TOTAL['use_tot_cnt'] > 0) ? round($TOTAL['use_time'] / $TOTAL['use_tot_cnt'], 1) : 0;

	//합계 평균 사용횟수
	$TOTAL['totCntAvg'] = ($diff_data > 0) ? number_format($TOTAL['useTotCntAvg'] / $diff_data , 1) : 0;

}

//클릭율
$TOTAL['mw_ctr_main'] = ($TOTAL['mw_eprs_main'] > 0) ? number_format($TOTAL['mw_click_main'] / $TOTAL['mw_eprs_main'] * 100, 1) : 0;
$TOTAL['mw_ctr_set'] = ($TOTAL['mw_eprs_set'] > 0) ? number_format($TOTAL['mw_click_set'] / $TOTAL['mw_eprs_set'] * 100, 1) : 0;

$reward_eprs_num = $TOTAL['reward_mobon_eprs_num']+$TOTAL['reward_news_eprs_num'];
$reward_click_num = $TOTAL['reward_mobon_click_num']+$TOTAL['reward_coupang_click_num']+$TOTAL['reward_news_click_num'];
$reward_income = $TOTAL['reward_mobon']+$TOTAL['reward_coupang_ori']+$TOTAL['reward_news_income'];

//총수익
$total_amt_all = number_format($TOTAL['mw_exhs_main']+$TOTAL['mw_exhs_set']+$TOTAL['brand_income']+$TOTAL['news_exhs_amt']+$TOTAL['offerwall_exhs_amt']);
$total_amt_all_ori = $TOTAL['mw_exhs_main_ori']+$TOTAL['mw_exhs_set_ori']+$TOTAL['brand_income']+$TOTAL['news_exhs_amt_ori']+$TOTAL['offerwall_exhs_amt_ori'];
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
		box-shadow: 0 0 8px 4px #666;
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
			<h4 class="panel-title">키보드 사용 통계</h4>
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
							<th colspan="3">앱 연동</th>
							<th colspan="<?=$hidden_page=="show"?"5":"4"?>">메인띠배너
								<a href="#" class="tooltips"><i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
									<span>
										<strong>메인띠배너 연동</strong><br />
										(2일전 데이터 매시갱신)
									</span>
								</a>
							</th>
							<th colspan="<?=$hidden_page=="show"?"5":"4"?>">설정띠배너
								<a href="#" class="tooltips"><i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
									<span>
										<strong>설정띠배너 연동</strong><br />
										(2일전 데이터 매시갱신)
									</span>
								</a>
							</th>

							<th colspan="<?=$hidden_page=="show"?"3":"2"?>">쿠팡 연동
								<a href="#" class="tooltips"><i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
									<span>
										<strong>브랜드 광고</strong><br />
										쿠팡 데이터 (매일 오후04:50분 전일 데이터 갱신)
									</span>
								</a>
							</th>
                            <?php if($news_show) { ?>
							<th colspan="<?=$hidden_page=="show"?"3":"2"?>">모비피드 연동
								<a href="#" class="tooltips"><i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
									<span>
										<strong>모비피드 연동</strong><br />
										API 스케쥴링 (매일 13시 26분 갱신)
									</span>
								</a>
							</th>
                            <?php } ?>
							<th colspan="<?=$hidden_page == "show"?"4":"3"?>">포미션 연동</th>

							<?php if($_SESSION['Adm']['id'] == "happy"){ ?>
								<th rowspan="2" style="color:blue;">총 매체수익(원)</th>
								<th rowspan="2" style="color:red;">총 원매출(원)</th>
							<?php }else{ ?>
								<th rowspan="2" style="color:red;">총 매출(원)</th>
								<th rowspan="2" style="color:blue;">총 수수료(원)</th>
								<th rowspan="2" style="color:red;">영업이익(원)</th>
							<?php } ?>

						</tr>
						<tr>
							<th>누적 설정수</th>
							<th>신규 설정수
								<a href="#" class="tooltips"><i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
									<span>
									<strong>신규 설정수</strong><br />
									일일 신규 사용자수
									</span>
								</a>
							</th>
							<th style="color:red;">평균 사용자수
								<a href="#" class="tooltips"><i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
									<span>
										<strong>사용자수</strong><br />
										일일 접속한 사용자수
									</span>
								</a>
							</th>

							<th>노출</th>
							<th>클릭</th>
							<th>클릭율(%)</th>
							<?php if($hidden_page == "show"){?>
							<th style="color:blue;">수익(원)</th>
							<?php }?>
							<th style="color:red;"><?=($_SESSION['Adm']['id'] == "happy")?"원매출(원)":"매출(원)"?></th>

							<th>노출</th>
							<th>클릭</th>
							<th>클릭율(%)</th>
							<?php if($hidden_page == "show"){?>
							<th style="color:blue;">수익(원)</th>
							<?php }?>
							<th style="color:red;"><?=($_SESSION['Adm']['id'] == "happy")?"원매출(원)":"매출(원)"?></th>

							<th>클릭수</th>
							<?php if($hidden_page == "show"){?>
							<th style="color:blue;">수익(원)</th>
							<?php }?>
							<th style="color:red;"><?=($_SESSION['Adm']['id'] == "happy")?"원매출(원)":"매출(원)"?></th>

                            <?php if($news_show) { ?>
							<th>클릭수</th>
							<?php if($hidden_page == "show"){?>
							<th style="color:blue;">수익(원)</th>
							<?php }?>
							<th style="color:red;"><?=($_SESSION['Adm']['id'] == "happy")?"원매출(원)":"매출(원)"?></th>
                            <?php } ?>

							<th>클릭수</th>
							<th>참여수</th>
							<?php if($hidden_page == "show"){?>
							<th style="color:blue;">수익(원)</th>
							<?php }?>
							<th style="color:red;"><?=($_SESSION['Adm']['id'] == "happy")?"원매출(원)":"매출(원)"?></th>

						</tr>
						</thead>
						<tbody id="ocb" >
						<tr class="" style="background-color: #F2F5A9;">
							<td>합계</td>
							<td></td>
							<td style='font-weight:700;'><?=number_format($TOTAL['activity_num'])?></td>
							<td style="color:red;"><?=number_format($TOTAL['use_cnt'])?></td>

							<!-- 메인띠베너 -->
							<td><?=number_format($TOTAL['mw_eprs_main'])?></td>
							<td><?=number_format($TOTAL['mw_click_main'])?></td>
							<td><?=number_format($TOTAL['mw_ctr_main'])?>%</td>
							<?php if($hidden_page == "show"){?>
							<td style="color:blue;font-weight:700;"><?=number_format($TOTAL['mw_exhs_main'])?></td>
							<?php }?>
							<td style="color:red;font-weight:700;"><?=number_format($TOTAL['mw_exhs_main_ori'])?></td>

							<!-- 설정띠베너 -->
							<td><?=number_format($TOTAL['mw_eprs_set'])?></td>
							<td><?=number_format($TOTAL['mw_click_set'])?></td>
							<td><?=number_format($TOTAL['mw_ctr_set'])?>%</td>
							<?php if($hidden_page == "show"){?>
							<td style="color:blue;font-weight:700;"><?=number_format($TOTAL['mw_exhs_set'])?></td>
							<?php }?>
							<td style="color:red;font-weight:700;"><?=number_format($TOTAL['mw_exhs_set_ori'])?></td>

							<!-- 브랜드 -->
							<td><?=number_format($TOTAL['brand_click_num'])?></td>
							<?php if($hidden_page == "show"){?>
							<td style="color:blue;font-weight:700;"><?=number_format($TOTAL['brand_income'])?></td>
							<?php }?>
							<td style="color:red;font-weight:700;"><?=number_format($TOTAL['brand_income_ori'])?></td>

                            <?php if($news_show) { ?>
							<!-- 뉴스 -->
							<td><?=number_format($TOTAL['news_click_num'])?></td>
							<?php if($hidden_page == "show"){?>
							<td style="color:blue;font-weight:700;"><?=number_format($TOTAL['news_exhs_amt'])?></td>
							<?php }?>
							<td style="color:red;font-weight:700;"><?=number_format($TOTAL['news_exhs_amt_ori'])?></td>
                            <?php } ?>

							<!-- 오퍼월 -->
							<td><?=number_format($TOTAL['offerwall_click_num'])?></td>
							<td><?=number_format($TOTAL['offerwall_participation'])?></td>
							<?php if($hidden_page == "show"){?>
							<td style="color:blue;font-weight:700;"><?=number_format($TOTAL['offerwall_exhs_amt'])?></td>
							<?php } ?>
							<td style="color:red;font-weight:700;"><?=number_format($TOTAL['offerwall_exhs_amt_ori'])?></td>

							<?php if($_SESSION['Adm']['id'] == "happy"){ ?>
								<td style="color:blue;font-weight:700;"><?=$total_amt_all?></td>
								<td style="color:red;font-weight:700;"><?=number_format($total_amt_all_ori)?></td>

							<?php }else{ ?>
								<td style="color:red;font-weight:700;"><?=number_format($total_amt_all_ori)?></td>
								<td style="color:blue;font-weight:700;"><?=number_format($total_amt_all_ori*0.7)?></td>
								<td style="color:red;font-weight:700;"><?=number_format($total_amt_all_ori*0.3)?></td>
							<?php } ?>

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

							// 쿠팡 클릭율
							//$TOTAL['cclick_rate'] = ($TOTAL['eprs_num'] > 0) ? ($TOTAL['cclick_num'] / $TOTAL['eprs_num']) * 100 : 0;

							//전체 사용율
							$row['totuseAvg'] = ($row['accumulate'] > 0) ? $row['use_cnt'] / $row['accumulate'] * 100 : 0;

							//합계 평균 사용시간
							$row['useAvg'] = ($row['use_tot_cnt'] > 0) ? round($row['use_time'] / $row['use_tot_cnt'], 1) : 0;

							//합계 평균 사용횟수
							$row['totCntAvg'] = ($row['use_cnt'] > 0) ? round($row['use_tot_cnt'] / $row['use_cnt'], 1) : 0;

							$reward_eprs_num = $row['reward_mobon_eprs_num']+$row['reward_news_eprs_num'];
							$reward_click_num = $row['reward_mobon_click_num']+$row['reward_coupang_click_num']+$row['reward_news_click_num'];
							$reward_income = $row['reward_mobon']+$row['reward_coupang_ori']+$row['reward_news_income'];

							//모비위드 클릭율
							$row['mw_ctr_main'] = ($row['mw_eprs_main'] > 0) ? number_format($row['mw_click_main'] / $row['mw_eprs_main'] * 100 , 1) : 0;
							$row['mw_ctr_set'] = ($row['mw_eprs_set'] > 0) ? number_format($row['mw_click_set'] / $row['mw_eprs_set'] * 100 , 1) : 0;

							$month_total = $row['mw_exhs_main']+$row['mw_exhs_set']+$row['brand_income']+$row['news_exhs_amt']+$row['offerwall_exhs_amt'];
							$month_total_ori = $row['mw_exhs_main_ori']+$row['mw_exhs_set_ori']+$row['brand_income_ori']+$row['news_exhs_amt_ori']+$row['offerwall_exhs_amt_ori'];
							?>
						<tr class="" style="background-color: #afe076;">
							<td><?=$key?></td>
							<td></td>
							<td style='font-weight:700;'><?=number_format($row['activity_num'])?></td>
							<td style="color:red;"><?=number_format($row['use_cnt']/$this_month_day_cnt)?></td>

							<!-- 메인띠베너 -->
							<td><?=number_format($row['mw_eprs_main'])?></td>
							<td><?=number_format($row['mw_click_main'])?></td>
							<td><?=number_format($row['mw_ctr_main'])?>%</td>
							<?php if($hidden_page == "show"){?>
							<td style="color:blue;font-weight:700;"><?=number_format($row['mw_exhs_main'])?></td>
							<?php } ?>
							<td style="color:red;font-weight:700;"><?=number_format($row['mw_exhs_main_ori'])?></td>

							<!-- 설정띠베너 -->
							<td><?=number_format($row['mw_eprs_set'])?></td>
							<td><?=number_format($row['mw_click_set'])?></td>
							<td><?=number_format($row['mw_ctr_set'])?>%</td>
							<?php if($hidden_page == "show"){?>
							<td style="color:blue;font-weight:700;"><?=number_format($row['mw_exhs_set'])?></td>
							<?php } ?>
							<td style="color:red;font-weight:700;"><?=number_format($row['mw_exhs_set_ori'])?></td>

							<!-- 쿠팡 -->
							<td><?=number_format($row['brand_click_num'])?></td>
							<?php if($hidden_page == "show"){?>
							<td style="color:blue;font-weight:700;"><?=number_format($row['brand_income'])?></td>
							<?php } ?>
							<td style="color:red;font-weight:700;"><?=number_format($row['brand_income_ori'])?></td>

							<!-- 뉴스 -->
							<td><?=number_format($row['news_click_num'])?></td>
							<?php if($hidden_page == "show"){?>
							<td style="color:blue;font-weight:700;"><?=number_format($row['news_exhs_amt'])?></td>
							<?php } ?>
							<td style="color:red;font-weight:700;"><?=number_format($row['news_exhs_amt_ori'])?></td>

							<!-- 오퍼월 -->
							<td><?=number_format($row['offerwall_click_num'])?></td>
							<td><?=number_format($row['offerwall_participation'])?></td>
							<?php if($hidden_page == "show"){?>
							<td style="color:blue;font-weight:700;"><?=number_format($row['offerwall_exhs_amt'])?></td>
							<?php } ?>
							<td style="color:red;font-weight:700;"><?=number_format($row['offerwall_exhs_amt_ori'])?></td>

							<?php if($_SESSION['Adm']['id'] == "happy"){ ?>
							<td style="color:blue;font-weight:700;"><?=number_format($month_total)?></td>
							<td style="color:red;font-weight:700;"><?=number_format($month_total_ori)?></td>
							<?php }else{ ?>
								<td style="color:red;font-weight:700;"><?=number_format($month_total_ori)?></td>
								<td style="color:blue;font-weight:700;"><?=number_format($month_total_ori*0.7)?></td>
								<td style="color:red;font-weight:700;"><?=number_format($month_total_ori*0.3)?></td>
							<?php } ?>

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
					<th>누적 설정수</th>\
					<th>신규 설정수</th>\
					<th style='color:red;'>평균 사용자수</th>\
					<th>메인띠배너<br />노출</th>\
					<th>메인띠배너<br />클릭</th>\
					<th>메인띠배너<br />클릭율(%)</th>";
		<?php if($_SESSION['Adm']['id'] == "happy"){ ?>
		tab_text = tab_text + "<th style='color:blue;'>메인띠배너<br />수익(원)</th>";
		tab_text = tab_text + "<th style='color:red;'>메인띠배너<br />원매출(원)</th>";
		<?php }else{ ?>
		tab_text = tab_text + "<th style='color:red;'>메인띠배너<br />매출(원)</th>";
		<?php } ?>
		tab_text = tab_text + "<th>설정띠배너<br />노출</th>\
			<th>설정띠배너<br />클릭</th>\
			<th>설정띠배너<br />클릭율(%)</th>";
		<?php if($_SESSION['Adm']['id'] == "happy"){ ?>
		tab_text = tab_text + "<th style='color:blue;'>설정띠배너<br />수익(원)</th>";
		tab_text = tab_text + "<th style='color:red;'>설정띠배너<br />원매출(원)</th>";
		<?php }else{ ?>
		tab_text = tab_text + "<th style='color:red;'>설정띠배너<br />매출(원)</th>";
		<?php } ?>
		tab_text = tab_text + "<th>쿠팡 광고<br />클릭 수</th>";
		<?php if($_SESSION['Adm']['id'] == "happy"){ ?>
		tab_text = tab_text + "<th style='color:blue;'>쿠팡 광고<br />수익(원)</th>";
		tab_text = tab_text + "<th style='color:red;'>쿠팡 광고<br />원매출(원)</th>";
		<?php }else{ ?>
		tab_text = tab_text + "<th style='color:red;'>쿠팡 광고<br />매출(원)</th>";
		<?php } ?>

        <?php if($news_show){ ?>
		tab_text = tab_text + "<th>모비피드 <br />클릭 수</th>";
		<?php if($_SESSION['Adm']['id'] == "happy"){ ?>
		tab_text = tab_text + "<th style='color:blue;'>모비피드 <br />수익(원)</th>";
		tab_text = tab_text + "<th style='color:red;'>모비피드 <br />원매출(원)</th>";
		<?php }else{ ?>
		tab_text = tab_text + "<th style='color:red;'>모비피드 <br />매출(원)</th>";
		<?php } ?>
        <?php } ?>

		tab_text = tab_text + "<th>포미션<br />클릭 수</th>\
			<th>포미션<br />참여 수</th>";
		<?php if($_SESSION['Adm']['id'] == "happy"){ ?>
		tab_text = tab_text + "<th style='color:blue;'>포미션<br />수익(원)</th>";
		tab_text = tab_text + "<th style='color:red;'>포미션<br />원매출(원)</th>";
		<?php }else{ ?>
		tab_text = tab_text + "<th style='color:red;'>포미션<br />매출(원)</th>";
		<?php } ?>
		<?php if($_SESSION['Adm']['id'] == "happy"){ ?>
		tab_text = tab_text + "<th style='color:blue;'>총 매체수익(원)</th>";
		tab_text = tab_text + "<th style='color:red'>총 원매출(원)</th>";
		<?php }else{ ?>
		tab_text = tab_text + "<th style='color:red;'>총 매출(원)</th>";
		tab_text = tab_text + "<th style='color:blue;'>총 수수료(원)</th>";
		tab_text = tab_text + "<th style='color:red;'>영업이익(원)</th>";
		<?php } ?>
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
	fnExcelReport('ocb','통계내역');
});

	//외부 통게 데이터 show/hide
	function show_display_partner(stats_dttm){
		var currentRow = $("."+stats_dttm).closest('tr');
		if(currentRow.is(":visible")){
			currentRow.hide();
		} else {
			currentRow.show();
		}
	}

function display_toggle(os_type){
	var startDate = '<?=empty($_GET['startDate'])?"":$_GET['startDate']?>';
	var endDate = '<?=empty($_GET['endDate'])?"":$_GET['endDate']?>';
	var type = '<?=empty($_GET['type'])?"":$_GET['type']?>';
	var now_os_type = '<?=empty($_GET['os_type'])?"":$_GET['os_type']?>';

	if(os_type == now_os_type){
		switch(os_type){
			case "A" : $("#os_type_a").prop("checked", true); break;
			case "I" : $("#os_type_i").prop("checked", true); break;
			default : $("#os_type").prop("checked", true); break;
		}
		return false;
	}
	location.href="?startDate="+startDate+"&endDate="+endDate+"&type="+type+"&os_type="+os_type;
}

$("section").css({"min-width":"2200px"});
</script>

<?php
include __foot__;
?>
