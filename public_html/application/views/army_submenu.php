<nav class="navbar navbar-default">
	<div class="container-fluid">
		<div class="collapse navbar-collapse">
			<ul class="nav navbar-nav">
				<li>
<?php if ($viewType == "mostused" && $monthsBack != 12): ?>
					<a class="active" href="<?=site_url('army/armyStats/mostused/0/none') ?>">Most Used Armies</a>
<?php else: ?>
					<a href="<?=site_url('army/armyStats/mostused/0/none') ?>">Most Used Armies</a>
<?php endif; ?>
				</li>
				<li>
<?php if ($viewType == "mostused" && $monthsBack == 12): ?>
					<a class="active" href="<?=site_url('army/armyStats/mostused/12/none') ?>">Most Used Armies (Last 12 Months)</a>
<?php else: ?>
					<a href="<?=site_url('army/armyStats/mostused/12/none') ?>">Most Used Armies (Last 12 Months)</a>
<?php endif; ?>
				</li>
				<li>
<?php if ($viewType == "booklistorder" && $monthsBack != 12): ?>
					<a class="active" href="<?=site_url('army/armyStats/booklistorder/0/none') ?>">Armies Used</a>
<?php else: ?>
					<a href="<?=site_url('army/armyStats/booklistorder/0/none') ?>">Armies Used</a>
<?php endif; ?>
				</li>
				<li>
<?php if ($viewType == "booklistorder" && $monthsBack == 12): ?>
					<a class="active" href="<?=site_url('army/armyStats/booklistorder/12/none') ?>">Armies Used (Last 12 Months)</a>
<?php else: ?>
					<a href="<?=site_url('army/armyStats/booklistorder/12/none') ?>">Armies Used (Last 12 Months)</a>
<?php endif; ?>
				</li>
				<li>
<?php if ($viewType == "firstused" && $monthsBack != 12): ?>
					<a class="active" href="<?=site_url('army/armyStats/firstused/0/none') ?>">First Use of Armies</a>
<?php else: ?>
					<a href="<?=site_url('army/armyStats/firstused/0/none') ?>">First Use of Armies</a>
<?php endif; ?>
				</li>
			</ul>
		</div>
	</div>
</nav>