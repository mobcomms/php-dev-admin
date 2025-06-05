<?php
/**********************
 *
 *    회원관리 페이지 (회원정보 리스트)
 *
 **********************/

include "./var.php";
include_once __func__;
include_once __pdoDB__;    ## DB Instance 생성

function commsad_curl ($method, $url, $query){
	//pre($url);
	//pre($query);
	$curl = curl_init();
	$headers = array(
		'Content-Type:application/json'
	);
	switch ($method) {
		case 'POST':
			curl_setopt($curl, CURLOPT_POST, true);
			if ($query) curl_setopt($curl, CURLOPT_POSTFIELDS, $query);
		break;
		case 'PUT':
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
			if ($query) curl_setopt($curl, CURLOPT_POSTFIELDS, $query);
		break;
		case 'DELETE':
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
			if ($query) curl_setopt($curl, CURLOPT_POSTFIELDS, $query);
		break;
		case 'PATCH':
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
			if ($query) curl_setopt($curl, CURLOPT_POSTFIELDS, $query);
		break;
		default: // GET
			if ($query) $url = sprintf("%s?%s", $url, http_build_query($query));
	}
	// OPTIONS:
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers );
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($curl, CURLOPT_SSLVERSION, true);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_TIMEOUT, 20);
	// EXECUTE:
	$result = curl_exec($curl);
	//pre($result);
	$error_info[0] = curl_errno($curl);
	$error_info[1] = curl_error($curl);
	$error_info[2] = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	curl_close($curl);
	return $result;
}

## 환경설정
define('_Menu_', 'user');
$target = empty($_REQUEST['target'])?"":trim($_REQUEST['target']);
switch($target){
	case "PPZ" :
		define('_title_', '적립 실패 관리 (오퍼월)');
		define('_subMenu_', 'manual_ppz');
	break;
	default :
		define('_title_', '적립 실패 관리 (머니박스)');
		define('_subMenu_', 'manual_box');
}
if(empty($_POST)){
	include_once __head__; ## html 헤더 출력
}
include_once __page__;
$now = ret(INPUT_GET, 'np');
$paging->np = $now;
$paging->ps = 50;

$startDate = !empty($_REQUEST['startDate']) ? $_REQUEST['startDate'] : date("Y-m-d",strtotime("-1 month"));
$endDate = !empty($_REQUEST['endDate']) ? $_REQUEST['endDate'] : date("Y-m-d");
$manual_pay = empty($_REQUEST['manual_pay']) ?"":$_REQUEST['manual_pay'];

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

$sdate = str_replace("-","",$startDate);
$edate = str_replace("-","",$endDate);

switch($manual_pay){
	case "N" : $add_query=" AND OUP.direct_process_date is null ";
		$sdate = str_replace("-","",$startDate);
		$edate = str_replace("-","",$endDate);
	break;
	case "Y" : $add_query=" AND OUP.direct_process_date is not null ";
		//if($startDate < 20240103) $startDate = "2024-01-03";
		$sdate = str_replace("-","",$startDate);
		$edate = str_replace("-","",$endDate);
	break;
	default : $add_query=""; break;
}

// 검색어
$key=empty($_REQUEST['key'])?"":$_REQUEST['key'];
$keyword=empty($_REQUEST['keyword'])?"":htmlspecialchars($_REQUEST['keyword']);
$where="reg_date_id > DATE_FORMAT(DATE_ADD(reg_date_id, INTERVAL +2 DAY ), '%Y%m%d') AND result_code != 0000 ";


// 검색어
if($keyword && $key=='user_id'){
	$where.=" AND (".$key." like '%".$keyword."%' or m.user_no='".$keyword."') ";
}else if($keyword){
	$where.=" AND ".$key." like '%".$keyword."%' ";
}
$PG_Param = "&key=".$key."&keyword=".$keyword."&manual_pay=".$manual_pay;


$domain="http://211.62.59.210:19007/moneybox";
$domain="http://211.62.59.210/moneybox";
//$domain="https://api.commsad.com/moneybox";


