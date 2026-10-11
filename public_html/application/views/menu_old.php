<div id="main_menubar">
	<div id="main_menu_items">
<?php if($menuItem == "rankings"): ?>
		<span class="menuitemselected" id="menuitem_rankings">
<?php else: ?>
		<span class="menuitem" id="menuitem_rankings">
<?php endif; ?>
			<a href="<?=site_url("rankings/comps")?>">
				<?=lang("title_Rankings")?>
			</a>
		</span>
		<!--
<?php if($menuItem == "kudos"): ?>
		<span class="menuitem" id="menuitem_kudos">
<?php else: ?>
		<span class="menuitem" id="menuitem_kudos">
<?php endif; ?>
			<a href="<?=site_url("rankings/kudos")?>">
				<?=lang("title_Kudos")?>
			</a>
		</span>
		-->
<?php if($menuItem == "glicko"): ?>
		<span class="menuitem" id="menuitem_glicko">
<?php else: ?>
		<span class="menuitem" id="menuitem_glicko">
<?php endif; ?>
			<a href="<?=site_url("rankings/glicko")?>">
				<?=lang("title_Glicko")?>
			</a>
		</span>
<?php if($menuItem == "players"): ?>
		<span class="menuitemselected" id="menuitem_players">
<?php else: ?>
		<span class="menuitem" id="menuitem_players">
<?php endif; ?>
			<a href="<?=site_url("player")?>">
				<?=lang("title_Players")?>
			</a>
		</span>
<?php if($menuItem == "competitions"): ?>
		<span class="menuitemselected" id="menuitem_competitions">
<?php else: ?>
		<span class="menuitem" id="menuitem_competitions">
<?php endif; ?>
		<a href="<?=site_url("competition")?>">
				<?=lang("title_Competitions")?>
			</a>
		</span>
<?php if($menuItem == "armies"): ?>
		<span class="menuitemselected" id="menuitem_armies">
<?php else: ?>
		<span class="menuitem" id="menuitem_armies">
<?php endif; ?>
			<a href="<?=site_url("army")?>">
				<?=lang("title_Armies")?>
			</a>
		</span>
<?php if($menuItem == "stats"): ?>
		<span class="menuitemselected" id="menuitem_stats">
<?php else: ?>
		<span class="menuitem" id="menuitem_stats">
<?php endif; ?>
			<a href="<?=site_url("matches")?>">
				<?=lang("title_Stats")?>
			</a>
		</span>
<?php if($menuItem == "roster"): ?>
		<span class="menuitemselected" id="menuitem_roster">
<?php else: ?>
		<span class="menuitem" id="menuitem_roster">
<?php endif; ?>
			<a href="<?=site_url("roster")?>">
				<?=lang("title_Roster")?>
			</a>
		</span>
		<!-- 
		<span class="menuitem" id="menuitem_topten">
			<a href="<?=site_url("army")?>">
				Top 10
			</a>
		</span>
		-->
<?php if ($this->session->userdata('loggedIn') == TRUE): ?>
	<?php if($menuItem == "admin"): ?>
		<span class="menuitemselected" id="menuitem_admin">
	<?php else: ?>
		<span class="menuitem" id="menuitem_admin">
	<?php endif; ?>
			<a href="<?=site_url("admin")?>">
				<?=lang("title_Admin")?>
			</a>
		</span>
		<span class="menuitem">
			<a href="<?=site_url("logoff")?>">
				Log Out
			</a>
		</span>
		
<?php else: ?>
	<?php if($menuItem == "login"): ?>
		<span class="menuitemselected" id="menuitem_login">
	<?php else: ?>
		<span class="menuitem" id="menuitem_login">
	<?php endif; ?>
			<a href="<?=site_url("login")?>">
				<?=lang("title_Admin")?>
			</a>
		</span>
<?php endif; ?>
	</div>
</div>