<?php $dbmmResults = array("25 - 0","24 - 1","23 - 2","22 - 3","21 - 4","20 - 5","19 - 6","18 - 7","17 - 8","16 - 9","15 - 10","14 - 11","13 - 12"); ?>

<div class="inner_main">

    <!--Load the AJAX API-->

    <script type="text/javascript" src="https://www.google.com/jsapi"></script>

    <script type="text/javascript">



      // Load the Visualization API and the piechart package.

      google.load('visualization', '1.0', {'packages':['corechart']});



      // Set a callback to run when the Google Visualization API is loaded.

      google.setOnLoadCallback(drawChart);



      // Callback that creates and populates a data table,

      // instantiates the pie chart, passes in the data and

      // draws it.

      function drawChart() {



        // Create the data table.

        var data = new google.visualization.arrayToDataTable([

            ['System'

<?php  foreach ($dbmmResults as $res): ?>

             ,'<?=$res?>'

<?php  endforeach;?>

             ]

<?php  foreach ($resultsData as $key=>$system): ?>

            ,['<?=$system['name']?>'

<?php    foreach ($dbmmResults as $res): ?>

<?php      if (array_key_exists($res, $system['scores'])): ?>

            ,<?=$system["scores"][$res]["percent"]?>

<?php      else: ?>

            ,0

<?php      endif; ?>

<?php    endforeach;?>

            ]

<?php  endforeach; ?>

        ]);

        // Set chart options

        var options = {'title':'Results',

                       'width':800,

                       'height':600,

                       'isStacked':true};



        // Instantiate and draw our chart, passing in some options.

        var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));

        chart.draw(data, options);

      }



<?php  /*	

<?php  foreach ($resultsData as $key=>$system): ?>

      // Set a callback to run when the Google Visualization API is loaded.

      google.setOnLoadCallback(drawChart<?=$key?>);



      // Callback that creates and populates a data table,

      // instantiates the pie chart, passes in the data and

      // draws it.

      function drawChart<?=$key?>() {



        // Create the data table.

        var data = new google.visualization.arrayToDataTable([

            ['Score','Frequency'],

<?php    foreach ($dbmmResults as $res): ?>

<?php      if (array_key_exists($res, $system['scores'])): ?>

        	['<?=$res?>', <?=$system["scores"][$res]["percent"]?>],

<?php      else: ?>

        	['<?=$res?>', 0],

<?php      endif; ?>

<?php    endforeach; ?>

			['',0]

        ]);

        // Set chart options

        var options = {'title':'Results',

                       'width':800,

                       'height':600};



        // Instantiate and draw our chart, passing in some options.

        var chart = new google.visualization.ColumnChart(document.getElementById('chart_div_<?=$key?>'));

        chart.draw(data, options);

      }

<?php  endforeach; ?>

*/ ?>

    </script>



	<!--Div that will hold the chart-->

	<div id="chart_div"></div>

<?php  /*

<?php  foreach ($resultsData as $key=>$system): ?>

	<h2><?=$system['name']?></h2>

	<!--Div that will hold the chart-->

    <div id="chart_div_<?=$key?>"></div>

<?php  endforeach; ?>

    

*/ ?>



	<table class="action_table" style="border: 1px solid black;">

		<tr>

<?php  foreach ($resultsData as $system): ?>

    		<td colspan="3" style="border: 2px solid black; border-bottom: 1px solid black; text-align: center;">

    			<?=$system["name"]?>

    		</td>

<?php  endforeach; ?>

    	</tr>

    	<tr class="table_header_row">

<?php  foreach ($resultsData as $system): ?>

    		<td class="table_header" style="border: 1px solid black; border-left: 2px solid black;">Result</td>

    		<td class="table_header" style="border: 1px solid black;">Number</td>

    		<td class="table_header" style="border: 1px solid black; border-right: 2px solid black; text-align: right;">%</td>

<?php  endforeach; ?>

    	</tr>

<?php  foreach ($dbmmResults as $res): ?>

    	<tr>

<?php  foreach ($resultsData as $system): ?>

<?php  if (array_key_exists($res, $system['scores'])): ?>

    		<td class="table_cell" style="border: 1px solid black; border-left: 2px solid black; text-align: center;">

    			<?=$res ?>

    		</td>

    		<td class="table_cell" style="border: 1px solid black; text-align: right;">

    			<?=$system["scores"][$res]["number"] ?>

    		</td>

    		<td class="table_cell" style="border: 1px solid black; border-right: 2px solid black; text-align: right;">

    			<?=$system["scores"][$res]["percent"] ?>

    		</td>

<?php  else: ?>

    		<td class="table_cell" style="border: 1px solid black; border-left: 2px solid black; text-align: center;">

    			<?=$res ?>

    		</td>

    		<td class="table_cell" style="border: 1px solid black; text-align: center;"> - </td>

    		<td class="table_cell" style="border: 1px solid black; border-right: 2px solid black; text-align: center;"> - </td>

<?php  endif; ?>

<?php  endforeach; ?>

    	</tr>

<?php  endforeach; ?>

    	<tr>

<?php  foreach ($resultsData as $system): ?>

    		<td class="table_cell" style="border: 1px solid black; border-left: 2px solid black; border-bottom: 2px solid black;"></td>

    		<td class="table_cell" style="border: 1px solid black; border-bottom: 2px solid black;">

    			<?=$system["total"] ?>

    		</td>

    		<td class="table_cell" style="border: 1px solid black; border-right: 2px solid black; border-bottom: 2px solid black; text-align: right;">

    			100.00

    		</td>

<?php  endforeach; ?>

    	</tr>

	</table>

    <p>Matches that were not scored from 25 points are excluded.</p>

</div>

