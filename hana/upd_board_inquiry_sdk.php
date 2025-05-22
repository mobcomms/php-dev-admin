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
<?php
include __foot__;
?>
