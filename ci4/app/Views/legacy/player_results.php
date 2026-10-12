<div class="container">
	<h2><?=$results[0]->first_name." ".$results[0]->last_name?></h2>
	<table class="table table-striped">
		<tr class="">
			<th class="">Competition</th>
			<th class="">Army</th>
			<th class="">Position</th>
			<th class="">Score</th>
			<th class="">Bonus</th>
		</tr>
		<?php  if (isset($results)): ?>
		<?php    foreach( $results AS $result ): ?>
		<tr>
			<td class="">
				<a href="<?=site_url('competition/details')."/".$result->competition_id?>">
					<?=$result->competition_name?>
				</a>
			</td>
			<td class=""><?=$result->name?></td>
			<td class=""><?=$result->position?></td>
			<td class=""><?=$result->score?></td>
			<td class=""><?=$result->bonus?></td>
		</tr>
		<?php  endforeach; ?>
		<?php  else: ?>
		<tr>
			<td class="" colspan="5">No competitions found</td>
		</tr>
		<?php  endif; ?>
	</table>
</div>