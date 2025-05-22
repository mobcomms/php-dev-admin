<?php
/**********************
 *
 *    통계 페이지 / 일자별 통계
 *
 **********************/

include "./var.php";
include_once __func__;
include_once __pdoDB__;    ## DB Instance 생성

//error_reporting(E_ALL & ~E_NOTICE);
//ini_set("display_errors", 1);

## 환경설정
define('_title_', 'CPC 단가 현황');
define('_Menu_', 'manage');
define('_subMenu_', 'statistics_cpc');

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

// 통계 데이터
$sql="
    SELECT
        CDMS.stats_dttm

        ,IFNULL(OCB_MAIN.click_num, 0) AS click_main_ocb
        ,IFNULL(OCB_MAIN.exhs_amt, 0) AS exhs_main_ocb
        ,IFNULL(OCB_SET.click_num, 0) AS click_set_ocb
        ,IFNULL(OCB_SET.exhs_amt, 0) AS exhs_set_ocb

        ,IFNULL(CDS_HANA_SDK1.eprs_num, 0) + IFNULL(CDS_HANA_SDK2.eprs_num, 0) + IFNULL(CDS_HANA_SDK3.eprs_num, 0) + IFNULL(CDS_HANA_SDK4.eprs_num, 0) AS eprs_hana
        ,IFNULL(CDS_HANA_SDK1.exhs_amt, 0) + IFNULL(CDS_HANA_SDK2.exhs_amt, 0) + IFNULL(CDS_HANA_SDK3.exhs_amt, 0) + IFNULL(CDS_HANA_SDK4.exhs_amt, 0) AS exhs_hana

        ,IFNULL(PCDS1.exhs_amt, 0) + IFNULL(PCDS21.exhs_amt, 0) + IFNULL(PCDS2.exhs_amt, 0) + IFNULL(PCDS22.exhs_amt, 0) + IFNULL(PCDS3.exhs_amt, 0) + IFNULL(PCDS23.exhs_amt, 0) AS exhs_ad_aos_paybooc
        ,IFNULL(PCDS4.exhs_amt, 0) + IFNULL(PCDS24.exhs_amt, 0) + IFNULL(PCDS5.exhs_amt, 0) + IFNULL(PCDS25.exhs_amt, 0) + IFNULL(PCDS6.exhs_amt, 0) + IFNULL(PCDS26.exhs_amt, 0) AS exhs_ad_ios_paybooc
        ,IFNULL(PCDS11.click_num, 0) + IFNULL(PCDS31.click_num, 0) + IFNULL(PCDS12.click_num, 0) + IFNULL(PCDS32.click_num, 0) + IFNULL(PCDS13.click_num, 0) + IFNULL(PCDS33.click_num, 0) AS click_ad_aos_paybooc
        ,IFNULL(PCDS14.click_num, 0) + IFNULL(PCDS34.click_num, 0) + IFNULL(PCDS15.click_num, 0) + IFNULL(PCDS35.click_num, 0) + IFNULL(PCDS16.click_num, 0) + IFNULL(PCDS36.click_num, 0) AS click_ad_ios_paybooc
        ,IFNULL(PCSTATS3.click_num, 0) AS coupang_click_num_paybooc1
        ,IFNULL(PCSTATS1.order_commission, 0) + IFNULL(PCSTATS1.cancel_commission, 0) AS coupang_order_paybooc1
        ,IFNULL(PCSTATS4.click_num, 0) AS coupang_click_num_paybooc2
        ,IFNULL(PCSTATS2.order_commission, 0) + IFNULL(PCSTATS2.cancel_commission, 0) AS coupang_order_paybooc2

        ,IFNULL(SCDS1.exhs_amt, 0) + IFNULL(SCDS2.exhs_amt, 0) + IFNULL(SCSTATS2.order_commission, 0) + IFNULL(SCSTATS2.cancel_commission, 0) + IFNULL(SCSTATS4.order_commission, 0) + IFNULL(SCSTATS4.cancel_commission, 0) AS exhs_point_shinhan
        ,IFNULL(SCDS3.exhs_amt, 0) + IFNULL(SCDS4.exhs_amt, 0) + IFNULL(SCSTATS1.order_commission, 0) + IFNULL(SCSTATS1.cancel_commission, 0) + IFNULL(SCSTATS3.order_commission, 0) + IFNULL(SCSTATS3.cancel_commission, 0) +IFNULL(SCDS5.exhs_amt, 0) + IFNULL(SCDS6.exhs_amt, 0) AS exhs_ladder_shinhan
        ,IFNULL(SCSP_AOS.spot_point_cnt1, 0) + IFNULL(SCSP_IOS.spot_point_cnt2, 0) AS point_cnt_shinhan
        ,IFNULL(SCSP_AOS.spot_point_cnt3, 0) + IFNULL(SCSP_IOS.spot_point_cnt4, 0) AS ladder_cnt_shinhan


    FROM hana.ckd_day_app_stats CDMS

    LEFT JOIN cashkeyboard.ckd_day_stats OCB_MAIN ON OCB_MAIN.stats_dttm = CDMS.stats_dttm AND OCB_MAIN.service_tp_code='11'
    LEFT JOIN cashkeyboard.ckd_day_stats OCB_SET ON OCB_SET.stats_dttm = CDMS.stats_dttm AND OCB_SET.service_tp_code='12'

    LEFT JOIN hana.ckd_day_ad_stats CDS_HANA_SDK1 ON CDS_HANA_SDK1.stats_dttm = CDMS.stats_dttm AND CDS_HANA_SDK1.service_tp_code='10'
    LEFT JOIN hana.ckd_day_ad_stats CDS_HANA_SDK2 ON CDS_HANA_SDK2.stats_dttm = CDMS.stats_dttm AND CDS_HANA_SDK2.service_tp_code='11'
    LEFT JOIN hana.ckd_day_ad_stats CDS_HANA_SDK3 ON CDS_HANA_SDK3.stats_dttm = CDMS.stats_dttm AND CDS_HANA_SDK3.service_tp_code='12'
    LEFT JOIN hana.ckd_day_ad_stats CDS_HANA_SDK4 ON CDS_HANA_SDK4.stats_dttm = CDMS.stats_dttm AND CDS_HANA_SDK4.service_tp_code='13'
        
    LEFT JOIN paybooc.ckd_day_ad_stats PCDS1 ON PCDS1.stats_dttm = CDMS.stats_dttm AND PCDS1.service_tp_code='01'
    LEFT JOIN paybooc.ckd_day_ad_stats PCDS2 ON PCDS2.stats_dttm = CDMS.stats_dttm AND PCDS2.service_tp_code='02'
    LEFT JOIN paybooc.ckd_day_ad_stats PCDS3 ON PCDS3.stats_dttm = CDMS.stats_dttm AND PCDS3.service_tp_code='03'
    LEFT JOIN paybooc.ckd_day_ad_stats PCDS4 ON PCDS4.stats_dttm = CDMS.stats_dttm AND PCDS4.service_tp_code='04'
    LEFT JOIN paybooc.ckd_day_ad_stats PCDS5 ON PCDS5.stats_dttm = CDMS.stats_dttm AND PCDS5.service_tp_code='05'
    LEFT JOIN paybooc.ckd_day_ad_stats PCDS6 ON PCDS6.stats_dttm = CDMS.stats_dttm AND PCDS6.service_tp_code='06'
    LEFT JOIN paybooc.ckd_day_ad_stats PCDS11 ON PCDS11.stats_dttm = CDMS.stats_dttm AND PCDS11.service_tp_code='11'
    LEFT JOIN paybooc.ckd_day_ad_stats PCDS12 ON PCDS12.stats_dttm = CDMS.stats_dttm AND PCDS12.service_tp_code='12'
    LEFT JOIN paybooc.ckd_day_ad_stats PCDS13 ON PCDS13.stats_dttm = CDMS.stats_dttm AND PCDS13.service_tp_code='13'
    LEFT JOIN paybooc.ckd_day_ad_stats PCDS14 ON PCDS14.stats_dttm = CDMS.stats_dttm AND PCDS14.service_tp_code='14'
    LEFT JOIN paybooc.ckd_day_ad_stats PCDS15 ON PCDS15.stats_dttm = CDMS.stats_dttm AND PCDS15.service_tp_code='15'
    LEFT JOIN paybooc.ckd_day_ad_stats PCDS16 ON PCDS16.stats_dttm = CDMS.stats_dttm AND PCDS16.service_tp_code='16'
    LEFT JOIN paybooc.ckd_day_ad_stats PCDS21 ON PCDS21.stats_dttm = CDMS.stats_dttm AND PCDS21.service_tp_code='21'
    LEFT JOIN paybooc.ckd_day_ad_stats PCDS22 ON PCDS22.stats_dttm = CDMS.stats_dttm AND PCDS22.service_tp_code='22'
    LEFT JOIN paybooc.ckd_day_ad_stats PCDS23 ON PCDS23.stats_dttm = CDMS.stats_dttm AND PCDS23.service_tp_code='23'
    LEFT JOIN paybooc.ckd_day_ad_stats PCDS24 ON PCDS24.stats_dttm = CDMS.stats_dttm AND PCDS24.service_tp_code='24'
    LEFT JOIN paybooc.ckd_day_ad_stats PCDS25 ON PCDS25.stats_dttm = CDMS.stats_dttm AND PCDS25.service_tp_code='25'
    LEFT JOIN paybooc.ckd_day_ad_stats PCDS26 ON PCDS26.stats_dttm = CDMS.stats_dttm AND PCDS26.service_tp_code='26'
    LEFT JOIN paybooc.ckd_day_ad_stats PCDS31 ON PCDS31.stats_dttm = CDMS.stats_dttm AND PCDS31.service_tp_code='31'
    LEFT JOIN paybooc.ckd_day_ad_stats PCDS32 ON PCDS32.stats_dttm = CDMS.stats_dttm AND PCDS32.service_tp_code='32'
    LEFT JOIN paybooc.ckd_day_ad_stats PCDS33 ON PCDS33.stats_dttm = CDMS.stats_dttm AND PCDS33.service_tp_code='33'
    LEFT JOIN paybooc.ckd_day_ad_stats PCDS34 ON PCDS34.stats_dttm = CDMS.stats_dttm AND PCDS34.service_tp_code='34'
    LEFT JOIN paybooc.ckd_day_ad_stats PCDS35 ON PCDS35.stats_dttm = CDMS.stats_dttm AND PCDS35.service_tp_code='35'
    LEFT JOIN paybooc.ckd_day_ad_stats PCDS36 ON PCDS36.stats_dttm = CDMS.stats_dttm AND PCDS36.service_tp_code='36'
    LEFT JOIN paybooc.ckd_day_coupang_stats PCSTATS1 ON PCSTATS1.stats_dttm = CDMS.stats_dttm AND PCSTATS1.service_tp_code='01'
    LEFT JOIN paybooc.ckd_day_coupang_stats PCSTATS2 ON PCSTATS2.stats_dttm = CDMS.stats_dttm AND PCSTATS2.service_tp_code='02'
    LEFT JOIN paybooc.ckd_day_coupang_stats PCSTATS3 ON PCSTATS3.stats_dttm = CDMS.stats_dttm AND PCSTATS3.service_tp_code='03'
    LEFT JOIN paybooc.ckd_day_coupang_stats PCSTATS4 ON PCSTATS4.stats_dttm = CDMS.stats_dttm AND PCSTATS4.service_tp_code='04'
    
    LEFT JOIN shinhancard.ckd_day_ad_stats SCDS1 ON SCDS1.stats_dttm = CDMS.stats_dttm AND SCDS1.service_tp_code='01'
    LEFT JOIN shinhancard.ckd_day_ad_stats SCDS2 ON SCDS2.stats_dttm = CDMS.stats_dttm AND SCDS2.service_tp_code='02'
    LEFT JOIN shinhancard.ckd_day_coupang_stats SCSTATS1 ON SCSTATS1.stats_dttm = CDMS.stats_dttm AND SCSTATS1.service_tp_code='01'
    LEFT JOIN shinhancard.ckd_day_coupang_stats SCSTATS2 ON SCSTATS2.stats_dttm = CDMS.stats_dttm AND SCSTATS2.service_tp_code='02'
    LEFT JOIN shinhancard.ckd_day_ad_stats SCDS3 ON SCDS3.stats_dttm = CDMS.stats_dttm AND SCDS3.service_tp_code='03'
    LEFT JOIN shinhancard.ckd_day_ad_stats SCDS4 ON SCDS4.stats_dttm = CDMS.stats_dttm AND SCDS4.service_tp_code='04'
    LEFT JOIN shinhancard.ckd_day_ad_stats SCDS5 ON SCDS5.stats_dttm = CDMS.stats_dttm AND SCDS5.service_tp_code='05'
    LEFT JOIN shinhancard.ckd_day_ad_stats SCDS6 ON SCDS6.stats_dttm = CDMS.stats_dttm AND SCDS6.service_tp_code='06'
    LEFT JOIN shinhancard.ckd_day_coupang_stats SCSTATS3 ON SCSTATS3.stats_dttm = CDMS.stats_dttm AND SCSTATS3.service_tp_code='03'
    LEFT JOIN shinhancard.ckd_day_coupang_stats SCSTATS4 ON SCSTATS4.stats_dttm = CDMS.stats_dttm AND SCSTATS4.service_tp_code='04'
    LEFT JOIN shinhancard.ckd_save_point_one_day SCSP_AOS ON SCSP_AOS.stats_dttm = CDMS.stats_dttm AND SCSP_AOS.os_type='A'
    LEFT JOIN shinhancard.ckd_save_point_one_day SCSP_IOS ON SCSP_IOS.stats_dttm = CDMS.stats_dttm AND SCSP_IOS.os_type='I'



    WHERE CDMS.stats_dttm BETWEEN {$sdate} AND {$edate}
    GROUP BY CDMS.stats_dttm
    ORDER BY CDMS.stats_dttm DESC
