<?php
/*************************************************
 *
 *      나스미디어 CPS 통계 api (광고장애시 송출)
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

function nas_curl($page=1): void
{
    global $curl_data;
    $get_date = empty($_REQUEST['date'])?"":$_REQUEST['date'];
    if (empty($get_date)) {
        $endDate = date('Ymd');
        $startDate = date('Ymd', strtotime($endDate . ' -1 day'));
    } else {
        if (strlen($get_date) != 2) {
            exit("조회 가능한 달이 아닙니다");
        }

        if ($get_date == date("m")) {
            //이번달 조회
            $startDate = date("Ym") . "01";
            $endDate = date('Ymd');
        } else {
            //지난달 조회
            $d = mktime(0, 0, 0, date("m"), 1, date("Y")); //이번달 1일
            $prev_month = strtotime("-1 month", $d); //한달전

            //시작일
            $startDate = date("Ym01", $prev_month);
            //마지막날
            $endDate = date("Ymt", $prev_month);
        }
    }

    #나스 미디에 CPS 연동
    $url = 'https://api-cps.nasadplatform.com/media_report/dailyCampaign';
    $params = [
        'apiCd' => '0c57f73bc4c287f51e6cd42d0eace2ab',
        'reportType' => 'sub_media',
        'startDate' => $startDate,
        'endDate' => $endDate,
        'page' => $page
    ];
    $fullUrl = $url . '?' . http_build_query($params);
    pre($fullUrl);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $fullUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 결과를 문자열로 반환
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // 리디렉션 따라가기 (curl --location)
    curl_setopt($ch, CURLOPT_USERAGENT, 'anick'); //없으면 연동 안됨.

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'cURL Error: ' . curl_error($ch);
        exit;
    }
    curl_close($ch);

    $media_data = json_decode($response, true);
    //pre($media_data);

    if (is_array($media_data) && !empty($media_data['dataTotal'])) {
        foreach($media_data['list'] as $value) {
//            if(!str_contains($value['subMediaNm'], "paybooc")) {
//                continue;
//            }
            $date = str_replace('-', '', $value['date']);
            if(isset($curl_data[$date][$value['subMediaNm']]) === false) {
                $curl_data[$date][$value['subMediaNm']]['click'] = 0;
                $curl_data[$date][$value['subMediaNm']]['totalSales'] = 0;
            }
            $curl_data[$date][$value['subMediaNm']]['click'] += $value['click'];
            $curl_data[$date][$value['subMediaNm']]['totalSales'] += $value['totalSales'];
        }
    }

    $pageTotal = $media_data['pageTotal']; //전체 페이지수
    $currentPage = $media_data['currentPage']; // 현재 페이지
    if(!empty($curl_data) && ($pageTotal > $currentPage)) {
        $page++;
        nas_curl($page);
    }
}

$curl_data = [];
nas_curl();
//pre($curl_data);

if(!empty($curl_data)) {
    foreach($curl_data as $date => $value) {
        foreach($value as $subMediaNm => $value2) {
            $sql = "
				INSERT INTO hana.ckd_day_nas_stats SET
					stats_dttm = '{$date}'
					,service_tp_code = '{$subMediaNm}'
					,click_num = '{$value2['click']}'
					,exhs_amt = '{$value2['totalSales']}'
					,reg_dttm = now()
				 ON DUPLICATE KEY UPDATE 
					click_num = '{$value2['click']}'
					,exhs_amt = '{$value2['totalSales']}'
					,alt_dttm = now()
			";
            //pre($sql);
            $NDO->sql_query($sql);
        }
    }

} else {
    exit('no_data<br>');
}
echo "OK";
?>