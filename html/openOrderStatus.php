<?php
# PHP script: oee.php
# Paul Ohashi
# Trans Cable International
# Started: July 2025
#
# The openOrderStatus.php file is a generic framework for the status of open orders.
#
# This page is looking up order-specific status data from Rubicon's database view,tci.v_sos.
# Status options: Canceled, Completed, Partially Shipped, Open, Picking, and On Hold
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
    <h2 style="text-align: center;">TCI Operational Efficiency</h2>
    <?php echo displayClock();?>
    <!--h2>Total Quantity by Operator</h2-->
    <table style="
                    border-collapse: collapse;
                    border: 1px solid #ccc;
                    width: 100%;
                    box-shadow:
                    -8px 0 10px -5px rgba(0, 123, 255, 0.6),  /* Left glow */
                    0 8px 10px -5px rgba(0, 123, 255, 0.6);   /* Bottom glow */">
                    <thead>
                        <tr>
                            <th style="width: 50px; border: 1px solid #fff;">
                    <?php echo displayClock();?>
                    <a href="http://tci-bt-linux01/oee/oee.php" style="text-decoration: none; color: #36A2EB;">Main</a>
                    <br><hr color=lightblue>
                    <a href="http://tci-bt-linux01/oee/quantByEmployee.php" style="text-decoration: none; color: #36A2EB;">Quantity</a>
                    <br><hr color=lightblue>
                    <a href="http://tci-bt-linux01/oee/operations.php" style="text-decoration: none; color: #36A2EB;">Operations</a>
                    <hr color=lightblue>
							</th>
                        <td>
                            <?php
                                $conn = connectRubiconTci();
                                if (isset($_GET['report'])) {
                                    $report = htmlspecialchars($_GET['report']); // Sanitize input
                                    switch ($report) {
                                        case "pickingOpenOrders":
                                            $reportType = "Picking";
                                            break;
                                        case "partiallyShipped":
                                            $reportType = "Partially Shipped";
                                            break;
                                        case "onHoldOperOrders":
                                            $reportType = "On Hold";
                                            break;
                                        case "openOpenOrders":
                                            $reportType = "Open";
                                            break;
                                        case "canceledOrders":
                                            $reportType = "Canceled";
                                            break;
                                        case "completedOrders":
                                            $reportType = "Completed";
                                            break;
                                        default:
                                            echo "Unknown report type.";
                                            break;
                                    }
                                } else {
                                    echo "No report specified.";
                                }

                                list($table_rows, $rowCount) = getSalesOrdersFromV_SOS($conn, $reportType);

                                echo "<h3 style=\"text-align: center;\">$rowCount $reportType Orders</h3>";

                                echo "<div style=\"margin: 0 auto; width: 80%; text-align: center;\">
                                    <table width=100% border=1>
                                        <tr style=\"background-color: lightgrey\";><td>Order #</td><td>Customer</td><td>Start Date</td><td>Status</td><td>Account Executive</td></tr>
                                        $table_rows
                                    </table>
                                    </div>
                                ";
                                echo poweredBy();
                                ?>
                        </td>
                        </tr>
                    </thead>
                </table>



</body>
</html>
