<script type="text/javascript">

<!--

function showInstructions() {

	if (document.getElementById('inputstyleselect').selectedIndex == 0) {

		document.getElementById('bhgswebinstructions').style.display = "block";

		document.getElementById('dyinstructions').style.display = "none";

	} else if (document.getElementById('inputstyleselect').selectedIndex == 1) {

		document.getElementById('bhgswebinstructions').style.display = "none";

		document.getElementById('dyinstructions').style.display = "block";

	}

}

-->

</script>

<form action="<?=site_url('competition/validateAllResults').'/'.$competitionId?>"

	enctype="multipart/form-data" method="POST">

	<p>Warning: uploading a results file will replace all the existing

		results for this competition!</p>

	<p>

		Choose an input file format: <select id="inputstyleselect"

			onChange="javascript:showInstructions();">

			<option value="bhgsweb">BHGS Web format</option>

			<option value="dy">David Young format</option>

		</select>

	

	

	<div id="bhgswebinstructions" style="display: block;">

		<fieldset>

			<legend>Instructions for BHGS Web format.</legend>

			<p>This currently expects all results to be in a BHGS style grid of

				opponents and scores.</p>

			<p>This should be a csv file with the following columns:</p>

			<p>Column 1 - Position.</p>

			<p>Column 2 - Player ID - only used to work out match pairings.</p>

			<p>Column 3 - Player Name - used to match in the database. If the

				player is not already in the database, you will be prompted to

				select one.</p>

			<p>2n columns of data. The first n columns are opponents, the second

				n columns are scores in each round. We therefore assume that there

				are n rounds in the competition.</p>

			<p>Penultimate column is overall score.</p>

			<p>Final column with army name. If the army does not exist in the

				database, you will be prompted to select one.</p>

			<p>There should be no header line in the csv file.</p>

			<p>If the player did not take part in a round, their opponent should

				be 'X', and their score should be '0'.</p>

			<p>If the player was given a Bye in a round, their opponent should be

				'B'.</p>

			<p>So for example:</p>

			<table>

				<tr>

				</tr>

			</table>

		</fieldset>

	</div>

	<div id="dyinstructions" style="display: none;">

		<fieldset>

			<legend>Instructions for David Young format.</legend>

			<p>Not currently supported but coming soon.</p>

		</fieldset>

	</div>

	<p></p>

	<fieldset>

		<legend>File to upload</legend>

		<p>The file to be uploaded must have Unix line terminators (LF).

			Windows line terminators (CR+LF) may also work. CR will not work -

			the file will be seen as a single long line.</p>

		<input type="file" id="csvfile" name="csvfile" /> <input type="submit"

			value="Submit" /> <input type="hidden" id="compid" name="compid"

			value="<?=$competitionId; ?>" />

	</fieldset>

</form>