";
//pre($sql);
$ret1 = $NDO->fetch_array($sql);



// 통계 데이터2
$sql="
SELECT
    CDMS.stats_dttm
    ,IFNULL(HCDS4.exhs_amt, 0) + IFNULL(HCDS5.exhs_amt, 0) AS exhs_happy
    ,IFNULL(HCSP.point_cnt, 0) + IFNULL(HCSP.spot_point_cnt, 0) + IFNULL(HCSP.spot_point_cnt1, 0) + IFNULL(HCSP.spot_point_cnt2, 0) AS save_happy
    
    ,IFNULL(VCDS1.exhs_amt, 0) AS exhs_valuewalk
    ,IFNULL(VCDS2.click_num, 0) AS click_valuewalk

    ,IFNULL(FCDS5.click_num, 0) + IFNULL(FCDS6.click_num, 0) AS click_finnq1
    ,IFNULL(FCDS1.exhs_amt, 0) + IFNULL(FCDS2.exhs_amt, 0) AS exhs_finnq1
    ,IFNULL(FCDS7.click_num, 0)+ IFNULL(FCDS8.click_num, 0) AS click_finnq2
    ,IFNULL(FCDS3.exhs_amt, 0) + IFNULL(FCDS4.exhs_amt, 0) AS exhs_finnq2
    ,IFNULL(FCSTATS5.click_num, 0) + IFNULL(FCSTATS6.click_num, 0) AS coupang_click_finnq1
    ,IFNULL(FCSTATS1.order_commission, 0) + IFNULL(FCSTATS2.order_commission, 0) + IFNULL(FCSTATS1.cancel_commission, 0) + IFNULL(FCSTATS2.cancel_commission, 0) AS coupang_order_finnq1
    ,IFNULL(FCSTATS7.click_num, 0) + IFNULL(FCSTATS8.click_num, 0) AS coupang_click_finnq2
    ,IFNULL(FCSTATS3.order_commission, 0) + IFNULL(FCSTATS4.order_commission, 0) + IFNULL(FCSTATS3.cancel_commission, 0) + IFNULL(FCSTATS4.cancel_commission, 0) AS coupang_order_finnq2

    ,IFNULL(MCDS1.exhs_amt, 0) AS exhs_moneyweather1
    ,IFNULL(MCDS2.exhs_amt, 0) AS exhs_moneyweather2
    ,IFNULL(MCDS3.exhs_amt, 0) AS exhs_moneyweather3
    ,IFNULL(MCSP.spot_point_cnt1, 0) AS save_moneyweather1
    ,IFNULL(MCSP.spot_point_cnt2, 0) AS save_moneyweather2
    ,IFNULL(MCSP.spot_point_cnt3, 0) AS save_moneyweather3

    FROM hana.ckd_day_app_stats CDMS
        
    LEFT JOIN happyscreen.ckd_day_ad_stats HCDS4 ON HCDS4.stats_dttm = CDMS.stats_dttm AND HCDS4.service_tp_code='04'
    LEFT JOIN happyscreen.ckd_day_ad_stats HCDS5 ON HCDS5.stats_dttm = CDMS.stats_dttm AND HCDS5.service_tp_code='05'
    LEFT JOIN happyscreen.ckd_save_point_one_day HCSP ON HCSP.stats_dttm = CDMS.stats_dttm

    LEFT JOIN valuewalk.ckd_day_ad_stats VCDS1 ON VCDS1.stats_dttm = CDMS.stats_dttm AND VCDS1.service_tp_code='03'
    LEFT JOIN valuewalk.ckd_day_ad_stats VCDS2 ON VCDS2.stats_dttm = CDMS.stats_dttm AND VCDS2.service_tp_code='07'

    LEFT JOIN finnq.ckd_day_ad_stats FCDS1 ON FCDS1.stats_dttm = CDMS.stats_dttm AND FCDS1.service_tp_code='01'
    LEFT JOIN finnq.ckd_day_ad_stats FCDS2 ON FCDS2.stats_dttm = CDMS.stats_dttm AND FCDS2.service_tp_code='02'
    LEFT JOIN finnq.ckd_day_ad_stats FCDS3 ON FCDS3.stats_dttm = CDMS.stats_dttm AND FCDS3.service_tp_code='03'
    LEFT JOIN finnq.ckd_day_ad_stats FCDS4 ON FCDS4.stats_dttm = CDMS.stats_dttm AND FCDS4.service_tp_code='04'
    LEFT JOIN finnq.ckd_day_ad_stats FCDS5 ON FCDS5.stats_dttm = CDMS.stats_dttm AND FCDS5.service_tp_code='05'
    LEFT JOIN finnq.ckd_day_ad_stats FCDS6 ON FCDS6.stats_dttm = CDMS.stats_dttm AND FCDS6.service_tp_code='06'
    LEFT JOIN finnq.ckd_day_ad_stats FCDS7 ON FCDS7.stats_dttm = CDMS.stats_dttm AND FCDS7.service_tp_code='07'
    LEFT JOIN finnq.ckd_day_ad_stats FCDS8 ON FCDS8.stats_dttm = CDMS.stats_dttm AND FCDS8.service_tp_code='08'
    LEFT JOIN finnq.ckd_day_coupang_stats FCSTATS1 ON FCSTATS1.stats_dttm = CDMS.stats_dttm AND FCSTATS1.service_tp_code='01'
    LEFT JOIN finnq.ckd_day_coupang_stats FCSTATS2 ON FCSTATS2.stats_dttm = CDMS.stats_dttm AND FCSTATS2.service_tp_code='02'
    LEFT JOIN finnq.ckd_day_coupang_stats FCSTATS3 ON FCSTATS3.stats_dttm = CDMS.stats_dttm AND FCSTATS3.service_tp_code='03'
    LEFT JOIN finnq.ckd_day_coupang_stats FCSTATS4 ON FCSTATS4.stats_dttm = CDMS.stats_dttm AND FCSTATS4.service_tp_code='04'
    LEFT JOIN finnq.ckd_day_coupang_stats FCSTATS5 ON FCSTATS5.stats_dttm = CDMS.stats_dttm AND FCSTATS5.service_tp_code='05'
    LEFT JOIN finnq.ckd_day_coupang_stats FCSTATS6 ON FCSTATS6.stats_dttm = CDMS.stats_dttm AND FCSTATS6.service_tp_code='06'
    LEFT JOIN finnq.ckd_day_coupang_stats FCSTATS7 ON FCSTATS7.stats_dttm = CDMS.stats_dttm AND FCSTATS7.service_tp_code='07'
    LEFT JOIN finnq.ckd_day_coupang_stats FCSTATS8 ON FCSTATS8.stats_dttm = CDMS.stats_dttm AND FCSTATS8.service_tp_code='08'

    LEFT JOIN (
        SELECT stats_dttm, sum(exhs_amt) AS exhs_amt FROM moneyweather.ckd_day_ad_stats WHERE service_tp_code = '01' and stats_dttm BETWEEN {$sdate} AND {$edate} GROUP BY stats_dttm
    ) AS MCDS1 ON MCDS1.stats_dttm = CDMS.stats_dttm
    LEFT JOIN (
        SELECT stats_dttm, sum(exhs_amt) AS exhs_amt FROM moneyweather.ckd_day_ad_stats WHERE service_tp_code = '02' and stats_dttm BETWEEN {$sdate} AND {$edate} GROUP BY stats_dttm
    ) AS MCDS2 ON MCDS2.stats_dttm = CDMS.stats_dttm    
    LEFT JOIN (
        SELECT stats_dttm, sum(exhs_amt) AS exhs_amt FROM moneyweather.ckd_day_ad_stats WHERE service_tp_code = '03' and stats_dttm BETWEEN {$sdate} AND {$edate} GROUP BY stats_dttm
    ) AS MCDS3 ON MCDS3.stats_dttm = CDMS.stats_dttm
    LEFT JOIN moneyweather.ckd_save_point_one_day MCSP ON MCSP.stats_dttm = CDMS.stats_dttm

    
    WHERE CDMS.stats_dttm BETWEEN {$sdate} AND {$edate}
    GROUP BY CDMS.stats_dttm
    ORDER BY CDMS.stats_dttm DESC
