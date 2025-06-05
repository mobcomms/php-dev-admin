<?php
/**********************************
 *
 *   Level 등급
 *    - super : 최고관리자
 *    - develop : 개발팀
 *    - sysop : 운영팀
 *    - public : 일반관리자
 *
 ***********************************/
?>
<div class="leftpanel">

	<div class="logopanel">
		<h1>hanapay-SDK : <font color="#1caf9a" size="3"> <?=strtoupper($admin['level'])?></font></h1>
	</div><!-- logopanel -->

	<div class="leftpanelinner">

		<h5 class="sidebartitle"></h5>
		<ul class="nav nav-pills nav-stacked nav-bracket">

			<li class="<?=_Menu_=='main'?'active':''?>"><a href="index.php"><i class="glyphicon glyphicon-copyright-mark"></i> <span>메인</span></a></li>
			<li class="<?=_Menu_=='manage'?'active':''?>"><a href="statistics_use.php"><i class="fa fa-krw"></i> <span>머니박스 통계</span></a></li>
			<li class="<?=_Menu_=='reward'?'active':''?>"><a href="statistics_save.php"><i class="fa fa-product-hunt"></i> <span> 포인트 적립 통계</span></a></li>

			<li class="nav-parent <?=_Menu_=='user'?'active nav-active':''?>"><a href=".php"><i class="glyphicon glyphicon-user"></i> <span>사용자 관리</span></a>
				<ul class="children" style="display:<?=_Menu_=='user'?'block':'none'?>;">
					<li class=" <?=_subMenu_=='info'?'active':''?>"><a href="member_list.php"><i class="fa fa-caret-right"></i>사용자 관리</a></li>
					<li class=" <?=_subMenu_=='manual_box'?'active':''?>"><a href="reward_point_manual.php"><i class="fa fa-caret-right"></i>[머니박스]적립 실패 관리</a></li>
					<li class=" <?=_subMenu_=='manual_ppz'?'active':''?>"><a href="reward_point_manual.php?target=PPZ"><i class="fa fa-caret-right"></i>[오퍼월]적립 실패 관리</a></li>
				</ul>
			</li>

			<li class="nav-parent <?=_Menu_=='inquiry'?'active nav-active':''?>">
				<a href="board_inquiry.php"><i class="fa fa-question-circle"></i> <span>문의관리</span></a>
				<ul class="children" style="display:<?=_Menu_=='inquiry'?'block':'none'?>;">
					<li class=" <?=_subMenu_=='inquiry'?'active':''?>"><a href="board_inquiry.php"><i class="fa fa-caret-right"></i>문의하기</a></li>
					<li class=" <?=_subMenu_=='inquiry_ppz'?'active':''?>"><a href="board_inquiry.php?target=PPZ"><i class="fa fa-caret-right"></i>오퍼월 문의하기</a></li>
				</ul>
			</li>

			<?php if($_SESSION['Adm']['id'] == "mango"){ ?>
			<li class=" <?=_Menu_=='save_set'?'active':''?>"><a href="statistics_save_set.php"><i class="fa fa-caret-right"></i>이벤트 포인트 설정</a></li>
			<?php } ?>

			<li class="nav-hover" style="position: fixed;top: 800px;"><a href="javascript:void(0)" onclick="document.logoutform.submit();"><i class="glyphicon glyphicon-log-out"></i> Log Out</a></li>

		</ul>
	</div><!-- leftpanelinner -->
</div><!-- leftpanel -->
