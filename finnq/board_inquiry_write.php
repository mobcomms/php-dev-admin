<?php
/**********************************************************
 *
 * 문의 하기 답변
 * *
 ************************************************************/

include "./var.php";
include_once __func__;
include __fn__;

$seq = empty($_REQUEST['seq'])?"":$_REQUEST['seq'];
$type = empty($_REQUEST['type'])?"":$_REQUEST['type'];
$bbs_state = empty($_REQUEST['bbs_state'])?"01":$_REQUEST['bbs_state'];
$keyword = empty($_REQUEST['keyword'])?"":urldecode($_REQUEST['keyword']);
$np = empty($_REQUEST['np'])?"":$_REQUEST['np'];

## 환경설정
define('_title_', '문의하기');
define('_Menu_', 'inquiry');
define('_subMenu_', 'inquiry');
$actTitle=($seq)?"수정":"등록";

## 등록 및 수정
if(!empty($_POST)){
	$method = empty($_POST['method'])?"":$_POST['method'];
	@extract($_REQUEST);
	$query_string = @http_build_query(compact('type','search_type', 'keyword', 'np'));

	if($method == 'upd'){
		$answer = $_REQUEST['answer'];
		$sql="
			UPDATE ckd_bbs_inquiry SET answer=:answer, bbs_state=:bbs_state, editdate=NOW() 
			WHERE del_yn = 'N' AND seq=:seq
		";
		//pre($sql);
		$result= $NDO->sql_query($sql,array(":answer"=>$answer, ":bbs_state"=>$bbs_state, ":seq"=>$seq));
		if($result) Loca("./board_inquiry.php?".$query_string, "적용 되었습니다.");

	}elseif($method == 'del'){
		$sql="
			UPDATE ckd_bbs_inquiry SET del_yn= 'Y' WHERE seq=:seq
		";
		$result= $NDO->sql_query($sql,array(":seq"=>$seq));
		if($result) Loca("./board_inquiry.php?".$query_string, "적용 되었습니다.");
	}elseif($method == 'del2'){
		$sql="
			UPDATE ckd_bbs_inquiry SET del_yn= 'Y' WHERE seq=:seq
		";
		$result= $NDO->sql_query($sql,array(":seq"=>$seq));
		if($result) echo "OK";
	}
	exit;
}

$sql="
	SELECT cui.user_app_os, cbi.type, cbi.question, cbi.answer, cbi.bbs_state, cbi.regdate, cbi.reg_user, cbi.editdate, file_path, file_name
	FROM ckd_bbs_inquiry AS cbi
	LEFT JOIN ckd_user_info AS cui ON  cbi.reg_user = cui.user_uuid
	LEFT JOIN ckd_file_upload cfu ON cfu.board_idx = cbi.seq
	WHERE del_yn = 'N' AND seq=:seq
	ORDER BY seq DESC
";
//pre($sql);
$result= $NDO->getData($sql,array(":seq"=>$seq));

switch($result['type']){
	case "01" : $inquiry_type = "이용 문의";break;
	case "02" : $inquiry_type = "앱 오류 및 건의 사항";break;
	case "03" : $inquiry_type = "기타";break;
	case "04" : $inquiry_type = "미답변";break;
	case "05" : $inquiry_type = "답변완료";break;
	default : $inquiry_type = "선택안함";break;
}
$img_link = empty($result['file_path'])?"":"https://finnq-api.commsad.com/".$result['file_path'].$result['file_name'];

//사용자 이력 확인
$reg_user = explode("@",$result['reg_user'])[0];
$sql="
	SELECT count(reg_user) cnt FROM ckd_bbs_inquiry
	WHERE del_yn = 'N' AND reg_user like '{$reg_user}%'
";

$result_user= $NDO->getData($sql);
$user_cnt = $result_user['cnt'];