";
//pre($sql);
$ret2 = $NDO->fetch_array($sql);
//pre($ret2);


//pre($ret);
$dbc = mysqli_connect("192.168.3.20", "mobcomms", "ahqlzjawmtjqltm!@#", "hanapay");
// 통계 데이터3
$sql="
	SELECT
		CDMS.stats_dttm
        ,IFNULL(CDS11.click_num, 0) + IFNULL(CDS12.click_num, 0) AS click_hanapay1
        ,IFNULL(CDS1.exhs_amt, 0) + IFNULL(CDS2.exhs_amt, 0) AS exhs_hanapay1
        ,IFNULL(CDS13.click_num, 0) + IFNULL(CDS14.click_num, 0) AS click_hanapay2
        ,IFNULL(CDS3.exhs_amt, 0) + IFNULL(CDS4.exhs_amt, 0) AS exhs_hanapay2
	
	FROM hanapay.ckd_day_app_stats CDMS
	LEFT JOIN hanapay.ckd_day_ad_stats CDS1 ON CDS1.stats_dttm = CDMS.stats_dttm AND CDS1.service_tp_code='01'
	LEFT JOIN hanapay.ckd_day_ad_stats CDS2 ON CDS2.stats_dttm = CDMS.stats_dttm AND CDS2.service_tp_code='02'
	LEFT JOIN hanapay.ckd_day_ad_stats CDS3 ON CDS3.stats_dttm = CDMS.stats_dttm AND CDS3.service_tp_code='03'
	LEFT JOIN hanapay.ckd_day_ad_stats CDS4 ON CDS4.stats_dttm = CDMS.stats_dttm AND CDS4.service_tp_code='04'

	LEFT JOIN hanapay.ckd_day_ad_stats CDS11 ON CDS11.stats_dttm = CDMS.stats_dttm AND CDS11.service_tp_code='11'
	LEFT JOIN hanapay.ckd_day_ad_stats CDS12 ON CDS12.stats_dttm = CDMS.stats_dttm AND CDS12.service_tp_code='12'
	LEFT JOIN hanapay.ckd_day_ad_stats CDS13 ON CDS13.stats_dttm = CDMS.stats_dttm AND CDS13.service_tp_code='13'
	LEFT JOIN hanapay.ckd_day_ad_stats CDS14 ON CDS14.stats_dttm = CDMS.stats_dttm AND CDS14.service_tp_code='14'
	
    WHERE CDMS.stats_dttm BETWEEN {$sdate} AND {$edate}
    GROUP BY CDMS.stats_dttm
    ORDER BY CDMS.stats_dttm DESC
