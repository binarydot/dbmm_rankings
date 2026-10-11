<div class="container">
<?php  if (isset($matches)): ?>
<table class="table table-striped">
	<tr class="table_header_row">
		<th class="">Competition</th>
		<th class="">Date</th>
		<th class="">Round</th>
		<th class="">Army</th>
		<th></th>
		<th class="">Score</th>
		<th class="">Opponent(s)</th>
	</tr>
<?php  foreach($matches AS $match): ?>
	<?php  $winnernames = "<a href=\"".site_url('player/matches')."/".$match->winner1CompetitorId."\">"; ?>
	<?php  $winnernames .= $match->winner1FirstName." ".$match->winner1LastName."</a>"; ?>
	<?php  if (property_exists($match, "winner2FirstName")) : ?>
		<?php  $winnernames .= ", <a href=\"".site_url('player/matches')."/".$match->winner2CompetitorId."\">"; ?>
		<?php  $winnernames .= $match->winner2FirstName." ".$match->winner2LastName."</a>"; ?>
	<?php  endif; ?>
	<?php  $losernames = "<a href=\"".site_url('player/matches')."/".$match->loser1CompetitorId."\">"; ?>
	<?php  $losernames .= $match->loser1FirstName." ".$match->loser1LastName."</a>"; ?>
	<?php  if (property_exists($match, "loser2FirstName")) : ?>
		<?php  $losernames .= ", <a href=\"".site_url('player/matches')."/".$match->loser2CompetitorId."\">"; ?>
		<?php  $losernames .= $match->loser2FirstName." ".$match->loser2LastName."</a>"; ?>
	<?php  endif; ?>
	<?php  if (property_exists($match, "winner1ArmyName") && $match->winner1ArmyName != null) : ?>
		<?php  $winnerarmy = "<a href=\"".site_url('army/listMatches')."/".$match->winner1ArmyId."\">"; ?>
		<?php  $winnerarmy .= $match->winner1ArmyName."</a>"; ?>
	<?php  endif; ?>
	<tr>
		<td class="table_cell">
			<a href="<?=site_url('competition/matches').'/'.$match->competition_id;?>">
				<?=$match->competition_name;?>
			</a>
		</td>
		<td class="table_cell">
			<?=$match->competition_date;?>
		</td>
		<td class="table_cell">
			<?=$match->round;?>
		</td>
		<td class="table_cell">
			<a href="<?=site_url('army/listMatches').'/'.$match->army_id;?>">
				<?=$match->army;?>
			</a>
		</td>
	<?php  if ($match->side == "W"): ?>
		<?php  if ($match->winner_score > 15): ?>
		<td class="table_cell green">
			<?=number_format($match->winner_score);?> - <?=number_format($match->loser_score);?>
		</td>
		<td class="table_cell"><?=$losernames;?></td>
		<?php  else: ?>
		<td class="table_cell" style="color:goldenrod;">
			<?=number_format($match->winner_score);?> - <?=number_format($match->loser_score);?>
		</td>
		<td class="table_cell"><?=$losernames;?></td>
		<?php  endif; ?>
	<?php  else: ?>
		<?php  if ($match->winner_score > 15): ?>
		<td class="table_cell red">
			<?=number_format($match->loser_score);?> - <?=number_format($match->winner_score);?>
		</td>
		<td class="table_cell"><?=$winnernames;?></td>
		<?php  else: ?>
		<td class="table_cell" style="color:goldenrod;">
			<?=number_format($match->loser_score);?> - <?=number_format($match->winner_score);?>
		</td>
		<td class="table_cell"><?=$winnernames;?></td>
		<?php  endif; ?>
	<?php  endif; ?>
	</tr>
<?php  endforeach; ?>
</table>
	<div class="">
   		<!--Load the AJAX API-->
    			<script type="text/javascript" src="https://www.google.com/jsapi"></script>
    			<script type="text/javascript">
<?php 
	$dbmmLabels = array("25 - 0","24 - 1","23 - 2","22 - 3","21 - 4","20 - 5","19 - 6","18 - 7","17 - 8","16 - 9","15 - 10","14 - 11","13 - 12","12 - 13","11 - 14","10 - 15","9 - 16","8 - 17","7 - 18","6 - 19","5 - 20","4 - 21","3 - 22","2 - 23","1 - 24","0 - 25"); 
	$dbmmResults = array(); 
	foreach($dbmmLabels as $dbmmLabel) {
		$dbmmResults[$dbmmLabel]["name"] = $dbmmLabel;
		$dbmmResults[$dbmmLabel]["count"] = 0;
	}
	foreach($matches as $match) {
		if ($match->side == "W") {
			$dbmmResults[round($match->winner_score)." - ".round($match->loser_score)]["count"]++;
		} else {
			$dbmmResults[round($match->loser_score)." - ".round($match->winner_score)]["count"]++;
		}
	}
?>
	      			// Load the Visualization API and the piechart package.
	      			google.load('visualization', '1.0', {'packages':['corechart']});
	
	      			// Set a callback to run when the Google Visualization API is loaded.
	      			google.setOnLoadCallback(drawChart);
	
	     			// Callback that creates and populates a data table,
	      			// instantiates the pie chart, passes in the data and
	      			// draws it.
	      			function drawChart() {
	
	        			// Create the data table.
        				var data = new google.visualization.arrayToDataTable([['Score','Frequency', { role: 'style' }],
        				<?php
	$a = 0;
	foreach ($dbmmResults as $dbmmResult) {
		if ($a < 10) {
			echo "['".$dbmmResult["name"]."',".$dbmmResult["count"].",'green'],";
		} elseif ($a < 16) {
			echo "['".$dbmmResult["name"]."',".$dbmmResult["count"].",'gold'],";
		} else {
			echo "['".$dbmmResult["name"]."',".$dbmmResult["count"].",'red'],";
		}
		$a++;
	}
        				?>
        				], false);
		        		// Set chart options
		        		var options = {'title':'Match Results',
		                       		'width':800,
		                       		'height':900,
		                       		'isStacked':false};
		
		        		// Instantiate and draw our chart, passing in some options.
		        		var chart = new google.visualization.BarChart(document.getElementById('chart_div'));
		        		chart.draw(data, options);
		      		}

    			</script>

		<!--Div that will hold the chart-->
		<div id="chart_div"></div>
		</div>
	</div>
<?php  else: ?>
<p>No matches found</p>
<?php  endif; ?>
</div>
