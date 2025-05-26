<?php
/**********************
 *
 *    OCB 키보드 광고 환경설정
 *
 **********************/
//error_reporting(E_ALL & ~E_NOTICE);
//ini_set("display_errors", 1);
## 환경설정
define('_title_', '환경설정');
define('_Menu_', 'adv');
define('_subMenu_', 'config');

include "./var.php";
include_once __func__;
include_once __pdoDB__;    ## DB Instance 생성
include_once __head__; ## html 헤더 출력

$sql="
	SELECT * FROM ocb_cfg_info
";
//pre($sql);
$config = $NDO->fetch_array($sql);
foreach($config as $res){
	$cfg[$res['cfg_nm']]=$res['cfg_val'];
}
$typing_game = unserialize($cfg['typing_game_YN']);
$ad_frequency = unserialize($cfg['ad_frequency']);
$noti_ad = unserialize($cfg['noti_ad']);
$ad_reward = unserialize($cfg['ad_reward']);
$reward_onoff = unserialize($cfg['reward_onoff']);
$offerwall_show = $cfg['offerwall_view'];
$offerwall_logic = $cfg['offerwall_logic'];

if($admin['level'] != 'super'){
	hist("권한이 없습니다.");
}

?>
<div class="pageheader">
	<h2><i class="fa fa-list-alt"></i> 환경설정 <span>환경설정</span></h2>
</div>

