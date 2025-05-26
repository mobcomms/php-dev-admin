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
$allow_ip = array('127.0.0.1','221.150.126.74','112.220.254.82','112.171.101.32','221.150.126.75','112.171.26.11','118.36.229.134');
//$allow_ip = array('112.220.254.82');

if(in_array($ip, $allow_ip) && $_SESSION['Adm']['id'] == "mango"){
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

// 통계 데이터
$sql="
	SELECT
		CDMS.stats_dttm
		,sum(activity_num) activity_num
		,IFNULL((SELECT count(*) AS live_cnt 
		FROM ckd_device_check aa
		WHERE service_tp_code='03' AND CDMS.stats_dttm = aa.stats_dttm
		GROUP BY stats_dttm),0) AS use_cnt 
		,sum(use_time) use_time
		,sum(use_tot_cnt) use_tot_cnt

		,IFNULL(CDS.eprs_num, 0) AS eprs_num
		,IFNULL(CDS.click_num, 0) AS click_num
		,IFNULL(CDS.exhs_amt, 0) AS exhs_amt

		,IFNULL(CDS3.eprs_num, 0) AS reward_mobon_eprs_num
		,IFNULL(CDS3.click_num, 0) AS reward_mobon_click_num
		,IFNULL(CDS3.exhs_amt, 0) AS reward_mobon_exhs_amt

		,IFNULL(CDS5.eprs_num, 0)  AS mobimixer_eprs_num
		,IFNULL(CDS5.click_num , 0)AS mobimixer_click_num
		,IFNULL(CDS5.exhs_amt, 0) AS mobimixer_exhs_amt

		,IFNULL(CDS6.eprs_num, 0) AS criteo_eprs_num
		,IFNULL(CDS6.click_num, 0) AS criteo_click_num
		,IFNULL(CDS6.exhs_amt, 0) AS criteo_exhs_amt

        ,IFNULL(CDS7.new_visit_num, 0) AS offerwall_use_cnt
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

		,IFNULL(CDMS.reward_coupang_eprs, 0) AS reward_coupang_eprs_num
		,IFNULL(CSTATS5.click_num, 0) AS reward_coupang_click_num
		,IFNULL(CSTATS5.order_commission + CSTATS5.cancel_commission, 0) AS reward_coupang_exhs_amt

		,IFNULL(CDMS.reward_thezoom_eprs, 0) AS reward_thezoom_eprs_num
		,IFNULL(CDMS.reward_thezoom_click, 0) AS reward_thezoom_click_num
		,IFNULL(CSTATS6.order_commission + CSTATS6.cancel_commission, 0) AS reward_thezoom_exhs_amt

		,IFNULL(CDMS.news_eprs, 0) AS news_eprs

		,IFNULL(CDS10.click_num, 0) AS gpt_click_num_m
		,IFNULL(CDS10.exhs_amt, 0) AS gpt_exhs_amt_m

		,IFNULL(CSTATS7.click_num, 0) AS gpt_click_c
		,IFNULL(CSTATS7.order_commission, 0) AS gpt_order_c
		,IFNULL(CSTATS7.cancel_commission, 0) AS gpt_cancel_c

		,IFNULL(CSTATS8.click_num, 0) AS ocbkeyicon_click
		,IFNULL(CSTATS8.order_commission, 0) AS ocbkeyicon_order
		,IFNULL(CSTATS8.cancel_commission, 0) AS ocbkeyicon_cancel

		,IFNULL(CDS14.eprs_num, 0) AS game_eprs_main
		,IFNULL(CDS14.click_num, 0) AS game_click_main
		,IFNULL(CDS14.exhs_amt, 0) AS game_exhs_main

		,IFNULL(CDS11.eprs_num, 0) AS game_eprs_roulette
		,IFNULL(CDS11.click_num, 0) AS game_click_roulette
		,IFNULL(CDS11.exhs_amt, 0) AS game_exhs_roulette

		,IFNULL(CDS12.eprs_num, 0) AS game_eprs_ladder
		,IFNULL(CDS12.click_num, 0) AS game_click_ladder
		,IFNULL(CDS12.exhs_amt, 0) AS game_exhs_ladder

		,IFNULL(CDS13.eprs_num, 0) AS game_eprs_ticket
		,IFNULL(CDS13.click_num, 0) AS game_click_ticket
		,IFNULL(CDS13.exhs_amt, 0) AS game_exhs_ticket

	FROM ocb_day_stats CDMS
	LEFT JOIN (
		SELECT stats_dttm
		,sum(eprs_num) AS eprs_num
		,sum(click_num) AS click_num
		,sum(exhs_amt) AS exhs_amt
		FROM ckd_day_stats
		WHERE service_tp_code='03' AND stats_dttm BETWEEN {$sdate} AND {$edate}
		GROUP BY stats_dttm
	) CDS ON CDS.stats_dttm = CDMS.stats_dttm

	LEFT JOIN ckd_day_stats CDS3 ON CDS3.stats_dttm = CDMS.stats_dttm AND CDS3.service_tp_code='06'
	LEFT JOIN ckd_day_stats CDS5 ON CDS5.stats_dttm = CDMS.stats_dttm AND CDS5.service_tp_code='08'
	LEFT JOIN ckd_day_stats CDS6 ON CDS6.stats_dttm = CDMS.stats_dttm AND CDS6.service_tp_code='09'
	LEFT JOIN ckd_day_stats CDS7 ON CDS7.stats_dttm = CDMS.stats_dttm AND CDS7.service_tp_code='10'
	LEFT JOIN ckd_day_stats CDS8 ON CDS8.stats_dttm = CDMS.stats_dttm AND CDS8.service_tp_code='11'
	LEFT JOIN ckd_day_stats CDS9 ON CDS9.stats_dttm = CDMS.stats_dttm AND CDS9.service_tp_code='12'
	LEFT JOIN ckd_day_stats CDS10 ON CDS10.stats_dttm = CDMS.stats_dttm AND CDS10.service_tp_code='13'

	LEFT JOIN ckd_day_stats CDS11 ON CDS11.stats_dttm = CDMS.stats_dttm AND CDS11.service_tp_code='15'
	LEFT JOIN ckd_day_stats CDS12 ON CDS12.stats_dttm = CDMS.stats_dttm AND CDS12.service_tp_code='16'
	LEFT JOIN ckd_day_stats CDS13 ON CDS13.stats_dttm = CDMS.stats_dttm AND CDS13.service_tp_code='17'
	LEFT JOIN ckd_day_stats CDS14 ON CDS14.stats_dttm = CDMS.stats_dttm AND CDS14.service_tp_code='18'

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
	LEFT JOIN ocb_day_coupang_stats CSTATS5 ON CSTATS5.stats_dttm = CDMS.stats_dttm AND CSTATS5.service_tp_code='09'
	LEFT JOIN ocb_day_coupang_stats CSTATS6 ON CSTATS6.stats_dttm = CDMS.stats_dttm AND CSTATS6.service_tp_code='02'
	LEFT JOIN ocb_day_coupang_stats CSTATS7 ON CSTATS7.stats_dttm = CDMS.stats_dttm AND CSTATS7.service_tp_code='10'
	LEFT JOIN ocb_day_coupang_stats CSTATS8 ON CSTATS8.stats_dttm = CDMS.stats_dttm AND CSTATS8.service_tp_code='11'

	WHERE CDMS.stats_dttm BETWEEN {$sdate} AND {$edate}
	GROUP BY CDMS.stats_dttm
	ORDER BY CDMS.stats_dttm DESC
";
//pre($sql);
$ret = $NDO->fetch_array($sql);

//전체 누적 설정수
$sql="
	SELECT stats_dttm,activity_num FROM ocb_day_stats
	WHERE stats_dttm >= '20210501' AND stats_dttm <= '{$edate}'
	ORDER BY ocb_day_stats.stats_dttm ASC
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
$make_array1 = array("accumulate","activity_num","use_time","accumulate","activity_num","use_time","use_cnt","usage_rate","use_tot_cnt","useTotCntAvg","ctr");
$make_array2 = array("brand_eprs_num","brand_click_num","dynamic_eprs_num","dynamic_click_num","banner_eprs_num","banner_click_num","kw_click_num","brand_income","banner_income","dynamic_income","kw_income");
$make_array3 = array("mobon","eprs_num","click_num","mobfeed","news_eprs_num","news_click_num","noti_eprs_num","noti_click_num","mobfeed_noti","reward_mobon_eprs_num","reward_mobon_click_num","reward_mobon","reward_mobon_ori");
$make_array4 = array("reward_coupang_eprs_num","reward_coupang_click_num","reward_coupang_income","reward_coupang_ori","reward_thezoom_eprs_num","reward_thezoom_click_num","reward_thezoom_income");
$make_array5 = array("reward_news_eprs_num","reward_news_click_num","reward_news_income","mobimixer_eprs_num","mobimixer_click_num","mobimixer_income","criteo_eprs_num","criteo_click_num","criteo_income","offerwall_use_cnt","offerwall_participation","offerwall_click_num","offerwall_exhs_amt");
$make_array6 = array("mw_eprs_main","mw_click_main","mw_exhs_main","mw_ctr_main","mw_eprs_set","mw_click_set","mw_exhs_set","mw_ctr_set","mw_exhs_main_ori","mw_exhs_set_ori","offerwall_exhs_amt_ori","game_eprs_total","game_click_total","game_exhs_total","game_ctr_total");
$make_array7 = array("gpt_click_num_m","gpt_exhs_amt_m","gpt_click_c","gpt_income","game_eprs_main","game_click_main","game_exhs_main","game_eprs_roulette","game_click_roulette","game_exhs_roulette","game_eprs_ladder","game_click_ladder","game_exhs_ladder","game_eprs_ticket","game_click_ticket","game_exhs_ticket","game_ctr_main","game_ctr_roulette","game_ctr_ladder","game_ctr_ticket");

$make_array = array_merge ($make_array1, $make_array2, $make_array3, $make_array4, $make_array5, $make_array6, $make_array7);
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
	$row['brand_click_num'] = $row['coupang_click'] + $row['ocbkeyicon_click'];
	$row['banner_click_num'] = $row['banner_click'];
	$row['dynamic_click_num'] = $row['dynamic_click'];
	$row['kw_click_num'] = $row['kw_click'];

	# 커미션 100 으로 보여줌
	//쿠팡 수익 계산 (주문 커미션 - 취소 커미션 ) * 0.8
	$commission = 1;
	$brand_income = ($row['coupang_order'] + $row['coupang_cancel'] + $row['ocbkeyicon_order'] + $row['ocbkeyicon_cancel']) * $commission ;
	$banner_income = ($row['banner_order'] + $row['banner_cancel']) * $commission;
	$dynamic_income = ($row['dynamic_order'] + $row['dynamic_cancel']) * $commission;
	$kw_income = ($row['kw_order'] + $row['kw_cancel']) * $commission;
	$reward_coupang_ori = $row['reward_coupang_click_num'] * 10;
	$reward_coupang_income = $row['reward_coupang_exhs_amt'] * $commission;
	$mobimixer_income = round($row['mobimixer_exhs_amt'] * $commission);
	$criteo_income = round($row['criteo_exhs_amt'] * $commission);
	$gpt_income = ($row['gpt_order_c'] + $row['gpt_cancel_c']) * $commission;

	// 모비온 수익
	// 모비온 통계 - RS 80% (키보드앱 정책) OK캐시백 8 : 키보드사업부 2
	$row['mobon']=round($row['exhs_amt'] * $commission);

	// 리워드 광고 (모비온)
	$row['reward_mobon_ori']=$row['reward_mobon_exhs_amt'] * $commission / 0.625;
	$row['reward_mobon']=$row['reward_mobon_click_num'] * 5;

	// 리워드 합산
	$reward_eprs_num = $row['reward_mobon_eprs_num']+$row['reward_coupang_eprs_num'];
	$reward_click_num = $row['reward_mobon_click_num']+$row['reward_coupang_click_num'];
	$reward_income = $row['reward_mobon']+$reward_coupang_ori;

	//CTR AVG
	$ctr = ($row['eprs_num'] > 0) ? number_format($row['click_num'] / $row['eprs_num'] * 100, 1) : 0;

	//오퍼월
	if($row['stats_dttm'] >= "20230420"){
		if($row['stats_dttm'] >= "20230527"){
			if($row['stats_dttm'] >= "20230531"){
				if($row['stats_dttm'] >= "20230601"){
					$offerwall_commission ="0.6";
					if($row['stats_dttm'] >= "20230830"){
						$offerwall_commission = "0.55";
						if($row['stats_dttm'] >= "20240201"){
							$offerwall_commission = "0.85";
						}
					}
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

	$offerwall_exhs_amt=round($row['offerwall_exhs_amt']);
	$row['offerwall_exhs_amt']=round($row['offerwall_exhs_amt'] * $offerwall_commission);

	//모비위드 CTR
	$mw_ctr_main = ($row['mw_eprs_main'] > 0) ? number_format($row['mw_click_main'] / $row['mw_eprs_main'] * 100, 1) : 0;
	$mw_ctr_set = ($row['mw_eprs_set'] > 0) ? number_format($row['mw_click_set'] / $row['mw_eprs_set'] * 100, 1) : 0;

	//모비위드
	if($row['stats_dttm'] >= "20230419"){
		$mw_commission = "0.8";
	}else{
		$mw_commission = "1";
	}
	$mw_exhs_main = round($row['mw_exhs_main']);
	$mw_exhs_set = round($row['mw_exhs_set']);
	$row['mw_exhs_main']=round($row['mw_exhs_main'] * $mw_commission);
	$row['mw_exhs_set']=round($row['mw_exhs_set'] * $mw_commission);

	$game_ctr_main = ($row['game_eprs_main'] > 0) ? number_format($row['game_click_main'] / $row['game_eprs_main'] * 100, 1) : 0;
	$game_ctr_roulette = ($row['game_eprs_roulette'] > 0) ? number_format($row['game_click_roulette'] / $row['game_eprs_roulette'] * 100, 1) : 0;
	$game_ctr_ladder = ($row['game_eprs_ladder'] > 0) ? number_format($row['game_click_ladder'] / $row['game_eprs_ladder'] * 100, 1) : 0;
	$game_ctr_ticket = ($row['game_eprs_ticket'] > 0) ? number_format($row['game_click_ticket'] / $row['game_eprs_ticket'] * 100, 1) : 0;
	$row['game_eprs_total'] = $row['game_eprs_roulette']+$row['game_eprs_ladder']+$row['game_eprs_ticket'];
	$row['game_click_total'] = $row['game_click_roulette']+$row['game_click_ladder']+$row['game_click_ticket'];
	$row['game_exhs_total'] = $row['game_exhs_roulette']+$row['game_exhs_ladder']+$row['game_exhs_ticket'];
	$row['game_ctr_total'] = $game_ctr_roulette+$game_ctr_ladder+$game_ctr_ticket;

	//합계
	$TOTAL['accumulate'] += $accumulate_array[$row['stats_dttm']];//날짜
	$TOTAL['activity_num'] += $row['activity_num'];//누적 설정수
	$TOTAL['use_time'] += $row['use_time'];//신규 설정수
	$TOTAL['use_cnt'] += $row['use_cnt'];//평균 사용자수
	$TOTAL['usage_rate'] += (int)number_format($usage_rate);//평균 사용율
	$TOTAL['use_tot_cnt'] += $row['use_tot_cnt'];//평균 사용시간
	$TOTAL['useTotCntAvg'] += $useTotCntAvg; //평균사용횟수

	//모비위드 CTR
	$TOTAL['mw_ctr_main'] += $mw_ctr_main;
	$TOTAL['mw_ctr_set'] += $mw_ctr_set;

	$TOTAL['mw_eprs_main'] += $row['mw_eprs_main'];
	$TOTAL['mw_click_main'] += $row['mw_click_main'];
	$TOTAL['mw_exhs_main'] += round($row['mw_exhs_main']);
	$TOTAL['mw_exhs_main_ori'] += round($mw_exhs_main);

	$TOTAL['mw_eprs_set'] += $row['mw_eprs_set'];
	$TOTAL['mw_click_set'] += $row['mw_click_set'];
	$TOTAL['mw_exhs_set'] += round($row['mw_exhs_set']);
	$TOTAL['mw_exhs_set_ori'] += round($mw_exhs_set);

	$TOTAL['ctr'] += $ctr;//모비온 클릭율
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

	$TOTAL['mobon'] += round($row['mobon']);
	$TOTAL['eprs_num'] += $row['eprs_num'];
	$TOTAL['click_num'] += $row['click_num'];

	$TOTAL['reward_mobon_eprs_num'] += $row['reward_mobon_eprs_num'];
	$TOTAL['reward_mobon_click_num'] += $row['reward_mobon_click_num'];
	$TOTAL['reward_mobon'] += round($row['reward_mobon']);
	$TOTAL['reward_mobon_ori'] += round($row['reward_mobon_ori']);

	$TOTAL['reward_coupang_eprs_num'] += $row['reward_coupang_eprs_num'];
	$TOTAL['reward_coupang_click_num'] += $row['reward_coupang_click_num'];
	$TOTAL['reward_coupang_income'] += round($reward_coupang_income);
	$TOTAL['reward_coupang_ori'] += round($reward_coupang_ori);

	$TOTAL['mobimixer_eprs_num'] += $row['mobimixer_eprs_num'];
	$TOTAL['mobimixer_click_num'] += $row['mobimixer_click_num'];
	$TOTAL['mobimixer_income'] += round($mobimixer_income);

	$TOTAL['criteo_eprs_num'] += $row['criteo_eprs_num'];
	$TOTAL['criteo_click_num'] += $row['criteo_click_num'];
	$TOTAL['criteo_income'] += round($criteo_income);

	$TOTAL['offerwall_use_cnt'] += $row['offerwall_use_cnt'];
	$TOTAL['offerwall_participation'] += $row['offerwall_participation'];
	$TOTAL['offerwall_click_num'] += $row['offerwall_click_num'];
	$TOTAL['offerwall_exhs_amt'] += round($row['offerwall_exhs_amt']);
	$TOTAL['offerwall_exhs_amt_ori'] += round($offerwall_exhs_amt);

	$TOTAL['gpt_click_num_m'] += $row['gpt_click_num_m'];
	$TOTAL['gpt_exhs_amt_m'] += round($row['gpt_exhs_amt_m']);

	$TOTAL['gpt_click_c'] += $row['gpt_click_c'];
	$TOTAL['gpt_income'] += round($gpt_income);

	$TOTAL['game_eprs_main'] += $row['game_eprs_main'];
	$TOTAL['game_click_main'] += $row['game_click_main'];
	$TOTAL['game_exhs_main'] += round($row['game_exhs_main']);

	$TOTAL['game_eprs_roulette'] += $row['game_eprs_roulette'];
	$TOTAL['game_click_roulette'] += $row['game_click_roulette'];
	$TOTAL['game_exhs_roulette'] += round($row['game_exhs_roulette']);

	$TOTAL['game_eprs_ladder'] += $row['game_eprs_ladder'];
	$TOTAL['game_click_ladder'] += $row['game_click_ladder'];
	$TOTAL['game_exhs_ladder'] += round($row['game_exhs_ladder']);

	$TOTAL['game_eprs_ticket'] += $row['game_eprs_ticket'];
	$TOTAL['game_click_ticket'] += $row['game_click_ticket'];
	$TOTAL['game_exhs_ticket'] += round($row['game_exhs_ticket']);

	//게임존 CTR
	$TOTAL['game_ctr_main'] += $game_ctr_main;
	$TOTAL['game_ctr_roulette'] += $game_ctr_roulette;
	$TOTAL['game_ctr_ladder'] += $game_ctr_ladder;
	$TOTAL['game_ctr_ticket'] += $game_ctr_ticket;

	$game_ctr_total = ($row['game_eprs_total'] > 0) ? number_format($row['game_click_total'] / $row['game_eprs_total'] * 100, 1) : 0;

	$TOTAL['game_eprs_total'] += $row['game_eprs_total'];
	$TOTAL['game_click_total'] += $row['game_click_total'];
	$TOTAL['game_exhs_total'] +=$row['game_exhs_total'];
	$TOTAL['game_ctr_total'] = ($TOTAL['game_eprs_total'] > 0) ? number_format($TOTAL['game_click_total'] / $TOTAL['game_eprs_total'] * 100, 1) : 0;

	if($type=="3M" || $type=="6M"){
		//합계
		$month = substr($row['stats_dttm'],0,6);
		if(!isset($M_TOTAL[$month])){
			$make_array = array_merge ($make_array1, $make_array2, $make_array3, $make_array4, $make_array5, $make_array6, $make_array7);
			foreach($make_array as $item){
				$M_TOTAL[$month][$item] = 0;
			}
		}

		$M_TOTAL[$month]['accumulate'] += $accumulate_array[$row['stats_dttm']];
		$M_TOTAL[$month]['activity_num'] += $row['activity_num'];
		$M_TOTAL[$month]['use_time'] += $row['use_time'];
		$M_TOTAL[$month]['use_cnt'] += $row['use_cnt'];
		$M_TOTAL[$month]['use_tot_cnt'] += $row['use_tot_cnt'];
		$M_TOTAL[$month]['ctr'] += $ctr;

		$M_TOTAL[$month]['mw_eprs_main'] += $row['mw_eprs_main'];
		$M_TOTAL[$month]['mw_click_main'] += $row['mw_click_main'];
		$M_TOTAL[$month]['mw_exhs_main'] += round($row['mw_exhs_main']);
		$M_TOTAL[$month]['mw_exhs_main_ori'] += round($mw_exhs_main);

		$M_TOTAL[$month]['mw_eprs_set'] += $row['mw_eprs_set'];
		$M_TOTAL[$month]['mw_click_set'] += $row['mw_click_set'];
		$M_TOTAL[$month]['mw_exhs_set'] += round($row['mw_exhs_set']);
		$M_TOTAL[$month]['mw_exhs_set_ori'] += round($mw_exhs_set);

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

		$M_TOTAL[$month]['mobon'] += round($row['mobon']);
		$M_TOTAL[$month]['eprs_num'] += $row['eprs_num'];
		$M_TOTAL[$month]['click_num'] += $row['click_num'];

		$M_TOTAL[$month]['reward_mobon_eprs_num'] += $row['reward_mobon_eprs_num'];
		$M_TOTAL[$month]['reward_mobon_click_num'] += $row['reward_mobon_click_num'];
		$M_TOTAL[$month]['reward_mobon'] += round($row['reward_mobon']);
		$M_TOTAL[$month]['reward_mobon_ori'] += round($row['reward_mobon_ori']);

		$M_TOTAL[$month]['reward_coupang_eprs_num'] += $row['reward_coupang_eprs_num'];
		$M_TOTAL[$month]['reward_coupang_click_num'] += $row['reward_coupang_click_num'];
		$M_TOTAL[$month]['reward_coupang_income'] += round($reward_coupang_income);
		$M_TOTAL[$month]['reward_coupang_ori'] += round($reward_coupang_ori);

		$M_TOTAL[$month]['mobimixer_eprs_num'] += $row['mobimixer_eprs_num'];
		$M_TOTAL[$month]['mobimixer_click_num'] += $row['mobimixer_click_num'];
		$M_TOTAL[$month]['mobimixer_income'] += round($mobimixer_income);

		$M_TOTAL[$month]['criteo_eprs_num'] += $row['criteo_eprs_num'];
		$M_TOTAL[$month]['criteo_click_num'] += $row['criteo_click_num'];
		$M_TOTAL[$month]['criteo_income'] += round($criteo_income);

		$M_TOTAL[$month]['offerwall_use_cnt'] += $row['offerwall_use_cnt'];
		$M_TOTAL[$month]['offerwall_participation'] += $row['offerwall_participation'];
		$M_TOTAL[$month]['offerwall_click_num'] += $row['offerwall_click_num'];
		$M_TOTAL[$month]['offerwall_exhs_amt'] += round($row['offerwall_exhs_amt']);
		$M_TOTAL[$month]['offerwall_exhs_amt_ori'] += round($offerwall_exhs_amt);

		$M_TOTAL[$month]['gpt_click_num_m'] += $row['gpt_click_num_m'];
		$M_TOTAL[$month]['gpt_exhs_amt_m'] += round($row['gpt_exhs_amt_m']);

		$M_TOTAL[$month]['gpt_click_c'] += $row['gpt_click_c'];
		$M_TOTAL[$month]['gpt_income'] += round($gpt_income);

		$M_TOTAL[$month]['game_eprs_main'] += $row['game_eprs_main'];
		$M_TOTAL[$month]['game_click_main'] += $row['game_click_main'];
		$M_TOTAL[$month]['game_exhs_main'] += round($row['game_exhs_main']);

		$M_TOTAL[$month]['game_eprs_roulette'] += $row['game_eprs_roulette'];
		$M_TOTAL[$month]['game_click_roulette'] += $row['game_click_roulette'];
		$M_TOTAL[$month]['game_exhs_roulette'] += round($row['game_exhs_roulette']);

		$M_TOTAL[$month]['game_eprs_ladder'] += $row['game_eprs_ladder'];
		$M_TOTAL[$month]['game_click_ladder'] += $row['game_click_ladder'];
		$M_TOTAL[$month]['game_exhs_ladder'] += round($row['game_exhs_ladder']);

		$M_TOTAL[$month]['game_eprs_ticket'] += $row['game_eprs_ticket'];
		$M_TOTAL[$month]['game_click_ticket'] += $row['game_click_ticket'];
		$M_TOTAL[$month]['game_exhs_ticket'] += round($row['game_exhs_ticket']);

		$M_TOTAL[$month]['game_eprs_total'] += $row['game_eprs_total'];
		$M_TOTAL[$month]['game_click_total'] += $row['game_click_total'];
		$M_TOTAL[$month]['game_exhs_total'] += round($row['game_exhs_total']);
		$M_TOTAL[$month]['game_ctr_total'] += $row['game_ctr_total'];

	}

	$css_dynamic_income = $dynamic_income<0 ?"color:red;":"color:blue;";
	$css_brand_income = $brand_income<0 ?"color:red;":"color:blue;";
	$css_banner_income = $banner_income<0 ?"color:red;":"color:blue;";
	$css_kw_income = $kw_income<0 ?"color:red;":"color:blue;";
	$css_reward_mobon_income = $row['reward_mobon']<0 ?"color:red;":"color:blue;";
	$css_reward_coupang_income = $reward_coupang_income<0 ?"color:red;":"color:blue;";
	$css_reward_couppang_income = $row['reward_coupang_exhs_amt']<0 ?"color:red;":"color:blue;";
	$css_thezoom_mobon_income = $row['reward_thezoom_exhs_amt']<0 ?"color:red;":"color:blue;";
	$css_offerwall_exhs_amt = $row['offerwall_exhs_amt']<0 ?"color:red;":"color:blue;";
	$css_mw_main = $row['mw_exhs_main']<0 ?"color:red;":"color:blue;";
	$css_mw_set = $row['mw_exhs_set']<0 ?"color:red;":"color:blue;";


	$total_amt = $row['mobon']+$dynamic_income+$brand_income+$banner_income+$kw_income+$reward_income+$mobimixer_income+$criteo_income+$row['offerwall_exhs_amt']+$row['mw_exhs_main']+$row['mw_exhs_set']+$row['gpt_exhs_amt_m']+$gpt_income+$row['game_exhs_main']+$row['game_exhs_total'];
	$total_amt_ori = $row['mobon']+$dynamic_income+$brand_income+$banner_income+$kw_income+$reward_income+$mobimixer_income+$criteo_income+$offerwall_exhs_amt+$mw_exhs_main+$mw_exhs_set+$row['gpt_exhs_amt_m']+$gpt_income+$row['game_exhs_main']+$row['game_exhs_total'];
	$css_total_income = $total_amt<0 ?"color:red;":"color:blue;";


	$use_cnt = empty($row['use_cnt'])?0:number_format($row['use_cnt']);
	$html .= "<tr class='{$fn->dateColor($row['stats_dttm'])}'>";
	$html .= "
			<td>{$row['stats_dttm']}</td>

			<td>".number_format($accumulate_array[$row['stats_dttm']])."</td>
			<td style='font-weight:700;'>".number_format($row['activity_num'])."</td>
			<td style='color:red;'>".$use_cnt."</td> 

			<td>".number_format($usage_rate)."%</td>
			<td>".$fn->convertTime($useTimeAvg)."</td>
			<td>".number_format($row['use_tot_cnt'])."</td>
			<td>{$useTotCntAvg}</td>

			<!-- 모비위드 메인 -->
			<td>".number_format($row['mw_eprs_main'])."</td>
			<td>".number_format($row['mw_click_main'])."</td>
			<td>".number_format($mw_ctr_main)."%</td>
			<td style='{$css_mw_main} font-weight:700;'>".number_format($row['mw_exhs_main'])."</td>
";
	if($hidden_page == "show"){
		$html .= "<td style='{$css_mw_main} font-weight:700;'>".number_format($mw_exhs_main)."</td>";
	}
	$html .= "
			<!-- 모비위드 설정 -->
			<td>".number_format($row['mw_eprs_set'])."</td>
			<td>".number_format($row['mw_click_set'])."</td>
			<td>".number_format($mw_ctr_set)."%</td>
			<td style='{$css_mw_set} font-weight:700;'>".number_format($row['mw_exhs_set'])."</td>
	";
	if($hidden_page == "show"){
		$html .= "<td style='{$css_mw_main} font-weight:700;'>".number_format($mw_exhs_set)."</td>";
	}
		$html .= "
			<!-- 돈버는 게임존  -->
			<td>".number_format($row['game_eprs_main'])."</td>
			<td>".number_format($row['game_click_main'])."</td>
			<td>".number_format($game_ctr_main)."%</td>
			<td style='color:blue;font-weight:700;'>".number_format($row['game_exhs_main'])."</td>
			
			<!-- 룰렛 -->
			<td class='disp_game1'>".number_format($row['game_eprs_roulette'])."</td>
			<td class='disp_game1'>".number_format($row['game_click_roulette'])."</td>
			<td class='disp_game1'>".number_format($game_ctr_roulette)."%</td>
			<td class='disp_game1' style='color:blue;font-weight:700;'>".number_format($row['game_exhs_roulette'])."</td>
			
			<!-- 사다리 -->
			<td class='disp_game1'>".number_format($row['game_eprs_ladder'])."</td>
			<td class='disp_game1'>".number_format($row['game_click_ladder'])."</td>
			<td class='disp_game1'>".number_format($game_ctr_ladder)."%</td>
			<td class='disp_game1' style='color:blue;font-weight:700;'>".number_format($row['game_exhs_ladder'])."</td>

			<!-- 복권 -->
			<td class='disp_game1'>".number_format($row['game_eprs_ticket'])."</td>
			<td class='disp_game1'>".number_format($row['game_click_ticket'])."</td>
			<td class='disp_game1'>".number_format($game_ctr_ticket)."%</td>
			<td class='disp_game1' style='color:blue;font-weight:700;'>".number_format($row['game_exhs_ticket'])."</td>
			
			<!-- 게임통합 -->
			<td class='disp_game2'>".number_format($row['game_eprs_total'])."</td>
			<td class='disp_game2'>".number_format($row['game_click_total'])."</td>
			<td class='disp_game2'>".number_format($game_ctr_total)."%</td>
			<td class='disp_game2' style='color:blue;font-weight:700;'>".number_format($row['game_exhs_total'])."</td>

			<!-- 모비온 -->
			<td>".number_format($row['eprs_num'])."</td>
			<td>".number_format($row['click_num'])."</td>
			<td>".$ctr."</td>
			<td style='color:blue;font-weight:700;'>".number_format($row['mobon'])."</td>

			<!-- 모비믹서 -->
			<td>".number_format($row['mobimixer_eprs_num'])."</td>
			<td>".number_format($row['mobimixer_click_num'])."</td>
			<td style='color:blue;font-weight:700;'>".number_format($mobimixer_income)."</td>

			<!-- criteo -->
			<td class='show_toggle'>".number_format($row['criteo_eprs_num'])."</td>
			<td class='show_toggle'>".number_format($row['criteo_click_num'])."</td>
			<td class='show_toggle' style='color:blue;font-weight:700;'>".number_format($criteo_income)."</td>
	";
	if($hidden_page == "show"){

		$html .= "
			<!-- 리워드(모비온) 광고 -->
			<td>".number_format($row['reward_mobon_eprs_num'])."</td>
			<td>".number_format($row['reward_mobon_click_num'])."</td>
			<td style='{$css_reward_mobon_income} font-weight:700;'>".number_format($row['reward_mobon_ori'])."</td>
			<td style='{$css_reward_mobon_income} font-weight:700;'>".number_format($row['reward_mobon'])."</td>
			<!-- 리워드(쿠팡) 광고 -->
			<td>".number_format($row['reward_coupang_eprs_num'])."</td>
			<td>".number_format($row['reward_coupang_click_num'])."</td>
			<td style='{$css_reward_couppang_income} font-weight:700;'>".number_format($reward_coupang_income)."</td>
			<td style='{$css_reward_couppang_income} font-weight:700;'>".number_format($reward_coupang_ori)."</td>
		";
	}
	$html .= "
			<!-- 리워드 광고 -->
			<td>".number_format($reward_eprs_num)."</td>
			<td>".number_format($reward_click_num)."</td>
			<td style='{$css_reward_mobon_income} font-weight:700;'>".number_format($reward_income)."</td>

			<!-- 다이나믹 -->
			<td class='show_toggle'>".number_format($row['dynamic_eprs_num'])."</td>
			<td class='show_toggle'>".number_format($row['dynamic_click_num'])."</td>
			<td class='show_toggle' style='{$css_dynamic_income} font-weight:700;'>".number_format($dynamic_income)."</td>

			<!-- 브랜드 -->
			<td>".number_format($row['use_tot_cnt'])."</td>
			<td>".number_format($row['brand_click_num'])."</td>
			<td style='{$css_brand_income} font-weight:700;'>".number_format($brand_income)."</td>

			<!-- 배너 -->
			<td class='show_toggle'>".number_format($row['banner_eprs_num']) ."</td>
			<td class='show_toggle'>".number_format($row['banner_click_num'])."</td>
			<td class='show_toggle' style='{$css_banner_income} font-weight:700;'>".number_format($banner_income)."</td>

			<!-- 키워드 -->
			<td class='show_toggle'>".number_format($row['kw_click_num'])."</td>
			<td class='show_toggle' style='{$css_brand_income} font-weight:700;'>".number_format($kw_income)."</td>

			<!-- 오퍼월 -->
			<td>".number_format($row['offerwall_use_cnt'])."</td>
			<td>".number_format($row['offerwall_click_num'])."</td>
			<td>".number_format($row['offerwall_participation'])."</td>
			<td style='{$css_offerwall_exhs_amt} font-weight:700;'>".number_format($row['offerwall_exhs_amt'])."</td>

	";
	if($hidden_page == "show"){
		$html .= "<td style='{$css_offerwall_exhs_amt} font-weight:700;'>".number_format($offerwall_exhs_amt)."</td>";
	}

		$html .= "
			<!-- GPT 이용권 -->
			<td>".number_format(($row['gpt_click_num_m']+$row['gpt_click_c'])*2)."</td>
			<td>".number_format($row['gpt_click_num_m'])."</td>
			<td style='{$css_offerwall_exhs_amt} font-weight:700;'>".number_format($row['gpt_exhs_amt_m'])."</td>
			<td>".number_format($row['gpt_click_c'])."</td>
			<td style='{$css_offerwall_exhs_amt} font-weight:700;'>".number_format($gpt_income)."</td>

			<!-- 총 수익(원) -->
			<td style='{$css_total_income} font-weight:700;'>".number_format($total_amt)."</td>
		";
	if($hidden_page == "show"){
		$html .= "<td style='{$css_total_income} font-weight:700;'>".number_format($total_amt_ori)."</td>";
	}
	$html .= "
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
$TOTAL['use_cnt'] = $TOTAL['use_cnt'] / ((int)$diff_data+1);

//합계 평균 사용율
$TOTAL['usage_rate'] = $TOTAL['usage_rate'] / ((int)$diff_data+1);

//합계 평균 사용시간
$TOTAL['useAvg'] = ($TOTAL['use_tot_cnt'] > 0) ? round($TOTAL['use_time'] / $TOTAL['use_tot_cnt'], 1) : 0;

//합계 평균 사용횟수
$TOTAL['totCntAvg'] = ($diff_data > 0) ? number_format($TOTAL['useTotCntAvg'] / $diff_data , 1) : 0;

//클릭율
$TOTAL['ctr'] = ($TOTAL['eprs_num'] > 0) ? number_format($TOTAL['click_num'] / $TOTAL['eprs_num'] * 100 , 1) : 0;

//클릭율
$TOTAL['mw_ctr_main'] = ($TOTAL['mw_eprs_main'] > 0) ? number_format($TOTAL['mw_click_main'] / $TOTAL['mw_eprs_main'] * 100, 1) : 0;
$TOTAL['mw_ctr_set'] = ($TOTAL['mw_eprs_set'] > 0) ? number_format($TOTAL['mw_click_set'] / $TOTAL['mw_eprs_set'] * 100, 1) : 0;

$TOTAL['game_ctr_main'] = ($TOTAL['game_eprs_main'] > 0) ? number_format($TOTAL['game_click_main'] / $TOTAL['game_eprs_main'] * 100, 1) : 0;
$TOTAL['game_ctr_roulette'] = ($TOTAL['game_eprs_roulette'] > 0) ? number_format($TOTAL['game_click_roulette'] / $TOTAL['game_eprs_roulette'] * 100, 1) : 0;
$TOTAL['game_ctr_ladder'] = ($TOTAL['game_eprs_ladder'] > 0) ? number_format($TOTAL['game_click_ladder'] / $TOTAL['game_eprs_ladder'] * 100, 1) : 0;
$TOTAL['game_ctr_ticket'] = ($TOTAL['game_eprs_ticket'] > 0) ? number_format($TOTAL['game_click_ticket'] / $TOTAL['game_eprs_ticket'] * 100, 1) : 0;

$reward_eprs_num = $TOTAL['reward_mobon_eprs_num']+$TOTAL['reward_coupang_eprs_num'];
$reward_click_num = $TOTAL['reward_mobon_click_num']+$TOTAL['reward_coupang_click_num'];
$reward_income = $TOTAL['reward_mobon']+$TOTAL['reward_coupang_ori'];



//총수익
$total_amt_all = number_format($TOTAL['mobon']+$TOTAL['dynamic_income']+$TOTAL['brand_income']+$TOTAL['banner_income']+$TOTAL['kw_income']+$reward_income+$TOTAL['mobimixer_income']+$TOTAL['criteo_income']+$TOTAL['offerwall_exhs_amt']+$TOTAL['mw_exhs_main']+$TOTAL['mw_exhs_set']+$TOTAL['gpt_exhs_amt_m']+$TOTAL['gpt_income']+$TOTAL['game_exhs_main']+$TOTAL['game_exhs_total']);
$total_amt_all_ori = number_format($TOTAL['mobon']+$TOTAL['dynamic_income']+$TOTAL['brand_income']+$TOTAL['banner_income']+$TOTAL['kw_income']+$reward_income+$TOTAL['mobimixer_income']+$TOTAL['criteo_income']+$TOTAL['offerwall_exhs_amt_ori']+$TOTAL['mw_exhs_main_ori']+$TOTAL['mw_exhs_set_ori']+$TOTAL['gpt_exhs_amt_m']+$TOTAL['gpt_income']+$TOTAL['game_exhs_main']+$TOTAL['game_exhs_total']);
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
							<?php if($hidden_page == "show"){ ?>
							<div style="display: inline-block;" class="pull-left">
								<button type="button" class="btn btn-danger" style="margin-left:10px;">모비위드 API 재호출(창 띄우기)</button>
							</div>
							<?php } ?>
							<label style="margin: 4px 0 0 40px">
								<div class="pull-left"><input style="width:20px;height:20px;accent-color: darkcyan;" type="checkbox" id='checkbox' onclick="display_toggle();" checked ></div>
								<div class="pull-left" style="padding-top:5px;padding-left:3px;">불필요 항목 비노출</div>
							</label>

							<label style="margin: 4px 0 0 40px">
								<div class="pull-left"><input style="width:20px;height:20px;accent-color: darkcyan;" type="checkbox" id='checkbox2' onclick="display_toggle2();" checked ></div>
								<div class="pull-left" style="padding-top:5px;padding-left:3px;">게임존 통합 노출</div>
							</label>

							<div style="display: inline-block;margin-left: 360px;" class="pull-right">
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
							<th style="width:700px" colspan="8">앱 연동</th>
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

							<th colspan="4">돈버는 게임존
								<a href="#" class="tooltips"><i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
									<span>
										<strong>돈버는 게임존 연동</strong><br />
										(2일전 데이터 매시갱신)
									</span>
								</a>
							</th>

							<th colspan="4" class="disp_game1">룰렛
								<a href="#" class="tooltips"><i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
									<span>
										<strong>룰렛 연동</strong><br />
										(2일전 데이터 매시갱신)
									</span>
								</a>
							</th>

							<th colspan="4" class="disp_game1">사다리
								<a href="#" class="tooltips"><i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
									<span>
										<strong>사다리 연동</strong><br />
										(2일전 데이터 매시갱신)
									</span>
								</a>
							</th>

							<th colspan="4" class="disp_game1">복권
								<a href="#" class="tooltips"><i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
									<span>
										<strong>복권 연동</strong><br />
										(2일전 데이터 매시갱신)
									</span>
								</a>
							</th>

							<th colspan="4" class="disp_game2">게임통합
								<a href="#" class="tooltips"><i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
									<span>
										<strong>게임통합 연동</strong><br />
										(2일전 데이터 매시갱신)
									</span>
								</a>
							</th>

							<th colspan="4">모비온 연동
								<a href="#" class="tooltips"><i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
									<span>
										<strong>모비피드 연동</strong><br />
										(매일 1시간 간격 갱신)
									</span>
								</a>
							</th>
							<th colspan="3">모비믹서 연동
								<a href="#" class="tooltips"><i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
									<span>
										<strong>모비믹서 연동</strong><br />
										(매일 오후1시 17,27,37분 2일전 갱신)
									</span>
								</a>
							</th>
							<th colspan="3" class="disp_criteo">크리테오 연동
								<a href="#" class="tooltips"><i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
									<span>
										<strong>크리테오 연동</strong><br />
										(매일 오전 10시 01,11,21 2일전 갱신)
									</span>
								</a>
							</th>
							<th colspan="<?=$hidden_page == "show"?"17":"3"?>" class="disp_reward">리워드 광고</th>
							<th colspan="3" class="disp_coupang">쿠팡 연동
								<a href="#" class="tooltips"><i class="fa fa-info-circle fa-lg" aria-hidden="true"></i>
									<span>
										<strong>리워드(모비온) 광고</strong><br />
										(기존)쿠팡 데이터 (매일 오후12:50분 전일 데이터 갱신)<br />
										(변경)쿠팡 데이터 (매일 오후04:50분 전일 데이터 갱신)
									</span>
								</a>
							</th>
							<th colspan="<?=$hidden_page == "show"?"5":"4"?>">포미션 연동</th>
							<th colspan="5">GPT 이용권</th>
							<th colspan="<?=$hidden_page == "show"?"2":"1"?>"></th>
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
							<?php if($hidden_page == "show"){?>
							<th style="color:blue;">수익(원)</th>
							<?php }?>

							<th>노출</th>
							<th>클릭</th>
							<th>클릭율(%)</th>
							<th style="color:blue;">수익(원)</th>
							<?php if($hidden_page == "show"){?>
							<th style="color:blue;">수익(원)</th>
							<?php }?>
							<th>노출</th>
							<th>클릭</th>
							<th>클릭율(%)</th>
							<th style="color:blue;">수익(원)</th>

							<th class="disp_game1">노출</th>
							<th class="disp_game1">클릭</th>
							<th class="disp_game1">클릭율(%)</th>
							<th class="disp_game1" style="color:blue;">수익(원)</th>

							<th class="disp_game1">노출</th>
							<th class="disp_game1">클릭</th>
							<th class="disp_game1">클릭율(%)</th>
							<th class="disp_game1" style="color:blue;">수익(원)</th>

							<th class="disp_game1">노출</th>
							<th class="disp_game1">클릭</th>
							<th class="disp_game1">클릭율(%)</th>
							<th class="disp_game1" style="color:blue;">수익(원)</th>

							<th class="disp_game2">노출</th>
							<th class="disp_game2">클릭</th>
							<th class="disp_game2">클릭율(%)</th>
							<th class="disp_game2" style="color:blue;">수익(원)</th>


							<th>광고노출</th>
							<th>광고클릭</th>
							<th>클릭율(%)</th>
							<th style='color:blue;'>수익(원)</th>

							<!-- 모비믹서 -->
							<th>모비믹서 노출</th>
							<th>모비믹서 클릭</th>
							<th style='color:blue;'>모비믹서 수익(원)</th>

							<!-- criteo -->
							<th class="show_toggle">criteo 노출</th>
							<th class="show_toggle">criteo 클릭</th>
							<th class="show_toggle" style='color:blue;'>criteo 수익(원)</th>

						<?php if($hidden_page == "show"){?>
							<th>리워드(모비온) 광고<br />노출</th>
							<th>리워드(모비온) 광고<br />클릭 수</th>
							<th style="color:blue;">리워드(모비온) 광고<br />소진</th>
							<th style="color:blue;">리워드(모비온) 광고<br />정산</th>

							<th>리워드(쿠팡) 광고<br />노출</th>
							<th>리워드(쿠팡) 광고<br />클릭 수</th>
							<th style="color:blue;">리워드(쿠팡) 광고<br />수익(원)</th>
							<th style="color:blue;">리워드(쿠팡) 광고<br />정산</th>
						<?php }?>
							<th>리워드 광고<br />노출</th>
							<th>리워드 광고<br />클릭 수</th>
							<th style="color:blue;">리워드 광고<br />수익(원)</th>

							<th class="show_toggle">다이나믹 광고<br />노출</th>
							<th class="show_toggle">다이나믹 광고<br />클릭 수</th>
							<th class="show_toggle" style="color:blue;">다이나믹 광고<br />수익(원)</th>

							<th>브랜드 광고<br />노출</th>
							<th>브랜드 광고<br />클릭 수</th>
							<th style="color:blue;">브랜드 광고<br />수익(원)</th>

							<th class="show_toggle">배너광고<br />노출</th>
							<th class="show_toggle">배너광고<br />클릭 수</th>
							<th class="show_toggle" style="color:blue;">배너광고<br />수익(원)</th>

							<th class="show_toggle">키워드 광고<br /> 클릭</th>
							<th class="show_toggle" style="color:blue;">키워드 광고<br />수익(원)</th>

							<th>오퍼월<br />사용자 수</th>
							<th>오퍼월<br />클릭 수</th>
							<th>오퍼월<br />참여 수</th>
							<th style="color:blue;">오퍼월<br />수익(원)</th>
							<?php if($hidden_page == "show"){?>
								<th style="color:blue;">오퍼월<br />수익(원)</th>
							<?php }?>

							<th>이용권<br />발행수</th>
							<th>모비온광고<br />클릭수</th>
							<th style="color:blue;">모비온광고<br />수익(원)</th>
							<th>쿠팡광고<br />클릭수</th>
							<th style="color:blue;">쿠팡광고<br />수익(원)</th>

							<th style="color:blue;">총 수익(원)</th>
							<?php if($hidden_page == "show"){?>
							<th style="color:blue;">총 수익(원)</th>
							<?php }?>
						</tr>
						</thead>
						<tbody id="ocb" >
						<tr class="" style="background-color: #F2F5A9;">
							<td style="width:85px">합계</td>
							<td style="width:85px"></td>
							<td style='width:100px;font-weight:700;'><?=number_format($TOTAL['activity_num'])?></td>
							<td style="width:110px;color:red;"><?=number_format($TOTAL['use_cnt'])?></td>
							<td style="width:85px"><?=number_format($TOTAL['usage_rate'])?>%</td>
							<td style="width:90px"><?=$fn->convertTime($TOTAL['useAvg'],1)?></td>
							<td style="width:100px"><?=number_format($TOTAL['use_tot_cnt'])?></td>
							<td style="width:95px"><?=number_format($TOTAL['totCntAvg'],1)?></td>

							<!-- 메인띠베너 -->
							<td><?=number_format($TOTAL['mw_eprs_main'])?></td>
							<td><?=number_format($TOTAL['mw_click_main'])?></td>
							<td><?=number_format($TOTAL['mw_ctr_main'])?>%</td>
							<td style="color:blue;font-weight:700;"><?=number_format($TOTAL['mw_exhs_main'])?></td>
							<?php if($hidden_page == "show"){?>
							<td style="color:blue;font-weight:700;"><?=number_format($TOTAL['mw_exhs_main_ori'])?></td>
							<?php }?>

							<!-- 설정띠베너 -->
							<td><?=number_format($TOTAL['mw_eprs_set'])?></td>
							<td><?=number_format($TOTAL['mw_click_set'])?></td>
							<td><?=number_format($TOTAL['mw_ctr_set'])?>%</td>
							<td style="color:blue;font-weight:700;"><?=number_format($TOTAL['mw_exhs_set'])?></td>
							<?php if($hidden_page == "show"){?>
							<td style="color:blue;font-weight:700;"><?=number_format($TOTAL['mw_exhs_set_ori'])?></td>
							<?php }?>


							<!-- 게임존 메인 -->
							<td><?=number_format($TOTAL['game_eprs_main'])?></td>
							<td><?=number_format($TOTAL['game_click_main'])?></td>
							<td><?=number_format($TOTAL['game_ctr_main'])?>%</td>
							<td style='color:blue;font-weight:700;'><?=number_format($TOTAL['game_exhs_main'])?></td>

							<!-- 룰렛 -->
							<td class="disp_game1"><?=number_format($TOTAL['game_eprs_roulette'])?></td>
							<td class="disp_game1"><?=number_format($TOTAL['game_click_roulette'])?></td>
							<td class="disp_game1"><?=number_format($TOTAL['game_ctr_roulette'])?>%</td>
							<td class="disp_game1" style='color:blue;font-weight:700;'><?=number_format($TOTAL['game_exhs_roulette'])?></td>

							<!-- 사다리 -->
							<td class="disp_game1"><?=number_format($TOTAL['game_eprs_ladder'])?></td>
							<td class="disp_game1"><?=number_format($TOTAL['game_click_ladder'])?></td>
							<td class="disp_game1"><?=number_format($TOTAL['game_ctr_ladder'])?>%</td>
							<td class="disp_game1" style='color:blue;font-weight:700;'><?=number_format($TOTAL['game_exhs_ladder'])?></td>

							<!-- 복권 -->
							<td class="disp_game1"><?=number_format($TOTAL['game_eprs_ticket'])?></td>
							<td class="disp_game1"><?=number_format($TOTAL['game_click_ticket'])?></td>
							<td class="disp_game1"><?=number_format($TOTAL['game_ctr_ticket'])?>%</td>
							<td class="disp_game1" style='color:blue;font-weight:700;'><?=number_format($TOTAL['game_exhs_ticket'])?></td>

							<!-- 게임 통합 -->
							<td class="disp_game2"><?=number_format($TOTAL['game_eprs_total'])?></td>
							<td class="disp_game2"><?=number_format($TOTAL['game_click_total'])?></td>
							<td class="disp_game2"><?=number_format($TOTAL['game_ctr_total'])?>%</td>
							<td class="disp_game2" style='color:blue;font-weight:700;'><?=number_format($TOTAL['game_exhs_total'])?></td>


							<!-- 모비온 -->
							<td><?=number_format($TOTAL['eprs_num'])?></td>
							<td><?=number_format($TOTAL['click_num'])?></td>
							<td><?=$TOTAL['ctr']?></td>
							<td style='color:blue;font-weight:700;'><?=number_format($TOTAL['mobon'])?></td>

							<!-- 모비믹서 -->
							<td><?=number_format($TOTAL['mobimixer_eprs_num'])?></td>
							<td><?=number_format($TOTAL['mobimixer_click_num'])?></td>
							<td style='color:blue;font-weight:700;'><?=number_format($TOTAL['mobimixer_income'])?></td>

							<!-- criteo -->
							<td class="show_toggle"><?=number_format($TOTAL['criteo_eprs_num'])?></td>
							<td class="show_toggle"><?=number_format($TOTAL['criteo_click_num'])?></td>
							<td class="show_toggle" style='color:blue;font-weight:700;'><?=number_format($TOTAL['criteo_income'])?></td>

						<?php if($hidden_page == "show"){?>
							<!-- 리워드(모비온) -->
							<td><?=number_format($TOTAL['reward_mobon_eprs_num'])?></td>
							<td><?=number_format($TOTAL['reward_mobon_click_num'])?></td>
							<td style="color:blue;font-weight:700;"><?=number_format($TOTAL['reward_mobon_ori'])?></td>
							<td style="color:blue;font-weight:700;"><?=number_format($TOTAL['reward_mobon'])?></td>
							<!-- 리워드(쿠팡) -->
							<td><?=number_format($TOTAL['reward_coupang_eprs_num'])?></td>
							<td><?=number_format($TOTAL['reward_coupang_click_num'])?></td>
							<td style="color:blue;font-weight:700;"><?=number_format($TOTAL['reward_coupang_income'])?></td>
							<td style="color:blue;font-weight:700;"><?=number_format($TOTAL['reward_coupang_ori'])?></td>
						<?php }?>
							<!-- 리워드 -->
							<td><?=number_format($reward_eprs_num)?></td>
							<td><?=number_format($reward_click_num)?></td>
							<td style="color:blue;font-weight:700;"><?=number_format($reward_income)?></td>

							<!-- 다이나믹 -->
							<td class="show_toggle"><?=number_format($TOTAL['dynamic_eprs_num'])?></td>
							<td class="show_toggle"><?=number_format($TOTAL['dynamic_click_num'])?></td>
							<td class="show_toggle" style="color:blue;font-weight:700;"><?=number_format($TOTAL['dynamic_income'])?></td>

							<!-- 브랜드 -->
							<td><?=number_format($TOTAL['use_tot_cnt'])?></td>
							<td><?=number_format($TOTAL['brand_click_num'])?></td>
							<td style="color:blue;font-weight:700;"><?=number_format($TOTAL['brand_income'])?></td>

							<!-- 배너 -->
							<td class="show_toggle"><?=number_format($TOTAL['banner_eprs_num'])?></td>
							<td class="show_toggle"><?=number_format($TOTAL['banner_click_num'])?></td>
							<td class="show_toggle" style="color:blue;font-weight:700;"><?=number_format($TOTAL['banner_income'])?></td>

							<!-- 키워드 -->
							<td class="show_toggle"><?=number_format($TOTAL['kw_click_num'])?></td>
							<td class="show_toggle" style="color:blue;font-weight:700;"><?=number_format($TOTAL['kw_income'])?></td>

							<!-- 오퍼월 -->
							<td><?=number_format($TOTAL['offerwall_use_cnt'])?></td>
							<td><?=number_format($TOTAL['offerwall_click_num'])?></td>
							<td><?=number_format($TOTAL['offerwall_participation'])?></td>
							<td style="color:blue;font-weight:700;"><?=number_format($TOTAL['offerwall_exhs_amt'])?></td>
							<?php if($hidden_page == "show"){?>
							<td style="color:blue;font-weight:700;"><?=number_format($TOTAL['offerwall_exhs_amt_ori'])?></td>
							<?php } ?>

							<!-- GPT 이용권 -->
							<td><?=number_format(($TOTAL['gpt_click_num_m']+$TOTAL['gpt_click_c'])*2)?></td>
							<td><?=number_format($TOTAL['gpt_click_num_m'])?></td>
							<td style="color:blue;font-weight:700;"><?=number_format($TOTAL['gpt_exhs_amt_m'])?></td>
							<td><?=number_format($TOTAL['gpt_click_c'])?></td>
							<td style="color:blue;font-weight:700;"><?=number_format($TOTAL['gpt_income'])?></td>

							<td style="color:blue;font-weight:700;"><?=$total_amt_all?></td>
							<?php if($hidden_page == "show"){?>
								<td style="color:blue;font-weight:700;"><?=$total_amt_all_ori?></td>
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

							//클릭율
							$row['ctr'] = ($row['eprs_num'] > 0) ? number_format($row['click_num'] / $row['eprs_num'] * 100 , 1) : 0;

							$reward_eprs_num = $row['reward_mobon_eprs_num']+$row['reward_coupang_eprs_num'];
							$reward_click_num = $row['reward_mobon_click_num']+$row['reward_coupang_click_num'];
							$reward_income = $row['reward_mobon']+$row['reward_coupang_ori'];
							$gpt_income = $row['gpt_income'];

							//모비위드 클릭율
							$row['mw_ctr_main'] = ($row['mw_eprs_main'] > 0) ? number_format($row['mw_click_main'] / $row['mw_eprs_main'] * 100 , 1) : 0;
							$row['mw_ctr_set'] = ($row['mw_eprs_set'] > 0) ? number_format($row['mw_click_set'] / $row['mw_eprs_set'] * 100 , 1) : 0;

							//게임존 클릭율
							$row['game_ctr_main'] = ($row['game_eprs_main'] > 0) ? number_format($row['game_click_main'] / $row['game_eprs_main'] * 100 , 1) : 0;
							$row['game_ctr_roulette'] = ($row['game_eprs_roulette'] > 0) ? number_format($row['game_click_roulette'] / $row['game_eprs_roulette'] * 100 , 1) : 0;
							$row['game_ctr_ladder'] = ($row['game_eprs_ladder'] > 0) ? number_format($row['game_click_ladder'] / $row['game_eprs_ladder'] * 100 , 1) : 0;
							$row['game_ctr_ticket'] = ($row['game_eprs_ticket'] > 0) ? number_format($row['game_click_ticket'] / $row['game_eprs_ticket'] * 100 , 1) : 0;
							$row['game_ctr_total'] = ($row['game_eprs_total'] > 0) ? number_format($row['game_click_total'] / $row['game_eprs_total'] * 100 , 1) : 0;

							$month_total = $row['mw_exhs_main']+$row['mw_exhs_set']+$row['mobon']+$row['mobimixer_income']+$row['criteo_income']+$reward_income+$row['dynamic_income']+$row['brand_income']+$row['banner_income']+$row['kw_income']+$row['offerwall_exhs_amt']+$row['gpt_exhs_amt_m']+$gpt_income+$row['game_exhs_main']+$row['game_exhs_total'];
							$month_total_ori = $row['mw_exhs_main_ori']+$row['mw_exhs_set_ori']+$row['mobon']+$row['mobimixer_income']+$row['criteo_income']+$reward_income+$row['dynamic_income']+$row['brand_income']+$row['banner_income']+$row['kw_income']+$row['offerwall_exhs_amt_ori']+$row['gpt_exhs_amt_m']+$gpt_income+$row['game_exhs_main']+$row['game_exhs_total'];
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

							<!-- 메인띠베너 -->
							<td><?=number_format($row['mw_eprs_main'])?></td>
							<td><?=number_format($row['mw_click_main'])?></td>
							<td><?=number_format($row['mw_ctr_main'])?>%</td>
							<td style="color:blue;font-weight:700;"><?=number_format($row['mw_exhs_main'])?></td>
							<?php if($hidden_page == "show"){?>
							<td style="color:blue;font-weight:700;"><?=number_format($row['mw_exhs_main_ori'])?></td>
							<?php } ?>

							<!-- 설정띠베너 -->
							<td><?=number_format($row['mw_eprs_set'])?></td>
							<td><?=number_format($row['mw_click_set'])?></td>
							<td><?=number_format($row['mw_ctr_set'])?>%</td>
							<td style="color:blue;font-weight:700;"><?=number_format($row['mw_exhs_set'])?></td>
							<?php if($hidden_page == "show"){?>
							<td style="color:blue;font-weight:700;"><?=number_format($row['mw_exhs_set_ori'])?></td>
							<?php } ?>

							<!-- 게임존 메인 -->
							<td><?=number_format($row['game_eprs_main'])?></td>
							<td><?=number_format($row['game_click_main'])?></td>
							<td><?=number_format($row['game_ctr_main'])?>%</td>
							<td style='color:blue;font-weight:700;'><?=number_format($row['game_exhs_main'])?></td>

							<!-- 룰렛 -->
							<td class="disp_game1"><?=number_format($row['game_eprs_roulette'])?></td>
							<td class="disp_game1"><?=number_format($row['game_click_roulette'])?></td>
							<td class="disp_game1"><?=number_format($row['game_ctr_roulette'])?>%</td>
							<td class="disp_game1" style='color:blue;font-weight:700;'><?=number_format($row['game_exhs_roulette'])?></td>

							<!-- 사다리 -->
							<td class="disp_game1"><?=number_format($row['game_eprs_ladder'])?></td>
							<td class="disp_game1"><?=number_format($row['game_click_ladder'])?></td>
							<td class="disp_game1"><?=number_format($row['game_ctr_ladder'])?>%</td>
							<td class="disp_game1" style='color:blue;font-weight:700;'><?=number_format($row['game_exhs_ladder'])?></td>

							<!-- 복권 -->
							<td class="disp_game1"><?=number_format($row['game_eprs_ticket'])?></td>
							<td class="disp_game1"><?=number_format($row['game_click_ticket'])?></td>
							<td class="disp_game1"><?=number_format($row['game_ctr_ticket'])?>%</td>
							<td class="disp_game1" style='color:blue;font-weight:700;'><?=number_format($row['game_exhs_ticket'])?></td>

							<!-- 게임 통합 -->
							<td class="disp_game2"><?=number_format($row['game_eprs_total'])?></td>
							<td class="disp_game2"><?=number_format($row['game_click_total'])?></td>
							<td class="disp_game2"><?=number_format($row['game_ctr_total'])?>%</td>
							<td class="disp_game2" style='color:blue;font-weight:700;'><?=number_format($row['game_exhs_total'])?></td>

							<!-- 모비온 -->
							<td><?=number_format($row['eprs_num'])?></td>
							<td><?=number_format($row['click_num'])?></td>
							<td><?=number_format($row['ctr'])?>%</td>
							<td style='color:blue;font-weight:700;'><?=number_format($row['mobon'])?></td>

							<!-- 모비믹서 -->
							<td><?=number_format($row['mobimixer_eprs_num'])?></td>
							<td><?=number_format($row['mobimixer_click_num'])?></td>
							<td style='color:blue;font-weight:700;'><?=number_format($row['mobimixer_income'])?></td>

							<!-- criteo -->
							<td class="show_toggle"><?=number_format($row['criteo_eprs_num'])?></td>
							<td class="show_toggle"><?=number_format($row['criteo_click_num'])?></td>
							<td class="show_toggle" style='color:blue;font-weight:700;'><?=number_format($row['criteo_income'])?></td>

						<?php if($hidden_page == "show"){?>
							<!-- 리워드(모비온) -->
							<td><?=number_format($row['reward_mobon_eprs_num'])?></td>
							<td><?=number_format($row['reward_mobon_click_num'])?></td>
							<td style="color:blue;font-weight:700;"><?=number_format($row['reward_mobon_ori'])?></td>
							<td style="color:blue;font-weight:700;"><?=number_format($row['reward_mobon'])?></td>
							<!-- 리워드(쿠팡) -->
							<td><?=number_format($row['reward_coupang_eprs_num'])?></td>
							<td><?=number_format($row['reward_coupang_click_num'])?></td>
							<td style="color:blue;font-weight:700;"><?=number_format($row['reward_coupang_income'])?></td>
							<td style="color:blue;font-weight:700;"><?=number_format($row['reward_coupang_ori'])?></td>
						<?php }?>
							<!-- 리워드 -->
							<td><?=number_format($reward_eprs_num)?></td>
							<td><?=number_format($reward_click_num)?></td>
							<td style="color:blue;font-weight:700;"><?=number_format($reward_income)?></td>

							<!-- 다이나믹 -->
							<td class="show_toggle"><?=number_format($row['dynamic_eprs_num'])?></td>
							<td class="show_toggle"><?=number_format($row['dynamic_click_num'])?></td>
							<td class="show_toggle" style="color:blue;font-weight:700;"><?=number_format($row['dynamic_income'])?></td>

							<!-- 브랜드 -->
							<td><?=number_format($row['use_tot_cnt'])?></td>
							<td><?=number_format($row['brand_click_num'])?></td>
							<td style="color:blue;font-weight:700;"><?=number_format($row['brand_income'])?></td>

							<!-- 배너 -->
							<td class="show_toggle"><?=number_format($row['banner_eprs_num'])?></td>
							<td class="show_toggle"><?=number_format($row['banner_click_num'])?></td>
							<td class="show_toggle" style="color:blue;font-weight:700;"><?=number_format($row['banner_income'])?></td>

							<!-- 키워드 -->
							<td class="show_toggle"><?=number_format($row['kw_click_num'])?></td>
							<td class="show_toggle" style="color:blue;font-weight:700;"><?=number_format($row['kw_income'])?></td>

							<!-- 오퍼월 -->
							<td><?=number_format($row['offerwall_use_cnt'])?></td>
							<td><?=number_format($row['offerwall_click_num'])?></td>
							<td><?=number_format($row['offerwall_participation'])?></td>
							<td style="color:blue;font-weight:700;"><?=number_format($row['offerwall_exhs_amt'])?></td>
							<?php if($hidden_page == "show"){?>
								<td style="color:blue;font-weight:700;"><?=number_format($row['offerwall_exhs_amt_ori'])?></td>
							<?php } ?>

							<!-- GPT 이용권 -->
							<td><?=number_format(($row['gpt_click_num_m']+$row['gpt_click_c'])*2)?></td>
							<td><?=number_format($row['gpt_click_num_m'])?></td>
							<td style="color:blue;font-weight:700;"><?=number_format($row['gpt_exhs_amt_m'])?></td>
							<td><?=number_format($row['gpt_click_c'])?></td>
							<td style="color:blue;font-weight:700;"><?=number_format($gpt_income)?></td>

							<td style="color:blue;font-weight:700;"><?=number_format($month_total)?></td>
							<?php if($hidden_page == "show"){?>
								<td style="color:blue;font-weight:700;"><?=number_format($month_total_ori)?></td>
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
					<th>사용율 (%)</th>\
					<th>평균<br />사용시간</th>\
					<th>총 사용횟수</th>\
					<th>평균 사용횟수</th>\
					<th>메인띠배너<br />노출</th>\
					<th>메인띠배너<br />클릭</th>\
					<th>메인띠배너<br />클릭율(%)</th>\
					<th style='color:blue;'>메인띠배너<br />수익(원)</th>\
					";
		<?php if($hidden_page == "show"){?>
		tab_text = tab_text + "<th style='color:blue;'>메인띠배너<br />수익(원)</th>";
		<?php } ?>

		tab_text = tab_text + "<th>설정띠배너<br />노출</th>\
					<th>설정띠배너<br />클릭</th>\
					<th>설정띠배너<br />클릭율(%)</th>\
					<th style='color:blue;'>설정띠배너<br />수익(원)</th>";
		<?php if($hidden_page == "show"){?>
		tab_text = tab_text + "<th style='color:blue;'>설정띠배너<br />수익(원)</th>";
		<?php } ?>

		tab_text = tab_text + "<th>게임존메인<br />노출</th>\
					<th>게임존메인<br />클릭</th>\
					<th>게임존메인<br />클릭율(%)</th>\
					<th style='color:blue;'>게임존메인<br />수익(원)</th>\
					<th>룰렛<br />노출</th>\
					<th>룰렛<br />클릭</th>\
					<th>룰렛<br />클릭율(%)</th>\
					<th style='color:blue;'>룰렛<br />수익(원)</th>\
					<th>사다리<br />노출</th>\
					<th>사다리<br />클릭</th>\
					<th>사다리<br />클릭율(%)</th>\
					<th style='color:blue;'>사다리<br />수익(원)</th>\
					<th>복권<br />노출</th>\
					<th>복권<br />클릭</th>\
					<th>복권<br />클릭율(%)</th>\
					<th style='color:blue;'>복권<br />수익(원)</th>\
					<th>게임존통합<br />노출</th>\
					<th>게임존통합<br />클릭</th>\
					<th>게임존통합<br />클릭율(%)</th>\
					<th style='color:blue;'>게임존통합<br />수익(원)</th>\
					<th>광고노출</th>\
					<th>광고클릭</th>\
					<th>클릭율(%)</th>\
					<th style='color:blue;'>수익(원)</th>\
					<th>모비믹서<br />노출</th>\
					<th>모비믹서<br />클릭 수</th>\
					<th style='color:blue;'>모비믹서<br />수익(원)</th>\
					<th>criteo<br />노출</th>\
					<th>criteo<br />클릭 수</th>\
					<th style='color:blue;'>criteo<br />수익(원)</th>";
				<?php if($hidden_page == "show"){?>
				tab_text = tab_text + "<th>리워드(모비온) 광고<br />노출</th>\
					<th>리워드(모비온) 광고<br />클릭 수</th>\
					<th style='color:blue;'>리워드(모비온) 광고<br />소진</th>\
					<th style='color:blue;'>리워드(모비온) 광고<br />정산</th>\
					<th>리워드(쿠팡) 광고<br />노출</th>\
					<th>리워드(쿠팡) 광고<br />클릭 수</th>\
					<th style='color:blue;'>리워드(쿠팡) 광고<br />수익(원)</th>\
					<th style='color:blue;'>리워드(쿠팡) 광고<br />정산</th>";
				<?php }?>
				tab_text = tab_text + "<th>리워드 광고<br />노출</th>\
					<th>리워드 광고<br />클릭 수</th>\
					<th style='color:blue;'>리워드 광고<br />수익(원)</th>\
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
					<th>오퍼월<br />사용자 수</th>\
					<th>오퍼월<br />클릭 수</th>\
					<th>오퍼월<br />참여 수</th>\
					<th style='color:blue;'>오퍼월<br />수익(원)</th>";
		<?php if($hidden_page == "show"){?>
		tab_text = tab_text + "<th style='color:blue;'>오퍼월<br />수익(원)</th>";
		<?php } ?>

		tab_text = tab_text + "\
					<th>GPT 이용권<br />발행수</th>\
					<th>GPT 모비온<br />클릭 수</th>\
					<th>GPT 모비온 <br />수익(원)</th>\
					<th>GPT 쿠팡<br />클릭 수</th>\
					<th>GPT 쿠팡<br />수익(원)</th>\
					\
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

	//외부 통게 데이터 show/hide
	function show_display_partner(stats_dttm){
		var currentRow = $("."+stats_dttm).closest('tr');
		if(currentRow.is(":visible")){
			currentRow.hide();
		} else {
			currentRow.show();
		}
	}

	function display_toggle(){
		$(".show_toggle").toggle();
		if($('#checkbox').prop('checked')){
			$(".disp_criteo").hide();//크리테오
			$(".disp_reward").attr("colspan",'<?=$hidden_page == "show"?"11":"3"?>');//리워드 광고
			$(".disp_coupang").attr("colspan",3);//쿠팡
		}else{
			$(".disp_criteo").show();
			$(".disp_reward").attr("colspan",'<?=$hidden_page == "show"?"14":"3"?>');
			$(".disp_coupang").attr("colspan",8);

		}
		table_resize();
	}
	function display_toggle2(){
		if($('#checkbox2').prop('checked')){
			$(".disp_game1").hide();//게임존(개별)
			$(".disp_game2").show();//게임존(통합)
		}else {
			$(".disp_game1").show();//게임존(개별)
			$(".disp_game2").hide();//게임존(통합)
		}
		table_resize();
	}

	function table_resize(){
		if(($('#checkbox').prop('checked'))){
			if(($('#checkbox2').prop('checked'))){
				$("section").css({"min-width":"6000px"});
			}else{
				$("section").css({"min-width":"6500px"});
			}

		}else{
			if(($('#checkbox2').prop('checked'))){
				$("section").css({"min-width":"8040px"});
			}else{
				$("section").css({"min-width":"8500px"});
			}
		}
	}

	display_toggle();
	display_toggle2();


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
	})
</script>

<?php
include __foot__;
?>
