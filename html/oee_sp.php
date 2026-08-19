<?php
# PHP script: oee.php
# Paul Ohashi
# Trans Cable International
# Started: July 2025
#
# This script is one part of an overall full-stack package of technologies including:
#
# * MySQL database (oee) to store the data - This may be obsolete as I'm now pulling data directly
#	from rubicon.transcableusa.com - Maria DB.
# * Python script to unpack a zipped up file containing a bunch of Rubicon reports
#   and insert each report into its own database tables (only one table at the moment 'labor_efficiency').
# * Need to pull data from Rubicon then insert it into DB instead of extracting from xlsx reports.
# * IIS Web Site to display the data
#
# Need some error reporting...yes, of course, because this tangled web of code is madness...
#ini_set('display_errors', 1);
#ini_set('display_startup_errors', 1);
#error_reporting(E_ALL);

require_once 'oeeFunctions.php';
require_once './functions/scrapFunction.php';
#require_once 'oeeTestFunctions.php';
?>
<html>
<head>
    <!-- meta tag below - change dashboard view every X seconds. bounce from the OE dashboard to the sales dashboard -->
    <!--meta http-equiv="refresh" content="60; URL='https://tci-bt-linux01/dev-oee/salesdashboard.php'"-->
    <title>DevTOE</title>
	<!-- Load the chart.js library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<!-- A pointer to CSS that:
	 * Makes it pretty.
	 * Adjusts the margins
	 * Controls the scrollbars -->
	<!--link rel="stylesheet" href="newstyledev.css"-->
	<link rel="stylesheet" href="newstyle.css">
	<!-- Real time updates on dashboard -->
	<meta http-equiv="refresh" content="300">

	<style>
    /* Remove scrollbars */
    html, body {
        margin: 30;
        padding: 0;
        overflow-x:     /* hides or shows X scrollbars with 'hidden' or blank */
        overflow-y:     /* hides or shows Y scrollbars with 'hidden' or blank */
        height: 100%;
        width: 100%;
        background-color: #ffffff
    }

    /* Ensure tables shrink to fit inside viewport */
    table {
        max-width: 100%;
        max-height: 100%;
        border-collapse: collapse;
    }
    </style>
    <!-- style: controls the display size of the dashboard in a browser -->
<style>
  body {
    transform: scale(0.6);         /* Shrinks content to 80% (100% = 1.0) */
    transform-origin: top left;    /* Keeps it aligned at top-left */
    width: 125%;                   /* Compensates for shrinking to fill iframe width */
    /* height: 100%;		   /* Compensates for shrinking to fill iframe height (This setting does strange things to the ticker) */
    overflow: hidden;              /* Optional: remove scrollbars in iframe */
    overflow-x: auto;
    overflow-y: hidden;
  }
</style>