<div class="contentpanel">

	<div class="panel panel-default">
		<div class="panel-heading">
			<div class="panel-btns">
				<!--a href="" class="panel-close">&times;</a-->
				<a href="" class="minimize">&minus;</a>
			</div><!-- panel-btns -->
			<h4 class="panel-title">앱버전 관리 </h4>
		</div><!-- panel-heading -->
		<div class="panel-body">

			<div class="table-responsive">
				<table class="table member_table" style="margin: 0;">
					<thead>
					<tr>
						<th class="col-md-1">항목</th>
						<th>내용</th>
					</tr>
					</thead>
					<tbody>
					<tr>
						<td>안드로이드</td>
						<td>

							<div class="pull-left">
								<input type="text" class="form-control pull-left border-input" name="android_ver" style="width:152px; height:35px; margin-left:10px;" value="<?=$cfg['android_ver']?>" />
								<button id="android_ver" class="btn btn-success" >적 용</button>
							</div>

						</td>
					</tr>
					</tbody>
				</table>
			</div><!-- table-responsive -->

		</div><!-- panel-body -->
	</div><!-- panel panel-default -->

	<div class="panel panel-default">
		<div class="panel-heading">
			<div class="panel-btns">
				<!--a href="" class="panel-close">&times;</a-->
				<a href="" class="minimize">&minus;</a>

			</div><!-- panel-btns -->
			<h4 class="panel-title">모비온 연동 상태 </h4>
		</div><!-- panel-heading -->

		<div class="panel-body">

			<div class="table-responsive">
				<table class="table member_table" style="margin: 0;">
					<thead>
					<tr>
						<th class="col-md-1">항목</th>
						<th>내용</th>
					</tr>
					</thead>
					<tbody>
					<tr>
						<td>모비온 상태</td>
						<td>
							<div class="pull-left btn-group btn-toggle mobon_YN" style="margin-bottom: 0;" data-otpc="<?=$cfg['mobon_YN']?>">
								<button class="btn btn-xs <?=($cfg['mobon_YN']!='N') ? 'btn-primary active' : 'btn-default' ?>" type="button" value="Y">ON </button>
								<button class="btn btn-xs <?=($cfg['mobon_YN']=='N') ? 'btn-primary active' : 'btn-default' ?>" type="button" value="N">OFF </button>
							</div>
						</td>
					</tr>
					</tbody>
				</table>
			</div><!-- table-responsive -->

		</div><!-- panel-body -->
	</div><!-- panel -->


	<div class="panel panel-default">
		<div class="panel-heading">
			<div class="panel-btns">
				<!--a href="" class="panel-close">&times;</a-->
				<a href="" class="minimize">&minus;</a>

			</div><!-- panel-btns -->
			<h4 class="panel-title">아이콘 노출 상태 </h4>
		</div><!-- panel-heading -->

		<div class="panel-body">

			<div class="table-responsive">
				<table class="table member_table" style="margin: 0;">
					<thead>
					<tr>
						<th class="col-md-1">항목</th>
						<th>내용</th>
					</tr>
					</thead>
					<tbody>
					<tr>
						<td>타이핑 게임 상태</td>
						<td>
							<div class="pull-left btn-group btn-toggle typing_game_YN" style="margin-top: 5px;margin-bottom: 0;" data-otpc="<?=$typing_game['status']?>">
								<button class="btn btn-xs <?=($typing_game['status']!='N') ? 'btn-primary active' : 'btn-default' ?>" type="button" value="Y">ON </button>
								<button class="btn btn-xs <?=($typing_game['status']=='N') ? 'btn-primary active' : 'btn-default' ?>" type="button" value="N">OFF </button>
							</div>
							<div style="float: left;margin-left:10px;margin-top: 10px;">URL :
							</div>
							<div style="float: left;">
								<input type="text" class="form-control pull-left border-input" name="typing_game_url" id="typing_game_url" style="width:1200px; height:35px; margin-left:10px;display: inline" value="<?=$typing_game['url']?>">
								<button id="typing_game_btn" class="btn btn-success">적 용</button>
							</div>
						</td>
					</tr>
					</tbody>
				</table>
			</div><!-- table-responsive -->

		</div><!-- panel-body -->
	</div><!-- panel -->


	<div class="panel panel-default">
		<div class="panel-heading">
			<div class="panel-btns">
				<!--a href="" class="panel-close">&times;</a-->
				<a href="" class="minimize">&minus;</a>

			</div><!-- panel-btns -->
			<h4 class="panel-title" style="width: 350px;line-height: 35px;">
				광고 노출 빈도 (frequency)
				<select class="form-control border-input" name="ratio" id="ratio" style="width:100px; height:40px; margin-left:10px;float: right;"  >
					<option value="0">미적용</option>
					<?php for($i=10;$i<=100;$i+=10){?>
					<option value="<?=$i?>" <?=$ad_frequency['ratio']==$i?"selected":""?>><?=$i?>%</option>
					<?php }?>
				</select>
			</h4>

		</div><!-- panel-heading -->

		<div class="panel-body">

			<div class="table-responsive">
				<p><code style="font-size: 12px;"> * 10회 1사이클 당 여러번 노출시 콤마로 구분 예) 3,6</code></p>
				<p><code style="font-size: 12px;"> * 노출 순서가 겹칠경우 누락될 수 있습니다</code></p>
				<table class="table member_table" style="margin: 0;">
					<thead>
					<tr>
						<th class="col-md-1">항목</th>
						<th class="col-md-6">내용</th>
						<th></th>
					</tr>

					</thead>
					<tbody>
					<tr>
						<td>모비온</td>
						<td>
							<div class="pull-left">
								<input type="text" class="form-control pull-left border-input" name="mobon" id="mobon" style="width:152px; height:35px; margin-left:10px;" value="<?=$ad_frequency['mobon']?>" />
