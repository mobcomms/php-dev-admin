<?php
/**********************
 *
 *    스팟성 포인트 지급
 *
 **********************/
//error_reporting(E_ALL & ~E_NOTICE);
//ini_set("display_errors", 1);

include "./var.php";
include_once __func__;
include_once __pdoDB__;    ## DB Instance 생성
include __fn__;

$code_id = '03';
if(!empty($_POST)){
	if($_POST['mode'] == "use_YN"){
		//pre($_POST);
		$use_YN = $_POST['use_YN']=='Y' ? 'N':'Y';
		$sql = "
			UPDATE ckd_game_zone_set SET use_YN='{$use_YN}'
			WHERE code_id = '{$code_id}'
		";
		//pre($sql);
		$result = $NDO->sql_query($sql);
		if(!empty($result)){
			echo $use_YN;
		}

	}else if($_POST['mode'] == "save"){
		$max_game_join = $_POST['max_game_join'];
        $max_game_join2 = $_POST['max_game_join2'];
		//$rejoin_game_time = $_POST['rejoin_game_time'];
        $ad1_type = $_POST['ad1_type'];
        $ad2_type = $_POST['ad2_type'];

        $sum_value = "0"; // 문자열로 초기화
        foreach ($_POST['odds_set_percent'] as $key => $value) {
            if (empty($value)) {
                continue;
            }
            //$sum_value = bcadd($sum_value, $value, 16); // 정밀도 16자리로 덧셈
            $sum_value += (float)$value; // 일반 float 덧셈 사용
        }
//        if (bccomp($sum_value, "1", 16) !== 0) {
//            exit("error1");
//        }
        if (abs($sum_value - 1.0) > 0.00001) { // 부동 소수점 비교를 위한 허용 오차 사용
            exit("error1");
        }
		$odds_set_point = serialize($_POST['odds_set_point']);
		$odds_set_percent = serialize($_POST['odds_set_percent']);

		$memo_title = $_POST['memo_title'];
		$memo = htmlspecialchars($_POST['memo']);
		$message_succ = $_POST['message_succ'];
		$message_failure = $_POST['message_failure'];

		//$column = "code_id='{$code_id}', max_game_join='{$max_game_join}', rejoin_game_time='{$rejoin_game_time}', odds_set_point='{$odds_set_point}', odds_set_percent='{$odds_set_percent}', memo='$memo', memo_title='$memo_title', message_succ='{$message_succ}', message_failure='{$message_failure}', reg_date=now()";
        $column = "code_id='{$code_id}', max_game_join='{$max_game_join}', max_game_join2='{$max_game_join2}', odds_set_point='{$odds_set_point}', odds_set_percent='{$odds_set_percent}', memo='$memo', memo_title='$memo_title', message_succ='{$message_succ}', message_failure='{$message_failure}',ad1_type='{$ad1_type}',ad2_type='{$ad2_type}', reg_date=now()";
		$sql = "
			INSERT INTO ckd_game_zone_set SET {$column}
			ON DUPLICATE KEY UPDATE {$column}
		";
		//pre($sql);
		$result = $NDO->sql_query($sql);
		if(!empty($result)){
			exit("ok");
		}
	}

	exit();
}
## 환경설정
define('_title_', '대박 복권 설정');
define('_Menu_', 'gamezone');
define('_subMenu_', 'set_ticket');

include_once __head__; ## html 헤더 출력

if($admin['level'] != 'super'){
	hist("권한이 없습니다.");
}

$qry = "SELECT * FROM ckd_game_zone_set WHERE code_id='{$code_id}'";
$result = $NDO->getdata($qry);
//pre($result);

if(!empty($result['odds_set_point'])){
	$odds_set_point = unserialize($result['odds_set_point']);
}
if(!empty($result['odds_set_percent'])){
	$odds_set_percent = unserialize($result['odds_set_percent']);
}

?>
<div class="pageheader">
	<h4 class="panel-title"><?=_title_?></h4>
</div>

