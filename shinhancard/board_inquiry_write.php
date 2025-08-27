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
$target = empty($_REQUEST['target'])?"":$_REQUEST['target'];
$np = empty($_REQUEST['np'])?"":$_REQUEST['np'];

## 환경설정
define('_title_', '문의하기');
define('_Menu_', 'inquiry');
if($target=="game") {
    define('_subMenu_', 'inquiry_game');
}else if($target=="moneybox"){
    define('_subMenu_', 'inquiry_box');
}else{
    define('_subMenu_', 'inquiry');
}
$actTitle=($seq)?"수정":"등록";

## 등록 및 수정
if(!empty($_POST)){
	$method = empty($_POST['method'])?"":$_POST['method'];
	@extract($_REQUEST);
	$query_string = @http_build_query(compact('type','search_type', 'keyword', 'np','target'));

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
//pre($result);
$img_link = empty($result['file_path'])?"":"https://shinhancard-api.commsad.com/".$result['file_path'].$result['file_name'];

//사용자 이력 확인
$reg_user = explode("@",$result['reg_user'])[0];
$sql="
	SELECT count(reg_user) cnt FROM ckd_bbs_inquiry
	WHERE del_yn = 'N' AND reg_user like '{$reg_user}%' AND title='{$target}'
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
									<a href="board_inquiry.php?search_type=02&target=<?=$target?>&keyword=<?=urlencode(explode("@",$result['reg_user'])[0])?>" class="btn btn-warning" onClick="">질문 <?=$user_cnt?>건 모아보기</a>
								<?php } ?>
							</div>
						</div>

						<div class="form-group">
							<label class=" col-sm-1 control-label" style="width: 200px;padding-top: 0">운영체제 :&nbsp;</label>
							<div class="col-sm-9"><?=$result['user_app_os'] == "A"?"Android":"iOS"?></div>
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
								<label><input type="radio" name="content" value="2">유관부서 전달 </label>&nbsp;&nbsp;&nbsp;
								<label><input type="radio" name="content" value="3">해지안내 </label>&nbsp;&nbsp;&nbsp;
								<label><input type="radio" name="content" value="4">데이터삭제 </label>&nbsp;&nbsp;&nbsp;
								<label><input type="radio" name="content" value="5">광고제거불가안내 </label>&nbsp;&nbsp;&nbsp;
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
				case "1" : text = "안녕하세요. 키보드 포인트 적립 운영진입니다.\n" +
					"전달 주신 내용으로는 정확한 문의 내용을 파악하기 어렵습니다.\n" +
					"발생한 오류  내용 전달해주시면,  확인해보도록 하겠습니다.\n" +
					"감사합니다.";
				break;
				case "2" : text = "안녕하세요. 키보드 포인트 적립 운영진입니다.\n" +
					"많은 기대로 이용을 해 주셨을 텐데 이용에 불편을 드려 죄송합니다.\n" +
					"말씀해 주신 내용은 유관부서로 전달하였으며, 빠른 시일 내에 처리 될 수 있도록 노력하겠습니다.\n" +
					"감사합니다.";
				break;
				case "3" : text = "안녕하세요. 키보드 포인트 적립 운영진입니다.\n" +
					"많은 기대로 이용을 해 주셨을 텐데 이용에 불편을 드려 죄송합니다.\n\n" +
					"첫번째 키보드 변경 방법입니다.\n" +
					"자판 오른쪽에 아래쪽 구석을 보시면  자판모양 아이콘이 있습니다.\n" +
					"이걸 클릭하시면 원래 키보드로 변경 가능합니다.\n\n" +
					"두번째 포인트 적립 OFF 처리 방법입니다.\n" +
					"휴대폰 설정 > 기본 키보드 및 추가 키보드설정 > 기본 키보드 > 키보드 포인트 적립 설정 해제.\n" +
					"감사합니다.";
				break;
				case "4" : text = "안녕하세요. 키보드 포인트 적립 운영진입니다.\n" +
					"많은 기대로 이용을 해 주셨을 텐데 이용에 불편을 드려 죄송합니다.\n\n" +
					"(주의) ※ 하나머니 앱이 로그인이 해제됩니다.\n" +
					"휴대폰 설정 > 어플리케이션 > \"하나머니\" 검색후 클릭 > 저장공간 > 데이터삭제\n\n" +
					"하신후에 천지인(기본) 을 다시 변경 해보시기 바랍니다.\n" +
					"감사합니다." ;
				break;
				case "5" : text = "안녕하세요. 키보드 포인트 적립 운영진입니다.\n" +
					"포인트 적립 키보드에서는 회원분들에게 다양한 적립 혜택을 드리기 위해 광고가 노출되고 있으며,\n" +
					"이에 따라, 광고는 제거 할 수 없습니다. \n이용에 참고 부탁드립니다.\n" +
					"감사합니다." ;
				break;
			}
			$("textarea").val(text);
		})

	});
</script>