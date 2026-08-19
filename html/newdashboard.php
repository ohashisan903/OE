<?php
# PHP script:
# Paul Ohashi
# Trans Cable International
# Started: July 2025
#
# This script is one part of an overall full-stack package of technologies including:
#
# Need some error reporting...yes, of course, because this tangled web of tech is madness...
#ini_set('display_errors', 1);
#ini_set('display_startup_errors', 1);
#error_reporting(E_ALL);

require_once 'oeeFunctions.php';
?>
<html>
<head>
    <title>TOEE</title>
	<!-- Load the chart.js library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<!-- A pointer to CSS that makes it pretty. -->
	<link rel="stylesheet" href="newstyle.css">
	<!-- Real time updates on dashboard -->
	<meta http-equiv="refresh" content="60">
</head>
<body>
    <h2 style="
    text-align: center;
    font-family: 'Poppins', sans-serif;
    font-size: 2.0em;
    background: linear-gradient(to right, #0077ff, #00c3ff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.4);
    letter-spacing: 1px;">
        Template
    </h2>
    <table style="width: 100%; border: 1px solid #000;">
        <tr>
            <!-- Left Column: Data Table -->
            <td style="width: 1%; vertical-align: top;">
                <!-- Navigation Links (Main, Quantity, Operations)-->
                <table style="
                    border-collapse: collapse;
                    border: 1px solid #ccc;
                    box-shadow:
                    -8px 0 10px -5px rgba(0, 123, 255, 0.6),  /* Left glow */
                    0 8px 10px -5px rgba(0, 123, 255, 0.6);   /* Bottom glow */">
                    <thead>
                        <tr>
                            <div class="card">
  <div class="card-header">Shift Aggregate</div>
  <div class="card-body">
    <div class="kpi">
      <span class="kpi-label"><a href="oeeoperatorefficiency.php">1st Shift</a></span>
      <span class="kpi-value">
        <?php
          $conn = connectRubiconTci();
          echo firstShiftTotalManufacturingReceiptQuantity($conn);
        ?>
      </span>
    </div>
    <div class="kpi">
      <span class="kpi-label"><a href="oeeoperatorefficiency.php">2nd Shift</a></span>
      <span class="kpi-value">
        <?php
          $conn = connectRubiconTci();
          echo secondShiftTotalManufacturingReceiptQuantity($conn);
        ?>
      </span>
    </div>
    <div class="kpi">
      <span class="kpi-label"><a href="oeeoperatorefficiency.php">3rd Shift</a></span>
      <span class="kpi-value">
        <?php
          $conn = connectRubiconTci();
          echo thirdShiftTotalManufacturingReceiptQuantity($conn);
        ?>
      </span>
    </div>
  </div>
</div>

                    </thead>
                </table>
            </td>
            <td>
                <table style="width: 100%; border: 1px solid #000;">
                    <tr>
                        <td>
                            <div class="card">
  <div class="card-header">Shift Aggregate</div>
  <div class="card-body">
    <div class="kpi">
      <span class="kpi-label"><a href="oeeoperatorefficiency.php">1st Shift</a></span>
      <span class="kpi-value">
        <?php
          $conn = connectRubiconTci();
          echo firstShiftTotalManufacturingReceiptQuantity($conn);
        ?>
      </span>
    </div>
    <div class="kpi">
      <span class="kpi-label"><a href="oeeoperatorefficiency.php">2nd Shift</a></span>
      <span class="kpi-value">
        <?php
          $conn = connectRubiconTci();
          echo secondShiftTotalManufacturingReceiptQuantity($conn);
        ?>
      </span>
    </div>
    <div class="kpi">
      <span class="kpi-label"><a href="oeeoperatorefficiency.php">3rd Shift</a></span>
      <span class="kpi-value">
        <?php
          $conn = connectRubiconTci();
          echo thirdShiftTotalManufacturingReceiptQuantity($conn);
        ?>
      </span>
    </div>
  </div>
</div>

                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>