<div class="contentpanel">
	<div class="panel panel-default">
		<div class="panel-heading">

			<div class="panel-btns">
				<!--a href="" class="panel-close">&times;</a-->
				<a href="" class="minimize">&minus;</a>
			</div><!-- panel-btns -->
			<h4 class="panel-title">
				<div style="float: left;line-height: 40px;padding-right: 20px;">게임 사용 여부</div>
				<div class="btn-group btn-toggle use_YN" style="margin-bottom: 0;" data-otpc="<?=$result['use_YN']?>" data-code_id="<?=$result['code_id']?>">
					<button class="btn btn-xs <?=($result['use_YN']!='N') ? 'btn-primary active' : 'btn-default' ?>" type="button" value="Y" style="z-index: 0;">ON </button>
					<button class="btn btn-xs <?=($result['use_YN']=='N') ? 'btn-primary active' : 'btn-default' ?>" type="button" value="N" style="z-index: 0;">OFF </button>
				</div>
				<button class="btn btn-success save" data-code_id="<?=$result['code_id']?>" style="margin-left:10px;">저장</button>
			</h4>

		</div><!-- panel-heading -->
		<form name="form">
			<input type="hidden" name="mode" value="save">
			<div class="panel-body">
                <div class="form-group" style="height: 40px;">
                    <div class="col-sm-2" style="line-height:35px;width:350px;">버튼1 참여방식
                        <div style="float: right;">
                            <label style="padding-right: 7px;cursor: pointer">
                                <div class="pull-left"><input style="width:20px;height:20px;margin-top: 7px;" type="radio" name="ad1_type" value="ad1_1" <?=$result['ad1_type']=="ad1_1"?"checked":""?>></div>
                                <div class="pull-left" style="padding-left: 3px;">연속참여</div>
                            </label>
                            <label style="padding-right: 7px;cursor: pointer">
                                <div class="pull-left"><input style="width:20px;height:20px;margin-top: 7px;" type="radio" name="ad1_type" value="ad1_2" <?=$result['ad1_type']=="ad1_2"?"checked":""?>></div>
                                <div class="pull-left" style="padding-left: 3px;">정시참여</div>
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-2" style="line-height:35px;width:140px;">1일 최대 참여 횟수</div>
                    <input type="number" class="form-control pull-left border-input" style="width:55px; height:35px;" autocomplete="off" name="max_game_join" value="<?=$result['max_game_join']?>">
                    <div class="col-sm-1" style="width: 50px;line-height:35px;">회</div>
                    <!--				<div class="col-sm-2" style="line-height:35px;width:100px;">재 참여 시간</div>-->
                    <!--                <input type="number" class="form-control pull-left border-input"  style="width:100px; height:35px;" autocomplete="off" name="rejoin_game_time" value="--><?php //=$result['rejoin_game_time']?><!--">-->
                </div>
                <hr style="border: 1px #e7e7e7 dashed">

                <div class="form-group" style="height: 40px;">
                    <div class="col-sm-2" style="line-height:35px;width:350px;">버튼2 참여방식
                        <div style="float: right;">
                            <label style="padding-right: 7px;cursor: pointer">
                                <div class="pull-left"><input style="width:20px;height:20px;margin-top: 7px;" type="radio" name="ad2_type" value="ad2_1" <?=$result['ad2_type']=="ad2_1"?"checked":""?>></div>
                                <div class="pull-left" style="padding-left: 3px;">연속참여</div>
                            </label>
                            <label style="padding-right: 7px;cursor: pointer">
                                <div class="pull-left"><input style="width:20px;height:20px;margin-top: 7px;" type="radio" name="ad2_type" value="ad2_2" <?=$result['ad2_type']=="ad2_2"?"checked":""?>></div>
                                <div class="pull-left" style="padding-left: 3px;">정시참여</div>
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-2" style="line-height:35px;width:140px;">1일 최대 참여 횟수</div>
                    <input type="number" class="form-control pull-left border-input" style="width:55px; height:35px;" autocomplete="off" name="max_game_join2" value="<?=$result['max_game_join2']?>">
                    <div class="col-sm-1" style="width: 50px;line-height:35px;">회</div>
                    <!--				<div class="col-sm-2" style="line-height:35px;width:100px;">재 참여 시간</div>-->
                    <!--                <input type="number" class="form-control pull-left border-input"  style="width:100px; height:35px;" autocomplete="off" name="rejoin_game_time" value="--><?php //=$result['rejoin_game_time']?><!--">-->
                </div>
                <hr style="border: 1px #e7e7e7 dashed">

				<div class="form-group">
					<div class="col-sm-2" style="line-height:35px;width:200px;font-weight: bold;">포인트 정보</div>
					P=정수만 입력 가능, 0 입력시 ‘꽝!’<br>확률 = 총 합 1이 되어야함
				</div>
				<div class="form-group">
					<div class="col-sm-2" style="line-height:35px;width:200px;">1</div>
					<div class="col-sm-2" style="line-height:35px;width:200px;">2</div>
					<div class="col-sm-2" style="line-height:35px;width:200px;">3</div>
					<div class="col-sm-2" style="line-height:35px;width:200px;">4</div>
					<div class="col-sm-2" style="line-height:35px;width:200px;">5</div>
					<div class="col-sm-2" style="line-height:35px;width:200px;">6</div>
				</div>
				<div class="form-group">
					<?php for($i=0;$i<6;$i++){ ?>
						<div class="col-sm-2" style="line-height:35px;width:200px;">
							<input type="number" class="form-control pull-left border-input" style="width:80px; height:35px;" autocomplete="off" name="odds_set_point[<?=$i?>]" value="<?=$odds_set_point[$i]?>">
							<div class="col-sm-1" style="line-height:35px;">P</div>
						</div>
					<?php } ?>
				</div>
				<div class="form-group">
					<?php for($i=0;$i<6;$i++){ ?>
						<div class="col-sm-2" style="line-height:35px;width:200px;">
							<input type="number" step="0.001" class="form-control pull-left border-input" style="width:80px; height:35px;" autocomplete="off" name="odds_set_percent[<?=$i?>]" value="<?=$odds_set_percent[$i]?>">
							<div class="col-sm-4" style="line-height:35px;">확률</div>
						</div>
					<?php } ?>
				</div>
				<hr style="border: 1px #e7e7e7 dashed">

				<div class="form-group">
					<div class="col-sm-2" style="line-height:35px;width:200px;font-weight: bold;">토스트 메시지</div>
				</div>
				<div class="form-group">
					<div class="col-sm-1" style="line-height:35px;width:100px;">당첨</div>
					<input type="text" class="form-control pull-left border-input" name="message_succ" value="<?=$result['message_succ']?>" style="width:600px; height:35px;" autocomplete="off">
				</div>
				<div class="form-group">
					<div class="col-sm-1" style="line-height:35px;width:100px;">꽝</div>
					<input type="text" class="form-control pull-left border-input" name="message_failure" value="<?=$result['message_failure']?>" style="width:600px; height:35px;" autocomplete="off">
				</div>
				<hr style="border: 1px #e7e7e7 dashed">

				<div class="form-group">
					<div class="col-sm-2" style="line-height:35px;width:200px;font-weight: bold;">안내 문구</div>
				</div>
				<div class="form-group">
					<div class="col-sm-1" style="line-height:35px;width:100px;">타이틀</div>
					<input type="text" class="form-control pull-left border-input" name="memo_title" value="<?=$result['memo_title']?>" style="width:600px; height:35px;" autocomplete="off">
				</div>
				<div class="form-group">
					<div class="col-sm-1" style="line-height:35px;width:100px;">내용</div>
					<textarea style="width:600px; height:160px;" class="form-control pull-left border-input" name="memo"><?=$result['memo']?></textarea>
				</div>
				<div class="form-group">
				</div>

			</div><!-- panel-body -->
		</form>
	</div><!-- panel panel-default -->
