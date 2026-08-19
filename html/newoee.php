<?php
require_once 'oeeFunctions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>TCI Operational Efficiency</title>
<link rel="stylesheet" href="newstyle.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<meta http-equiv="refresh" content="300">

<style>
  html, body {
    margin: 0;
    padding: 0;
    height: 100%;
    font-family: 'Poppins', sans-serif;
    background: #f5f7fa;
  }

  .dashboard {
    display: flex;
    height: calc(100vh - 40px); /* leave room for ticker */
  }

  .sidebar {
    flex: 0 0 220px;
    background: #f8f8f8;
    border-right: 1px solid #ccc;
    padding: 20px;
    overflow-y: auto;
  }

  .sidebar a {
    display: block;
    margin: 8px 0;
    text-decoration: none;
    color: #0077ff;
    font-weight: bold;
  }

  .main {
    flex: 1;
    display: flex;
    flex-direction: column;
    padding: 20px;
    overflow-y: auto;
  }

  .row {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
    flex-wrap: wrap;
  }

  .card {
    flex: 1;
    background: #fff;
    border-radius: 12px;
    padding: 15px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    min-width: 250px;
  }

  .card h3 {
    margin-top: 0;
    font-size: 1.2em;
    border-bottom: 1px solid #ddd;
    padding-bottom: 5px;
  }

  /* Ticker */
  .ticker-wrap {
    position: fixed;
    bottom: 0;
    width: 100%;
    background: #222;
    color: #fff;
    overflow: hidden;
    height: 40px;
    display: flex;
    align-items: center;
    font-size: 16px;
    font-weight: bold;
    border-top: 2px solid #444;
    z-index: 1000;
  }
  .ticker {
    display: inline-block;
    white-space: nowrap;
    padding-left: 100%;
    animation: ticker 100s linear infinite;
  }
  @keyframes ticker {
    0%   { transform: translateX(0); }
    100% { transform: translateX(-100%); }
  }

  /* Responsive */
  @media (max-width: 768px) {
    .dashboard {
      flex-direction: column;
    }
    .sidebar {
      width: 100%;
      border-right: none;
      border-bottom: 1px solid #ccc;
    }
    .row {
      flex-direction: column;
    }
  }
</style>
</head>
<body>

<div style="display: flex; align-items: center; padding: 10px;">
  <img src="pics/TransCableLogo.png" style="height: 60px; margin-right: 10px;">
  <h2 style="
    flex: 1;
    text-align: center;
    font-size: 2em;
    background: linear-gradient(to right, #0077ff, #00c3ff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.4);
    margin: 0;
  ">
    TCI Operational Efficiency
  </h2>
</div>

<div class="dashboard">
  <!-- Sidebar -->
  <div class="sidebar">
    <div><?php echo displayClock(); ?></div>
    <a href="oee.php">Main</a>
    <a href="quantByEmployee.php">Quantity</a>
    <a href="operations.php">Operations</a>

    <h3>Shift Aggregate</h3>
    <p>1st Shift: [<?php /* echo firstShiftTotalManufacturingReceiptQuantity($conn); */ ?>]</p>
    <p>2nd Shift: [<?php /* echo secondShiftTotalManufacturingReceiptQuantity($conn); */ ?>]</p>
    <p>3rd Shift: [<?php /* echo thirdShiftTotalManufacturingReceiptQuantity($conn); */ ?>]</p>
  </div>

  <!-- Main Content -->
  <div class="main">

    <div class="row">
      <div class="card">
        <h3>Top Operators</h3>
        <p>[<?php /* displayQuarterlyTransactionTotals($conn); */ ?>]</p>
      </div>
      <div class="card">
        <h3>LRB Manufacturing Receipt</h3>
        <p>Today: [<?php /* total_output_today($conn); */ ?>]</p>
        <p>7 Day: [<?php /* total_output_seven_days($conn); */ ?>]</p>
        <p>30 Day: [<?php /* total_output_thirty_days($conn); */ ?>]</p>
        <p>This Month: [<?php /* whatMonthIsIt($conn); */ ?>]</p>
      </div>
    </div>

    <div class="row">
      <div class="card">
        <h3>Open Orders Status</h3>
        <canvas id="statusChart" style="max-width:400px; height:250px;"></canvas>
      </div>
      <div class="card">
        <h3>Work Center Output</h3>
        <p>[<?php /* workCenterOutput($conn); */ ?>]</p>
      </div>
    </div>

    <div class="row">
      <div class="card">
        <h3>Indirect Labor Work Centers</h3>
        <p>[<?php /* indirectLaborWorkCentersDaily($conn); */ ?>]</p>
      </div>
    </div>
  </div>
</div>

<!-- Ticker -->
<div class="ticker-wrap">
  <div class="ticker" id="ticker">Loading ticker...</div>
</div>

<script>
  // Placeholder pie chart data
  const ctx = document.getElementById('statusChart').getContext('2d');
  const chart = new Chart(ctx, {
    type: 'pie',
    data: {
      labels: ['Open', 'Picking', 'On Hold', 'Completed'],
      datasets: [{
        data: [12, 5, 3, 20],
        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#8BC34A']
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { position: 'bottom' },
        title: { display: true, text: 'Status of Open Orders' },
        datalabels: {
          color: '#000',
          font: { weight: 'bold', size: 14 },
          formatter: function(value) { return value; }
        }
      }
    },
    plugins: [ChartDataLabels]
  });

  // Ticker refresh
  function refreshTicker() {
    fetch('ticker.php')
      .then(response => response.text())
      .then(data => {
        document.getElementById('ticker').innerHTML = data;
      })
      .catch(err => console.error('Error loading ticker:', err));
  }
  refreshTicker();
  setInterval(refreshTicker, 30000);
</script>

</body>
</html>
