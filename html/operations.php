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
	<!-- Real time updates on dashboard -->
	<meta http-equiv="refresh" content="60">
        <!-- A pointer to CSS that makes it pretty. -->
        <link rel="stylesheet" href="./css/oedashboard.css">


</head>
<body>
<div style="display: flex; align-items: center;">
    <img src="pics/TransCableLogo.png" style="height: 60px; margin-right: 10px;">
    <h2 style="
        flex: 1;
        text-align: center;
        font-family: 'Poppins', sans-serif;
        font-size: 2.0em;
        background: linear-gradient(to right, #0077ff, #00c3ff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.4);
        letter-spacing: 1px;
        margin: 0;
    ">
        TCI Operations
    </h2>
</div>
    <table style="width: 100%;">
        <tr>
            <!-- Left Column: Data Table -->
            <td style="vertical-align: top;">
                <table style="
		    width: 8%;
                    border-collapse: collapse;
                    border: 1px solid #ccc;
                    box-shadow:
                    -8px 0 10px -5px rgba(0, 123, 255, 0.6),  /* Left glow */
                    0 8px 10px -5px rgba(0, 123, 255, 0.6);   /* Bottom glow */">
		    <tbody>
                    </tbody>
                    <th>
                        <tr>
                            <th>
                    <?php echo displayClock();?>
                    <a href="http://tci-bt-linux01/oee/oee.php" style="text-decoration: none; color: #36A2EB;">Main</a>
                    <br><hr color=lightblue>
                    <a href="http://tci-bt-linux01/oee/quantByEmployee.php" style="text-decoration: none; color: #36A2EB;">Quantity</a>
                    <br><hr color=lightblue>
                    <a href="http://tci-bt-linux01/oee/operations.php" style="text-decoration: none; color: #36A2EB;">Operations</a>
                    <hr color=lightblue>
							</th>
                        </tr>
                    </th>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