</div>

<script>
	$(document).ready(function(){

		//ON OFF 버튼 통합
		$(".use_YN").on("click",function(){
			var use_YN = $(this).data("otpc");
			var code_id = $(this).data("code_id");
			var formData = {mode:"use_YN", code_id:code_id, use_YN:use_YN};
			$.post("..<?=__self__?>",formData,function(result){
				if(result === "Y" || result === "N"){
					$("div[data-code_id="+code_id+"]").data("otpc",result);
					alert("적용 되었습니다.");
				}else{
					if(use_YN === "Y") {
						$(".use_YN[data-code_id='"+code_id+"']").children("button").eq(0).addClass("btn-primary").removeClass("btn-default").addClass("active");
						$(".use_YN[data-code_id='"+code_id+"']").children("button").eq(1).addClass("btn-default").removeClass("btn-primary").removeClass("active");
					}else{
						$(".use_YN[data-code_id='"+code_id+"']").children("button").eq(0).addClass("btn-default").removeClass("btn-primary").removeClass("active");
						$(".use_YN[data-code_id='"+code_id+"']").children("button").eq(1).addClass("btn-primary").removeClass("btn-default").addClass("active");
					}
					alert("수정 실패");
				}
			},"html");
		});

		//저장
		$(".save").on("click",function(){
			var formData = $("form").serialize();
			$.post("..<?=__self__?>",formData,function(result){
				if(result === "ok"){
					alert("적용 되었습니다.");
				}else if(result === "error1"){
					alert("확률의 합계가 1이 아닙니다.");
				}else{
					alert("수정 실패");
				}
			},"html");
		});

		$("input[name^='pay_cycle']").on("change",function(){
			var code_id = $(this).data("code_id");
			var this_value= $(this).val();

			if(this_value === "04"){
				$("input[name='term["+code_id+"]']").prop("readonly", false);
			}else{
				$("input[name='term["+code_id+"]']").prop("readonly", true);
			}
		});

		$(function() {$("[id^='boardSdate'], [id^='boardEdate']" ).datepicker({dateFormat: 'yy-mm-dd'});});
	});
</script>

<?php
include __foot__;
?>
