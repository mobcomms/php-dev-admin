<?php
include_once '../var.php';
include_once __pdoDB__;    ## DB Instance 생성
include_once __fn__;

$reg_user = empty($_REQUEST['uuid'])?"":$_REQUEST['uuid'];
$target = empty($_REQUEST['target'])?"":$_REQUEST['target'];

$sql = "
	SELECT count(*)AS cnt
	FROM ckd_user_info  
	WHERE user_uuid=:reg_user
";
$row = $NDO->getData($sql,[":reg_user"=>$reg_user]);
if(empty($reg_user) || empty($row) || $row['cnt'] < 1){
	exit("사용자 정보를 확인할 수 없어요. 보너스적립 앱에서 인증해 주세요.");
}

if(!empty($_POST)){

	if($_POST['mode'] == "inquiry"){
		$type =  empty($_REQUEST['type'])?"":$_REQUEST['type'];
		$question = empty($_REQUEST['question'])?"":htmlspecialchars($_REQUEST['question']);
		$reg_user = empty($_REQUEST['uuid'])?"":$_REQUEST['uuid'];
		$sql = "
			INSERT INTO ckd_bbs_inquiry (title, type,question,reg_user) 
			VALUES ('{$target}','{$type}','{$question}','{$reg_user}')
		";
		//pre($sql);
		$ret = $NDO->sql_query($sql);
		if (!$ret) {
			exit('등록 시 오류가 발생하였습니다.\n\n다시 시도해 주세요.');
		}else{
?>
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, minimum-scale=1, user-scalable=0">
	</head>
	<body>
		<script>
			var isMobile = {
				Android: function () {
					return navigator.userAgent.match(/Chrome/) == null ? false : true;
				},
				iOS: function () {
					return navigator.userAgent.match(/iPhone|iPad|iPod/i) == null ? false : true;
				},
				any: function () {
					return (isMobile.Android() || isMobile.iOS());
				}
			};

			try{
				if(isMobile.any()) {
					if(isMobile.Android()) {
						HybridApp.showMessage('문의 사항이 등록됐어요.');
					} else if (isMobile.iOS()) {
						var message = {
							funcName: "showMessage",
							msg: "문의 사항이 등록됐어요."
						};
						<?php if($target == "SDK"){?>
							window.webkit.messageHandlers.HybridApp.postMessage(message);
						<?php }else{ ?>
							window.webkit.messageHandlers.fcmRegister.postMessage(message);
						<?php } ?>
					}
				}
			} catch (e){
				console.log(e)
			}

		</script>
	</body>
</html>
<?php
			exit;
		}
	}else{
		//약관 철회
		$sql = "
			UPDATE ckd_user_info SET user_agree_terms='N'
			WHERE user_uuid = :reg_user  
		";
		//pre($sql);
		$ret = $NDO->sql_query($sql,[":reg_user"=>$reg_user]);
		if($ret){
			?>
			<script>
				var isMobile = {
					Android: function () {
						return navigator.userAgent.match(/Chrome/) == null ? false : true;
					},
					iOS: function () {
						return navigator.userAgent.match(/iPhone|iPad|iPod/i) == null ? false : true;
					},
					any: function () {
						return (isMobile.Android() || isMobile.iOS());
					}
				};
				if(isMobile.Android()) {
					HybridApp.termsExpired();
				} else if (isMobile.iOS()) {
					var message = {
						funcName: "termsExpired",
					};
					window.webkit.messageHandlers.fcmRegister.postMessage(message);
				}
			</script>
			<?php
		}
		exit;
	}
}
?>
<!DOCTYPE html>
	<html lang="ko">
	<head>
	<meta charset="UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>보너스적립 키보드</title>

	<!-- swiper -->
	<link rel="stylesheet" href="./css/lib/swiper_bundle.css" />
	<link rel="stylesheet" href="./css/pretendard.css">
	<!-- swiper end -->
	<script src="./js/html5shiv.min.js"></script>
	<script src="./js/jquery-3.6.4.min.js"></script>
	<script src="./js/jquery.easing.1.3.js"></script>
	<script src="./js/lib/swiper.js"></script>

	<link rel="stylesheet" href="./css/base.css" />
	<link rel="stylesheet" href="./css/common.css" />
	</head>

	<body>
		<div class="common_wrap">
		<div class="index_container">
		<div class="info_tabs">
		<div class="swiper mySwiper">
			<ul class="swiper-wrapper">
				<li class="tab-item swiper-slide active" target-wrapper="first-dynamic-table" target-tab="tab01">문의하기</li>
				<li class="tab-item swiper-slide" target-wrapper="first-dynam ic-table" target-tab="tab02">문의 내역 확인</li>
				<li class="tab-item swiper-slide" target-wrapper="first-dynam ic-table" target-tab="tab03" >약관 철회</li>
			</ul>
		</div>
		<div id="first-dynamic-table">
		<div class="tab_content active" id="tab01">
		<div class="content_container">
		<div class="content_content">

			<div class="content_title">
				<p>건의사항이나 궁금하신 사항이 있으신가요?</p>
				<p>고객님의 의견을 경청하겠습니다.</p>
			</div>
		</div>

			<form name="contact" class="input_inner" id="contact" method="POST" enctype="multipart/form-data">
				<?php if (is_ios()) {?>
				<input type="hidden" name="type_os" value="ios">
				<?php } elseif(is_android()) { ?>
				<input type="hidden" name="type_os" value="aos">
				<?php } else { ?>
				<input type="hidden" name="type_os" value="pc/etc">
				<?php } ?>
				<input type="hidden" name="mode" value="inquiry">
				<input type="hidden" name="target" value="<?=$target?>">
				<select name="type" id="content_select" required>
					<option value="">문의 유형을 선택해주세요.</option>
					<option value="01">이용 문의</option>
					<option value="02">적립 문의</option>
					<option value="03">기타</option>
				</select>
				<div class="text_field">
					<textarea name="question" form="contact" cols="40" rows="5" maxlength="3000" id="contact_textarea" placeholder="문의 또는 제안 내용을 입력해주세요. (한글 최대 1,000자, 띄어쓰기 포함)" required ></textarea>
					<div class="text_count">
						<span id="text_title">Text</span>
						<span id="text_number">0/ 1000</span>
					</div>
				</div>
				<!-- input type="file" name="brand_img" onchange="checkFile(this)" accept="image/*" -->
			</form>
		</div>
			<div class="active_button_container">
				<button type="submit" id="btn" form="contact">문의 보내기</button>
			</div>

		<div class="loading" style="position: fixed;bottom: 0;width: 100%;text-align: center;display: none"><img src="https://<?=__host__?>/img/loading.gif"></div>
		</div>

		<div class="tab_content" id="tab02">
			<div class="no_data">문의하신 내용이 없어요.</div>
		</div>

			<div class="tab_content" id="tab03">
				<div class="content_container">
					<div class="content_agree_terms_content">
						<div class="content_agree_terms_title">
							<span>전체 약관 동의 철회하기</span>
						</div>
					</div>
					<div class="content_agree_terms_description">
						<p>
							약관 동의를 철회하더라도 지금까지 보너스 적립에서 적립된
							머니는 소멸되지 않습니다.<br />
							하지만, 다시 보너스 적립 서비스를 이용하기 위해서는 다시
							한번 이용 약관에 동의해야 합니다.
						</p>
					</div>
				</div>
				<form name="form" class="input_inner" method="POST">
					<input type="hidden" name="mode" value="agree_terms">
					<input type="hidden" name="uuid" value="<?=$reg_user?>">
					<div class="active_button_container">
						<button id="agree_terms" class="active">전체 약관 동의 철회</button>
					</div>
				</form>
			</div>
		</div>
		</div>
		</div>
		</div>

		<script src="./js/common.js"></script>
		<script>
		//버튼 클릭시 비활설화
		$("#btn").on("click",function(){
			if($("#btn").hasClass("active")){
				$(this).hide();
				$(".loading").show();
			}
		});

		//문의보내기 버튼 활성화
		$("#content_select").on("change",function(){
			btn_active();
		});
		$("#contact_textarea").on("keyup input",function(){
			btn_active();
		});

		function btn_active(){
			if($("#content_select").val() != ""){
				if($("#contact_textarea").val() != ""){
					$("#btn").addClass("active");
				}else{
					$("#btn").removeClass("active");
				}
			}else{
				$("#btn").removeClass("active");
			}
		}

		var swiper = new Swiper(".mySwiper", {
			slidesPerView: "100%",
		});

		$(document).ready(function () {

			//탭 메뉴 클릭시 답변 가져오기
			$(".tab-item").on("click", function () {
				if($(this).attr("target-tab") == "tab02"){
					$(".no_data").hide();
					var data = {
						reg_user: '<?=$reg_user?>'
					}
					$.post("./contens.php", data, function(result){
						if(!result){
							$(".no_data").show();
						}else{
							$("#tab02").html(result);
						}
					});
				}
			});

			$(document).on("click", ".tab_inner", function () {
				if ($(this).hasClass("off")) {
					return;
				}
				if ($(this).hasClass("active")) {
					$(this).removeClass("active");
				}else{
					$(".tab_inner").removeClass("active");
					$(this).addClass("active");
				}
			});

			$("#contact_textarea").on("focusin",function(){
				$(".content_title").hide();
				$("#contact").css("margin-top", "10px");
			});
			$("#contact_textarea").on("focusout",function(){
				$(".content_title").show();
				$("#contact").css("margin-top", "0");
			});

		});//ready


		</script>
	</body>
</html>
