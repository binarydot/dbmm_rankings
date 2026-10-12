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
<? if(isset($matchResults['rounds'])): ?>
        var data = new google.visualization.arrayToDataTable([
            ['Player'
<?   foreach($matchResults['rounds'] as $roundNumber => $round): ?>
             ,'Round <?=$roundNumber?> Score'
<?   endforeach; ?>
			]
<?   foreach($competitionResults['results'] as $result):?>
        	,['<?=$result['competitorName']?>'
<?     foreach($matchResults['rounds'] as $roundNumber => $round): ?>
<?       $playerFound = false; ?>
<?       foreach($round as $match): ?>
<?         if($match['winners'][0]['ID'] == $result['competitorID'] ||
			(isset($match['winners'][1]['ID']) &&
			$match['winners'][1]['ID'] == $result['competitorID'])): ?>
          	, <?=$match['winnerScore']?>
<?           $playerFound = true; ?>
<?         endif; ?>
<?         if($match['losers'][0]['ID'] == $result['competitorID'] ||
			(isset($match['losers'][1]['ID']) && 
			$match['losers'][1]['ID'] == $result['competitorID'])): ?>
          	, <?=$match['loserScore']?>
<?           $playerFound = true; ?>
<?         endif; ?>
<?       endforeach; ?>
<?       if ($playerFound == false): ?>
            , 0
<?       endif; ?>
<?     endforeach; ?>
			]
<?   endforeach; ?>
        ]);
<? else: ?>
        var data = new google.visualization.arrayToDataTable([
            ['Player','Score']
<?   foreach($competitionResults['results'] as $result):?>
        	,['<?=$result['competitorName']?>', <?=$result['score']?>]
<?   endforeach; ?>
        ]);
<? endif; ?>
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