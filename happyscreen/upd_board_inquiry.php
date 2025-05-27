<?php
/**********************
 *
 *    팝업/푸시관리 - 팝업관리
 *
 **********************/

include "./var.php";

## 환경설정
define('_title_', '팝업관리');
define('_Menu_', 'pop');
define('_subMenu_', 'popup');

include_once __func__;
include_once __head_pop__; ## html 헤더 출력

if(!empty($_POST)){
	$sql="
		UPDATE ckd_bbs_inquiry SET answer=:answer, bbs_state='02', editdate=NOW() 
		WHERE del_yn = 'N' AND seq IN ({$_POST['seq']});
	";
	$result= $NDO->sql_query($sql,array(":answer"=>$_POST['answer']));

?>
	<script>
		self.close();
		opener.document.location.reload();
	</script>
<?php
	exit;
}
?>

<div class="contentpanel" style="padding: 0">

	<div class="panel panel-default" style="margin: 0">
		<div class="panel-heading">
			<h4 class="panel-title">일괄답변</h4>
		</div><!-- panel-heading -->
		<div class="panel-body" style="padding-top: 0;padding-bottom: 0px;">

			<div class="row">
				<div class="panel panel-default">
					<form method="post" onsubmit="return chk_save()">
					<table class="table member_table" style="width: 700px;">
						<colgroup>
							<col width="150px">
							<col width="550px">
						</colgroup>
						<tbody>
						<tr>
							<td>선택된 글의 key값</td>
							<td style="text-align: left;">
								<span id="open_seq"></span>
								<input type="hidden" name="seq" id="seq">
							</td>
						</tr>
							<tr>
								<td>준비된 답변 자동 입력</td>
								<td style="text-align: left;">
									<label><input type="radio" name="content" value="0" checked >없음</label>&nbsp;&nbsp;&nbsp;
									<label><input type="radio" name="content" value="1">정보부족 </label>&nbsp;&nbsp;&nbsp;
									<label><input type="radio" name="content" value="2">유관부서 전달 </label>&nbsp;&nbsp;&nbsp;
									<label><input type="radio" name="content" value="3">해지안내 </label>&nbsp;&nbsp;&nbsp;
									<label><input type="radio" name="content" value="4">데이터삭제 </label>&nbsp;&nbsp;&nbsp;
									<label><input type="radio" name="content" value="5">광고제거불가안내 </label>&nbsp;&nbsp;&nbsp;
								</td>
							</tr>
							<tr>
								<td>답변 내용</td>
								<td style="text-align: left;">
									<textarea name="answer" style="width: 360px;height: 300px;"></textarea>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<button type="button" class="btn btn-danger" onclick="self.close()">취소</button>
									<button type="submit" class="btn btn-primary">적용</button>
								</td>
							</tr>
						</tbody>
					</table>
					</form>
				</div>
			</div>

		</div><!-- panel-body -->
	</div><!-- panel panel-default -->
</div><!-- contentpanel -->

<script>

	$(document).ready(function(){
		chk_seq = [];
		$("._checkbox",opener.document).each(function(){
			if($(this).is(':checked')){
				chk_seq.push($(this).val());
			}
		});
		$("#open_seq").text(chk_seq);
		$("#seq").val(chk_seq);

		$("input[name=content]").on("click", function(){
			var text = "";
			switch($(this).val()){
				case "1" : text = "안녕하세요. 키보드 해피스크린 키보드 운영진입니다.\n" +
					"전달 주신 내용으로는 정확한 문의 내용을 파악하기 어렵습니다.\n" +
					"발생한 오류  내용 전달해주시면,  확인해보도록 하겠습니다.\n" +
					"감사합니다.";
				break;
				case "2" : text = "안녕하세요. 키보드 해피스크린 키보드 운영진입니다.\n" +
					"많은 기대로 이용을 해 주셨을 텐데 이용에 불편을 드려 죄송합니다.\n" +
					"말씀해 주신 내용은 유관부서로 전달하였으며, 빠른 시일 내에 처리 될 수 있도록 노력하겠습니다.\n" +
					"감사합니다.";
				break;
				case "3" : text = "안녕하세요. 키보드 해피스크린 키보드 운영진입니다.\n" +
					"많은 기대로 이용을 해 주셨을 텐데 이용에 불편을 드려 죄송합니다.\n\n" +
					"첫번째 키보드 변경 방법입니다.\n" +
					"자판 오른쪽에 아래쪽 구석을 보시면  자판모양 아이콘이 있습니다.\n" +
					"이걸 클릭하시면 원래 키보드로 변경 가능합니다.\n\n" +
					"두번째 해피스크린 키보드 OFF 처리 방법입니다.\n" +
					"휴대폰 설정 > 기본 키보드 및 추가 키보드설정 > 기본 키보드 > 키보드 해피스크린 키보드 설정 해제.\n" +
					"감사합니다.";
				break;
				case "4" : text = "안녕하세요. 키보드 해피스크린 키보드 운영진입니다.\n" +
					"많은 기대로 이용을 해 주셨을 텐데 이용에 불편을 드려 죄송합니다.\n\n" +
					"(주의) ※ 하나머니 앱이 로그인이 해제됩니다.\n" +
					"휴대폰 설정 > 어플리케이션 > \"하나머니\" 검색후 클릭 > 저장공간 > 데이터삭제\n\n" +
					"하신후에 천지인(기본) 을 다시 변경 해보시기 바랍니다.\n" +
					"감사합니다." ;
				break;
				case "5" : text = "안녕하세요. 키보드 해피스크린 키보드 운영진입니다.\n" +
					"해피스크린 키보드 키보드에서는 회원분들에게 다양한 적립 혜택을 드리기 위해 광고가 노출되고 있으며,\n" +
					"이에 따라, 광고는 제거 할 수 없습니다. \n이용에 참고 부탁드립니다.\n" +
					"감사합니다." ;
				break;
			}
			$("textarea").val(text);
		})

	});
</script>
<?php
include __foot__;
?>
