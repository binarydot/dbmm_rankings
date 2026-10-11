<div class="inner_main">

	<script language="JavaScript">

	<!--

	function doSubmit() {

		if (document.getElementById('password1').value != document.getElementById('password2').value) {

			alert("Passwords do not match");

			return;

		}

		document.forms[0].submit();

	}

	-->

	</script>

	<form name="editpassword" action="<?=site_url("admin/updatePasswordOther")?>" method="post">

		<input name="id" id="id" value="<?=$id?>" type="hidden">

		<table>

			<tr>

				<td class="table_cell">Password</td>

				<td class="table_cell">

					<input name="password1" id="password1" value="" type="password">

				</td>

			</tr>

			<tr>

				<td class="table_cell">Password (repeat)</td>

				<td class="table_cell">

					<input name="password2" id="password2" value="" type="password">

				</td>

			</tr>

		</table>

		<table class="button_table">

			<tr class="action_bar">

				<td colspan="2" class="buttons">

				<td>

					<span class="button">

						<a href="javascript:doSubmit();">Set</a>

					</span>

					<span class="button">

						<a href="<?=site_url("admin/userDetails")."/".$id?>">Cancel</a>

					</span>

				</td>

			</tr>

		</table>

	</form>

</div>



