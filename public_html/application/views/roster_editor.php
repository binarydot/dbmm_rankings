<?php
?>
<script type="text/javascript" src="<?=base_url()?>/js/roster_builder_model.js"></script>
<script type="text/javascript" src="<?=base_url()?>/js/roster_builder_controller.js"></script>
<style>
.rosterTable {
	border-collapse:collapse;
}
.rosterTable table,.rosterTable th,.rosterTable td {
	border: 1px solid red;
}
.rosterTable th {
	background-color: red;
	color: white;
	padding-left: 20px;
	padding-right: 20px;
}
.rosterPopupTable td {
	padding-top: 10px;
	padding-bottom: 10px;
	padding-left: 10px;
	padding-right: 10px;
}
</style>
<div><h1>DBMM Roster Editor</h1></div>
<div id="popupHolder"></div>
<div>
<div>
	<span>This is pre-alpha at the moment - i.e. it doesn&apos;t work at all. One day I will finish it :)</span>
</div>
<div id="command1Div">
<fieldset><legend>Command 1</legend>
<div id="command1Elements"><span class="button"><a href="javascript:showElementTypePopup(1);">Add Element</a></span></div>
</fieldset>
</div>
<div id="command2Div">
<fieldset><legend>Command 2</legend>
<div id="command2Elements"><span class="button"><a href="javascript:showElementTypePopup(2);">Add Element</a></span></div>
</fieldset>
</div>
<div id="command3Div">
<fieldset><legend>Command 3</legend>
<div id="command3Elements"><span class="button"><a href="javascript:showElementTypePopup(3);">Add Element</a></span></div>
</fieldset>
</div>
<div id="command4Div">
<fieldset><legend>Command 4</legend>
<div id="command4Elements"><span class="button"><a href="javascript:showElementTypePopup(4);">Add Element</a></span></div>
</fieldset>
</div>
<div id="commandBaggageDiv">
<fieldset><legend>Baggage Command</legend>
<div id="commandBaggageElements"><span class="button"><a href="javascript:showElementTypePopup(5);">Add Element</a></span></div>
</fieldset>
</div>
<div id="stratagemsDiv">
<fieldset><legend>Stratagems</legend>
<div id="stratagems">
<span class="button">Add Stratagem</span>
</div>
</div>
</fieldset>
</div>
<div id="totals">
<table class="rosterTable">
<tr>
<th>Command</th><th>AP</th><th>ME</th><th>Elements</th><th>Disheartened</th><th>Breakpoint</th><th>Shattered</th>
</tr>
<tr>
<td>Command 1</td>
<td id="command1AP"></td>
<td id="command1ME"></td>
<td id="command1Els"></td>
<td id="command1DH"></td>
<td id="command1BP"></td>
<td id="command1Sh"></td>
</tr>
<tr>
<td>Command 2</td>
<td id="command2AP"></td>
<td id="command2ME"></td>
<td id="command2Els"></td>
<td id="command2DH"></td>
<td id="command2BP"></td>
<td id="command2Sh"></td>
</tr>
<tr>
<td>Command 3</td>
<td id="command3AP"></td>
<td id="command3ME"></td>
<td id="command3Els"></td>
<td id="command3DH"></td>
<td id="command3BP"></td>
<td id="command3Sh"></td>
</tr>
<tr>
<td>Command 4</td>
<td id="command4AP"></td>
<td id="command4ME"></td>
<td id="command4Els"></td>
<td id="command4DH"></td>
<td id="command4BP"></td>
<td id="command4Sh"></td>
</tr>
<tr>
<td>Baggage Command</td>
<td id="command5AP"></td>
<td id="command5ME"></td>
<td id="command5Els"></td>
<td id="command5DH"></td>
<td id="command5BP"></td>
<td id="command5Sh"></td>
</tr>
<tr>
<td>Army</td>
<td id="armyAP"></td>
<td id="armyME"></td>
<td id="armyEls"></td>
<td></td>
<td id="armyBP"></td>
<td></td>
</tr>
</table>
</div>
</div>