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

        var data1 = new google.visualization.arrayToDataTable([

            ['Match','Rating','Min','Max']

<?php    $a = 1; ?>

<?php    foreach(array_reverse($ratings['record']) as $key => $rating):?>

        	,[<?=$a?>

        	,<?=$rating['rating']?>

          	,<?=$rating['rating'] - $rating['reliability']?>

          	,<?=$rating['rating'] + $rating['reliability']?>

			]

<?php      $a++; ?>

<?php    endforeach; ?>

        ]);



        

        // Create the data table for the second chart.

        var data2 = new google.visualization.DataTable();

<?php    $a = 1; ?>

        data2.addColumn('date', 'Date');

        data2.addColumn('number', 'Rating');

        data2.addColumn('number', 'Min');

        data2.addColumn('number', 'Max');

        data2.addRows([

<?php    foreach(array_reverse($ratings['record']) as $key => $rating):?>

<?php      if($a > 1): ?>

			,

<?php      endif; ?>

        	[new Date("<?=substr($key,0,-1)?>")

        	,<?=$rating['rating']?>

          	,<?=$rating['rating'] - $rating['reliability']?>

          	,<?=$rating['rating'] + $rating['reliability']?>

			],

<?php    endforeach; ?>

        ]);



                

        // Set chart options

        var options1 = {title:'Glicko Rating by Match',

        		       isStacked:true,

                       width:800,

                       height:600

                       };



        // Set chart options

        var options2 = {title:'Glikco Rating by Date',

        		       isStacked:true,

                       width:800,

                       height:600

                       };



        // Instantiate and draw our chart, passing in some options.

        var chart1 = new google.visualization.LineChart(document.getElementById('chart_div1'));

        chart1.draw(data1, options1);

        // Instantiate and draw our chart, passing in some options.

        var chart2 = new google.visualization.LineChart(document.getElementById('chart_div2'));

        chart2.draw(data2, options2);



 <?php    foreach(array_reverse($ratings['record']) as $key => $rating):?>

    	new Date(<?=substr($key,0,-1)?>)

<?php    endforeach; ?>

        

      }



    </script>

    <!--Div that will hold the chart-->

    <div id="chart_div1"></div>

    <div id="chart_div2"></div>

   

    