include __head__; ## html 헤더 출력
?>
<div class="contentpanel">

	<div class="panel panel-default">
		<div class="panel-heading">
			<div class="panel-btns">
				<a href="" class="minimize">&minus;</a>
			</div><!-- panel-btns -->
			<h4 class="panel-title"><?=_title_?> <?=$actTitle?></h4>
		</div><!-- panel-heading -->
		<div class="panel-body">

			<form method="POST" name="basicForm" id="basicForm" class="form-horizontal">
				<input type="hidden" name="seq" value="<?=$seq?>" />
				<input type="hidden" name="keyword" value="<?=$keyword?>" />
				<input type="hidden" name="np" value="<?=$np?>" />
				<input type="hidden" name="method" />
				<div class="row">
					<div class="panel panel-default">

						<div class="form-group">
							<label class=" col-sm-1 control-label" style="width: 200px;padding-top: 0">문의 날짜 :&nbsp;</label>
							<div class="col-sm-9"><?=$result['regdate']?></div>
						</div>

						<div class="form-group">
							<label class=" col-sm-1 control-label" style="width: 200px;padding-top: 0">문의 유형 :&nbsp;</label>
							<div class="col-sm-9"><?=$inquiry_type?></div>
						</div>

						<div class="form-group">
							<label class=" col-sm-1 control-label" style="width: 200px;padding-top: 0;<?=$user_cnt > 1?"line-height: 35px;":""?>">문의한 아이디 :&nbsp;</label>
							<div class="col-sm-9">
								<div style="float: left;line-height: 35px;"><?=$result['reg_user']?></div>
								<?php if($user_cnt > 1){ ?>
									<a href="board_inquiry.php?search_type=02&keyword=<?=urlencode(explode("@",$result['reg_user'])[0])?>" class="btn btn-warning" onClick="">질문 <?=$user_cnt?>건 모아보기</a>
								<?php } ?>
							</div>
						</div>

						<div class="form-group">
							<label class=" col-sm-1 control-label" style="width: 200px;padding-top: 0">운영체제 :&nbsp;</label>
							<div class="col-sm-9"><?=empty($result['user_app_os'])?"없음":$result['user_app_os']?></div>
						</div>

						<div class="form-group">
							<label class=" col-sm-1 control-label" style="width: 200px;padding-top: 0">문의 내용 :&nbsp;</label>
							<div class="col-sm-9"><?=nl2br($result['question'])?></div>
						</div>

						<?php if(!empty($img_link)){ ?>
						<div class="form-group">
							<label class=" col-sm-1 control-label" style="width: 200px;padding-top: 0">첨부파일 :&nbsp;</label>
							<div class="col-sm-9"><a href="<?=$img_link?>" target="_blank"><img src="<?=$img_link?>" style="max-width: 30%; height: auto;"> (이미지 클릭시 새탭)</a></div>
						</div>
						<?php } ?>

						<div class="form-group">
							<label class=" col-sm-1 control-label" style="width: 200px;padding-top: 0">준비된 답변 자동 입력</label>
							<div class="col-sm-9">
								<label><input type="radio" name="content" value="0" checked >없음</label>&nbsp;&nbsp;&nbsp;
								<label><input type="radio" name="content" value="1">광고로딩 </label>&nbsp;&nbsp;&nbsp;
								<label><input type="radio" name="content" value="2">포미션플레이스 </label>&nbsp;&nbsp;&nbsp;
								<label><input type="radio" name="content" value="3">정보부족 </label>&nbsp;&nbsp;&nbsp;
								<label><input type="radio" name="content" value="4">동전 적립 방법 </label>&nbsp;&nbsp;&nbsp;
								<label><input type="radio" name="content" value="5">유관부서 전달 </label>&nbsp;&nbsp;&nbsp;
								<label><input type="radio" name="content" value="6">데이터삭제 </label>&nbsp;&nbsp;&nbsp;
								<label><input type="radio" name="content" value="7">포미션 기한 만료 1</label>&nbsp;&nbsp;&nbsp;
								<label><input type="radio" name="content" value="12">포미션 기한 만료 2</label>&nbsp;&nbsp;&nbsp;
								<label><input type="radio" name="content" value="8">수행 미션 문의 </label>&nbsp;&nbsp;&nbsp;
								<label><input type="radio" name="content" value="9">이미지 최상단 재업로드 안내 </label>&nbsp;&nbsp;&nbsp;
								<label><input type="radio" name="content" value="10">미션 실패 안내 </label>&nbsp;&nbsp;&nbsp;
								<label><input type="radio" name="content" value="11">페이북 서비스 장애 안내 </label>&nbsp;&nbsp;&nbsp;
							</div>
						</div>

						<div class="form-group">
							<label class=" col-sm-1 control-label" style="width: 200px;padding-top: 0">답변 내용</label>
							<div class="col-sm-9">
								<textarea name="answer" style="width: 360px;height: 300px;"><?=$result['answer']?></textarea>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-1 control-label" style="width: 200px;padding-top: 5px;">답변 상태</label>
							<div class="col-sm-10">
								<label style="padding-right: 7px">
									<div class="pull-left"><input style="width:20px;height:20px;" type="checkbox" name="bbs_state" value="02" <?=($result['bbs_state']=='02')?'checked':''?> ></div>
									<div class="pull-left" style="padding-top:5px;padding-left: 3px;">체크시  답변상태 답변완료로 변경됨</div>
								</label>
							</div>
						</div>

					</div>
				</div>

				<div class="row">
					<div class="text-center">
						<div class="">
							<button type="button" class="btn btn-success" onClick="sendIt();"><?=$actTitle?></button>
							<?php if($seq){?>
								<button type="button" class="btn btn-danger" onClick="del_brand();">삭제</button>
							<?php }?>
							<button type="button" class="btn btn-default" onclick="history.back();">목록</button>
						</div>
					</div>
				</div>
			</form>

		</div><!-- panel-body -->
	</div><!-- panel panel-default -->
