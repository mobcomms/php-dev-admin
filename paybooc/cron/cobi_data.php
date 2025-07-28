<?php
/*************************************************
 *
 *      모비위드 통계 api
 *
 *************************************************/
//error_reporting(E_ALL);
//ini_set("display_errors", 1);
if(!empty($_REQUEST['postman']) && $_REQUEST['postman'] == "postman"){
	include_once '../paybooc/var_cron.php';
	include_once __pdoDB__;    ## DB Instance 생성
	include_once __fn__;

}else{
	$path = '/home/paybooc/public_html/';
	include_once $path . 'paybooc/var_cron.php';
	include_once $path . 'Class/Class.Func.php';
	include_once $path . 'Class/Class.PDO.DB.php';
}

function cobi_get($url)
{
    $header_data = [];
    $header_data[] = "Authorization:Bearer 0e49295b10a0542411466c758c40759c";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header_data); //header 지정하기
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $c_result = curl_exec($ch);
    curl_close($ch);
    return $c_result;
}

function get_curl($get_date="")
{
    global $NDO, $fn;
    if (empty($get_date)) {
        $endDate = date('Y-m-d');
        $startDate = date('Y-m-d', strtotime($endDate . ' -1 day'));
    } else {
        if (strlen($get_date) != 2) {
            exit("조회 가능한 달이 아닙니다");
        }

        if ($get_date == date("m")) {
            //이번달 조회
            $startDate = date("Y-m") . "-01";
            $endDate = date('Y-m-d');
        } else {
            //지난달 조회

            $d = mktime(0, 0, 0, date("m"), 1, date("Y")); //이번달 1일
            $prev_month = strtotime("-1 month", $d); //한달전

            //시작일
            $startDate = date("Y-m-01", $prev_month);
            //마지막날
            $endDate = date("Y-m-t", $prev_month);
        }
    }

    # 매체 리스트 조회
    $url = "https://cnp.covi.co.kr/api/report/site";
    pre($url);
    $media_data = cobi_get($url);
    $media_data = json_decode($media_data, true);
    //pre($media_data);

    if ($media_data['result']['code'] == 200 && $media_data['result']['message'] == "Ok" && !empty($media_data['data'])) {
        foreach ($media_data['data'] as $row) {

            $site_id = $row['id'];
            # 매체 리포트 조회
            $url = "https://cnp.covi.co.kr/api/report/site/{$site_id}?start_day={$startDate}&end_day={$endDate}";
            pre($url);
            $report_data = cobi_get($url);
            $report_data = json_decode($report_data, true);
            //pre($report_data);
            if ($report_data['result']['code'] == 200 && $report_data['result']['message'] == "Ok" && !empty($report_data['data'])) {
                foreach ($report_data['data']['daily'] as $row2) {

                    $date = str_replace("-","",$row2['rptday']);
                    $impression = $row2['impression'];
                    $click = $row2['click'];
                    $budget_media = $row2['budget_media'];

                    $sql = "
                        INSERT INTO ckd_day_ad_stats SET 
                            stats_dttm = '{$date}'
                            ,service_tp_code = '{$site_id}'
                            ,partner_no = '{$site_id}' 
                            ,eprs_num='{$impression}'
                            ,click_num='{$click}'
                            ,exhs_amt='{$budget_media}'
                            ,reg_dttm=NOW()
                        ON DUPLICATE KEY UPDATE eprs_num='{$impression}', click_num='{$click}', exhs_amt='{$budget_media}', alt_dttm=NOW()
                    ";
                    //pre($sql);
                    $NDO->sql_query($sql);

                }
            }
        }
    }
}

$get_date = empty($_REQUEST['date'])?"":$_REQUEST['date'];
get_curl($get_date);

echo "OK";
?>