</head>
<body background=#ffffff>
    <!-- PO: Controls the border that surrounds the entire dashboard,
	     including the navigation menu -->
    <table style="width: 50%; max-width: 100%; border: 0px solid #000;">
	<tr>
	   <td colspan=4>
		<div style="display: flex; align-items: center;">
    		<img src="pics/TransCableLogo.png" style="height: 60px; margin-right: 10px;">
    		<h2 style="
        		flex: 1;
        		text-align: center;
        		align: center;
        		font-family: 'Poppins', sans-serif;
        		font-size: 2.0em;
        		background: linear-gradient(to right, #0077ff, #00c3ff);
        		-webkit-background-clip: text;
        		-webkit-text-fill-color: transparent;
        		text-shadow: 1px 1px 2px rgba(0,0,0,0.4);
        		letter-spacing: 1px;
        		margin: 0;
    		">
        	TCI Operational Efficiency
    		</h2>
	   </td>
	</tr>
        <tr> 
            <!-- Left Column: Navigation table -->
            <td style="width: 1%; vertical-align: top;">
                <!-- PO: Navigation Links (Main, Quantity, Operations)
		     Controls the border around the navigation table, top left of dashboard  -->
                <table style="
                    width: 50%;
                    border-collapse: collapse;
                    border: 0px solid #000;
                    box-shadow:
                    -8px 0 10px -5px rgba(0, 123, 255, 0.6),  /* Left glow */
                    0 8px 10px -5px rgba(0, 123, 255, 0.6);   /* Bottom glow */">
                    <thead>
                        <tr>
			    <!-- PO: controls the border of the links in the main
				 navigation menu table  -->
                            <th style="width: 100%; border: 0px solid #000;">
                    <?php echo displayClock();?>
                    <a href="http://tci-bt-linux01/oee/oee.php" style="text-decoration: none; color: #36A2EB;">Main</a>
                    <br><hr color=#ddecf0>
                    <a href="http://tci-bt-linux01/oee/quantByEmployee.php" style="text-decoration: none; color: #36A2EB;">Quantity</a>
                    <br><hr color=#ddecf0>
                    <a href="http://tci-bt-linux01/oee/operations.php" style="text-decoration: none; color: #36A2EB;">Operations</a>
                    <hr color=#ddecf0>
							</th>
                        </tr>
                        <tr>
			    <!-- PO: Controls a light blue border around the words
				 "Shift Aggregate" in the main navigation menu  -->
                            <td colspan=2 style="font-weight: bold; text-align: center; background-color: lightgrey; border: 0px solid #ddecf0;">
                                Shift Aggregate
                            </td>
                        </tr>
                        <tr>
			    <!-- PO: controls the border around the words "1st Shift"
				 on the right side, in the main navigation menu  -->
                            <th style="width='50%'; width: 100%; border: 0px solid #000;">
                                <strong><p>

                                <a href="http://tci-bt-linux01/oee/oeeoperatorefficiency.php" style="text-decoration: none; color: #000;">1st Shift</a>
                            </th>
			    <!-- PO: controls the border around the 1st Shift total in
				 the main navigation menu  -->
                            <th style="width='50%'; width: 100%; border: 0px solid #000;">
                                <?php
                                    $conn = connectRubiconTci();
                                    echo firstShiftTotalManufacturingReceiptQuantity($conn);
                                ?>
                            </th>
                        </tr>
                        <tr>
			    <!-- PO: controls the border around the text "2nd Shift" on
				 the right side, in the main navigation menu  -->
                            <th style="width='50%'; width: 100%; border: 0px solid #000;">
                                <strong><p>
                                <a href="http://tci-bt-linux01/oee/oeeoperatorefficiency.php" style="text-decoration: none; color: #000;">2nd Shift</a>
                            </th>
			    <!-- PO: controls the border around the 2nd Shift numbers (footage) in the
				 main navigation menu  -->
                            <th style="width='50%'; width: 100%; border: 0px solid #000;">
                                <?php
                                    $conn = connectRubiconTci();
                                    echo secondShiftTotalManufacturingReceiptQuantity($conn);
                                ?>
                            </th>
                            <tr>
			    <!-- PO: controls the border around the text "3rd Shift" -->
                            <th style="width='50%'; width: 100%; border: 0px solid #000;">
                                <strong><p>
                                <a href="http://tci-bt-linux01/oee/oeeoperatorefficiency.php" style="text-decoration: none; color: #000;">3rd Shift</a>
                            </th style="width='50%'; width: 100%;">
			    <!-- PO: controls the border around the 3rd Shift numbers (footage) in the
				 main navigation menu  -->
                            <th style="width='50%'; width: 100%; border: 0px solid #000;">
                                <?php
                                    $conn = connectRubiconTci();
                                    echo thirdShiftTotalManufacturingReceiptQuantity($conn);
                                ?>
                            </th>
                        </tr>
                        </tr>
                    </thead>
                </table>
            </td>

            <!-- PO: Table definition with tables for Operator table, LRB Manufacturing
		 Receipt table, Sales gauge,
                  Sales Order status, and Work Center down time -->
            <td style="width: 400px; align=left; text-align=left">

                <!-- PO: Table with Operator, LRB Manufacturing, and Work Center tables -->
                <table style="width: 350px; max-width: 50%; border: 0px solid #000;">
                    <tbody style="align: center;">
                        <strong></strong>
					</tbody>
                <!-- PO: controls the border around the Operator list with quarterly numbers -->
                <th style="width: 200px; border: 0px solid #000;">
                    <?php
			$conn = connectRubiconTci();
			displayQuarterlyTransactionTotals($conn);
		    ?>
				</th>
                <th style="text-align: center;">

		<!-- PO: Border for table containing LRB Manufacturing Receipt, Sales Gague, Sales pie chart, and Work Center PWOs  -->
                <table style="width: 100%; max-width: 100%; border: 0px solid #000;">

                    <tr>
                        <!--  Border for the table row with the text LRB Manufacturing Receipt -->
                        <td colspan=3 style="text-align: center; background-color: lightgrey; border: 0px solid #000">
                            <strong>LRB Manufacturing Receipt</strong>
                        </td>
                    </tr>

                    <tr>
                        <td colspan=2 style="background-color: #ddecf0;">
                            <strong><p>
                            <a href="lrbManufacturingReceipt.php?when=today" style="text-decoration: none; color: #000;">
                                Today
                            </a>
                            </strong> <!-- (<?php echo date("m/d/y"); ?>)</span></p> -->
                        </td>
                        <td style="background-color: #ddecf0;">
                            <?php
                                $conn = connectRubiconTci();
                                total_output_today($conn);
                            ?>
                        </td>
                    </tr>

                    <tr>
                        <td colspan=2>
                            <strong><p>
                            <a href="lrbManufacturingReceipt.php?when=7day" style="text-decoration: none; color: #000;">
                                7 day
                            </a></span></p>
                            </strong>
                        </td>
                        <td colspan=2>
                            <?php
                                $conn = connectRubiconTci();
                                total_output_seven_days($conn);
                            ?>
                        </td>
                    </tr>

                    <tr>
                        <td colspan=2 style="background-color: #ddecf0;">
                            <strong><p>
                            <a href="lrbManufacturingReceipt.php?when=30day" style="text-decoration: none; color: #000;">
                            30 day</a>
                            </strong></p>
                        </td colspan=2>
                        <td style="background-color: #ddecf0;">
                            <?php
                                $conn = connectRubiconTci();
                                total_output_thirty_days($conn);
                            ?>
                        </td>
                    </tr>

                    <tr>
                        <td colspan=2 style="background-color: ; font-weight: bold;">
                            <a href="lrbManufacturingReceipt.php?when=thisMonth" style="text-decoration: none; color: #000;">
                            <?php
                                $currentMonth = date('F');
                                echo "$currentMonth";
                            ?>
                            </a>
                            </strong>
			</td>
			<td>
                            <?php
                                $conn = connectRubiconTci();
                                total_output_this_month($conn);
                            ?>
                        </td>
		    <tr>
			<td colspan=3>
			<hr>
			</td>
                    </tr>
                </th>
                    <!-- Table row for the monthly sales goal gage and pie chart with
			 Open Sales Orders -->
                <tr>
		<td></td> <!-- Empty first column for gague alignment -->
                <td colspan=1>
<center>
			<!-- <hr --> <!-- PO: hr above the Sales gas gague -->
			<strong>
                        <?php
                            // Get the current month
                            $currentMonth = date('F');
                            // Get current day of the month
                            $dayOfMonth = date('j');  // 1–31
                            // Calculate week number (week 1 = days 1–7, week 2 = 8–14, etc.)
                            $weekOfMonth = ceil($dayOfMonth / 7);

                            echo "$currentMonth Sales Goal<br> (Week $weekOfMonth)";
                        ?>
                    	</strong>
                    <?php # Query Maria for the open sales orders Pie chart
                        $conn = connectRubiconTci();
                        $result = $conn->query("SELECT status, COUNT(*) as count FROM v_sos WHERE open = '1' GROUP BY status");

                        $labels = [];
                        $data = [];

                        while ($row = $result->fetch_assoc()) {
                            $labels[] = $row['status'];
                            $data[] = $row['count'];
                        }
                        $conn->close();
                    ?>
                    <!-- Load Chart.js for open sales orders pie chart -->
                    <script src="https://cdn.jsdelivr.net/npm/chart.js">
                    </script>
                    <!-- Data Labels Plugin -->
                    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
                        <a href="salesdashboard.php">
                            <div id="gauge_div" style="width: 200px; height: 200px; align: left;">
                            </div>
                        </a>
                    <hr>
			<!-- PO: controls the border around the "Status of Open Orders" pie chart -->
                        <div style="width: 250px; height: 250px; border: 0px solid #000; margin: 0 auto;">
                            <canvas id="statusChart" style="display: block; margin: auto;"></canvas>
                        </div>
                    <?php
                        // Monthly sales goal
                        $monthlyGoal = 1250000;

                        // Current sales value (example placeholder, replace with DB query)
                        $currentSalesThisMonth = 900000;

                        // Current month short name
                        $currentMonth = date("M");

			// Week of current month
			$currentWeekOfMonth = ceil(date('j') / 7);

			// Empty variable
			$emptyVariable; 

                    ?>
                        <style>
                            /* Show text inside the gauge. Comment either gauge to hide */
                            #gauge_div text {
                              fill: transparent !important;
                            } 
                            gauge_div text:first-child {
                              fill: black !important;         /* keep the month label visible */
                            }
                        </style>

                        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js">
                        </script>
                        <script type="text/javascript">
                          google.charts.load('current', {packages:['gauge']});
                          google.charts.setOnLoadCallback(drawChart);

                          function drawChart() {
                            var monthlyGoal   = <?php echo $monthlyGoal; ?>;
                            var currentSales  = <?php echo $currentSalesThisMonth; ?>;
                            var currentMonth  = "<?php echo $currentMonth, " (", $currentWeekOfMonth, ")"; ?>";

                            var data = google.visualization.arrayToDataTable([
                              ['Label', 'Value'],
                              [currentMonth, currentSales]
                            ]);

                            var redMaxPct    = 0.60;
                            var yellowMaxPct = 0.90;
                            var greenMaxPct  = 1.00;

                            var options = {
                              width: 400, height: 200,
                              redFrom:    0, redTo:    monthlyGoal * redMaxPct,
                              yellowFrom: monthlyGoal * redMaxPct, yellowTo: monthlyGoal * yellowMaxPct,
                              greenFrom:  monthlyGoal * yellowMaxPct, greenTo: monthlyGoal * greenMaxPct,
                              minorTicks: 5,
                              max:        monthlyGoal * greenMaxPct
                            };

                            var chart = new google.visualization.Gauge(
                              document.getElementById('gauge_div')
                            );
                            chart.draw(data, options);
                          }
                        </script>
			<!-- Breaks to drop content below the Sales Pie Chart -->
			<br><br><br>
			<a href='salesdashboard.php' style="text-decoration: none; color: black;">Sales Dashboard</a>
