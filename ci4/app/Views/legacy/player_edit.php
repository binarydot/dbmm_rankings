<div id="main">

	<span class="heading">

		Edit <?=$playerData['first_name']?> <?=$playerData['last_name']?>

	</span>

	<form action="<?=site_url("player/update").'/'.$playerData['competitor_id']?>" method="post"

		name="player_edit">

		<span><?=$errorMessage?></span>

		<table>

			<tr>

				<td class="table_cell">ID</td>

				<td class="table_cell"><?=$playerData['competitor_id']?>

				</td>

			</tr>

			<tr>

				<td class="table_cell">IWF ID</td>

				<td class="table_cell">

					<input value="<?=$playerData['iwf_id']?>" id="iwf_id" name="iwf_id" />

				</td>

			</tr>

			<tr>

				<td class="table_cell">First Name</td>

				<td class="table_cell">

				<input value="<?=$playerData['first_name']?>" id="first_name" name="first_name" />

				</td>

			</tr>

			<tr>

				<td class="table_cell">Last Name</td>

				<td class="table_cell">

					<input value="<?=$playerData['last_name']?>" id="last_name" name="last_name" />

				</td>

			</tr>

			<tr>

				<td class="table_cell">Region</td>

				<td class="table_cell">

					<select id="rar" name="rar">

						<?php foreach ($rars as $rarinst): ?>

						<?php 	if ($playerData['rar_id'] == $rarinst->rar_id): ?>

						<option value="<?=$rarinst->rar_id?>" selected>

							<?=$rarinst->rar_name?>

						</option>

						<?php 	else: ?>

						<option value="<?=$rarinst->rar_id?>">

							<?=$rarinst->rar_name?>

						</option>

						<?php 	endif;?>

						<?php endforeach; ?>

					</select>

				</td>

			</tr>

			<tr>

				<td class="table_cell">Anonymous</td>

				<td class="table_cell">

					<select id="anonymous" name="anonymous">

						<?php 	if ($playerData['anonymous'] == 0): ?>

						<option value="0" selected>

							No

						</option>

						<option value="1">

							Yes

						</option>

						<?php 	else: ?>

						<option value="0">

							No

						</option>

						<option value="1" selected>

							Yes

						</option>

						<?php 	endif;?>

					</select>

				</td>

			</tr>

			<tr>

				<td class="table_cell">Creation Date</td>

				<td class="table_cell"><?=$playerData['cr_date']?>

				</td>

			</tr>

		</table>

		<table>

			<tr>

				<td>

					<span class="button">

						<a href="<?=site_url('player/details').'/'.$playerData['competitor_id']?>">

							Cancel

						</a>

					</span>

				</td>

				<td>

					<span class="button">

						<a href="javascript:document.forms[0].submit();">

							Save

						</a>

					</span>

				</td>

			</tr>

		</table>

	</form>

</div>

