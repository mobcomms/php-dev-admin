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
define('_title_', '키보드 사용 통계(신)');
define('_Menu_', 'manage');
define('_subMenu_', 'mobwithad');

include_once __head__;

$startDate=isset($_REQUEST['startDate']) ? $_REQUEST['startDate'] : date("Y-m-d",strtotime("-30 day"));
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

$sdate=str_replace("-","",$startDate);
$edate=str_replace("-","",$endDate);

##########################################################################################################################################################
//전체 누적 설정수
$sql="
	SELECT stats_dttm,activity_num FROM ocb_day_stats
	WHERE stats_dttm >= '20210501' AND stats_dttm <= '{$edate}'
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
##########################################################################################################################################################

// 통계 데이터
$sql="
	SELECT
		CDMS.stats_dttm
		,sum(activity_num) activity_num
		,(SELECT count(*) AS live_cnt 
		FROM ckd_device_check aa
		WHERE service_tp_code='03' AND CDMS.stats_dttm = aa.stats_dttm
		GROUP BY stats_dttm) AS use_cnt 
		,sum(use_time) use_time
		,sum(use_tot_cnt) use_tot_cnt

		,IFNULL(CDS1.news_eprs_num, 0) AS news_eprs_num
		,IFNULL(CDS1.news_click_num, 0) AS news_click_num
		,IFNULL(CDS1.news_exhs_amt, 0) AS news_exhs_amt

		,IFNULL(CDS2.news_eprs_num, 0) AS noti_eprs_num
		,IFNULL(CDS2.news_click_num, 0) AS noti_click_num
		,IFNULL(CDS2.news_exhs_amt, 0) AS noti_exhs_amt

		,IFNULL(CDS7.eprs_num, 0) AS offerwall_participation
		,IFNULL(CDS7.click_num, 0) AS offerwall_click_num
		,IFNULL(CDS7.exhs_amt, 0) AS offerwall_exhs_amt

		,IFNULL(CDS8.eprs_num, 0) AS mw_eprs_main
		,IFNULL(CDS8.click_num, 0) AS mw_click_main
		,IFNULL(CDS8.exhs_amt, 0) AS mw_exhs_main

		,IFNULL(CDS9.eprs_num, 0) AS mw_eprs_set
		,IFNULL(CDS9.click_num, 0) AS mw_click_set
		,IFNULL(CDS9.exhs_amt, 0) AS mw_exhs_set

		,sum(brand_eprs_num) brand_eprs_num
		,sum(brand_click_num) brand_click_num
		,sum(banner_eprs_num) banner_eprs_num
		,sum(banner_click_num) banner_click_num
		,sum(dynamic_eprs_num) dynamic_eprs_num

		,IFNULL(CSTATS.click_num, 0) AS coupang_click
		,IFNULL(CSTATS.order_commission, 0) AS coupang_order
		,IFNULL(CSTATS.cancel_commission, 0) AS coupang_cancel

		,IFNULL(CSTATS2.click_num, 0) AS banner_click
		,IFNULL(CSTATS2.order_commission, 0) AS banner_order
		,IFNULL(CSTATS2.cancel_commission, 0) AS banner_cancel

		,IFNULL(CSTATS3.click_num, 0) AS dynamic_click
		,IFNULL(CSTATS3.order_commission, 0) AS dynamic_order
		,IFNULL(CSTATS3.cancel_commission, 0) AS dynamic_cancel

		,IFNULL(CSTATS4.click_num, 0) AS kw_click
		,IFNULL(CSTATS4.order_commission, 0) AS kw_order
		,IFNULL(CSTATS4.cancel_commission, 0) AS kw_cancel

		,IFNULL(CDMS.news_eprs, 0) AS news_eprs
	FROM ocb_day_stats CDMS

	LEFT JOIN ckd_day_stats CDS1 ON CDS1.stats_dttm = CDMS.stats_dttm AND CDS1.service_tp_code='04'
	LEFT JOIN ckd_day_stats CDS2 ON CDS2.stats_dttm = CDMS.stats_dttm AND CDS2.service_tp_code='05'
	LEFT JOIN ckd_day_stats CDS4 ON CDS4.stats_dttm = CDMS.stats_dttm AND CDS4.service_tp_code='07'
	LEFT JOIN ckd_day_stats CDS7 ON CDS7.stats_dttm = CDMS.stats_dttm AND CDS7.service_tp_code='10'
	LEFT JOIN ckd_day_stats CDS8 ON CDS8.stats_dttm = CDMS.stats_dttm AND CDS8.service_tp_code='11'
	LEFT JOIN ckd_day_stats CDS9 ON CDS9.stats_dttm = CDMS.stats_dttm AND CDS9.service_tp_code='12'

	LEFT JOIN (
		SELECT stats_dttm, SUM(click_num) AS click_num, SUM(order_commission) AS order_commission, SUM(cancel_commission) AS cancel_commission
		FROM ocb_day_coupang_stats
		WHERE service_tp_code IN ('03','07') AND stats_dttm BETWEEN {$sdate} AND {$edate}
		GROUP BY stats_dttm
	) CSTATS ON CSTATS.stats_dttm = CDMS.stats_dttm

	LEFT JOIN ocb_day_coupang_stats CSTATS2 ON CSTATS2.stats_dttm = CDMS.stats_dttm AND CSTATS2.service_tp_code='04'

	LEFT JOIN (
		SELECT stats_dttm, SUM(click_num) AS click_num, SUM(order_commission) AS order_commission, SUM(cancel_commission) AS cancel_commission
		FROM ocb_day_coupang_stats
		WHERE service_tp_code IN ('05','08') AND stats_dttm BETWEEN {$sdate} AND {$edate}
		GROUP BY stats_dttm
	) CSTATS3 ON CSTATS3.stats_dttm = CDMS.stats_dttm

	LEFT JOIN ocb_day_coupang_stats CSTATS4 ON CSTATS4.stats_dttm = CDMS.stats_dttm AND CSTATS4.service_tp_code='06'

	WHERE CDMS.stats_dttm BETWEEN {$sdate} AND {$edate}
	GROUP BY CDMS.stats_dttm
	ORDER BY CDMS.stats_dttm DESC