<!--								<button class="btn btn-success" >적 용</button>-->
<!--								<code>&emsp;&emsp;*.</code>-->
							</div>
						</td>
						<td rowspan="7"><button id="ad_frequency" class="btn btn-success" style="float: left;">적 용</button></td>
					</tr>
					<tr>
						<td>외부광고</td>
						<td>
							<div class="pull-left">
								<input type="text" class="form-control pull-left border-input" name="mediation" id="mediation" style="width:152px; height:35px; margin-left:10px;" value="<?=$ad_frequency['mediation']?>" />
							</div>
						</td>
					</tr>
					<tr>
						<td>텍스트광고<br />(구배너광고)</td>
						<td>
							<div class="pull-left">
								<input type="text" class="form-control pull-left border-input" name="banner" id="banner" style="width:152px; height:35px; margin-left:10px;" value="<?=$ad_frequency['banner']?>" />
							</div>
						</td>
					</tr>
					<tr>
						<td>쿠팡 다이나믹 배너</td>
						<td>
							<div class="pull-left">
								<input type="text" class="form-control pull-left border-input" name="coupang" id="coupang" style="width:152px; height:35px; margin-left:10px;" value="<?=$ad_frequency['coupang']?>" />
							</div>
						</td>
					</tr>
					<tr>
						<td>크리테오</td>
						<td>
							<div class="pull-left">
								<input type="text" class="form-control pull-left border-input" name="criteo" id="criteo" style="width:152px; height:35px; margin-left:10px;" value="<?=$ad_frequency['criteo']?>" />
							</div>
						</td>
					</tr>
					<tr>
						<td>리워드 광고</td>
						<td>
							<div class="pull-left">
								<input type="text" class="form-control border-input" name="reward_cycle" id="reward_cycle" style="width:152px; height:35px; margin-left:10px;" value="<?=$ad_frequency['reward']?>">
							</div>
						</td>
					</tr>
					</tbody>
				</table>
			</div><!-- table-responsive -->

		</div><!-- panel-body -->
	</div><!-- panel -->


	<div class="panel panel-default">
		<div class="panel-heading">
			<div class="panel-btns">
				<!--a href="" class="panel-close">&times;</a-->
				<a href="" class="minimize">&minus;</a>

			</div><!-- panel-btns -->
			<h4 class="panel-title">노티 광고 설정 <button id="noti_ad" class="btn btn-success">적 용</button></h4>

		</div><!-- panel-heading -->

		<div class="panel-body">

			<div class="table-responsive">
				<table class="table member_table" style="margin: 0;">
					<thead>
					<tr>
						<th class="col-md-1">항목</th>
						<th>내용</th>
					</tr>
					</thead>
					<tbody>
					<tr>
						<td>유지시간</td>
						<td>
							<div style="float: left;">
								<input type="number" class="form-control border-input" name="noti_holding_time" id="noti_holding_time" style="width:50px; height:35px; margin-left:10px;display: inline" value="<?=$noti_ad['noti_holding_time']?>"> 초
							</div>
						</td>
					</tr>
					<tr>
						<td>지급 포인트</td>
						<td>
							<div style="float: left;">
								<input type="number" class="form-control border-input" name="noti_point" id="noti_point" style="width:50px; height:35px; margin-left:10px;display: inline" value="<?=$noti_ad['noti_point']?>">
							</div>
						</td>
					</tr>
					</tbody>
				</table>
			</div><!-- table-responsive -->

		</div><!-- panel-body -->
	</div><!-- panel -->


	<div class="panel panel-default">
		<div class="panel-heading">
			<div class="panel-btns">
				<!--a href="" class="panel-close">&times;</a-->
				<a href="" class="minimize">&minus;</a>

			</div><!-- panel-btns -->
			<h4 class="panel-title">리워드 광고 설정 <button id="ad_reward" class="btn btn-success">적 용</button></h4>

		</div><!-- panel-heading -->

		<div class="panel-body">

			<div class="table-responsive">
				<table class="table member_table" style="margin: 0;">
					<thead>
					<tr>
						<th class="col-md-1">항목</th>
						<th>내용</th>
					</tr>
					</thead>
					<tbody>
					<tr>
						<td>최대 노출 수</td>
						<td>
							<div style="float: left;">
								<input type="number" class="form-control border-input" name="reward_max_view" id="reward_max_view" style="width:60px; height:35px; margin-left:10px;display: inline" value="<?=$ad_reward['reward_max_view']?>">  (한 회원당 일일 X회 광고 노출)
							</div>
						</td>
					</tr>
					<tr>
						<td>유지시간</td>
						<td>
							<div style="float: left;">
								<input type="number" class="form-control border-input" name="reward_holding_time" id="reward_holding_time" style="width:50px; height:35px; margin-left:10px;display: inline" value="<?=$ad_reward['reward_holding_time']?>"> 초
							</div>
						</td>
					</tr>
					<tr>
						<td>지급 포인트</td>
						<td>
							<div style="float: left;">
								<input type="number" class="form-control border-input" name="reward_point" id="reward_point" style="width:60px; height:35px; margin-left:10px;display: inline" value="<?=$ad_reward['reward_point']?>">
							</div>
						</td>
					</tr>
					<tr>
						<td>모비온</td>
						<td>
							<div class="pull-left btn-group btn-toggle reward_mobon" style="margin-bottom: 0;">
								<button class="btn btn-xs <?=($reward_onoff['reward_mobon']!='N') ? 'btn-primary active' : 'btn-default' ?>" type="button" value="Y">ON </button>
								<button class="btn btn-xs <?=($reward_onoff['reward_mobon']=='N') ? 'btn-primary active' : 'btn-default' ?>" type="button" value="N">OFF </button>
							</div>
						</td>
					</tr>
					<tr>
						<td>뉴스</td>
						<td>
							<div class="pull-left btn-group btn-toggle reward_news" style="margin-bottom: 0;">
								<button class="btn btn-xs <?=($reward_onoff['reward_news']!='N') ? 'btn-primary active' : 'btn-default' ?>" type="button" value="Y">ON </button>
								<button class="btn btn-xs <?=($reward_onoff['reward_news']=='N') ? 'btn-primary active' : 'btn-default' ?>" type="button" value="N">OFF </button>
							</div>
						</td>
					</tr>
					<tr>
						<td rowspan="3">리워드 통합</td>
						<td>
							<div class="pull-left btn-group btn-toggle reward_coupang" style="margin-top: 5px;margin-bottom: 0;">
								<button class="btn btn-xs <?=($reward_onoff['reward_coupang']!='N') ? 'btn-primary active' : 'btn-default' ?>" type="button" value="Y">ON </button>
								<button class="btn btn-xs <?=($reward_onoff['reward_coupang']=='N') ? 'btn-primary active' : 'btn-default' ?>" type="button" value="N">OFF </button>
							</div>
							<div style="float: left;margin-left:10px;margin-top: 10px;">쿠팡</div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="pull-left btn-group btn-toggle reward_criteo" style="margin-top: 5px;margin-bottom: 0;">
								<button class="btn btn-xs <?=($reward_onoff['reward_criteo']!='N') ? 'btn-primary active' : 'btn-default' ?>" type="button" value="Y">ON </button>
								<button class="btn btn-xs <?=($reward_onoff['reward_criteo']=='N') ? 'btn-primary active' : 'btn-default' ?>" type="button" value="N">OFF </button>
							</div>
							<div style="float: left;margin-left:10px;margin-top: 10px;">크리테오</div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="pull-left btn-group btn-toggle reward_moneytree" style="margin-top: 5px;margin-bottom: 0;">
								<button class="btn btn-xs <?=($reward_onoff['reward_moneytree']!='N') ? 'btn-primary active' : 'btn-default' ?>" type="button" value="Y">ON </button>
								<button class="btn btn-xs <?=($reward_onoff['reward_moneytree']=='N') ? 'btn-primary active' : 'btn-default' ?>" type="button" value="N">OFF </button>
							</div>
							<div style="float: left;margin-left:10px;margin-top: 10px;">머니트리</div>
						</td>
					</tr>
					</tbody>
				</table>
			</div><!-- table-responsive -->

		</div><!-- panel-body -->
	</div><!-- panel -->



	<div class="panel panel-default">
		<div class="panel-heading">
			<div class="panel-btns">
				<!--a href="" class="panel-close">&times;</a-->
				<a href="" class="minimize">&minus;</a>

			</div><!-- panel-btns -->
			<h4 class="panel-title">오퍼월 버튼 설정 <button id="offerwall_btn" class="btn btn-success">적 용</button></h4>

		</div><!-- panel-heading -->

		<div class="panel-body">

			<div class="table-responsive">
				<table class="table member_table" style="margin: 0;">
					<thead>
					<tr>
						<th class="col-md-1">항목</th>
						<th>내용</th>
					</tr>
					</thead>
					<tbody>
					<tr>
						<td>로직변경</td>
						<td>
							<div class="pull-left btn-group btn-toggle offerwall_logic" style="margin-bottom: 0;" data-otpc="<?=$offerwall_logic?>">
								<button class="btn btn-xs <?=($offerwall_logic=='Hybrid') ? 'btn-primary active' : 'btn-default' ?>" type="button" value="Hybrid">Hybrid</button>
								<button class="btn btn-xs <?=($offerwall_logic=='Native') ? 'btn-primary active' : 'btn-default' ?>" type="button" value="Native">Native</button>
							</div>
						</td>
					</tr>
					<tr>
						<td>오퍼월 버튼 설정</td>
						<td>
							<div style="float: left;">
								적립버튼 클릭 횟수<input type="number" class="form-control border-input" name="offerwall_show" id="offerwall_show" style="width:50px; height:35px; margin-left:10px;display: inline" value="<?=$offerwall_show?>">  회째 오퍼월 버튼 노출
							</div>
						</td>
					</tr>
					</tbody>
				</table>
			</div><!-- table-responsive -->

		</div><!-- panel-body -->
	</div><!-- panel -->


