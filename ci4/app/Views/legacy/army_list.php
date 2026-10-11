<div class="inner_main container-fluid">
<p>The table below summarises the
<?php if ($viewType == "firstuse"): ?>
	 players who made first use
<?php else: ?>
	 usage and results
<?php endif; ?>
 of each army in rated competitions where such details have been supplied. </p>
<!-- rb 3/9/08 DY also notes the rules version and RAR of use in this text.-->
<?php if ($viewType == "mostused"): ?>
	<p>The armies are ranked in order of most uses in rated competitions.</p>
<?php else: ?>
	<p>It is in book then list number order.</p>
<?php endif; ?>
	<p>The version column shows if it is the DBM or DBMM version of the army
	list. Only armies that have been used are included. You can click on the name of an
	individual army to get a detailed record of that individual armies use to date.</p>
	<p>The total number of recorded matches (games) played with the army in all competitions is
	shown and the average 25 point score in those games which used the 0-25 scoring system.</p>

<?php if ($viewType == "firstuse"): ?>
	<p>Some armies have not been used in a rated game. They appear in the separate Unused Armies List.
	This table lists the name of the player who first used the army and the competition and round in which
	they first used it.</p>
<?php else: ?>
	<p>The middle columns show the percentage of games "won", "drawn" and "lost" for
	each army. For the moment the winner is simply the player with the higher score in the
	game and hence under 0-25 scoring there are no draws.</p>
<?php endif; ?>

<p>Each army&apos;s current Top Dog player handicap is also shown. This depends upon the army&apos;s average points per game and the number of games it has been used in. For more details on the Top Dog handicap system devised by Dean
Bedlington please see <a href="<?=site_url('army/topdog')."/".$viewType."/".$monthsBack."/".$rankingMethod?>">here</a>. 
Remember that your final Top Dog is calculated as 2/3 of your player handicap plus 1/3 of your army&apos;s handicap. Note that for some early competitions no round by round data is available and hence these statistics are all listed as "N/A". </p>

<table class="table table-striped table-condensed">
	<thead>
<!-- 
 echo "	<tr class=\"table_header_row\">\n";
if ($rankingMethod != "none"){
echo "		<td class=\"table_header\"></td>\n";
}
echo "		<td class=\"table_header_center\" colspan=\"4\">Army</td>\n";
if ($viewType == "firstuse"){
echo "		<td class=\"table_header_center\" colspan=\"3\">Matches</td>\n";
echo "		<td class=\"table_header_center\" colspan=\"3\">First Use Details</td>\n";
} else {
echo "		<td class=\"table_header_center\" colspan=\"6\">Matches</td>\n";
}
echo "	</tr>\n";
-->
		<tr class="">
<?php if ($rankingMethod != "none"): ?>
			<th class="">Rank</th>
<?php endif; ?>
			<th class="">Book</th>
			<th class="">List</th>
			<th class="">Name</th>
			<th class="text-center" title="The version column shows if it is the DBM or DBMM version of the army list.">Version</td>
			<th class="text-center">Total</th>
			<th class="text-center">Avg. Score</th>
			<th class="text-center">Top Dog</th>
<?php if ($viewType == "firstuse"): ?>
			<th class="text-center">First User(s)</th>
			<th class="text-center">When</th>
			<th class="text-center">Competition</th>
<?php else: ?>
			<th class="text-center">% Won</th>
			<th class="text-center">% Drawn</th>
			<th class="text-center">% Lost</th>
<?php endif; ?>
		</tr>
	</thead>
	<tbody>
<?php foreach($allArmies as $army): ?>
		<tr>
<?php 	if ($rankingMethod != "none"): ?>
			<td class="text-center"><?=$army['armyRank'] ?></td>
<?php 	endif; ?>
			<td class="text-center"><?=$army['armyBook'] ?></td>
			<td class="textcenter"><?=$army['armyList'] ?></td>
			<td class="">
				<a href="<?=site_url('army/listMatches/')."/".$army['armyID'] ?>">
					<?=$army['armyName'] ?>
				</a>
			</td>
			<td class="text-center"><?=$army['rulesSystemName'] ?></td>
			<td class="text-center"><?=$army['numMatches'] ?></td>
<?php 	if ($army['avgScore'] != "N/A"): ?>
			<td class="text-center" title="<?=$army['avgScoreHover'] ?>">
				<?=number_format($army['avgScore'] ,2)?>
			</td>
<?php 	else: ?>
			<td class="text-center">N/A</td>
<?php 	endif; ?>
			<td class="text-center"><?=number_format($army['topDog'] ,2) ?></td>
<?php 	if ($viewType == "firstuse"): ?>
<?php 		if($first_f_name == "N/A"): //ie no first user name found ?>
			<td class="">N/A</td>
<?php 		else: ?>
			<td class=""><?=$army['firstFName']." ".$army['firstLName'] ?></td>
<?php 		endif; ?>
			<td class="text-center"><?=$army['firstDate'] ?></td>
<?php 		if ($army['firstRound'] != "N/A") {
				$army['firstRound'] = "R".$army['firstRound'];
			} ?>
			<td class="">
				<a href="<?=site_url('competition/').$army['firstComp'] ?>">
					<?=$army['firstRound']." ".$army['firstCompName'] ?>
				</a>
			</td>
<?php 	else: ?>
			<td class="text-center"><?=$army['percentWins'] ?></td>
			<td class="text-center"><?=$army['percentDraws'] ?></td>
			<td class="text-center"><?=$army['percentLosses'] ?></td>
<?php 	endif; ?>
		</tr>
<?php endforeach; ?>
		</tbody>
	</table>
</div>