</center>
                </td>
		<td style='text-align: center;'></td> <!-- 3rd column -->
                </tr>
                <tr>
                    <td colspan=3>
<hr>
                        <!-- Table definition for work center down time, Work Center,
			     PWO, Time (Under pie chart) -->
                        <?php
                            $conn = connectRubiconTci();
                            echo indirectLaborWorkCentersDailyShort($conn);
                        ?>
                    </td>
                </tr>
                </table>
    <script>
        const links = {
            'Canceled':             'openOrderStatus.php?report=canceledOrders',
            'Completed':            'openOrderStatus.php?report=completedOrders',
            'Partially Shipped':    'openOrderStatus.php?report=partiallyShipped',
            'Picking':              'openOrderStatus.php?report=pickingOpenOrders',
            'Open':                 'openOrderStatus.php?report=openOpenOrders',
            'On Hold':              'openOrderStatus.php?report=onHoldOperOrders'
        };
        const ctx = document.getElementById('statusChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'pie',
                data: {
                    labels: <?php echo json_encode($labels); ?>,
                        datasets: [{
                            data: <?php echo json_encode($data); ?>,
                            backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#8BC34A']
                        }]
                        },
                        options: {
                            responsive: false,
			    maintainAspectRatio: false,
                            onClick: function (evt, elements) {
                            if (elements.length > 0) {
                                const chartIndex = elements[0].index;
                                const label = chart.data.labels[chartIndex];
                                const url = links[label];
                                    if (url) {
                                        window.location.href = url;
                                    }
                            }
                            },
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                },
                                title: {
                                    display: true,
                                    text: 'Status of Open Orders'
                                },
                                datalabels: {
                                    color: '#000',
                                    font: {
                                        weight: 'bold',
                                        size: 14
                                    },
                                    formatter: function(value) {
                                                return value;
                                    }
                                }
                            }
                        },
                            plugins: [ChartDataLabels]
                        });
    </script>
                <th>
                    <!-- OE Work Center Output table (Top right table) -->
			<!-- PO: controls the border around the work center table  -->
                        <table style="width: 100%; max-width: 100%; border: 0px solid #000;">
                            <tr rowspan=2 style="background-color: lightgrey;">
				<!-- PO: controls the border around the "day, date" above
				     the work center table -->
                                <td colspan=10 style="font-weight: bold; font-size: 18px; text-align: center;
                                border: 0px solid #000; border-collapse: collapse;">

                                <?php echo date('l, m/d'); ?></strong>
                                </p>
                                        <?php
                                            $fontSize = isset($_GET['size']) ? htmlspecialchars($_GET['size']) : '12px';
                                            $today = (new DateTime())->modify('-0 day')->format('Y-m-d');
                                        ?>
                                </td>
                            </tr>
                            <tr style="background-color: lightgrey; font-weight: bold; font-size: 16px;">
                                <td width='50%' style="text-align: center;">Work Center</td>
                                <td width='50%' style="text-align: center;">1st</td>
                                <td width='50%' style="text-align: center;">U</td>
                                <td width='50%' style="text-align: center;">E</td>
                                <td style="text-align: center;">2nd</td>
                                <td style="text-align: center;">U</td>
                                <td style="text-align: center;">E</td>
                                <td style="text-align: center;">3rd</td>
                                <td style="text-align: center;">U</td>
                                <td style="text-align: center;">E</td>
                                <?php
                                    $conn = connectRubiconTci();
                                    echo workCenterOutput($conn);
                                ?>
                                </td>
                            </tr>
                        </table>
				<hr> <!-- PO: hr below the work center output table -->
                        	<?php echo poweredBy();?>
            </td>
	    <td> <!-- PO: 4th column -->
			<table style="border: 0px solid; width: 300px;">
				<!-- TCI Scrap -->
				<?php
					$conn = connectRubiconTci();
					#echo "<table border='1'>";
					 echo "<tr style=\"background-color: lightgrey;\"><th colspan=4 style=\"text-align: center;\">TCI Scrap</th></tr>";
					echo "<tr style=\"background-color: lightgrey;\"><th>Scrap Lbs</th><th>Scrap Qty</th><th>Scrap Ext</th><th><a href=scrap.php style=\"text-decoration: none; color: black; font-weight: bold;\">Today</a></th></tr>";
					echo getTodayScrapTotals($conn);
					#echo "</table>";
				?>
				 </td>
			     </tr>
			     <tr>
				 <td>
				<?php
					$conn = connectRubiconTci();
					#echo "<table border='1'>";
					echo "<tr style=\"background-color: lightgrey;\"><th>Scrap Lbs</th><th>Scrap Qty</th><th>Scrap Ext</th><th><a href=scrap.php style=\"text-decoration: none; color: black; font-weight: bold;\">Month-to-Date</th></tr>";
					echo getMonthToDateScrapTotals($conn);
					#echo "</table>";
				?>
			      <tr>
				  <td>
				<?php
					$conn = connectRubiconTci();
					#echo "<table border='1'>";
					echo "<tr style=\"background-color: lightgrey;\"><th>Scrap Lbs</th><th>Scrap Qty</th><th>Scrap Ext</th><th><a href=scrap.php style=\"text-decoration: none; color: black; font-weight: bold;\">This Quarter</th></tr>";
					echo getQuartlyToDateScrapTotals($conn);
					#echo "</table>";
				?>
			         </td>
			      </tr>
			      <tr>
				 <td>
				<?php
					$conn = connectRubiconTci();
					#echo "<table border='1'>";
					echo "<tr style=\"background-color: lightgrey;\"><th>Scrap Lbs</th><th>Scrap Qty</th><th>Scrap Ext</th><th><a href=scrap.php style=\"text-decoration: none; color: black; font-weight: bold;\">This Year</th></tr>";
					echo getYearToDateScrapTotals($conn);
					#echo "</table>";
				?>
			         </td>
			     </tr>
                              <tr>
                                 <td colspan=4>
				<iframe 
    					src="scraplinegraph.php" 
    					width="750" 
    					height="250" 
    					style="border:0; overflow:hidden"
    					scrolling="no">
				</iframe>
				<hr>
			</table>
<!-- TICKER -->
        <tr>
            <td>
                <div class="ticker-wrap">
                    <div class="ticker" id="ticker">Loading ticker...</div>
                </div>

               <!-- CSS for ticker -->
                <style>
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
                      z-index: 1000; /* ensures ticker is on top */
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
                    </style>

                    <!-- JavaScript to fetch and auto-refresh ticker -->
                    <script>
                    function refreshTicker() {
                        fetch('ticker.php')
                            .then(response => response.text())
                            .then(data => {
                                const ticker = document.getElementById('ticker');
                                //ticker.textContent = data; // for straight white font color
                                ticker.innerHTML = data;
                            })
                            .catch(err => console.error('Error loading ticker:', err));
                    }

                    // Initial load
                    refreshTicker();

                    // Refresh every 30 seconds
                    setInterval(refreshTicker, 30000);
                    </script>
            </td>
        </tr>
    </table>
</body>
</html>
