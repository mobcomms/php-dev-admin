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
		<h1>shinhan : <font color="#1caf9a" size="3"> <?=strtoupper($admin['level'])?></font></h1>
	</div><!-- logopanel -->

	<div class="leftpanelinner">

		<h5 class="sidebartitle"></h5>
		<ul class="nav nav-pills nav-stacked nav-bracket">

			<li class="<?=_Menu_=='main'?'active':''?>"><a href="index.php"><i class="glyphicon glyphicon-copyright-mark"></i> <span>메인</span></a></li>

			<li class="<?=_Menu_=='use'?'active':''?>"><a href="statistics_use.php"><i class="fa fa-krw"></i> <span>매출 통계</span></a></li>

			<?php if($_SESSION['Adm']['id'] == "mango"){ ?>
			<li class="<?=_Menu_=='reward'?'active':''?>"><a href="statistics_save.php"><i class="fa fa-product-hunt"></i> <span>리워드 통계</span></a></li>
			<li class="<?=_Menu_=='user'?'active':''?>"><a href="member_list.php"><i class="glyphicon glyphicon-user"></i> <span>사용자 관리</span></a></li>

            <li class="nav-parent <?=_Menu_=='event'?'active nav-active':''?>"><a href="board_inquiry.php"><i class="fa fa-question-circle"></i> <span>이벤트 관리</span></a>
                <ul class="children" style="display:<?=_Menu_=='event'?'block':'none'?>;">
                    <li class=" <?=_subMenu_=='event_set'?'active':''?>"><a href="set_event.php"><i class="fa fa-caret-right"></i>이벤트 설정</a></li>
                    <li class=" <?=_subMenu_=='event_info'?'active':''?>"><a href="event_info.php?mode=1"><i class="fa fa-caret-right"></i>이벤트 진행 리스트(1월)</a></li>
                    <li class=" <?=_subMenu_=='event_info2'?'active':''?>"><a href="event_info.php?mode=2"><i class="fa fa-caret-right"></i>이벤트 진행 리스트(2월)</a></li>
                    <li class=" <?=_subMenu_=='event_info3'?'active':''?>"><a href="event_info.php?mode=3"><i class="fa fa-caret-right"></i>이벤트 진행 리스트(3월)</a></li>
                    <li class=" <?=_subMenu_=='event_info5'?'active':''?>"><a href="event_info.php?type=M&point=5000"><i class="fa fa-caret-right"></i>이벤트 진행 리스트(5월~)</a></li>
                </ul>
            </li>

			<li class="nav-parent <?=_Menu_=='inquiry'?'active nav-active':''?>"><a href="board_inquiry.php"><i class="fa fa-question-circle"></i> <span>문의관리</span></a>
				<ul class="children" style="display:<?=_Menu_=='inquiry'?'block':'none'?>;">
					<li class=" <?=_subMenu_=='inquiry'?'active':''?>"><a href="board_inquiry.php"><i class="fa fa-caret-right"></i>광고 문의</a></li>
					<li class=" <?=_subMenu_=='inquiry_game'?'active':''?>"><a href="board_inquiry.php?target=game"><i class="fa fa-caret-right"></i>미션존 문의</a></li>
                    <li class=" <?=_subMenu_=='inquiry_box'?'active':''?>"><a href="board_inquiry.php?target=moneybox"><i class="fa fa-caret-right"></i>머니박스 문의</a></li>
				</ul>
			</li>
			<?php } ?>

			<li class="nav-parent <?=_Menu_=='setting'?'active nav-active':''?>"><a href="member_info.php"><i class="fa fa-cog"></i> <span>제품별 설정</span></a>
				<ul class="children" style="display:<?=_Menu_=='setting'?'block':'none'?>;">
					<li class=" <?=_subMenu_=='banner_set'?'active':''?>"><a href="set_banner.php"><i class="fa fa-caret-right"></i>광고 설정</a></li>
					<li class=" <?=_subMenu_=='ladder_set'?'active':''?>"><a href="set_ladder.php"><i class="fa fa-caret-right"></i>미션존 설정</a></li>
				</ul>
			</li>

		</ul>

	</div><!-- leftpanelinner -->
</div><!-- leftpanel -->
