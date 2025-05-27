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
<?php
include __foot__;
?>
