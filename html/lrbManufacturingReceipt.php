<?php
# PHP script:
# Paul Ohashi
# Trans Cable International
# Started: July 2025
#
# This script is one part of an overall full-stack package of technologies including:
#
#ini_set('display_errors', 1);
#ini_set('display_startup_errors', 1);
#error_reporting(E_ALL);

require_once './functions/oeeFunctions.php';
#require_once './functions/oeeTestFunctions.php';
?>
<html>
<head>
    <title>OE</title>
	<!-- Load the chart.js library -->
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<!-- Real time updates on dashboard -->
	<meta http-equiv="refresh" content="60">
	<!-- A pointer to CSS that makes it pretty. -->
	<link rel="stylesheet" href="./css/oedashboard.css">

<style>
/* Remove scrollbars */
html, body {
    margin: 5;
    padding: 0;
    overflow-x:     /* hides or shows X scrollbars with 'hidden' or blank */
    overflow-y:     /* hides or shows Y scrollbars with 'hidden' or blank */
    height: 100%;
    width: 100%;
    background-color: #ffffff
}

table {
    max-width: 100%;
    max-height: 100%;
    border-collapse: collapse;

    /* POP Enhancements */
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 3px 10px rgba(0,0,0,0.15);
    font-family: 'Poppins', sans-serif;
}

body {
    zoom: 45%;
}

.header-bar {
    display: grid;
    grid-template-columns: auto 1fr auto;
    align-items: center;
    width: 100%;
}

.header-left {
    display: flex;
    align-items: center;
    gap: 15px;
}

.header-logo {
    height: 60px;
}

.header-left-text {
    font-family: 'Poppins', sans-serif;
    font-size: clamp(18px, 2vw, 24px);
    font-weight: bold;
    background: linear-gradient(to right, #0077ff, #00c3ff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.4);
    white-space: nowrap;
    transform: translateX(10px);
}

.header-title {
    text-align: center;
    font-family: 'Poppins', sans-serif;
    font-size: 2em;
    background: linear-gradient(to right, #0077ff, #00c3ff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.4);
    letter-spacing: 1px;
    transform: translateX(50px);
    margin: 0;
}

.header-right-text {
    font-family: 'Poppins', sans-serif;
    font-size: clamp(18px, 2vw, 24px);
    background: linear-gradient(to right, #0077ff, #00c3ff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.4);
    text-align: center;
    font-weight: bold;
    white-space: wrap;
    transform: translateX(-40px);
}

.mission-statement-text {
    font-family: 'Poppins', sans-serif;
    font-size: clamp(18px, 2vw, 24px);
    font-weight: bold;
    background: linear-gradient(to right, #0077ff, #00c3ff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.4);
    white-space: wrap;
    transform: translateX(0px);
}

</style>

</head>
<body>
    <?php
    if (isset($_GET['when'])) {
        $when = htmlspecialchars($_GET['when']); // Sanitize input
    } else {
        echo "No timeframe specified.";
    }
    ?>

    <h2 style="
    text-align: center;
    font-family: 'Poppins', sans-serif;
    font-size: 2.0em;
    background: linear-gradient(to right, #0077ff, #00c3ff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.4);
    letter-spacing: 1px;">
        <?php
            $currentMonth = date('F');
            $conn = connectRubiconTci();
            list($rows, $numberOfLRBs) = lrbManufacturingReceipt($conn, $when);
            if ($when === 'thisMonth') {
                $when = date('F');
            }
            echo "$numberOfLRBs Closed LRBs ($when)";
        ?>
    </h2>
    <table style="width: 50%; border: 0px solid #ccc;">
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
                            <th style="width: 100%; border: 1px solid #ccc;">
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
                <table style="width: 100%; border: 1px solid #ccc;">
                    <tr>
                        <td>
                            <div style="display: flex; width: 100%;">
                                <div style="width: 100%;">
                                    <table style="width: 100%; border: 0px solid #ccc;">
                                        <!-- Left content -->
                                        <?php
                                            echo $rows;
                                        ?>
                                    </table>
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
