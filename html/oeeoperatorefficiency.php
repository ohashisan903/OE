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

require_once './functions/oeeFunctions.php';
require_once './functions/oeeTestFunctions.php';
?>
<html>
<head>
    <title>TOEE</title>
	<!-- Load the chart.js library -->
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<!-- Real time updates on dashboard -->
	<meta http-equiv="refresh" content="120">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- pointer to CSS  -->
        <link rel="stylesheet" href="./css/oedashboard.css">
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
        TCI Operator Efficiency
    </h2>
    <table style="width: 100%; border: 0px solid #000;">
        <tr>
            <!-- Left Column: Data Table -->
           <td style="width: 1%; vertical-align: top;">
                <!-- Navigation Links (Main, Quantity, Operations)-->
                <table style="
                    border-collapse: collapse;
                    border: 0px solid #ccc;
                    box-shadow:
                    -8px 0 10px -5px rgba(0, 123, 255, 0.6),  /* Left glow */
                    0 8px 10px -5px rgba(0, 123, 255, 0.6);   /* Bottom glow */">
                    <thead>
                        <tr>
                            <th style="width: 100%; border: 1px solid #fff;">
                    <?php echo displayClock();?>
                    <a href="./oee.php" style="text-decoration: none; color: #36A2EB;">Main</a>
                    <br><hr color=lightblue>
                    <a href="./quantByEmployee.php" style="text-decoration: none; color: #36A2EB;">Quantity</a>
                    <br><hr color=lightblue>
                    <a href="./operations.php" style="text-decoration: none; color: #36A2EB;">Operations</a>
                    <hr color=lightblue>
							</th>
                        </tr>
                    </thead>
                </table>
            </td>
            <td>
                <table style="width: 33%; border: 0px solid #000; text-align: center;">
                    <tr>
                        <td>
                            <div style="display: flex; width: 100%;">
                                <div style="display: flex; justify-content: space-between; text-align: center;">
    <!-- 1st Shift -->
    <div style="flex: 1; padding: 10px;">
        <div style="
            font-family: 'Poppins', sans-serif;
            font-size: 1.0em;
            background: linear-gradient(to right, #0077ff, #00c3ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.4);
            letter-spacing: 1px;">
            1st Shift
        </div>
        <table style="width: 100%; border: 0; margin-top: 5px;">
            <tr style="background: lightgrey; font-weight: bold;">
                <td>Operator</td><td>Quantity</td>
            </tr>
            <?php
                $conn = connectRubiconTci();
                echo operatorEfficiencyReportFirst($conn);
            ?>
        </table>
    </div>

    <!-- 2nd Shift -->
    <div style="flex: 1; padding: 10px;">
        <div style="
            font-family: 'Poppins', sans-serif;
            font-size: 1.0em;
            background: linear-gradient(to right, #0077ff, #00c3ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.4);
            letter-spacing: 1px;">
            2nd Shift
        </div>
        <table style="width: 100%; border: 0; margin-top: 5px;">
            <tr style="background: lightgrey; font-weight: bold;">
                <td>Operator</td><td>Quantity</td>
            </tr>
            <?php
                $conn = connectRubiconTci();
                echo operatorEfficiencyReportSecond($conn);
            ?>
        </table>
    </div>

    <!-- 3rd Shift -->
    <div style="flex: 1; padding: 10px;">
        <div style="
            font-family: 'Poppins', sans-serif;
            font-size: 1.0em;
            background: linear-gradient(to right, #0077ff, #00c3ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.4);
            letter-spacing: 1px;">
            3rd Shift
        </div>
        <table style="width: 100%; border: 0; margin-top: 5px;">
            <tr style="background: lightgrey; font-weight: bold;">
                <td>Operator</td><td>Quantity</td>
            </tr>
            <?php
                $conn = connectRubiconTci();
                echo operatorEfficiencyReportThird($conn);
            ?>
        </table>
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