</div><!-- contentpanel -->

<script>
$(document).ready(function(){

	//앱버전 관리
	$("#android_ver").on("click",function(){
		var android_ver = $("input[name='android_ver']").val();
		if(!android_ver){
			alert("버전을 입력 하세요.");
			$("input[name='android_ver']").focus();
			return;
		}
		var formData = {mode:"android_ver",android_ver:android_ver};
		$.post("./ajax/ajax_process.php",formData,function(result){
			if(result === "ok"){
				alert("적용 되었습니다.");
			}else if(result === "empty"){
				alert("버전을 입력 하세요.");
				$("input[name='android_ver']").focus();
			}else {
				alert("수정 실패");
			}
		},"html");
	});
	//모비온 연동 상태
	$(".mobon_YN").on("click",function(){
		var mType = $(this).data("otpc");
		var formData = {mode:"mobon_YN",mType:mType};
		$.post("./ajax/ajax_process.php",formData,function(result){
			if(result){
				$(".mobon_YN").data("otpc",result);
				alert("적용 되었습니다.");
			}else{
				if(formData.mType === "Y") {
					$(".mobon_YN > button").eq(0).addClass("btn-primary").removeClass("btn-default").addClass("active");
					$(".mobon_YN > button").eq(1).addClass("btn-default").removeClass("btn-primary").removeClass("active");
				}else{
					$(".mobon_YN > button").eq(0).addClass("btn-default").removeClass("btn-primary").removeClass("active");
					$(".mobon_YN > button").eq(1).addClass("btn-primary").removeClass("btn-default").addClass("active");
				}

				alert("수정 실패");
			}
		},"html");
	});
	//타이핑 게임 연동 상태
	$(".typing_game_YN, #typing_game_btn").on("click",function(){
		var id = $(this).attr("id");

		if(id==="typing_game_btn"){
			var mType = $(".typing_game_YN").data("otpc")==="Y"?"N":"Y";
		}else{
			var mType = $(".typing_game_YN").data("otpc");

		}
		var url = $("#typing_game_url").val();
		var formData = {mode:"typing_game_YN",mType:mType, url:url};
		$.post("./ajax/ajax_process.php",formData,function(result){
			if(result){
				$(".typing_game_YN").data("otpc",result);
				alert("적용 되었습니다.");
			}else{
				if(formData.mType === "Y") {
					$(".typing_game_YN > button").eq(0).addClass("btn-primary").removeClass("btn-default").addClass("active");
					$(".typing_game_YN > button").eq(1).addClass("btn-default").removeClass("btn-primary").removeClass("active");
				}else{
					$(".typing_game_YN > button").eq(0).addClass("btn-default").removeClass("btn-primary").removeClass("active");
					$(".typing_game_YN > button").eq(1).addClass("btn-primary").removeClass("btn-default").addClass("active");
				}

				alert("수정 실패");
			}
		},"html");
	});
	//광고 노출 빈도 (frequency)
	$("#ad_frequency").on("click",function(){
		var ratio = $("#ratio").val();
		var mobon = $("#mobon").val();
		var mediation = $("#mediation").val();
		var banner = $("#banner").val();
		var notice = $("#notice").val();
		var coupang = $("#coupang").val();
		var criteo = $("#criteo").val();
		var reward = $("#reward_cycle").val();

		var formData = {
			mode:"ad_frequency"
			,ratio:ratio
			,mobon:mobon
			,mediation:mediation
			,banner:banner
			,coupang:coupang
			,notice:notice
			,criteo:criteo
			,reward:reward
		};
		$.post("./ajax/ajax_process.php",formData,function(result){
			switch(result){
				case "error0" : alert("노출빈도 비율이 잘못되었습니다.");$("#ratio").focus();break;
				case "error1" : alert("모비온 값이 잘못되었습니다.");$("#mobon").focus();break;
				case "error2" : alert("외부광고 값이 잘못되었습니다.");$("#mediation").focus();break;
				case "error3" : alert("배너광고 값이 잘못되었습니다.");$("#banner").focus();break;
				case "error4" : alert("쿠팡광고 값이 잘못되었습니다.");$("#coupang").focus();break;
				case "error5" : alert("공지사항 값이 잘못되었습니다.");$("#notice").focus();break;
				case "error6" : alert("크리테오 값이 잘못되었습니다.");$("#criteo").focus();break;
				case "error7" : alert("리워드 값이 잘못되었습니다.");$("#reward_cycle").focus();break;
				case "ok" : alert("적용 되었습니다.");break;
				default : alert("업데이트 실패");break;
			}
		},"html");
	});

	//노티광고 설정
	$("#noti_ad").on("click",function(){
		var noti_holding_time = $("input[name='noti_holding_time']").val();
		if(!noti_holding_time){
			alert("유지시간을 입력 하세요.");
			$("input[name='noti_holding_time']").focus();
			return;
		}
		var noti_point = $("input[name='noti_point']").val();
		if(!noti_point){
			alert("지급 포인트를 입력 하세요.");
			$("input[name='noti_point']").focus();
			return;
		}
		var formData = {mode:"noti_ad",noti_holding_time:noti_holding_time,noti_point:noti_point};
		$.post("./ajax/ajax_process.php",formData,function(result){
			if(result === "ok"){
				alert("적용 되었습니다.");
			}else {
				alert("수정 실패");
			}
		},"html");
	});


	//광고 리워드 설정
	$("#ad_reward").on("click",function(){
		var reward_max_view = $("input[name='reward_max_view']").val();
		var reward_holding_time = $("input[name='reward_holding_time']").val();
		var reward_point = $("input[name='reward_point']").val();

		if(!reward_max_view){
			alert("최대 노출 수를 입력 하세요.");
			$("input[name='reward_max_view']").focus();
			return;
		}
		if(!reward_holding_time){
			alert("유지시간을 입력 하세요.");
			$("input[name='reward_holding_time']").focus();
			return;
		}
		if(!reward_point){
			alert("지급할 포인트를 입력 하세요.");
			$("input[name='reward_point']").focus();
			return;
		}

		var formData = {mode:"ad_reward",reward_max_view:reward_max_view,reward_holding_time:reward_holding_time,reward_point:reward_point};
		$.post("./ajax/ajax_process.php",formData,function(result){
			if(result === "ok"){
				alert("적용 되었습니다.");
			}else {
				alert("수정 실패");
			}
		},"html");
	});

	//리워드광고 on / off
	$(".reward_coupang, .reward_moneytree, .reward_news, .reward_mobon, .reward_criteo").on("click",function(){
		var reward_coupang = $(".reward_coupang > .active ").val();
		var reward_moneytree = $(".reward_moneytree > .active ").val();
		var reward_news = $(".reward_news > .active ").val();
		var reward_mobon = $(".reward_mobon > .active ").val();
		var reward_criteo = $(".reward_criteo > .active ").val();

		if($(this).hasClass("reward_coupang") == true){
			var this_click = ".reward_coupang";
		}
		if($(this).hasClass("reward_moneytree") == true){
			var this_click = ".reward_moneytree";
		}
		if($(this).hasClass("reward_news") == true){
			var this_click = ".reward_news";
		}
		if($(this).hasClass("reward_mobon") == true){
			var this_click = ".reward_mobon";
		}
		if($(this).hasClass("reward_criteo") == true){
			var this_click = ".reward_criteo";
		}
		var formData = { mode:"reward_onoff",reward_coupang:reward_coupang,reward_moneytree:reward_moneytree,reward_news:reward_news,reward_mobon:reward_mobon,reward_criteo:reward_criteo };
		$.post("./ajax/ajax_process.php",formData,function(result){
			if(result){
				alert("적용 되었습니다.");
			}else{

				switch(this_click){
					case ".reward_coupang" :
						if(reward_coupang === "N") {
							$(this_click+" > button").eq(0).addClass("btn-primary").removeClass("btn-default").addClass("active");
							$(this_click+" > button").eq(1).addClass("btn-default").removeClass("btn-primary").removeClass("active");
						}else{
							$(this_click+" > button").eq(0).addClass("btn-default").removeClass("btn-primary").removeClass("active");
							$(this_click+" > button").eq(1).addClass("btn-primary").removeClass("btn-default").addClass("active");
						}
					break;
					case ".reward_moneytree" :
						if(reward_moneytree === "N") {
							$(this_click+" > button").eq(0).addClass("btn-primary").removeClass("btn-default").addClass("active");
							$(this_click+" > button").eq(1).addClass("btn-default").removeClass("btn-primary").removeClass("active");
						}else{
							$(this_click+" > button").eq(0).addClass("btn-default").removeClass("btn-primary").removeClass("active");
							$(this_click+" > button").eq(1).addClass("btn-primary").removeClass("btn-default").addClass("active");
						}
					break;
					case ".reward_news" :
						if(reward_news === "N") {
							$(this_click+" > button").eq(0).addClass("btn-primary").removeClass("btn-default").addClass("active");
							$(this_click+" > button").eq(1).addClass("btn-default").removeClass("btn-primary").removeClass("active");
						}else{
							$(this_click+" > button").eq(0).addClass("btn-default").removeClass("btn-primary").removeClass("active");
							$(this_click+" > button").eq(1).addClass("btn-primary").removeClass("btn-default").addClass("active");
						}
					break;
					case ".reward_mobon" :
						if(reward_mobon === "N") {
							$(this_click+" > button").eq(0).addClass("btn-primary").removeClass("btn-default").addClass("active");
							$(this_click+" > button").eq(1).addClass("btn-default").removeClass("btn-primary").removeClass("active");
						}else{
							$(this_click+" > button").eq(0).addClass("btn-default").removeClass("btn-primary").removeClass("active");
							$(this_click+" > button").eq(1).addClass("btn-primary").removeClass("btn-default").addClass("active");
						}
					break;
					case ".reward_criteo" :
						if(reward_criteo === "N") {
							$(this_click+" > button").eq(0).addClass("btn-primary").removeClass("btn-default").addClass("active");
							$(this_click+" > button").eq(1).addClass("btn-default").removeClass("btn-primary").removeClass("active");
						}else{
							$(this_click+" > button").eq(0).addClass("btn-default").removeClass("btn-primary").removeClass("active");
							$(this_click+" > button").eq(1).addClass("btn-primary").removeClass("btn-default").addClass("active");
						}
					break;
				}
				alert("수정 실패");
			}
		},"html");
	});

	//오퍼월 버튼 설정
	$("#offerwall_btn").on("click",function(){
		var offerwall_show = $("input[name='offerwall_show']").val();
		if(!offerwall_show){
			alert("오퍼월 설정의 숫자를 입력 하세요.");
			$("input[name='offerwall_show']").focus();
			return;
		}
		var formData = {mode:"offerwall_show",offerwall_show:offerwall_show};
		$.post("./ajax/ajax_process.php",formData,function(result){
			if(result === "ok"){
				alert("적용 되었습니다.");
			}else {
				alert("수정 실패");
			}
		},"html");
	});

	//오퍼월 로직 설정
	$(".offerwall_logic").on("click",function(){
		var mType = $(this).data("otpc")==="Hybrid"?"Native":"Hybrid";
		var formData = {mode:"offerwall_logic",mType:mType};
		$.post("./ajax/ajax_process.php",formData,function(result){
			if(result){
				$(".offerwall_logic").data("otpc",result);
				alert("적용 되었습니다.");
			}else{
				if(formData.mType === "Y") {
					$(".offerwall_logic > button").eq(0).addClass("btn-primary").removeClass("btn-default").addClass("active");
					$(".offerwall_logic > button").eq(1).addClass("btn-default").removeClass("btn-primary").removeClass("active");
				}else{
					$(".offerwall_logic > button").eq(0).addClass("btn-default").removeClass("btn-primary").removeClass("active");
					$(".offerwall_logic > button").eq(1).addClass("btn-primary").removeClass("btn-default").addClass("active");
				}

				alert("수정 실패");
			}
		},"html");
	});
});
</script>

<?php
include __foot__;
?>