";
//pre($sql);
$ret = $NDO->fetch_array($sql);
##########################################################################################################################################################

$html='';
$TOTAL = [];
$M_TOTAL = [];
$make_array1 = array("accumulate","activity_num","use_time","accumulate","activity_num","use_time","use_cnt","usage_rate","use_tot_cnt","useTotCntAvg","ctr");
$make_array2 = array("mw_eprs_main","mw_click_main","mw_exhs_main","mw_ctr_main","mw_eprs_set","mw_click_set","mw_exhs_set","mw_ctr_set","brand_eprs_num","brand_click_num","dynamic_eprs_num","dynamic_click_num");
$make_array3 = array("banner_eprs_num","banner_click_num","kw_click_num","brand_income","banner_income","dynamic_income","kw_income","mobfeed","mobfeed_noti");
$make_array4 = array("news_eprs_num","news_click_num","noti_eprs_num","noti_click_num","offerwall_participation","offerwall_click_num","offerwall_exhs_amt");
$make_array = array_merge ($make_array1, $make_array2, $make_array3, $make_array4);
foreach($make_array as $item){
	$TOTAL[$item] = 0;
}

foreach($ret as $key => $row){

	//사용율
	$usage_rate = ($accumulate_array[$row['stats_dttm']]) ? ($row['use_cnt'] / $accumulate_array[$row['stats_dttm']]) * 100 : 0;
	//평균 사용 시간
	$useTimeAvg = ($row['use_time'] > 0) ? round($row['use_time'] / $row['use_tot_cnt']) : 0;
	//평균 사용횟수
	$useTotCntAvg = ($row['use_cnt'] > 0) ? round($row['use_tot_cnt'] / $row['use_cnt'], 1) : 0;


/*
 * 현재 브랜드 광고쪽에 자체 광고가 아닌 쿠팡 광고를 노출하고 있기 때문에 쿠팡 연동 값을 대입한다.
 */
	//앱 자체 클릭 체크 ($row['brand_click_num'])
	//쿠팡 API 클릭 체크 ($row['coupang_click'])
	$row['brand_click_num'] = $row['coupang_click'];
	$row['banner_click_num'] = $row['banner_click'];
	$row['dynamic_click_num'] = $row['dynamic_click'];
	$row['kw_click_num'] = $row['kw_click'];

	# 커미션 100 으로 보여줌
	//쿠팡 수익 계산 (주문 커미션 - 취소 커미션 ) * 0.8
	$commission = 1;
	$brand_income = ($row['coupang_order'] + $row['coupang_cancel']) * $commission;
	$banner_income = ($row['banner_order'] + $row['banner_cancel']) * $commission;
	$dynamic_income = ($row['dynamic_order'] + $row['dynamic_cancel']) * $commission;
	$kw_income = ($row['kw_order'] + $row['kw_cancel']) * $commission;

	//모비위드 CTR
	$mw_ctr_main = ($row['mw_eprs_main'] > 0) ? number_format($row['mw_click_main'] / $row['mw_eprs_main'] * 100, 1) : 0;
	$mw_ctr_set = ($row['mw_eprs_set'] > 0) ? number_format($row['mw_click_set'] / $row['mw_eprs_set'] * 100, 1) : 0;

	// 모비피드 수익
	$row['mobfeed']=$row['news_exhs_amt'] * $commission;

	// 모비피드 노티
	$row['mobfeed_noti']=$row['noti_exhs_amt'] * $commission;

	//오퍼월
	if($row['stats_dttm'] >= "20230420"){
		if($row['stats_dttm'] >= "20230527"){
			if($row['stats_dttm'] >= "20230531"){
				if($row['stats_dttm'] >= "20230601"){
					$offerwall_commission ="0.6";
				}else{
					$offerwall_commission ="0.8";
				}
			}else{
				$offerwall_commission ="0.7";
			}
		}else{
			$offerwall_commission ="0.6";
		}
	}else{
		$offerwall_commission ="1";
	}
	$row['offerwall_exhs_amt']=round($row['offerwall_exhs_amt'] * $offerwall_commission);

	//모비위드
	if($row['stats_dttm'] >= "20230419"){
		$mw_commission = "0.8";
	}else{
		$mw_commission = "1";
	}
	$row['mw_exhs_main']=round($row['mw_exhs_main'] * $mw_commission);
	$row['mw_exhs_set']=round($row['mw_exhs_set'] * $mw_commission);

	//합계
	$TOTAL['accumulate'] += $accumulate_array[$row['stats_dttm']];//날짜
	$TOTAL['activity_num'] += $row['activity_num'];//누적 설정수
	$TOTAL['use_time'] += $row['use_time'];//신규 설정수
	$TOTAL['use_cnt'] += $row['use_cnt'];//평균 사용자수asasdasdasdasddefswerf
	$TOTAL['usage_rate'] += number_format($usage_rate);//평균 사용율
	$TOTAL['use_tot_cnt'] += $row['use_tot_cnt'];//평균 사용시간
	$TOTAL['useTotCntAvg'] += $useTotCntAvg; //평균사용횟수

	//모비위드 CTR
	$TOTAL['mw_ctr_main'] += $mw_ctr_main;
	$TOTAL['mw_ctr_set'] += $mw_ctr_set;

	$TOTAL['mw_eprs_main'] += $row['mw_eprs_main'];
	$TOTAL['mw_click_main'] += $row['mw_click_main'];
	$TOTAL['mw_exhs_main'] += round($row['mw_exhs_main']);

	$TOTAL['mw_eprs_set'] += $row['mw_eprs_set'];
	$TOTAL['mw_click_set'] += $row['mw_click_set'];
	$TOTAL['mw_exhs_set'] += round($row['mw_exhs_set']);

	$TOTAL['brand_eprs_num'] += $row['brand_eprs_num'];
	$TOTAL['brand_click_num'] += $row['brand_click_num'];

	$TOTAL['dynamic_eprs_num'] += $row['dynamic_eprs_num'];
	$TOTAL['dynamic_click_num'] += $row['dynamic_click_num'];

	$TOTAL['banner_eprs_num'] += $row['banner_eprs_num'];
	$TOTAL['banner_click_num'] += $row['banner_click_num'];

	$TOTAL['kw_click_num'] += $row['kw_click_num'];

	$TOTAL['brand_income'] += round($brand_income);
	$TOTAL['banner_income'] += round($banner_income);
	$TOTAL['dynamic_income'] += round($dynamic_income);

	$TOTAL['kw_income'] += round($kw_income);

	$TOTAL['mobfeed'] += round($row['mobfeed']);
	if($row['stats_dttm'] >= "20230119"){
		$row['news_eprs_num'] = $row['news_eprs'];
	}
	$TOTAL['news_eprs_num'] += $row['news_eprs_num'];
	$TOTAL['news_click_num'] += $row['news_click_num'];

	$TOTAL['mobfeed_noti'] += round($row['mobfeed_noti']);
	$TOTAL['noti_eprs_num'] += $row['noti_eprs_num'];
	$TOTAL['noti_click_num'] += $row['noti_click_num'];

	$TOTAL['offerwall_participation'] += $row['offerwall_participation'];
	$TOTAL['offerwall_click_num'] += $row['offerwall_click_num'];
	$TOTAL['offerwall_exhs_amt'] += round($row['offerwall_exhs_amt']);

	if($type=="3M" || $type=="6M"){
		//합계
		$month = substr($row['stats_dttm'],0,6);
		if(!isset($M_TOTAL[$month])){
			$make_array = array_merge ($make_array1, $make_array2, $make_array3, $make_array4);
			foreach($make_array as $item){
				$M_TOTAL[$month][$item] = 0;
			}
		}

		$M_TOTAL[$month]['accumulate'] += $accumulate_array[$row['stats_dttm']];
		$M_TOTAL[$month]['activity_num'] += $row['activity_num'];
		$M_TOTAL[$month]['use_time'] += $row['use_time'];
		$M_TOTAL[$month]['use_cnt'] += $row['use_cnt'];
		$M_TOTAL[$month]['use_tot_cnt'] += $row['use_tot_cnt'];

		$M_TOTAL[$month]['mw_ctr_main'] += $mw_ctr_main;
		$M_TOTAL[$month]['mw_ctr_set'] += $mw_ctr_set;

		$M_TOTAL[$month]['brand_eprs_num'] += $row['brand_eprs_num'];
		$M_TOTAL[$month]['brand_click_num'] += $row['brand_click_num'];

		$M_TOTAL[$month]['banner_eprs_num'] += $row['banner_eprs_num'];
		$M_TOTAL[$month]['banner_click_num'] += $row['banner_click_num'];

		$M_TOTAL[$month]['dynamic_eprs_num'] += $row['dynamic_eprs_num'];
		$M_TOTAL[$month]['dynamic_click_num'] += $row['dynamic_click_num'];

		$M_TOTAL[$month]['kw_click_num'] += $row['kw_click_num'];

		$M_TOTAL[$month]['brand_income'] += round($brand_income);
		$M_TOTAL[$month]['banner_income'] += round($banner_income);
		$M_TOTAL[$month]['dynamic_income'] += round($dynamic_income);
		$M_TOTAL[$month]['kw_income'] += round($kw_income);

		$M_TOTAL[$month]['mobfeed'] += round($row['mobfeed']);
		if($row['stats_dttm'] >= "20230119"){
			$row['news_eprs_num'] = $row['news_eprs'];
		}
		$M_TOTAL[$month]['news_eprs_num'] += $row['news_eprs_num'];
		$M_TOTAL[$month]['news_click_num'] += $row['news_click_num'];

		$M_TOTAL[$month]['mobfeed_noti'] += round($row['mobfeed_noti']);
		$M_TOTAL[$month]['noti_eprs_num'] += $row['noti_eprs_num'];
		$M_TOTAL[$month]['noti_click_num'] += $row['noti_click_num'];

		$M_TOTAL[$month]['offerwall_participation'] += $row['offerwall_participation'];
		$M_TOTAL[$month]['offerwall_click_num'] += $row['offerwall_click_num'];
		$M_TOTAL[$month]['offerwall_exhs_amt'] += round($row['offerwall_exhs_amt']);

	}

	//개별 수익 판단.
	$css_mw_main = $row['mw_exhs_main']<0 ?"color:red;":"color:blue;";
	$css_mw_set = $row['mw_exhs_set']<0 ?"color:red;":"color:blue;";
	$css_dynamic_income = $dynamic_income<0 ?"color:red;":"color:blue;";
	$css_brand_income = $brand_income<0 ?"color:red;":"color:blue;";
	$css_banner_income = $banner_income<0 ?"color:red;":"color:blue;";
	$css_kw_income = $kw_income<0 ?"color:red;":"color:blue;";
	$css_mobfeed_income = $row['mobfeed']<0 ?"color:red;":"color:blue;";
	$css_mobfeed_noti_income = $row['mobfeed_noti']<0 ?"color:red;":"color:blue;";
	$css_offerwall_exhs_amt = $row['offerwall_exhs_amt']<0 ?"color:red;":"color:blue;";

	//총수익
	$total_amt = $row['mw_exhs_main']+$row['mw_exhs_set']+$dynamic_income+$brand_income+$banner_income+$kw_income+$row['mobfeed']+$row['mobfeed_noti']+$row['offerwall_exhs_amt'];
	$css_total_income = $total_amt<0 ?"color:red;":"color:blue;";

	$html .= "<tr class='{$fn->dateColor($row['stats_dttm'])}'>";
	$html .= "
			<td>{$row['stats_dttm']}</td>

			<td>".number_format($accumulate_array[$row['stats_dttm']])."</td>
			<td style='font-weight:700;'>".number_format($row['activity_num'])."</td>
			<td style='color:red;'>".number_format($row['use_cnt'])."</td> 

			<td>".number_format($usage_rate)."%</td>
			<td>".$fn->convertTime($useTimeAvg)."</td>
			<td>".number_format($row['use_tot_cnt'])."</td>
			<td>{$useTotCntAvg}</td>

			<!-- 모비위드 메인 -->
			<td>".number_format($row['mw_eprs_main'])."</td>
			<td>".number_format($row['mw_click_main'])."</td>
			<td>".number_format($mw_ctr_main)."%</td>
			<td style='{$css_mw_main} font-weight:700;'>".number_format($row['mw_exhs_main'])."</td>

			<!-- 모비위드 설정 -->
			<td>".number_format($row['mw_eprs_set'])."</td>
			<td>".number_format($row['mw_click_set'])."</td>
			<td>".number_format($mw_ctr_set)."%</td>
			<td style='{$css_mw_set} font-weight:700;'>".number_format($row['mw_exhs_set'])."</td>

			<!-- 다이나믹 -->
			<td>".number_format($row['dynamic_eprs_num'])."</td>
			<td>".number_format($row['dynamic_click_num'])."</td>
			<td style='{$css_dynamic_income} font-weight:700;'>".number_format($dynamic_income)."</td>

			<!-- 브랜드 -->
			<td>".number_format($row['use_tot_cnt'])."</td>
			<td>".number_format($row['brand_click_num'])."</td>
			<td style='{$css_brand_income} font-weight:700;'>".number_format($brand_income)."</td>

			<!-- 배너 -->
			<td>".number_format($row['banner_eprs_num']) ."</td>
			<td>".number_format($row['banner_click_num'])."</td>
			<td style='{$css_banner_income} font-weight:700;'>".number_format($banner_income)."</td>

			<!-- 키워드 -->
			<td>".number_format($row['kw_click_num'])."</td>
			<td style='{$css_brand_income} font-weight:700;'>".number_format($kw_income)."</td>

			<!-- 뉴스 -->
			<td>".number_format($row['news_eprs_num'])."</td>
			<td>".number_format($row['news_click_num'])."</td>
			<td style='{$css_mobfeed_income} font-weight:700;'>".number_format($row['mobfeed'])."</td>

			<!-- 노티 -->
			<td>".number_format($row['noti_eprs_num'])."</td>
			<td>".number_format($row['noti_click_num'])."</td>
			<td style='{$css_mobfeed_noti_income} font-weight:700;'>".number_format($row['mobfeed_noti'])."</td>

			<!-- 오퍼월 -->
			<td>".number_format($row['offerwall_click_num'])."</td>
			<td>".number_format($row['offerwall_participation'])."</td>
			<td style='{$css_offerwall_exhs_amt} font-weight:700;'>".number_format($row['offerwall_exhs_amt'])."</td>

			<!-- 총 수익(원) -->
			<td style='{$css_total_income} font-weight:700;'>".number_format($total_amt)."</td>
		 </tr>
	";

}//foreach

