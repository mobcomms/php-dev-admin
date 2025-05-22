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
		<h1>finnq-SDK : <font color="#1caf9a" size="3"> <?=strtoupper($admin['level'])?></font></h1>
	</div><!-- logopanel -->

	<div class="leftpanelinner">

		<h5 class="sidebartitle"></h5>
		<ul class="nav nav-pills nav-stacked nav-bracket">

			<li class="<?=_Menu_=='main'?'active':''?>"><a href="index.php"><i class="glyphicon glyphicon-copyright-mark"></i> <span>메인</span></a></li>

			<li class="nav-parent <?=_Menu_=='inquiry'?'active nav-active':''?>"><a href="layouts.html"><i class=" glyphicon glyphicon-picture"></i> <span>문의하기</span></a>
				<ul class="children" style="display:<?=_Menu_=='inquiry'?'block':'none'?>;">
					<li class=" <?=_subMenu_=='inquiry_ppz'?'active':''?>"><a href="board_inquiry.php?target=PPZ"><i class="fa fa-caret-right"></i>오퍼월 문의하기</a></li>
				</ul>
			</li>

		</ul>

	</div><!-- leftpanelinner -->
</div><!-- leftpanel -->
