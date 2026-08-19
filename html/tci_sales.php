<?php
// Example: Fetch current sales total dynamically
$currentSales = 900000; // Replace with query from your DB
?>

<!DOCTYPE html>
<html>
  <head>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {packages:['gauge']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Label', 'Value'],
          ['Sales', <?php echo $currentSales; ?>]
        ]);

        var options = {
          width: 400, height: 200,
          redFrom: 0, redTo: 200000,           // Danger zone
          yellowFrom:200000, yellowTo:600000,  // Medium zone
          greenFrom:600000, greenTo:1000000,   // Success zone
          minorTicks: 5,
          max: 1000000
        };

        var chart = new google.visualization.Gauge(
          document.getElementById('gauge_div')
        );
        chart.draw(data, options);
      }
    </script>
  </head>
  <body>
    <div id="gauge_div" style="width: 400px; height: 200px;"></div>
  </body>
</html>
