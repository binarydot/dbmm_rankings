    <!--Load the AJAX API-->
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">

      // Load the Visualization API and the piechart package.
      google.load('visualization', '1.0', {'packages':['corechart']});

<?php if($submarine['resultsAvailable']): ?>
      // Set a callback to run when the Google Visualization API is loaded.
      google.setOnLoadCallback(drawSubmarineChart);
<?php endif; ?>

      // Callback that creates and populates a data table,
      // instantiates the pie chart, passes in the data and
      // draws it.
      function drawSubmarineChart() {

        // Create the data table.
<? if(isset($submarine)): ?>
        var data = new google.visualization.arrayToDataTable(<?=$submarine['positions']?>);
<? endif; ?>
        // Set chart options
        var options = {'title':'Submarine Chart',
        		vAxis:{direction:-1,minValue:0},  
        		hAxis:{},
        		lineWidth:5,
                       'width':800,
                       'height':<?=count($submarine['results'])*60; ?>};

        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.LineChart(document.getElementById('submarine_chart_div'));
        chart.draw(data, options);
      }
    </script>
    <!--Div that will hold the chart-->
    <div id="submarine_chart_div"></div>