</div><!-- contentpanel -->

<?php
include __foot__;
?>

<script>
	function sendIt(){
		var f=document.basicForm;
		f.method.value = 'upd';
		if(!f.answer.value){
			alert('답변을 입력해주세요.');
			f.answer.focus();
			return;
		}
		f.submit();
	}

	function del_brand(){
		if(!confirm("정말로 삭제하시겠습니까?")){
			return false;
		}
		var f=document.basicForm;
		f.method.value = 'del';
		f.submit();
	}

	$(document).ready(function(){
		$("input[name=content]").on("click", function(){
			var text = "";
			switch($(this).val()){
				case "1" : text = "안녕하세요. 보너스 적립 운영진입니다.\n" +
					"이용에 불편을 드려 죄송합니다. 보너스 적립에 많은 사용자가 동시접속할 경우 로딩 시간이 길어질 수 있는 점 양해부탁드립니다.\n" +
					"참여가 안 되는 경우, 페이지를 닫고 재시도 부탁드립니다.\n" +
					"감사합니다.";
				break;
				case "2" : text = "안녕하세요. 보너스 적립 운영진입니다.\n" +
					"지정된 플레이스 클립보드 복사 > 네이버 > 해당 플레이스 클릭 > '주변'메뉴 클릭> '명소'탭 클릭 > 첫 번째 명소 플레이스가 확인 됩니다.\n" +
					"해당 플레이스 기재 후 적립받기 누러주시면 적립 가능합니다.\n" +
					"감사합니다.";
				break;
				case "3" : text = "안녕하세요. 보너스 적립 운영진입니다.\n" +
					"전달 주신 내용으로는 정확한 문의 내용을 파악하기 어렵습니다.\n" +
					"미션 문제에 대한 자세한 내용을 전달 해주시면 확인 하겠습니다.\n" +
					"감사합니다.";
				break;
				case "4" : text = "안녕하세요. 보너스 적립 운영진입니다.\n" +
					"혜택탭 클릭 > 머니박스에 동전모으기 하나씩 클릭 > 광고 시청 후 페이북 앱으로 돌아오시면 페이북 머니로 동전이 자동 적립 됩니다.\n" +
					"감사합니다.";
				break;
				case "5" : text = "안녕하세요. 보너스 적립 운영진입니다.\n" +
					"많은 기대로 이용을 해 주셨을 텐데 이용에 불편을 드려 죄송합니다.\n" +
					"말씀해 주신 내용은 유관부서로 전달하였으며, 빠른 시일 내에 처리 될 수 있도록 노력하겠습니다.\n" +
					"감사합니다.";
				break;
				case "6" : text = "안녕하세요. 보너스 적립 운영진입니다.\n" +
					"많은 기대로 이용을 해 주셨을 텐데 이용에 불편을 드려 죄송합니다.\n" +
					"해당 미션이 자동 적립이 안 될 시, 참여기간이 만료되었거나, 참여인원이 초과될 수 있습니다.\n" +
					"감사합니다.";
				break;
				case "7" : text = "안녕하세요. 보너스 적립 운영진입니다.\n" +
					"많은 기대로 이용을 해 주셨을 텐데 이용에 불편을 드려 죄송합니다.\n" +
					"해당 미션은 참여기간이 만료되었거나, 참여인원이 초과된 것으로 확인 됩니다. .\n" +
					"감사합니다.";
				break;
				case "12" : text = "안녕하세요. 보너스 적립 운영진입니다.\n" +
					"많은 기대로 이용을 해 주셨을 텐데 이용에 불편을 드려 죄송합니다.\n" +
					"해당 미션이 자동 적립이 안 될 시, 참여기간이 만료되었거나, 참여인원이 초과될 수 있습니다.\n" +
					"감사합니다.";
				break;
				case "8" : text = "안녕하세요. 보너스 적립 운영진입니다.\n" +
					"많은 기대로 이용을 해 주셨을 텐데 이용에 불편을 드려 죄송합니다.\n" +
					"먼저 수행해주신 미션과 작성해주신 답변을 해당 '문의하기'로 남겨주시면 최대한 빠르게 확인 하겠습니다.\n" +
					"번거로우시겠지만, 아래 정보를 제공해주시면 안내드린 후 신속히 조치하겠습니다.\n\n" +

					"1. 수행하신 미션명\n" +
					"2. 미션종류 (참여형 or 플레이스 형)\n" +
					"3. 답변 내용\n\n" +

					"제공해주신 정보는 최대한 빠르게 검토하여 수정 및 정정 조치를 취하겠습니다.\n" +
					"다시 한 번 이용에 불편을 드려 죄송합니다.\n" +
					"편리하게 서비스를 이용하실 수 있도록 최선을 다하겠습니다.\n" +
					"감사합니다.";
				break;
				case "9" : text = "안녕하세요. 보너스 적립 운영진입니다.\n" +
					"많은 기대로 이용을 해 주셨을 텐데 이용에 불편을 드려 죄송합니다.\n" +
					"이미지 업로드 시 인식이 안 될 경우, 상단 매장 이미지 스크롤 후, 업체명을 최상단에 위치시켜 재업로드 부탁드립니다.\n" +
					"감사합니다.";
				break;
				case "10" : text = "안녕하세요. 보너스 적립 운영진입니다.\n" +
					"현재 내부 확인 결과, 사용자님께서는 해당 미션 클릭 이력이 확인이 안 되어 조회가 어려운 점 양해 부탁드립니다.\n" +
					"번거로우시겠지만 해당 미션 다시 한 번 시도 부탁드립니다.\n" +
					"이용에 불편을 드려 정말 죄송합니다.\n" +
					"감사합니다.";
				break;
				case "11" : text = "안녕하세요. 보너스 적립 운영진입니다.\n" +
					"많은 기대로 이용을 해 주셨을 텐데 이용에 불편을 드려 죄송합니다.\n" +
					"현재 일시적인 서비스 폭주로 인하여 유관부서에서 빠르게 대처하고 있습니다.\n" +
					"현재 보너스 적립은 정상적으로 해결 되었습니다.\n" +
					"현재 보너스 적립 혜택은 정상적으로 가능하며, 페이북 앱 내 적립 내역을 통하여 확인 가능합니다.\n" +
					"다시 한 번 이용에 불편을 드려 정말 죄송합니다.\n" +
					"감사합니다.";
				break;

			}
			$("textarea").val(text);
		})

	});
</script>