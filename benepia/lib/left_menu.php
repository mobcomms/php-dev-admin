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
		<h1>benepia : <font color="#1caf9a" size="3"> <?=strtoupper($admin['level'])?></font></h1>
	</div><!-- logopanel -->

	<div class="leftpanelinner">

		<h5 class="sidebartitle"></h5>
		<ul class="nav nav-pills nav-stacked nav-bracket">

			<li class="<?=_Menu_=='main'?'active':''?>"><a href="index.php"><i class="glyphicon glyphicon-copyright-mark"></i> <span>메인</span></a></li>

			<?php if($_SESSION['Adm']['id'] == "mango"){ ?>
			<li class="<?=_Menu_=='use'?'active':''?>"><a href="statistics_use.php"><i class="fa fa-krw"></i> <span>매출 통계</span></a></li>
			<li class="<?=_Menu_=='reward'?'active':''?>"><a href="statistics_save.php"><i class="fa fa-product-hunt"></i> <span>리워드 통계</span></a></li>
			<li class="<?=_Menu_=='user'?'active':''?>"><a href="member_list.php"><i class="glyphicon glyphicon-user"></i> <span>사용자 관리</span></a></li>
			<li class="nav-parent <?=_Menu_=='inquiry'?'active nav-active':''?>"><a href="board_inquiry.php"><i class="fa fa-question-circle"></i> <span>문의관리</span></a>
				<ul class="children" style="display:<?=_Menu_=='inquiry'?'block':'none'?>;">
					<li class=" <?=_subMenu_=='inquiry'?'active':''?>"><a href="board_inquiry.php?target=game"><i class="fa fa-caret-right"></i>문의하기</a></li>
				</ul>
			</li>
			<?php } ?>

			<li class="nav-parent <?=_Menu_=='gamezone'?'active nav-active':''?>"><a href="member_info.php"><i class="fa fa-cog"></i> <span>미션존 설정</span></a>
				<ul class="children" style="display:<?=_Menu_=='gamezone'?'block':'none'?>;">
					<li class=" <?=_subMenu_=='set_roulette'?'active':''?>"><a href="set_roulette.php"><i class="fa fa-caret-right"></i>룰렛 설정</a></li>
					<li class=" <?=_subMenu_=='set_ladder'?'active':''?>"><a href="set_ladder.php"><i class="fa fa-caret-right"></i>사다리 설정</a></li>
				</ul>
			</li>

		</ul>

	</div><!-- leftpanelinner -->
</div><!-- leftpanel -->
