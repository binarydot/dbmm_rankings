<nav class="navbar navbar-default">
	<div class="container-fluid">
		<div class="collapse navbar-collapse">
			<ul class="nav navbar-nav">
				<li>
					<a href="<?=site_url('army/armyStats/mostused/0/none') ?>">Most Used Armies</a>
				</li>
				<li>
					<a href="<?=site_url('army/armyStats/mostused/12/none') ?>">Most Used Armies in the Last 12 Months</a>
				</li>
				<li>
					<a href="<?=site_url('army/armyStats/booklistorder/0/none') ?>">Armies Used</a>
				</li>
				<li>
					<a href="<?=site_url('army/armyStats/booklistorder/12/none') ?>">Armies Used in the Last 12 Months</a>
				</li>
				<li>
					<a href="<?=site_url('army/armyStats/firstused/0/none') ?>">First Use of Armies</a>
				</li>
			</ul>
		</div>
	</div>
</nav>
<div class="container-fluid">
	<h1><?=$armyMatches['armyName'];?></h1>
    	<table class="table table-striped table-condensed">
	    	<thead>
			<tr class="">
    				<th class="">Date</th>
    				<th class="">Competition</th>
    				<th class="">Round</th>
    				<th class="">Player(s)</th>
    				<th class="">Score</th>
    				<th class="">Opponent(s)</th>
    				<th class="">Opposing Army</th>
    			</tr>
    		</thead>
    		<tbody>
<?php $a = 0; ?>
<?php foreach ($armyMatches['matchDetails'] as $matchDetail): ?>
	<?php if ($a % 2): ?>
			<tr>
	<?php else: ?>
			<tr bgcolor="#eeeeee">
	<?php endif; ?>
				<td class=""><?=$matchDetail['compDate']?></td>
				<td class="">
					<a href="<?=site_url('competition/matches')."/".$matchDetail['compID']?>">
						<?=$matchDetail['compName']?>
					</a>
				</td>
				<td class=""><?=$matchDetail['compRound']?></td>
				<td class="">
					<?php foreach ($matchDetail['winners'] as $winner): ?>
					<a href="<?=site_url('player/details')."/".$winner['winnerID']?>">
						<?=$winner['winnerName']?>
					</a></p>
					<?php endforeach; ?>
				</td>
				<td class="text-center"><?=$matchDetail['matchScore']?></td>
				<td class="">
					<?php foreach ($matchDetail['losers'] as $loser): ?>
					<a href="<?=site_url('player/details')."/".$loser['loserID']?>">
						<?=$loser['loserName']?>
					</a></p>
					<?php endforeach; ?>
				</td>
				<td class="">
					<a href="<?=site_url('army/details').'/'.$matchDetail['otherArmyID']?>">
						<?=$matchDetail['otherArmyName']?>
					</a>
				</td>
			</tr>
			<?php $a++; ?>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
    	