if(!empty($_POST)){
	$userkey = empty($_REQUEST['userkey']) ?"":$_REQUEST['userkey'];
	$boxid = empty($_REQUEST['boxid']) ?"":$_REQUEST['boxid'];
	$regdateid = empty($_REQUEST['regdateid']) ?"":$_REQUEST['regdateid'];

	if($target == "PPZ"){
		$url = "{$domain}/offerwall/retry";
	}else{
		$url = "{$domain}/point/retry";
	}
	$param = [
		'clientCode'=>'hanapay'
		,'productCode'=>'string'
		,'userKey' => $userkey
		,'boxId' => $boxid
		,'regDateId' => $regdateid
	];
	//pre($param);
	$result = commsad_curl("PATCH", $url, json_encode($param));
	print($result);
	exit;
}else{

	if($target == "PPZ"){
		$url = "{$domain}/offerwall/fail";
	}else{
		$url = "{$domain}/point/fail";
	}

	$param = [
		'clientCode'=>'hanapay'
		,'productCode'=>'string'
		,'startDate' => $sdate
		,'endDate' => $edate
		,'directProcessYn' => $manual_pay
		,'searchType'=>'USER_KEY'
		,'searchWord'=>$keyword
		,'page' => empty($paging->np)?"0":$paging->np-1
		,'pageSize'=>$paging->ps
	];
	//pre($param);
}

try{
	if(empty($_POST)){
		$result = commsad_curl("GET", $url, $param);
	}
	$result = json_decode($result, true);
	if(empty($result['resultCode'])){
		throw new Exception('API 연동 실패', 99);
	}
	$PG = $paging->init(empty($result['data']['totalElements'])?0:$result['data']['totalElements']);
	if($result['resultCode'] != 0000){
		throw new Exception($result['resultMessage'], $result['resultCode']);
	}
	if(empty($result['data']['failList'])){
		throw new Exception('검색된 데이터가 없습니다.', 80);
	}
	$row = $result['data']['failList'];
	$getMessage = "";
} catch(Exception $e) {
	$getMessage = $e->getMessage();
}
?>