";
//pre($sql);
$result = mysqli_query($dbc, $sql);
$ret3 = mysqli_fetch_all($result,MYSQLI_ASSOC);

// 배열 병합
$ret = array();
foreach ($ret1 as $key => $item1) {
    $ret[$key] = array_merge($item1, $ret2[$key]);
    $ret[$key] = array_merge($ret[$key], $ret3[$key]);
}
//pre($ret);

function date_color_code($day){
    $yoil = array("#fff3f3","","","","","","#f1fcff");
    return ($yoil[date('w', strtotime($day))]);
}


$TOTAL = [];
foreach($ret as $key => $row){
    //OCB cpc
    $TOTAL['cpc_main_ocb'] = ($TOTAL['cpc_main_ocb'] ?? 1) + round(empty($row['click_main_ocb'])?0:($row['exhs_main_ocb']/$row['click_main_ocb']),2);
    $TOTAL['cpc_set_ocb'] = ($TOTAL['cpc_set_ocb'] ?? 0) + round(empty($row['click_set_ocb'])?0:($row['exhs_set_ocb']/$row['click_set_ocb']),2);
    //hanamoney
    $TOTAL['ctm_hana'] = ($TOTAL['ctm_hana'] ?? 0) + round(empty($row['eprs_hana'])?0:($row['exhs_hana']/$row['eprs_hana']),2);
    //hanapay
    $TOTAL['cpc_aos_hanapay'] = ($TOTAL['cpc_aos_hanapay'] ?? 0) + round(empty($row['click_hanapay1'])?0:($row['exhs_hanapay1']/$row['click_hanapay1']),2);
    $TOTAL['cpc_ios_hanapay'] = ($TOTAL['cpc_ios_hanapay'] ?? 0) + round(empty($row['click_hanapay2'])?0:($row['exhs_hanapay2']/$row['click_hanapay2']),2);
    //paybooc
    $TOTAL['cpc_ad_aos_paybooc'] = ($TOTAL['cpc_ad_aos_paybooc'] ?? 0) + (empty($row['click_ad_aos_paybooc'])?0:($row['exhs_ad_aos_paybooc']/$row['click_ad_aos_paybooc']));
    $TOTAL['cpc_ad_ios_paybooc'] = ($TOTAL['cpc_ad_ios_paybooc'] ?? 0) + (empty($row['click_ad_ios_paybooc'])?0:($row['exhs_ad_ios_paybooc']/$row['click_ad_ios_paybooc']));
    $TOTAL['cpc_coupang_aos_paybooc'] = ($TOTAL['cpc_coupang_aos_paybooc'] ?? 0) + (empty($row['coupang_click_num_paybooc1'])?0:($row['coupang_order_paybooc1']/$row['coupang_click_num_paybooc1']));
    $TOTAL['cpc_coupang_ios_paybooc'] = ($TOTAL['cpc_coupang_ios_paybooc'] ?? 0) + (empty($row['coupang_click_num_paybooc2'])?0:($row['coupang_order_paybooc2']/$row['coupang_click_num_paybooc2']));
    //shinhan
    $TOTAL['cpc_point_shinhan'] = ($TOTAL['cpc_point_shinhan'] ?? 0) + (empty($row['point_cnt_shinhan'])?0:($row['exhs_point_shinhan']/$row['point_cnt_shinhan']));
    $TOTAL['cpc_ladder_shinhan'] = ($TOTAL['cpc_ladder_shinhan'] ?? 0) + (empty($row['ladder_cnt_shinhan'])?0:($row['exhs_ladder_shinhan']/$row['ladder_cnt_shinhan']));
    //happy
    $TOTAL['cpc_happy'] = ($TOTAL['cpc_happy'] ?? 0) + (empty($row['save_happy'])?0:($row['exhs_happy']/$row['save_happy']));
    //valuewalk
    $TOTAL['cpc_valuewalk'] = ($TOTAL['cpc_valuewalk'] ?? 0) + (empty($row['click_valuewalk'])?0:($row['exhs_valuewalk']/$row['click_valuewalk']));
    //finnq
    $TOTAL['cpc_finnq1'] = ($TOTAL['cpc_finnq1'] ?? 0) + (empty($row['click_finnq1'])?0:($row['exhs_finnq1']/$row['click_finnq1']));
    $TOTAL['cpc_finnq2'] = ($TOTAL['cpc_finnq2'] ?? 0) + (empty($row['click_finnq2'])?0:($row['exhs_finnq2']/$row['click_finnq2']));
    $TOTAL['cpc_coupang_finnq1'] = ($TOTAL['cpc_coupang_finnq1'] ?? 0) + (empty($row['coupang_click_finnq1'])?0:($row['coupang_order_finnq1']/$row['coupang_click_finnq1']));
    $TOTAL['cpc_coupang_finnq2'] = ($TOTAL['cpc_coupang_finnq2'] ?? 0) + (empty($row['coupang_click_finnq2'])?0:($row['coupang_order_finnq2']/$row['coupang_click_finnq2']));
    //moneyweather
    $TOTAL['cpc_money1'] = ($TOTAL['cpc_money1'] ?? 0) + (empty($row['save_moneyweather1'])?0:($row['exhs_moneyweather1']/$row['save_moneyweather1']));
    $TOTAL['cpc_money2'] = ($TOTAL['cpc_money2'] ?? 0) + (empty($row['save_moneyweather2'])?0:($row['exhs_moneyweather2']/$row['save_moneyweather2']));
    $TOTAL['cpc_money3'] = ($TOTAL['cpc_money3'] ?? 0) + (empty($row['save_moneyweather3'])?0:($row['exhs_moneyweather3']/$row['save_moneyweather3']));


    if($type=="3M" || $type=="6M"){
        //평균
        $month = substr($row['stats_dttm'],0,6);
        $this_date = $key."01";
        $date = date("Y-m-d",strtotime ("-1 days", strtotime($this_date)));
        $date = date("Y-m-d",strtotime ("+1 days", strtotime($date)));
        $this_month_day_cnt = date('t',strtotime($date));

        $M_TOTAL[$month]['cpc_main_ocb'] = ($M_TOTAL[$month]['cpc_main_ocb'] ?? 0) + round(empty($row['click_main_ocb'])?0:$row['exhs_main_ocb']/$row['click_main_ocb'],2)/$this_month_day_cnt;
        $M_TOTAL[$month]['cpc_set_ocb'] = ($M_TOTAL[$month]['cpc_set_ocb'] ?? 0) + round(empty($row['click_set_ocb'])?0:$row['exhs_set_ocb']/$row['click_set_ocb'],2)/$this_month_day_cnt;

        $M_TOTAL[$month]['ctm_hana'] = ($M_TOTAL[$month]['ctm_hana'] ?? 0) + round(empty($row['eprs_hana'])?0:$row['exhs_hana']/$row['eprs_hana'],2)/$this_month_day_cnt;

        $M_TOTAL[$month]['cpc_aos_hanapay'] = ($M_TOTAL[$month]['cpc_aos_hanapay'] ?? 0) + round(empty($row['click_hanapay1'])?0:($row['exhs_hanapay1']/$row['click_hanapay1']))/$this_month_day_cnt;
        $M_TOTAL[$month]['cpc_ios_hanapay'] = ($M_TOTAL[$month]['cpc_ios_hanapay'] ?? 0) + round(empty($row['click_hanapay2'])?0:($row['exhs_hanapay2']/$row['click_hanapay2']))/$this_month_day_cnt;

        $M_TOTAL[$month]['cpc_ad_aos_paybooc'] = ($M_TOTAL[$month]['cpc_ad_aos_paybooc'] ?? 0) + (empty($row['click_ad_aos_paybooc'])?0:($row['exhs_ad_aos_paybooc']/$row['click_ad_aos_paybooc']));
        $M_TOTAL[$month]['cpc_ad_ios_paybooc'] = ($M_TOTAL[$month]['cpc_ad_ios_paybooc'] ?? 0) + (empty($row['click_ad_ios_paybooc'])?0:($row['exhs_ad_ios_paybooc']/$row['click_ad_ios_paybooc']));
        $M_TOTAL[$month]['cpc_coupang_aos_paybooc'] = ($M_TOTAL[$month]['cpc_coupang_aos_paybooc'] ?? 0) + (empty($row['coupang_click_num_paybooc1'])?0:($row['coupang_order_paybooc1']/$row['coupang_click_num_paybooc1']));
        $M_TOTAL[$month]['cpc_coupang_ios_paybooc'] = ($M_TOTAL[$month]['cpc_coupang_ios_paybooc'] ?? 0) + (empty($row['coupang_click_num_paybooc2'])?0:($row['coupang_order_paybooc2']/$row['coupang_click_num_paybooc2']));

        $M_TOTAL[$month]['cpc_point_shinhan'] = ($M_TOTAL[$month]['cpc_point_shinhan'] ?? 0) + (empty($row['point_cnt_shinhan'])?0:($row['exhs_point_shinhan']/$row['point_cnt_shinhan']));
        $M_TOTAL[$month]['cpc_ladder_shinhan'] = ($M_TOTAL[$month]['cpc_ladder_shinhan'] ?? 0) + (empty($row['ladder_cnt_shinhan'])?0:($row['exhs_ladder_shinhan']/$row['ladder_cnt_shinhan']));

        $M_TOTAL[$month]['cpc_happy'] = ($M_TOTAL[$month]['cpc_happy'] ?? 0) + (empty($row['save_happy'])?0:($row['exhs_happy']/$row['save_happy']));

        $M_TOTAL[$month]['cpc_valuewalk'] = ($M_TOTAL[$month]['cpc_valuewalk'] ?? 0) + (empty($row['click_valuewalk'])?0:($row['exhs_valuewalk']/$row['click_valuewalk']));

        $M_TOTAL[$month]['cpc_finnq1'] = ($M_TOTAL[$month]['cpc_finnq1'] ?? 0) + (empty($row['click_finnq1'])?0:($row['exhs_finnq1']/$row['click_finnq1']));
        $M_TOTAL[$month]['cpc_finnq2'] = ($M_TOTAL[$month]['cpc_finnq2'] ?? 0) + (empty($row['click_finnq2'])?0:($row['exhs_finnq2']/$row['click_finnq2']));
        $M_TOTAL[$month]['cpc_coupang_finnq1'] = ($M_TOTAL[$month]['cpc_coupang_finnq1'] ?? 0) + (empty($row['coupang_click_finnq1'])?0:($row['coupang_order_finnq1']/$row['coupang_click_finnq1']));
        $M_TOTAL[$month]['cpc_coupang_finnq2'] = ($M_TOTAL[$month]['cpc_coupang_finnq2'] ?? 0) + (empty($row['coupang_click_finnq2'])?0:($row['coupang_order_finnq2']/$row['coupang_click_finnq2']));

        $M_TOTAL[$month]['cpc_money1'] = ($M_TOTAL[$month]['cpc_money1'] ?? 0) + (empty($row['save_moneyweather1'])?0:($row['exhs_moneyweather1']/$row['save_moneyweather1']));
        $M_TOTAL[$month]['cpc_money2'] = ($M_TOTAL[$month]['cpc_money2'] ?? 0) + (empty($row['save_moneyweather2'])?0:($row['exhs_moneyweather2']/$row['save_moneyweather2']));
        $M_TOTAL[$month]['cpc_money3'] = ($M_TOTAL[$month]['cpc_money3'] ?? 0) + (empty($row['save_moneyweather3'])?0:($row['exhs_moneyweather3']/$row['save_moneyweather3']));

    }
}//foreach