if(empty($html)){
	$html="<tr><td colspan='20'>데이터가 없습니다.</td></tr>";
}

//평균 사용자 수
$date1 = new DateTime($startDate);
$date2 = new DateTime($endDate);
$interval = $date1->diff($date2);
$diff_data = $interval->format('%a');

//합계 평균 사용자 수
$TOTAL['use_cnt'] = $TOTAL['use_cnt'] / ($diff_data+1);

//합계 평균 사용율
$TOTAL['usage_rate'] = $TOTAL['usage_rate'] / ($diff_data+1);

//합계 평균 사용시간
$TOTAL['useAvg'] = ($TOTAL['use_tot_cnt'] > 0) ? round($TOTAL['use_time'] / $TOTAL['use_tot_cnt'], 1) : 0;

//합계 평균 사용횟수
$TOTAL['totCntAvg'] = ($diff_data > 0) ? number_format($TOTAL['useTotCntAvg'] / $diff_data , 1) : 0;

//클릭율
$TOTAL['mw_ctr_main'] = ($TOTAL['mw_eprs_main'] > 0) ? number_format($TOTAL['mw_click_main'] / $TOTAL['mw_eprs_main'] * 100, 1) : 0;
$TOTAL['mw_ctr_set'] = ($TOTAL['mw_eprs_set'] > 0) ? number_format($TOTAL['mw_click_set'] / $TOTAL['mw_eprs_set'] * 100, 1) : 0;