<div class="contentpanel">

	<div class="panel panel-default" style="width:1500px">
		<div class="panel-heading">
			<div class="panel-btns">
				<!--a href="" class="panel-close">&times;</a-->
				<a href="" class="minimize">&minus;</a>
			</div><!-- panel-btns -->
			<h4 class="panel-title"><?=_title_?> </h4>
		</div><!-- panel-heading -->

		<div class="panel-body">


			<form name="scform" method="get" action="">
				<input type="hidden" name="target" value="<?=$target?>">
				<div class="row">
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

							<div class="pull-left">
								<span>
									<a class="btn btn-default <?=($type=='30')?'active':''?>" href="?target=<?=$target?>&type=30">최근30일</a>
									<a class="btn btn-default <?=($type=='M')?'active':''?>" href="?target=<?=$target?>&type=M">이번달</a>
									<a class="btn btn-default <?=($type=='B1')?'active':''?>" href="?target=<?=$target?>&type=B1">전월</a>
									<a class="btn btn-default <?=($type=='B2')?'active':''?>" href="?target=<?=$target?>&type=B2">전전월</a>
									<a class="btn btn-default <?=($type=='3M')?'active':''?>" href="?target=<?=$target?>&type=3M">3개월</a>
									<a class="btn btn-default <?=($type=='6M')?'active':''?>" href="?target=<?=$target?>&type=6M">6개월</a>
								</span>
							</div>
						</div><!-- header -->
				</div><!-- row -->

				<div class="row" style="margin-top: 15px;">
					<div class="pull-left" style="margin: 4px 0 0 0px; line-height: 30px;font-weight: bold;">수기 지급 여부</div>
					<label style="margin: 4px 0 0 20px">
						<div class="pull-left"><input style="width:20px;height:20px;accent-color: darkcyan;" type="radio" name="manual_pay" value="" <?=empty($manual_pay)?"checked":""?>></div>
						<div class="pull-left" style="padding-top:5px;padding-left:3px;">전체</div>
					</label>
					<label style="margin: 4px 0 0 20px">
						<div class="pull-left"><input style="width:20px;height:20px;accent-color: darkcyan;" type="radio" name="manual_pay" value="N" <?=($manual_pay=="N")?"checked":""?>></div>
						<div class="pull-left" style="padding-top:5px;padding-left:3px;">미지급</div>
					</label>
					<label style="margin: 4px 0 0 20px">
						<div class="pull-left"><input style="width:20px;height:20px;accent-color: darkcyan;" type="radio" name="manual_pay" value="Y" <?=($manual_pay=="Y")?"checked":""?>></div>
						<div class="pull-left" style="padding-top:5px;padding-left:3px;">지급 완료</div>
					</label>
				</div>

				<div class="row">
					<div class="pull-left">
						<select class="form-control pull-left border-input" name="key" style="width:150px;height: 40px;">
							<option value="user_key" <?=($key=='user_key')?'selected':''?>> 유저식별값 </option>
						</select>
						<input type="text" class="form-control pull-left border-input" name="keyword" style="width:300px;margin-left:10px;height: 40px;" value="<?=$keyword?>" placeholder="검색어" autocomplete="off" />
						<button class="btn btn-success" style="height: 40px;">검 색</button>
					</div>
				</div><!-- row -->
			</form>

			<div class="row col-xs-12 col-md-12">
				<div class="table-responsive" >
					<table class="table table-hover mb30 member_table" id="" style="border: 1px solid #b0b0b0;">
						<thead>
						<tr>
							<th>NO.</th>
							<th>유저식별값</th>
							<th>적립금액</th>
							<th>적립실패일시</th>
							<th>수기지급여부</th>
							<th>수기지급일시</th>
							<th>수기지급</th>
						</tr>
						</thead>
						<tbody>
					<?php if(empty($getMessage)){ ?>
					<?php foreach($result['data']['failList'] AS $row){ ?>
							<tr>
								<td><?=number_format($PG->first_num)?></td>
								<td><?=$row['userKey']?></td>
								<td><?=$row['earnPoint']?></td>
								<td><?=$row['regDate']?></td>
								<td><?=$row['directPorcessYn']=="N"?"미지급":"지급 완료"?></td>
								<td><?=$row['directProcessDate']?></td>
								<td>
									<?php if($row['directPorcessYn']=="N"){ ?>
										<button style="margin-bottom: 3px;" class="btn btn-xs btn-primary manual_pay" data-userkey="<?=$row['userKey']?>" data-boxid="<?=$row['boxId']?>" data-regdateid="<?=$row['regDateId']?>">지급하기</button>
									<?php }else{ ?>
										<button style="margin-bottom: 3px;cursor: default;" class="btn btn-xs btn-default">지급완료</button>
									<?php } ?>
								</td>
							</tr>
					<?php
								$PG->first_num--; }
						}else{
					?>
						<td colspan="7"><?=$getMessage?></td>
					<?php } ?>
					<tr><td colspan="7" style="text-align: left">※ 포인트 지급 실패시 재시도 횟수에 의해 자동으로 재시도(2일 소요)되며 재시도가 실패한 목록 입니다.</td></tr>
						</tbody>
					</table>
					<div class="row">
						<?=$paging->paging_new($PG,$PG_Param);?>
					</div><!-- row -->

				</div><!-- table-responsive -->

			</div><!-- row -->



		</div><!-- panel-body -->
	</div><!-- panel -->

</div><!-- contentpanel -->

<script>
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

	$(".manual_pay").on("click", function(){
		if(!confirm("수기 지급하시겠습니까?")){
			return false;
		}
		var userkey = $(this).data('userkey');
		var boxid = $(this).data('boxid');
		var regdateid = $(this).data('regdateid');

		$.post("./reward_point_manual.php",{userkey:userkey,"boxid":boxid,"regdateid":regdateid},function(data){
		//{"resultCode":"4000","resultMessage":"수기 지급 처리를 실패하였습니다. 개발자에게 문의해주세요."}
			var response = jQuery.parseJSON(data);
			alert(response.resultMessage);
			if(response.resultCode == "0000"){
				location.reload();
			}
		});

	});
</script>

<?php
include __foot__;
?>