//시작일과 종료일 날짜 계산
$date1 = new DateTime($startDate);
$date2 = new DateTime($endDate);
$interval = $date1->diff($date2);
$diff_date = (int)$interval->format('%a')+1;

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
                        </div><!-- header -->
                    </div><!-- card -->
                </div><!-- row -->
            </form>
            <div class="row member_table .col-xs-12 .col-md-12" style="margin-top:8px">

                <div class="table-responsive">
                    <table class="table table-hover mb30" style="border:1px solid #b0b0b0;">
                        <thead>
                        <tr>
                            <th>매체사</th>
                            <th colspan="2">OCB</th>
                            <th>하나머니</th>
                            <th colspan="2">하나페이</th>
                            <th colspan="4">페이북</th>
                            <th colspan="2">신한카드</th>
                            <th>해피스크린</th>
                            <th>가치워크</th>
                            <th colspan="4">핀크</th>
                            <th colspan="3">돈씨</th>
                        </tr>
                        <tr>
                            <th>서비스</th>
                            <th>키보드(메인)</th>
                            <th>키보드(설정)</th>
                            <th>머니사다리</th>
                            <th>일반광고(AOS)</th>
                            <th>일반광고(IOS)</th>
                            <th>일반광고(AOS)</th>
                            <th>일반광고(IOS)</th>
                            <th>쿠팡광고(AOS)</th>
                            <th>쿠팡광고(IOS)</th>
                            <th>포인트베너</th>
                            <th>사다리타기</th>
                            <th>키보드</th>
                            <th>행운룰렛</th>
                            <th>일반광고(AOS)</th>
                            <th>일반광고(IOS)</th>
                            <th>쿠팡광고(AOS)</th>
                            <th>쿠팡광고(IOS)</th>
                            <th>복권</th>
                            <th>사다리</th>
                            <th>룰렛</th>
                        </tr>
                        <tr>
                            <th>매체사별<br>계산방식</th>
                            <th colspan="2">각매출/일별적립요청수</th><!-- OCB -->
                            <th>매출/노출수<br>(cpm 단가)</th><!-- 하나머니 -->
                            <th colspan="2">매출/클릭수</th><!-- 하나페이 -->
                            <th colspan="4">매출/클릭수</th><!-- 페이북 -->
                            <th colspan="2">전체매출/일별 요청수</th><!-- 신한카드 -->
                            <th>전체매출/일별 적립 요청수</th><!-- 해피스크린 -->
                            <th>전체매출/일별 적립 요청수</th><!-- 가치워크 -->
                            <th colspan="4">매출/클릭수</th><!-- 핀크 -->
                            <th colspan=3">매출/클릭수</th><!-- 돈씨 -->

                        </tr>
                        </thead>
                        <tbody id="ocb" >
                        <tr class="" style="background-color: #F2F5A9;">
                            <td>합계</td>
                            <!-- OCB -->
                            <td><?=number_format($TOTAL['cpc_main_ocb']/$diff_date,2)?></td><!-- 메인띠베너 -->
                            <td><?=number_format($TOTAL['cpc_set_ocb']/$diff_date,2)?></td><!-- 설정띠베너 -->
                            <!-- 하나머니 -->
                            <td><?=number_format($TOTAL['ctm_hana']/$diff_date, 2)?></td><!-- 머니사다리 -->
                            <!-- 하나페이 -->
                            <td><?=number_format($TOTAL['cpc_aos_hanapay']/$diff_date, 2)?></td>
                            <td><?=number_format($TOTAL['cpc_ios_hanapay']/$diff_date, 2)?></td>
                            <!-- 페이북 -->
                            <td><?=number_format($TOTAL['cpc_ad_aos_paybooc']/$diff_date, 2)?></td>
                            <td><?=number_format($TOTAL['cpc_ad_ios_paybooc']/$diff_date, 2)?></td>
                            <td><?=number_format($TOTAL['cpc_coupang_aos_paybooc']/$diff_date, 2)?></td>
                            <td><?=number_format($TOTAL['cpc_coupang_ios_paybooc']/$diff_date, 2)?></td>
                            <!-- 신한카드 -->
                            <td><?=number_format($TOTAL['cpc_point_shinhan']/$diff_date, 2)?></td>
                            <td><?=number_format($TOTAL['cpc_ladder_shinhan']/$diff_date, 2)?></td>
                            <!-- 해피스크린 -->
                            <td><?=number_format($TOTAL['cpc_happy']/$diff_date, 2)?></td>
                            <!-- 가치워크 -->
                            <td><?=number_format($TOTAL['cpc_valuewalk']/$diff_date, 2)?></td>
                            <!-- 핀크 -->
                            <td><?=number_format($TOTAL['cpc_finnq1']/$diff_date, 2)?></td>
                            <td><?=number_format($TOTAL['cpc_finnq2']/$diff_date, 2)?></td>
                            <td><?=number_format($TOTAL['cpc_coupang_finnq1']/$diff_date, 2)?></td>
                            <td><?=number_format($TOTAL['cpc_coupang_finnq2']/$diff_date, 2)?></td>
                            <!-- 돈씨 -->
                            <td><?=number_format($TOTAL['cpc_money3']/$diff_date, 2)?></td>
                            <td><?=number_format($TOTAL['cpc_money1']/$diff_date, 2)?></td>
                            <td><?=number_format($TOTAL['cpc_money2']/$diff_date, 2)?></td>
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
                   ?>
                        <tr class="" style="background-color: #afe076;">
                            <td><?=$key?></td>
                            <!-- OCB -->
                            <td><?=number_format($row['cpc_main_ocb'],2)?></td><!-- 메인띠베너 -->
                            <td><?=number_format($row['cpc_set_ocb'],2)?></td><!-- 설정띠베너 -->
                            <!-- 하나머니 -->
                            <td><?=number_format($row['ctm_hana'], 2)?></td>
                            <!-- 하나페이 -->
                            <td><?=number_format($row['cpc_aos_hanapay'],2)?></td>
                            <td><?=number_format($row['cpc_ios_hanapay'],2)?></td>
                            <!-- 페이북 -->
                            <td><?=number_format($row['cpc_ad_aos_paybooc'], 2)?></td>
                            <td><?=number_format($row['cpc_ad_ios_paybooc'], 2)?></td>
                            <td><?=number_format($row['cpc_coupang_aos_paybooc'], 2)?></td>
                            <td><?=number_format($row['cpc_coupang_ios_paybooc'], 2)?></td>
                            <!-- 신한카드 -->
                            <td><?=number_format($row['cpc_point_shinhan'], 2)?></td>
                            <td><?=number_format($row['cpc_ladder_shinhan'], 2)?></td>
                            <!-- 해피스크린 -->
                            <td><?=number_format($row['cpc_happy'], 2)?></td>
                            <!-- 가치워크 -->
                            <td><?=number_format($row['cpc_valuewalk'], 2)?></td>
                            <!-- 핀크 -->
                            <td><?=number_format($row['cpc_finnq1'], 2)?></td>
                            <td><?=number_format($row['cpc_finnq2'], 2)?></td>
                            <td><?=number_format($row['cpc_coupang_finnq1'], 2)?></td>
                            <td><?=number_format($row['cpc_coupang_finnq2'], 2)?></td>
                            <!-- 돈씨 -->
                            <td><?=number_format($row['cpc_money3'], 2)?></td>
                            <td><?=number_format($row['cpc_money1'], 2)?></td>
                            <td><?=number_format($row['cpc_money2'], 2)?></td>
                        </tr>

                    <?php }}?>



                    <?php foreach($ret as $key => $row){

                        $date = $row['stats_dttm'];
                        $formatted_date = substr($date, 0, 4) . "-" . substr($date, 4, 2) . "-" . substr($date, 6, 2);

                        $cpc_main_ocb = empty($row['click_main_ocb'])?0:($row['exhs_main_ocb']/$row['click_main_ocb']);
                        $cpc_set_ocb = empty($row['click_set_ocb'])?0:($row['exhs_set_ocb']/$row['click_set_ocb']);

                        $ctm_hana = empty($row['eprs_hana'])?0:($row['exhs_hana']/$row['eprs_hana']);

                        $cpc_aos_hanapay = empty($row['click_hanapay1'])?0:($row['exhs_hanapay1']/$row['click_hanapay1']);
                        $cpc_ios_hanapay = empty($row['click_hanapay2'])?0:($row['exhs_hanapay2']/$row['click_hanapay2']);

                        $cpc_ad_aos_paybooc = empty($row['click_ad_aos_paybooc'])?0:($row['exhs_ad_aos_paybooc']/$row['click_ad_aos_paybooc']);
                        $cpc_ad_ios_paybooc = empty($row['click_ad_ios_paybooc'])?0:($row['exhs_ad_ios_paybooc']/$row['click_ad_ios_paybooc']);
                        $cpc_coupang_aos_paybooc = empty($row['coupang_click_num_paybooc1'])?0:($row['coupang_order_paybooc1']/$row['coupang_click_num_paybooc1']);
                        $cpc_coupang_ios_paybooc = empty($row['coupang_click_num_paybooc2'])?0:($row['coupang_order_paybooc2']/$row['coupang_click_num_paybooc2']);

                        $cpc_point_shinhan = empty($row['point_cnt_shinhan'])?0:($row['exhs_point_shinhan']/$row['point_cnt_shinhan']);
                        $cpc_ladder_shinhan = empty($row['ladder_cnt_shinhan'])?0:($row['exhs_ladder_shinhan']/$row['ladder_cnt_shinhan']);

                        $cpc_happy = empty($row['save_happy'])?0:($row['exhs_happy']/$row['save_happy']);
                        $cpc_valuewalk = empty($row['click_valuewalk'])?0:($row['exhs_valuewalk']/$row['click_valuewalk']);

                        $cpc_finnq1 = empty($row['click_finnq1'])?0:($row['exhs_finnq1']/$row['click_finnq1']);
                        $cpc_finnq2 = empty($row['click_finnq2'])?0:($row['exhs_finnq2']/$row['click_finnq2']);
                        $cpc_coupang_finnq1 = empty($row['coupang_click_finnq1'])?0:($row['coupang_order_finnq1']/$row['coupang_click_finnq1']);
                        $cpc_coupang_finnq2 = empty($row['coupang_click_finnq2'])?0:($row['coupang_order_finnq2']/$row['coupang_click_finnq2']);

                        $cpc_money1 = empty($row['save_moneyweather1'])?0:($row['exhs_moneyweather1']/$row['save_moneyweather1']);
                        $cpc_money2 = empty($row['save_moneyweather2'])?0:($row['exhs_moneyweather2']/$row['save_moneyweather2']);
                        $cpc_money3 = empty($row['save_moneyweather3'])?0:($row['exhs_moneyweather3']/$row['save_moneyweather3']);

                        //GS
                        $cpc_gs = empty($row['gs_click'])?0:($row['gs_amt']/$row['gs_click']);
                        ?>
                        <tr class='<?=$fn->dateColor($row['stats_dttm'])?>'>
                            <td><?=$formatted_date?></td>
                            <!-- OCB -->
                            <td><?=number_format($cpc_main_ocb,2)?></td>
                            <td><?=number_format($cpc_set_ocb,2)?></td>
                            <!-- 하나머니 -->
                            <td><?=number_format($ctm_hana,2)?></td>
                            <!-- 하나페이 -->
                            <td><?=number_format($cpc_aos_hanapay,2)?></td>
                            <td><?=number_format($cpc_ios_hanapay,2)?></td>
                            <!-- 페이북 -->
                            <td><?=number_format($cpc_ad_aos_paybooc,2)?></td>
                            <td><?=number_format($cpc_ad_ios_paybooc, 2)?></td>
                            <td><?=number_format($cpc_coupang_aos_paybooc, 2)?></td>
                            <td><?=number_format($cpc_coupang_ios_paybooc, 2)?></td>
                            <!-- 신한카드 -->
                            <td><?=number_format($cpc_point_shinhan,2)?></td>
                            <td><?=number_format($cpc_ladder_shinhan, 2)?></td>
                            <!-- 해피스크린 -->
                            <td><?=number_format($cpc_happy, 2)?></td>
                            <!-- 가치워크 -->
                            <td><?=number_format($cpc_valuewalk, 2)?></td>
                            <!-- 핀크 -->
                            <td><?=number_format($cpc_finnq1, 2)?></td>
                            <td><?=number_format($cpc_finnq2, 2)?></td>
                            <td><?=number_format($cpc_coupang_finnq1, 2)?></td>
                            <td><?=number_format($cpc_coupang_finnq2, 2)?></td>
                            <!-- 돈씨 -->
                            <td><?=number_format($cpc_money3, 2)?></td>
                            <td><?=number_format($cpc_money1, 2)?></td>
                            <td><?=number_format($cpc_money2, 2)?></td>
                        </tr>
                    <?php } ?>
                        </tbody>
                    </table>

                </div><!-- table-responsive -->
            </div><!-- row -->
        </div><!-- panel-body -->
    </div><!-- panel -->
</div><!-- contentpanel -->
<script>
    $("section").css({"min-width":"3000px"});

</script>
<?php
include __foot__;
?>