//총수익
$total_amt_all = $TOTAL['mw_exhs_main']+$TOTAL['mw_exhs_set']+$TOTAL['dynamic_income']+$TOTAL['brand_income']+$TOTAL['banner_income']+$TOTAL['kw_income']+$TOTAL['mobfeed']+$TOTAL['mobfeed_noti']+$TOTAL['offerwall_exhs_amt'];
?>
<style>
	a.tooltips {outline:none; }
	a.tooltips strong {line-height:30px;}
	a.tooltips:hover {text-decoration:none;}
	a.tooltips span {
		z-index:10;display:none; padding:14px 20px;
		margin-top:30px; margin-left:-160px;
		width:300px; line-height:16px;
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
			<h4 class="panel-title">키보드 사용 통계</h4>
		</div><!-- panel-heading -->

		<div class="panel-body">

			<form name="scform" method="get" action="">
				<input type="hidden" name="rep" value="<?=_rep_;?>" />
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
					<table class="table table-hover mb30" style="border:1px solid #b0b0b0;">
						<thead>
						<tr>
							<th colspan="8">앱 연동
								<a href="#" class="tooltips"><i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
									<span>
										<strong>앱 연동</strong><br />
										(1일전 데이터 갱신)
									</span>
								</a>
							</th>
							<th colspan="4">메인띠배너
								<a href="#" class="tooltips"><i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
									<span>
										<strong>메인띠배너 연동</strong><br />
										(2일전 데이터 갱신)
									</span>
								</a>
							</th>
							<th colspan="4">설정띠배너
								<a href="#" class="tooltips"><i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
									<span>
										<strong>설정띠배너 연동</strong><br />
										(2일전 데이터 갱신)
									</span>
								</a>
							</th>

							<th colspan="11">쿠팡 연동
								<a href="#" class="tooltips"><i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
									<span>
										<strong>리워드(모비온) 광고</strong><br />
										쿠팡 데이터 (매일 오후12:50분 전일 데이터 갱신)
									</span>
								</a>
							</th>
							<th colspan="6">모비피드 연동
								<a href="#" class="tooltips"><i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
									<span>
										<strong>모비피드 연동</strong><br />
										API 스케쥴링 (매일 13시 26분 갱신)
									</span>
								</a>
							</th>
							<th colspan="3">포미션 연동
								<a href="#" class="tooltips"><i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
									<span>
										<strong>포미션 연동</strong><br />
										(1시간마다 갱신. 새벽1시에 전일 완전한 데이터)
									</span>
								</a>
							</th>
							<th></th>
						</tr>
						<tr>
							<th>날짜</th>
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
							<th>사용율 (%)</th>
							<th>평균<br />사용시간
								<a href="#" class="tooltips"><i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
									<span>
										<strong>평균 사용시간</strong><br />
										키보드 사용 시간  (수일 전 사용한 누적 시간 - 누적된 데이터를 서버에 전송하기전 앱을 삭제하면 누락됨)
									</span>
								</a>
							</th>
							<th>총 사용횟수
								<a href="#" class="tooltips"><i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
									<span>
										<strong>총 사용횟수</strong><br />
										키보드 사용 횟수 (수일 전 사용한 누적 카운트 - 누적된 데이터를 서버에 전송하기전 앱을 삭제하면 누락됨)
									</span>
								</a>
							</th>
							<th>평균 사용횟수</th>

							<th>노출</th>
							<th>클릭</th>
							<th>클릭율(%)</th>
							<th style="color:blue;">수익(원)</th>

							<th>노출</th>
							<th>클릭</th>
							<th>클릭율(%)</th>
							<th style="color:blue;">수익(원)</th>

							<th>다이나믹 광고<br />노출</th>
							<th>다이나믹 광고<br />클릭 수</th>
							<th style="color:blue;">다이나믹 광고<br />수익(원)</th>

							<th>브랜드 광고<br />노출</th>
							<th>브랜드 광고<br />클릭 수</th>
							<th style="color:blue;">브랜드 광고<br />수익(원)</th>

							<th>배너광고<br />노출</th>
							<th>배너광고<br />클릭 수</th>
							<th style="color:blue;">배너광고<br />수익(원)</th>

							<th>키워드 광고<br /> 클릭</th>
							<th style="color:blue;">키워드 광고<br />수익(원)</th>

							<th>뉴스<br />노출</th>
							<th>뉴스<br />클릭 수</th>
							<th style="color:blue;">뉴스<br />수익(원)</th>

							<th>노티 뉴스<br />노출</th>
							<th>노티 뉴스<br />클릭 수</th>
							<th style="color:blue;">노티 뉴스<br />수익(원)</th>

							<th>오퍼월<br />클릭 수</th>
							<th>오퍼월<br />참여 수</th>
							<th style="color:blue;">오퍼월<br />수익(원)</th>
							
							<th style="color:blue;">총 수익(원)</th>
						</tr>
						</thead>
						<tbody id="ocb" >
						<tr class="" style="background-color: #F2F5A9;">
							<td>합계</td>
							<td></td>
							<td style='font-weight:700;'><?=number_format($TOTAL['activity_num'])?></td>
							<td style="color:red;"><?=number_format($TOTAL['use_cnt'])?></td>
							<td><?=number_format($TOTAL['usage_rate'])?>%</td>
							<td><?=$fn->convertTime($TOTAL['useAvg'],1)?></td>
							<td><?=number_format($TOTAL['use_tot_cnt'])?></td>
							<td><?=number_format($TOTAL['totCntAvg'],1)?></td>

							<!-- 메인띠베너 -->
							<td><?=number_format($TOTAL['mw_eprs_main'])?></td>
							<td><?=number_format($TOTAL['mw_click_main'])?></td>
							<td><?=number_format($TOTAL['mw_ctr_main'])?>%</td>
							<td style="color:blue;font-weight:700;"><?=number_format($TOTAL['mw_exhs_main'])?></td>

							<!-- 설정띠베너 -->
							<td><?=number_format($TOTAL['mw_eprs_set'])?></td>
							<td><?=number_format($TOTAL['mw_click_set'])?></td>
							<td><?=number_format($TOTAL['mw_ctr_set'])?>%</td>
							<td style="color:blue;font-weight:700;"><?=number_format($TOTAL['mw_exhs_set'])?></td>

							<!-- 다이나믹 -->
							<td><?=number_format($TOTAL['dynamic_eprs_num'])?></td>
							<td><?=number_format($TOTAL['dynamic_click_num'])?></td>
							<td style="color:blue;font-weight:700;"><?=number_format($TOTAL['dynamic_income'])?></td>

							<!-- 브랜드 -->
							<td><?=number_format($TOTAL['use_tot_cnt'])?></td>
							<td><?=number_format($TOTAL['brand_click_num'])?></td>
							<td style="color:blue;font-weight:700;"><?=number_format($TOTAL['brand_income'])?></td>

							<!-- 배너 -->
							<td><?=number_format($TOTAL['banner_eprs_num'])?></td>
							<td><?=number_format($TOTAL['banner_click_num'])?></td>
							<td style="color:blue;font-weight:700;"><?=number_format($TOTAL['banner_income'])?></td>

							<!-- 키워드 -->
							<td><?=number_format($TOTAL['kw_click_num'])?></td>
							<td style="color:blue;font-weight:700;"><?=number_format($TOTAL['kw_income'])?></td>

							<!-- 뉴스 -->
							<td><?=number_format($TOTAL['news_eprs_num'])?></td>
							<td><?=number_format($TOTAL['news_click_num'])?></td>
							<td style="color:blue;font-weight:700;"><?=number_format($TOTAL['mobfeed'])?></td>

							<!-- 노티 -->
							<td><?=number_format($TOTAL['noti_eprs_num'])?></td>
							<td><?=number_format($TOTAL['noti_click_num'])?></td>
							<td style="color:blue;font-weight:700;"><?=number_format($TOTAL['mobfeed_noti'])?></td>

							<!-- 오퍼월 -->
							<td><?=number_format($TOTAL['offerwall_click_num'])?></td>
							<td><?=number_format($TOTAL['offerwall_participation'])?></td>
							<td style="color:blue;font-weight:700;"><?=number_format($TOTAL['offerwall_exhs_amt'])?></td>

							<td style="color:blue;font-weight:700;"><?=number_format($total_amt_all)?></td>
						</tr>
					<?php
					//3개월 6개월 합계
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

							//모비위드 클릭율
							$row['mw_ctr_main'] = ($row['mw_eprs_main'] > 0) ? number_format($row['mw_click_main'] / $row['mw_eprs_main'] * 100 , 1) : 0;
							$row['mw_ctr_set'] = ($row['mw_eprs_set'] > 0) ? number_format($row['mw_click_set'] / $row['mw_eprs_set'] * 100 , 1) : 0;

							$month_total = $row['dynamic_income']+$row['brand_income']+$row['banner_income']+$row['kw_income']+$row['mobfeed']+$row['mobfeed_noti']+$row['offerwall_exhs_amt'];
//3개월 6개월 합계
################################################################################################################################################################
					?>
						<tr class="" style="background-color: #afe076;">
							<td><?=$key?></td>
							<td></td>
							<td style='font-weight:700;'><?=number_format($row['activity_num'])?></td>
							<td style="color:red;"><?=number_format($row['use_cnt']/$this_month_day_cnt)?></td>
							<td><?=number_format($row['totuseAvg'])?>%</td>
							<td><?=$fn->convertTime($row['useAvg'],1)?></td>
							<td><?=number_format($row['use_tot_cnt'])?></td>
							<td><?=number_format($row['totCntAvg'],1)?></td>

							<!-- 모비위드 메인 -->
							<td><?=number_format($row['dynamic_eprs_num'])?></td>
							<td><?=number_format($row['dynamic_click_num'])?></td>
							<td><?=number_format($row['dynamic_click_num'])?></td>
							<td style="color:blue;font-weight:700;"><?=number_format($row['dynamic_income'])?></td>

							<!-- 모비위드 설정 -->
							<td><?=number_format($row['dynamic_eprs_num'])?></td>
							<td><?=number_format($row['dynamic_click_num'])?></td>
							<td><?=number_format($row['dynamic_click_num'])?></td>
							<td style="color:blue;font-weight:700;"><?=number_format($row['dynamic_income'])?></td>

							<!-- 다이나믹 -->
							<td><?=number_format($row['dynamic_eprs_num'])?></td>
							<td><?=number_format($row['dynamic_click_num'])?></td>
							<td style="color:blue;font-weight:700;"><?=number_format($row['dynamic_income'])?></td>

							<!-- 브랜드 -->
							<td><?=number_format($row['use_tot_cnt'])?></td>
							<td><?=number_format($row['brand_click_num'])?></td>
							<td style="color:blue;font-weight:700;"><?=number_format($row['brand_income'])?></td>

							<!-- 배너 -->
							<td><?=number_format($row['banner_eprs_num'])?></td>
							<td><?=number_format($row['banner_click_num'])?></td>
							<td style="color:blue;font-weight:700;"><?=number_format($row['banner_income'])?></td>

							<!-- 키워드 -->
							<td><?=number_format($row['kw_click_num'])?></td>
							<td style="color:blue;font-weight:700;"><?=number_format($row['kw_income'])?></td>

							<!-- 뉴스 -->
							<td><?=number_format($row['news_eprs_num'])?></td>
							<td><?=number_format($row['news_click_num'])?></td>
							<td style="color:blue;font-weight:700;"><?=number_format($row['mobfeed'])?></td>

							<!-- 노티 -->
							<td><?=number_format($row['noti_eprs_num'])?></td>
							<td><?=number_format($row['noti_click_num'])?></td>
							<td style="color:blue;font-weight:700;"><?=number_format($row['mobfeed_noti'])?></td>

							<!-- 오퍼월 -->
							<td><?=number_format($row['offerwall_click_num'])?></td>
							<td><?=number_format($row['offerwall_participation'])?></td>
							<td style="color:blue;font-weight:700;"><?=number_format($row['offerwall_exhs_amt'])?></td>

							<td style="color:blue;font-weight:700;"><?=number_format($month_total)?></td>
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
					<th>사용율 (%)</th>\
					<th>평균<br />사용시간</th>\
					<th>총 사용횟수</th>\
					<th>평균 사용횟수</th>\
					<th>메인띠배너<br />노출</th>\
					<th>메인띠배너<br />클릭</th>\
					<th>메인띠배너<br />클릭율(%)</th>\
					<th style='color:blue;'>메인띠배너<br />수익(원)</th>\
					<th>설정띠배너<br />노출</th>\
					<th>설정띠배너<br />클릭</th>\
					<th>설정띠배너<br />클릭율(%)</th>\
					<th style='color:blue;'>설정띠배너<br />수익(원)</th>\
					<th>다이나믹 광고<br />노출</th>\
					<th>다이나믹 광고<br />클릭 수</th>\
					<th style='color:blue;'>다이나믹 광고<br />수익(원)</th>\
					<th>브랜드 광고<br />노출</th>\
					<th>브랜드 광고<br />클릭 수</th>\
					<th style='color:blue;'>브랜드 광고<br />수익(원)</th>\
					<th>배너광고<br />노출</th>\
					<th>배너광고<br />클릭 수</th>\
					<th style='color:blue;'>배너광고<br />수익(원)</th>\
					<th>키워드광고<br />클릭 수</th>\
					<th style='color:blue;'>키워드광고<br />수익(원)</th>\
					<th>뉴스광고<br />노출</th>\
					<th>뉴스광고<br />클릭 수</th>\
					<th style='color:blue;'>뉴스광고<br />수익(원)</th>\
					<th>노티뉴스<br />노출</th>\
					<th>노티뉴스<br />클릭 수</th>\
					<th style='color:blue;'>노티뉴스<br />수익(원)</th>\
					<th>오퍼월<br />클릭 수</th>\
					<th>오퍼월<br />참여 수</th>\
					<th style='color:blue;'>오퍼월<br />수익(원)</th>\
					<th style='color:blue;'>총 수익(원)</th>\
				</tr>\
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
	fnExcelReport('ocb','OCB통계');
});
	// 날짜 설정
	function report(type, sub, sdate, edate){
		sdate=(sdate) ? sdate :  '<?=date("Y-m-d")?>';
		edate=(edate) ? edate :  '<?=date("Y-m-d")?>';
		location.href='<?=$_SERVER['PHP_SELF']?>?startDate='+sdate+'&endDate='+edate+'&type='+type+'&sub='+sub;
	}

	//외부 통게 데이터 show/hide
	function show_display_partner(stats_dttm){
		var currentRow = $("."+stats_dttm).closest('tr');
		if(currentRow.is(":visible")){
			currentRow.hide();
		} else {
			currentRow.show();
		}
	}

	$("section").css({"min-width":"3500px"});
</script>

<?php
include __foot__;
?>
