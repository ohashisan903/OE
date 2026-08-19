<?php
require_once './functions/oeeFunctions.php';
require_once './functions/scrapFunction.php';

$conn = connectRubiconTci();
$monthlyScrap = getMonthlyScrapTotals($conn);

// Make sure all values are numeric (Chart.js requires numbers)
$lbsJSON = json_encode(array_map('floatval', array_values($monthlyScrap["lbs"])));
$qtyJSON = json_encode(array_map('floatval', array_values($monthlyScrap["qty"])));
$extJSON = json_encode(array_map('floatval', array_values($monthlyScrap["ext"])));
?>

<html>
<head>
    <title>Scrap Line Graph</title>
    <link rel="stylesheet" href="newstyle.css">
    <meta http-equiv="refresh" content="60">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<h2 style="
    text-align: center;
    font-family: 'Poppins', sans-serif;
    font-size: 2em;
    background: linear-gradient(to right, #0077ff, #00c3ff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.4);
    letter-spacing: 1px;">
    Scrap Dashboard (Monthly)
</h2>

<canvas id="scrapLineChart" width="900" height="275"></canvas>

<script>
const ctx = document.getElementById('scrapLineChart').getContext('2d');

const scrapChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],
        datasets: [
            {
                label: 'Scrap Pounds',
                data: <?php echo $lbsJSON; ?>,
                borderColor: '#1e90ff',
                backgroundColor: 'rgba(30,144,255,0.1)',
                borderWidth: 2,
                yAxisID: 'lbsAxis'
            },
            {
                label: 'Scrap Footage',
                data: <?php echo $qtyJSON; ?>,
                borderColor: '#28a745',
                backgroundColor: 'rgba(40,167,69,0.1)',
                borderWidth: 2,
                yAxisID: 'qtyAxis'
            },
            {
                label: 'Scrap Cost',
                data: <?php echo $extJSON; ?>,
                borderColor: '#dc3545',
                backgroundColor: 'rgba(220,53,69,0.1)',
                borderWidth: 2,
                yAxisID: 'costAxis'
            }
        ]
    },
    options: {
        responsive: false,
        maintainAspectRatio: false,
        scales: {
            lbsAxis: {
                type: 'linear',
                position: 'left',
                title: { display: true, text: 'Pounds' }
            },
            qtyAxis: {
                type: 'linear',
                position: 'left',
                title: { display: true, text: 'Footage' },
                grid: { drawOnChartArea: false }
            },
            costAxis: {
                type: 'linear',
                position: 'right',
                title: { display: true, text: 'Cost ($)' },
                ticks: { callback: value => '$' + value.toFixed(2) }
            }
        }
    }
});
</script>
</body>
</html>
