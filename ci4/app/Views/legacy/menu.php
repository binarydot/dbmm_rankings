<div class="row container-fluid">
<nav id="main_menubar">
	<ul class="nav nav-tabs" id="main_menu_items">
<?php if($menuItem == "rankings"): ?>
		<li class="active" id="menuitem_rankings">
<?php else: ?>
		<li class="" id="menuitem_rankings">
<?php endif; ?>
			<a href="<?=site_url("rankings/comps")?>">
				<i class="fa fa-star fa-lg"></i> <?=legacy_lang("title_Rankings")?>
			</a>
		</li>
		<!--
<?php if($menuItem == "kudos"): ?>
		<li class="active" id="menuitem_kudos">
<?php else: ?>
		<li class="" id="menuitem_kudos">
<?php endif; ?>
			<a href="<?=site_url("rankings/kudos")?>">
				<?=legacy_lang("title_Kudos")?>
			</a>
		</li>
		-->
<?php if($menuItem == "glicko"): ?>
		<li class="active" id="menuitem_glicko">
<?php else: ?>
		<li class="" id="menuitem_glicko">
<?php endif; ?>
			<a href="<?=site_url("rankings/glicko")?>">
				<i class="fa fa-line-chart fa-lg"></i> <?=legacy_lang("title_Glicko")?>
			</a>
		</li>
<?php if($menuItem == "players"): ?>
		<li class="active" id="menuitem_players">
<?php else: ?>
		<li class="" id="menuitem_players">
<?php endif; ?>
			<a href="<?=site_url("player")?>">
				<i class="fa fa-users fa-lg"></i> <?=legacy_lang("title_Players")?>
			</a>
		</li>
<?php if($menuItem == "competitions"): ?>
		<li class="active" id="menuitem_competitions">
<?php else: ?>
		<li class="" id="menuitem_competitions">
<?php endif; ?>
		<a href="<?=site_url("competition")?>">
				<i class="fa fa-trophy fa-lg"></i> <?=legacy_lang("title_Competitions")?>
			</a>
		</li>
<?php if($menuItem == "armies"): ?>
		<li class="active" id="menuitem_armies">
<?php else: ?>
		<li class="" id="menuitem_armies">
<?php endif; ?>
			<a href="<?=site_url("army")?>">
				<i class="fa fa-shield fa-lg"></i> <?=legacy_lang("title_Armies")?>
			</a>
		</li>
<?php if($menuItem == "stats"): ?>
		<li class="active" id="menuitem_stats">
<?php else: ?>
		<li class="" id="menuitem_stats">
<?php endif; ?>
			<a href="<?=site_url("matches")?>">
				<i class="fa fa-pie-chart fa-lg"></i> <?=legacy_lang("title_Stats")?>
			</a>
		</li>
		<!--
<?php if($menuItem == "roster"): ?>
		<li class="active" id="menuitem_roster">
<?php else: ?>
		<li class="" id="menuitem_roster">
<?php endif; ?>
			<a href="<?=site_url("roster")?>">
				<?=legacy_lang("title_Roster")?>
			</a>
		</li>
		-->
		<!-- 
		<li class="" id="menuitem_topten">
			<a href="<?=site_url("army")?>">
				Top 10
			</a>
		</li>
		-->
<?php if ($legacySession->userdata('loggedIn') == TRUE): ?>
	<?php if($menuItem == "admin"): ?>
		<li class="active" id="menuitem_admin">
	<?php else: ?>
		<li class="" id="menuitem_admin">
	<?php endif; ?>
			<a href="<?=site_url("admin")?>">
				<i class="fa fa-wrench fa-lg"></i> <?=legacy_lang("title_Admin")?>
			</a>
		</li>
		<li class="">
			<a href="<?=site_url("logoff")?>">
				<i class="fa fa-sign-out fa-lg"></i> Log Out
			</a>
		</li>
		
<?php else: ?>
	<?php if($menuItem == "login"): ?>
		<li class="active" id="menuitem_login">
	<?php else: ?>
		<li class="" id="menuitem_login">
	<?php endif; ?>
			<a href="<?=site_url("login")?>">
				<i class="fa fa-wrench fa-lg"></i> <?=legacy_lang("title_Admin")?>
			</a>
		</li>
<?php endif; ?>
		<li class="" id="menuitem_forum">
			<a href="http://dbmm.org.uk/forum" target="_blank">
				Forum
			</a>
		</li>
	</ul>
</nav>
</div>