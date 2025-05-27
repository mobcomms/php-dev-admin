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
$target = empty($_REQUEST['target'])?"":trim($_REQUEST['target']);

## 환경설정
define('_title_', '문의하기');
define('_Menu_', 'inquiry');

switch($target){
	case "SDK" : define('_subMenu_', 'inquiry_sdk');break;
	case "PPZ" : define('_subMenu_', 'inquiry_ppz');break;
    case "HOTPLACE" : define('_subMenu_', 'inquiry_hotplace');break;
	default : define('_subMenu_', 'inquiry');
}

$actTitle=($seq)?"수정":"등록";

## 등록 및 수정
if(!empty($_POST)){
	$method = empty($_POST['method'])?"":$_POST['method'];
	@extract($_REQUEST);
	$query_string = @http_build_query(compact('target','type','search_type', 'keyword', 'np'));

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
	SELECT 
	COALESCE(cui.user_idx, cuis.user_idx) AS user_idx
	,COALESCE(cui.user_app_os, cuis.user_app_os) AS user_app_os
	,COALESCE(cui.user_adid, cuis.user_adid) AS user_adid
	,cbi.type, cbi.type_os, cbi.question, cbi.answer, cbi.bbs_state, cbi.regdate, cbi.reg_user, cbi.editdate
	,file_path, file_name
	FROM ckd_bbs_inquiry AS cbi
	LEFT JOIN ckd_user_info AS cui ON  cbi.reg_user = cui.user_uuid
	LEFT JOIN ckd_user_info_sdk AS cuis ON  cbi.reg_user = cuis.user_uuid
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
$img_link = empty($result['file_path'])?"":"https://hana-api.commsad.com/".$result['file_path'].$result['file_name'];

//사용자 이력 확인
$reg_user = explode("@",$result['reg_user'])[0];
$sql="
	SELECT count(reg_user) cnt FROM ckd_bbs_inquiry
	WHERE del_yn = 'N' AND reg_user like '{$reg_user}%'
";
$result_user= $NDO->getData($sql);
$user_cnt = $result_user['cnt'];

//등록된 사용자 체크
$result_user_check= $result['user_idx'];
if(!empty($result_user_check)){
	$user_check = true;
}else{
	$user_check = false;
}

if($result['user_app_os'] =="A"){
	$type_os = "Android";
}else if($result['user_app_os'] =="I"){
	$type_os = "iOS";
}else{
	if($result['type_os'] =="aos"){
		$type_os = "Android";
	}else if($result['type_os'] =="ios"){
		$type_os = "iOS";
	}else{
		$type_os = "etc";
	}
}

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
							<label class=" col-sm-1 control-label" style="width: 200px;padding-top: 5px;<?=$user_cnt > 1?"line-height: 35px;":""?>">문의한 아이디 :&nbsp;</label>
							<div class="col-sm-9">
								<div style="float: left;line-height: 35px;"><?=$result['reg_user']?></div>
								<?php if($user_cnt > 1){ ?>
									<a href="board_inquiry.php?search_type=02&keyword=<?=urlencode(explode("@",$result['reg_user'])[0])?>&target=<?=$target?>" class="btn btn-warning" onClick="">질문 <?=$user_cnt?>건 모아보기</a>
								<?php } ?>
								<?php if($user_check === false){ ?>
									<button type="button" class="btn btn-danger">미등록사용자</button>
								<?php } ?>
							</div>
						</div>

						<div class="form-group">
							<label class=" col-sm-1 control-label" style="width: 200px;padding-top: 0">문의한 ADID :&nbsp;</label>
							<div class="col-sm-9"><?=empty($result['user_adid'])?"":$result['user_adid']?></div>
						</div>

						<div class="form-group">
							<label class=" col-sm-1 control-label" style="width: 200px;padding-top: 0">운영체제 :&nbsp;</label>
							<div class="col-sm-9"><?=$type_os?></div>
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
								<label><input type="radio" name="content" value="1">정보부족 </label>&nbsp;&nbsp;&nbsp;
								<label><input type="radio" name="content" value="2">사다리 및 캐릭터 클릭 불가 </label>&nbsp;&nbsp;&nbsp;
								<label><input type="radio" name="content" value="3">유관부서 전달 </label>&nbsp;&nbsp;&nbsp;
								<label><input type="radio" name="content" value="4">광고제거불가안내 </label>&nbsp;&nbsp;&nbsp;
								<label><input type="radio" name="content" value="5">특정 광고 안 뜨는 현상 </label>&nbsp;
								<label><input type="radio" name="content" value="6">광고 로딩 및 실행 지연 </label>&nbsp;
								<label><input type="radio" name="content" value="7">광고 참여 시간 안내 </label>&nbsp;
								<label><input type="radio" name="content" value="8">광고 없음 및 클릭불가 </label>&nbsp;

								<label><input type="radio" name="content" value="11">사다리타기방법 </label>&nbsp;
								<label><input type="radio" name="content" value="12">네트워크 활성화 </label>&nbsp;
								<label><input type="radio" name="content" value="13">5만원 되는 거 맞아요? 1</label>&nbsp;
								<label><input type="radio" name="content" value="17">5만원 되는 거 맞아요? 2</label>&nbsp;
								<label><input type="radio" name="content" value="14">앱 최신버전 </label>&nbsp;
								<label><input type="radio" name="content" value="15">머니사다리 이외 문의 </label>&nbsp;
								<label><input type="radio" name="content" value="16">적립현황 어디서 확인해요? </label>&nbsp;
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
				case "1" : text = "안녕하세요. 머니사다리 운영진입니다.\n" +
					"전달 주신 내용으로는 정확한 문의 내용을 파악하기 어렵습니다.\n" +
					"발생한 오류  내용 전달해주시면,  확인해보도록 하겠습니다.\n" +
					"감사합니다.";
				break;
				case "2" : text = "안녕하세요. 머니사다리 운영진입니다.\n" +
					"전달 주신 내용은, 일부 사용자들에게 발견되고 있는 현상으로\n" +
					"유관부서에서 빠르게 대처하고 있습니다.\n\n" +
					"소중한 의견에 감사드리며, 하루 빨리 좋은 서비스를 제공할 수 있도록 노력하겠습니다.\n" +
					"감사합니다.";
				break;
				case "3" : text = "안녕하세요. 머니사다리 운영진입니다.\n" +
					"많은 기대로 이용을 해 주셨을 텐데 이용에 불편을 드려 죄송합니다.\n" +
					"말씀해 주신 내용은 유관부서로 전달하였으며, 빠른 시일 내에 처리 될 수 있도록 노력하겠습니다.\n" +
					"감사합니다.";
				break;
				case "4" : text = "안녕하세요. 머니사다리 운영진입니다.\n" +
					"머니사다리에서는 회원분들에게 다양한 적립 혜택을 드리기 위해 광고가 노출되고 있으며,\n" +
					"이에 따라, 광고는 제거 할 수 없습니다.\n" +
					"이용에 참고 부탁드립니다.\n" +
					"감사합니다." ;
				break;
				case "5" : text = "안녕하세요. 머니사다리 운영진입니다.\n" +
					"전달 주신 내용은, 일부 광고주 대상으로 발견되고 있는 현상으로 유관 부서에서 빠르게 대처하고 있습니다.\n" +
					"소중한 의견에 감사드리며, 하루 빨리 좋은 서비스를 제공할 수 있도록 노력하겠습니다. \n" +
					"감사합니다." ;
				break;
				case "6" : text = "안녕하세요. 머니사다리 운영진입니다.\n" +
					"이용에 불편을 드려 죄송합니다. 머니사다리에 많은 사용자가 동시접속할 경우 광고 로딩 시간이 길어질 수 있는 점 양해부탁드립니다.\n" +
					"광고를 시청해도 계속해서 참여가 안되는 경우, 페이지를 닫고 재시도 부탁드립니다. 이용에 불편함이 없으시도록 빠르게 조치하겠습니다.\n" +
					"감사합니다." ;
				break;
				case "7" : text = "안녕하세요. 머니사다리 운영진입니다.\n" +
					"머니사다리는 사용자분들이 더 많은 머니를 적립하실 수 있도록 매시간마다 1회씩 참여 가능하도록 개편되었습니다.\n" +
					"이용에 참고부탁드립니다.\n" +
					"감사합니다." ;
				break;
				case "8" : text = "안녕하세요. 머니사다리 운영진입니다.\n" +
					"이용에 불편을 드려 죄송합니다. 현재 일시적인 트래픽 증가로 일부 기기에서 광고가 나오지 않는 현상이 발생하고 있습니다. 최대한 빨리 조치 후 더 나은 서비스로 보답하겠습니다.\n" +
					"감사합니다." ;
				break;


				case "11" : text = "안녕하세요. 머니사다리 운영진입니다.\n" +
					"광고 클릭 후 참여 기회가 적립 > 캐릭터를 선택해주시면 사다리타기가 완료됩니다.\n" +
					"감사합니다." ;
				break;
				case "12" : text = "안녕하세요. 머니사다리 운영진입니다.\n" +
					"이용에 불편을 드려 죄송합니다.\n" +
					"사용하고 계시는 휴대폰 네트워크 활성화 이후 머니사다리 재시도 부탁드립니다.\n" +
					"감사합니다." ;
				break;
				case "13" : text = "안녕하세요. 머니사다리 운영진입니다.\n" +
					"머니사다리는 확률성 콘텐츠인 관계로 어떠한 분이 몇 원에 당첨될지는 알 수 없습니다. 꾸준히 참여하시면 언젠가는 대박 기회를 얻으실 거예요! 머니사다리가 응원합니다 ^^\n"
				break;
				case "17" : text = "안녕하세요. 머니사다리 운영진입니다.\n" +
					"머니사다리는 확률성 콘텐츠로 사용자분들에게 최대한 많은 혜택을 제공해드리고자, 꽝 없이 1원에서 5만원까지 랜덤으로 당첨금액을 제공해드리고 있습니다.\n" +
					"감사합니다." ;
				break;
				case "14" : text = "안녕하세요. 머니사다리 운영진입니다.\n" +
					"많은 기대로 이용을 해 주셨을 텐데 이용에 불편을 드려 죄송합니다.\n" +
					"하나머니 앱을 최신 버전으로 업데이트 설치 이후 머니사다리 서비스 재시도 부탁드립니다.\n" +
					"감사합니다." ;
				break;
				case "15" : text = "안녕하세요. 머니사다리 운영진입니다.\n" +
					"해당 문의하기는 '머니사다리' 입니다. \n" +
					"이외 문의사항은 하나머니 앱 접속 > 하단 우측 전체 메뉴 클릭 > 아래 스크롤 해주시어 1:1 문의하기에 남겨주시면 최대한 빨리 확인 하여 답변 드리겠습니다. \n" +
					"감사합니다." ;
				break;
				case "16" : text = "안녕하세요. 머니사다리 운영진입니다.\n" +
					"해당 문의하기는 '머니사다리' 입니다. \n" +
					"이외 문의사항은 하나머니 앱 접속 > 하단 우측 전체 메뉴 클릭 > 아래 스크롤 해주시어 1:1 문의하기에 남겨주시면 최대한 빨리 확인 하여 답변 드리겠습니다. \n" +
					"감사합니다." ;
				break;
			}
			$("textarea").val(text);
		})

	});
</script>