<?php
# PHP script:
# Paul Ohashi
# Trans Cable International
# Started: July 2025
#
# Scrap reporting
#
# Need some error reporting...yes, of course, because this tangled web of tech is madness...
#ini_set('display_errors', 1);
#ini_set('display_startup_errors', 1);
#error_reporting(E_ALL);

require_once './functions/oeeFunctions.php';
require_once './functions/scrapFunction.php';

?>
<html>
<head>
    <title>Scrap</title>
	<!-- Load the chart.js library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<!-- A pointer to CSS that makes it pretty. -->
	<link rel="stylesheet" href="./css/scrap.css">
	<!-- Real time updates on dashboard -->
	<meta http-equiv="refresh" content="60">


<style>
	.scrap-shift-section {
    		margin-bottom: 35px;
	}

	.scrap-table-title {
    		margin-bottom: 5px;
	}

	.shift-time {
    		margin-bottom: 15px;
    		font-size: 14px;
	}

		.scrap-summary {
    		display: flex;
    		gap: 15px;
    		margin-bottom: 15px;
    		flex-wrap: wrap;
	}

	.summary-item {
    		padding: 10px 15px;
    		border: 1px solid #ccc;
    		border-radius: 5px;
    		min-width: 130px;
	}

	.summary-label {
    		display: block;
    		font-size: 12px;
    		font-weight: bold;
	}

	.summary-value {
    		display: block;
    		font-size: 18px;
    		margin-top: 4px;
	}

	.scrap-table {
    		width: 100%;
    		border-collapse: collapse;
	}

	.scrap-table th,
	.scrap-table td {
    		padding: 8px 10px;
    		text-align: left;
    		border-bottom: 1px solid #ddd;
	}

	.scrap-table th {
    		font-weight: bold;
	}

	.no-data {
    		text-align: center;
    		padding: 20px;
	}


	.scrap-24hour-summary {
    		margin-bottom: 35px;
    		padding: 15px 20px;
    		border: 2px solid #ccc;
    		border-radius: 6px;
	}

	.scrap-24hour-title {
    		margin-top: 0;
    		margin-bottom: 15px;
	}

	.scrap-24hour-summary-items {
    		display: flex;
    		gap: 15px;
    		flex-wrap: wrap;
	}

	.grand-total {
    		min-width: 160px;
    		padding: 12px 18px;
	}

	.scrap-table tbody tr:nth-child(odd) {
    		background-color: #e6f4fa;
	}

	.scrap-table tbody tr:nth-child(even) {
    		background-color: transparent;
	}


</style>
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
        24 Hour Scrap
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
                            <th style="width: 100%; border: 1px solid #fff;">
                    <?php echo displayClock();?>
                    <a href="./oee.php" style="text-decoration: none; color: #36A2EB;">Main</a>
                    <br><hr color=lightblue>
                    <!--a href="./quantByEmployee.php" style="text-decoration: none; color: #36A2EB;">Quantity</a>
                    <br><hr color=lightblue>
                    <a href="./operations.php" style="text-decoration: none; color: #36A2EB;">Operations</a>
                    <hr color=lightblue -->
			    </th>
                        </tr>
                    </thead>
                </table>
            </td>
            <td>
<?php
$conn = connectRubiconTci();

echo "<table border='1'>";
#echo "<tr><th>Scrap Lbs</th><th>Scrap Qty</th><th>Scrap Ext</th><th>Month-to-Date</th></tr>";
#echo getMonthToDateScrapTotals($conn);

echo getScrapTables24Hours($conn);

echo "</table>";

?>
            </td>
        </tr>
    </table>
</body>
</html>
