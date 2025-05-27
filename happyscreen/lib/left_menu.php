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
		<h1>HAPPY SCREEN : <font color="#1caf9a" size="3"> <?=strtoupper($admin['level'])?></font></h1>
	</div><!-- logopanel -->

	<div class="leftpanelinner">

		<h5 class="sidebartitle"></h5>
		<ul class="nav nav-pills nav-stacked nav-bracket">

			<li class="<?=_Menu_=='main'?'active':''?>"><a href="index.php"><i class="glyphicon glyphicon-copyright-mark"></i> <span>메인</span></a></li>

			<li class="nav-parent <?=_Menu_=='manage'?'active nav-active':''?>"><a href="member_info.php"><i class="fa fa-bar-chart-o"></i> <span>통계</span></a>
				<ul class="children" style="display:<?=_Menu_=='manage'?'block':'none'?>;">
					<li class=" <?=_subMenu_=='use_new'?'active':''?>"><a href="statistics_use.php"><i class="fa fa-caret-right"></i>키보드 사용 통계</a></li>
					<li class=" <?=_subMenu_=='save'?'active':''?>"><a href="statistics_save.php"><i class="fa fa-caret-right"></i>키보드 적립 통계</a></li>
					<li class=" <?=_subMenu_=='info'?'active':''?>"><a href="member_list.php"><i class="fa fa-caret-right"></i>키보드 사용자</a></li>
				</ul>
			</li>

			<li class="nav-parent <?=_Menu_=='adv'?'active nav-active':''?>"><a href="partner_list.php"><i class="fa fa-list-alt"></i> <span>설정</span></a>
				<ul class="children" style="display:<?=_Menu_=='adv'?'block':'none'?>;">
					<li class=" <?=_subMenu_=='util'?'active':''?>"><a href="board_brand_util.php"><i class="fa fa-caret-right"></i>브랜드 유틸 고정광고</a></li>
					<li class=" <?=_subMenu_=='limit'?'active':''?>"><a href="statistics_limit.php"><i class="fa fa-caret-right"></i>키보드 적립 제한 설정</a></li>
					<li class=" <?=_subMenu_=='spot_point'?'active':''?>"><a href="spot_point.php"><i class="fa fa-caret-right"></i>이벤트 포인트 설정</a></li>
					<?php if($admin['level'] == 'super'){ ?>
						<li class=" <?=_subMenu_=='config'?'active':''?>"><a href="preferences.php"><i class="fa fa-caret-right"></i>환경설정</a></li>
					<?php } ?>
				</ul>
			</li>

			<li class="nav-parent <?=_Menu_=='theme'?'active nav-active':''?>"><a href="layouts.html"><i class=" glyphicon glyphicon-picture"></i> <span>테마관리</span></a>
				<ul class="children" style="display:<?=_Menu_=='theme'?'block':'none'?>;">
					<li class=" <?=_subMenu_=='theme'?'active':''?>"><a href="theme.php"><i class="fa fa-caret-right"></i> 테마</a></li>
				</ul>
			</li>

			<li class="nav-parent <?=_Menu_=='inquiry'?'active nav-active':''?>"><a href="layouts.html"><i class=" glyphicon glyphicon-picture"></i> <span>문의하기</span></a>
				<ul class="children" style="display:<?=_Menu_=='inquiry'?'block':'none'?>;">
					<li class=" <?=_subMenu_=='inquiry'?'active':''?>"><a href="board_inquiry.php"><i class="fa fa-caret-right"></i> 문의하기</a></li>
				</ul>
			</li>

		</ul>

	</div><!-- leftpanelinner -->
</div><!-- leftpanel -->
