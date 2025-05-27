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
		<h1>valuewalk-SDK : <font color="#1caf9a" size="3"> <?=strtoupper($admin['level'])?></font></h1>
	</div><!-- logopanel -->

	<div class="leftpanelinner">

		<h5 class="sidebartitle"></h5>
		<ul class="nav nav-pills nav-stacked nav-bracket">

			<li class="<?=_Menu_=='main'?'active':''?>"><a href="index.php"><i class="glyphicon glyphicon-copyright-mark"></i> <span>메인</span></a></li>
			<li class="<?=_Menu_=='manage'?'active':''?>"><a href="statistics_use.php"><i class="fa fa-krw"></i> <span>매출 통계</span></a></li>
			<li class="<?=_Menu_=='reward'?'active':''?>"><a href="statistics_save.php"><i class="fa fa-product-hunt"></i> <span>적립 통계</span></a></li>

			<?php if($_SESSION['Adm']['id'] == "mango"){ ?>
			<li class="<?=_Menu_=='user'?'active':''?>"><a href="member_list.php"><i class="glyphicon glyphicon-user"></i> <span>사용자 관리</span></a></li>

			<li class="nav-parent <?=_Menu_=='setting'?'active nav-active':''?>"><a href="member_info.php"><i class="fa fa-cog"></i> <span>제품별 설정</span></a>
				<ul class="children" style="display:<?=_Menu_=='setting'?'block':'none'?>;">
					<li class=" <?=_subMenu_=='ladder_set'?'active':''?>"><a href="set_ladder.php"><i class="fa fa-caret-right"></i>사다리 설정</a></li>
					<li class=" <?=_subMenu_=='roulette_set'?'active':''?>"><a href="set_roulette.php"><i class="fa fa-caret-right"></i>룰렛 설정</a></li>
				</ul>
			</li>
			<?php } ?>
			<li class="nav-hover" style="position: fixed;top: 800px;width: 200px;"><a href="javascript:;" onclick="document.logoutform.submit();"><i class="glyphicon glyphicon-log-out"></i> Log Out</a></li>

		</ul>
	</div><!-- leftpanelinner -->
</div><!-- leftpanel -->
