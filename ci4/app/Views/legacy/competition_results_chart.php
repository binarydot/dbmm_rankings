    <!--Load the AJAX API-->
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">

      // Load the Visualization API and the piechart package.
      google.load('visualization', '1.0', {'packages':['corechart']});

<?php if($competitionResults['resultsAvailable']): ?>
      // Set a callback to run when the Google Visualization API is loaded.
      google.setOnLoadCallback(drawChart);
<?php endif; ?>

      // Callback that creates and populates a data table,
      // instantiates the pie chart, passes in the data and
      // draws it.
      function drawChart() {

        // Create the data table.
<?php  if(isset($matchResults['rounds'])): ?>
        var data = new google.visualization.arrayToDataTable([
            ['Player'
<?php    foreach($matchResults['rounds'] as $roundNumber => $round): ?>
             ,'Round <?=$roundNumber?> Score'
<?php    endforeach; ?>
			]
<?php    foreach($competitionResults['results'] as $result):?>
        	,['<?=$result['competitorName']?>'
<?php      foreach($matchResults['rounds'] as $roundNumber => $round): ?>
<?php        $playerFound = false; ?>
<?php        foreach($round as $match): ?>
<?php          if(in_array($result['competitorID'], array_column($match['winners'] ?? [], 'ID'))): ?>
          	, <?=$match['winnerScore']?>
<?php            $playerFound = true; ?>
<?php          endif; ?>
<?php          if(in_array($result['competitorID'], array_column($match['losers'] ?? [], 'ID'))): ?>
          	, <?=$match['loserScore']?>
<?php            $playerFound = true; ?>
<?php          endif; ?>
<?php        endforeach; ?>
<?php        if ($playerFound == false): ?>
            , 0
<?php        endif; ?>
<?php      endforeach; ?>
			]
<?php    endforeach; ?>
        ]);
<?php  else: ?>
        var data = new google.visualization.arrayToDataTable([
            ['Player','Score']
<?php    foreach($competitionResults['results'] as $result):?>
        	,['<?=$result['competitorName']?>', <?=$result['score']?>]
<?php    endforeach; ?>
        ]);
<?php  endif; ?>
        // Set chart options
        var options = {'title':'Results',
        		       'isStacked':true,
                       'width':800,
                       'height':<?=count($competitionResults['results'])*30; ?>};

        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.BarChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
    </script>
    <!--Div that will hold the chart-->
    <div id="chart_div"></div>