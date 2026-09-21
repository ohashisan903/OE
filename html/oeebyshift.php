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
?>
<html>
<head>
    <title>TOEE</title>
	<!-- Load the chart.js library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<!-- A pointer to CSS that makes it pretty. -->
	<link rel="stylesheet" href="./css/style.css">
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
        TCI Operational Efficiency
    </h2>
    <?php echo displayClock();?>
    <table style="width: 100%;">
        <tr>
            <!-- Left Column: Data Table -->
            <td style="width: 10%; vertical-align: top;">
                <table style="border: 1px;">
				     <tbody>
                    </tbody>
                    <thead>
                        <tr>
                            <th>
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
        </tr>
    </table>
</body>
</html>
