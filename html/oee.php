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

require_once './functions/oeeFunctions.php';
require_once './functions/scrapFunction.php';
#require_once 'oeeTestFunctions.php';
?>
<html>
<head>
	<title>OE</title>
	<!-- Load the chart.js library -->
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<!-- Refresh the dashboard every 5 minutes  -->
	<meta http-equiv="refresh" content="300">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- pointer to CSS  -->
	<link rel="stylesheet" href="./css/oedashboard.css">

</head>
<body background=#ffffff>
<!-- PO: Header table with logo, four icons, TCI Operational Efficiency, four icons-->
<table style="width: 100%; max-width: 100%; border: 0px solid #000;">
    <tr>
        <td colspan="4">

    <div class="header-bar">

        <!-- LEFT SIDE -->
        <div class="header-left">
            <img src="pics/TransCableLogo.png" class="header-logo">
            <div class="header-left-text">
	            Safety - Quality - Scrap - Rate
            </div>
        </div>

        <!-- CENTER TITLE -->
        <h2 class="header-title">
            TCI Operational Efficiency
        </h2>

        <!-- RIGHT SIDE -->
        <div class="header-right-text">
                Safety - Quality - Scrap - Rate
        </div>

    </div>

        </td>
    </tr>
</table>

<!-- Master table with sub-tables embedded  -->
<table style="width: 100%; border: 0px solid #000;">
        <tr> 
            <td style="width: 1%; vertical-align: top;">

            <!-- Table 1 with clock and navigation links (Main, Quantity, Operations, Monitor, and shift aggregate -->
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
                    		<a href="./oee.php" style="text-decoration: none; color: #36A2EB;">Main</a>
                    		<br><hr color=#ddecf0>
                    		<a href="./quantByEmployee.php" style="text-decoration: none; color: #36A2EB;">Quantity</a>
                    		<br><hr color=#ddecf0>
                    		<a href="./operations.php" style="text-decoration: none; color: #36A2EB;">Operations</a>
                    		<hr color=#ddecf0>
                    		<a href="./monitor/monitor.php" style="text-decoration: none; color: #36A2EB;">Monitor</a>
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

                                <a href="./oeeoperatorefficiency.php" style="text-decoration: none; color: #000;">1st Shift</a>
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
                                <a href="./oeeoperatorefficiency.php" style="text-decoration: none; color: #000;">2nd Shift</a>
                            </th>
			    <!-- PO: controls the border around the 2nd Shift numbers (footage) in the
				 main navigation menu  -->
                            <th style="width='50%'; width: 100%; border: 0px solid #000;">
                                <?php
                                    $conn = connectRubiconTci();
                                    echo secondShiftTotalManufacturingReceiptQuantity($conn);
                                ?>
                            </th>
                        </tr>

                        <tr>
			    <!-- PO: controls the border around the text "3rd Shift" -->
                            <th style="width='50%'; width: 100%; border: 0px solid #000;">
                                <strong><p>
                                <a href="./oeeoperatorefficiency.php" style="text-decoration: none; color: #000;">3rd Shift</a>
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

                        <tr>
			    <!-- PO: controls the border around the text "24 Hour Report" -->
                            <th style="width='50%'; width: 100%; border: 0px solid #000;">
                                <strong><p>
                                <a href="./24hourproductionoutput.php" style="text-decoration: none; color: #000;">24 Hour Report</a>
                            </th style="width='50%'; width: 100%;">
			    <!-- PO: controls the border around the 3rd Shift numbers (footage) in the
				 main navigation menu  -->
                            <th style="width='50%'; width: 100%; border: 0px solid #000;">
                            </th>
                        </tr>

                    </thead>
                </table>
            </td>
	    <!-- End of navigation table 1  -->

            <!-- PO: Table definition with 4 tables; Operator table, LRB Manufacturing
		 Receipt table, Sales gauge, Sales Order status, and Work Center down time -->
            <td style="width: 400px; align=left; text-align=left">

                <!-- PO: Table with Operator, LRB Manufacturing, and Work Center tables -->
                <table style="width: 350px; max-width: 50%; border: 0px solid #000;">

                <!-- PO: controls working border around the Quartly Footage table with Operator numbers -->
                <th style="width: 200px; border: 0px solid #000;">
                    <?php
			$conn = connectRubiconTci();
			displayMonthlyTransactionTotals($conn);
		    ?>
		</th>

                <th style="text-align: center;">

		<!-- PO: Table containing LRB Manufacturing Receipt, Sales Gague, Sales pie chart, and Work Center PWOs  -->
                <table style="width: 100%; max-width: 100%;">
                    <tr>
                        <!--  Controls working border for the table row with the text LRB Manufacturing Receipt -->
                        <td colspan=3 style="text-align: center; background-color: lightgrey; border: 0px solid #000">
                            <strong>LRB Manufacturing Receipt</strong>
                        </td>
                    </tr>

		    <!-- Row displays today's LRBs in Manufacturing Receipt  -->
                    <tr>
                        <td colspan=2 style="background-color: #ddecf0;">
                            <strong><p>
                            <a href="./lrbManufacturingReceipt.php?when=today" style="text-decoration: none; color: #000;">
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

		    <!-- Row displays 7-day LRBs in Manufacturing Receipt  -->
                    <tr>
                        <td colspan=2>
                            <strong><p>
                            <a href="./lrbManufacturingReceipt.php?when=7day" style="text-decoration: none; color: #000;">
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

		    <!-- Row displays 30-day LRBs in Manufacturing Receipt  -->
                    <tr>
                        <td colspan=2 style="background-color: #ddecf0;">
                            <strong><p>
                            <a href="./lrbManufacturingReceipt.php?when=30day" style="text-decoration: none; color: #000;">
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

		    <!-- Row displays month-to-date LRBs in Manufacturing Receipt  -->
                    <tr>
                        <td colspan=2 style="background-color: ; font-weight: bold;">
                            <a href="./lrbManufacturingReceipt.php?when=thisMonth" style="text-decoration: none; color: #000;">
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
                    <!-- Table row for the monthly sales goal gauge and pie chart with
			 Open Sales Orders -->
                <tr>
                <td colspan="3" style="text-align: center;">
			<!-- PO: hr above the Sales gas gague -->
			<strong>
                        <?php
                            // Get the current month
                            $currentMonth = date('F');
                            // Get current day of the month
                            $dayOfMonth = date('j');  
                            // Calculate week number (week 1 = days 7, week 2 = 14, etc.)
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
                    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                    <!-- Data Labels Plugin -->
                    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
                        <a href="./salesdashboard.php">
                            <div id="gauge_div" style="width: 200px; height: 200px; align: center;">
                            </div>
                        </a>
                    <hr>
			<!-- PO: controls working border around the "Status of Open Orders" pie chart -->
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

                        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
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
			<a href='./salesdashboard.php' style="text-decoration: none; color: black;">Sales Dashboard</a>
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
                    <!-- OE Work Center Status table (Top right table) -->
			<!-- PO: controls the border around the work center table  -->
                        <table style="width: 100%; max-width: 100%; border: 0px solid #000;">
			    <tr  rowspan=2 style="background-color: lightgrey;">
				<td colspan=4 style="font-size: 1.25rem; text-align: center;  font-weight: bold;">Work Center Status</td>
			    </tr>
                            <tr rowspan=2 style="background-color: lightgrey;">
				<!-- PO: controls the border around the "day, date" above
				     the work center table -->
                                <td colspan=10 style="font-weight: bold; font-size: 18px; text-align: center;
                                border: 0px solid #000; border-collapse: collapse;">
                                <?php echo date('l, m/d/y'); ?></strong>
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
                                <td style="text-align: center;">2nd</td>
                                <td style="text-align: center;">3rd</td>
                                <?php
                                    $conn = connectRubiconTci();
                                    echo workCenterOutput($conn);
                                ?>
                                </td>
                            </tr>
                        </table>
				<hr> <!-- PO: hr below the work center output table -->
        		<div class="header-left">
            		<div class="mission-statement-text">
            			We manufacture to speficications, while delivering on time, within cost
	     			parameters, in a clean and safe environment, while continually improving
            		</div>
        		</div>
				<hr> <!-- PO: hr below the company motto -->
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
    					width="880" 
    					height="350" 
    					style="border:0; overflow:hidden"
    					scrolling="no">
				</iframe>
				<hr>
			</table>

<!-- Audit dashboard  -->
<!-- 2026 SCRAP GOAL  -->
<table style="vertical-align: top;">
<tr>
<td>



<table>
<tr style="background-color: lightgrey; font-weight: bold;">
	    <td colspan=5 style="text-align: center; font-weight: bold;">
		Production Scrap KPI
	    </td>
</tr>
<tr style="background-color: lightgrey; font-weight: bold;">
            <td colspan=2 style="text-align: center; font-weight: bold;">
                2025 SCRAP GOAL &le; 5%
            </td>
	</tr>
        <tr>
            <td style="background-color: #ddecf0;">
                1st Qtr
            </td>
            <td style="background-color: #ddecf0;">
                8%
            </td>
        </tr>
        <tr>
            <td>
                2nd Qtr
            </td>
            <td>
                5%
            </td>
        </tr>
        <tr>
            <td style="background-color: #ddecf0;">
                3rd Qtr
            </td>
            <td style="background-color: #ddecf0;">
                5%
            </td>
        </tr>
        <tr>
            <td>
                4th Qtr
            </td>
            <td>
                5%
            </td>
        </tr>
        <tr>
            <td style="background-color: #ddecf0; font-weight: bold;">
                2025 AVG
            </td>
            <td style="background-color: #ddecf0; font-weight: bold;">
                6%
            </td>
        </tr>
        <tr>
            <td colspan=2>
            </td>
        </tr>
<!-- 2026 SCRAP GOAL  -->
        <tr style="background-color: lightgrey; font-weight: bold;">
            <td colspan=2 style="text-align: center; font-weight: bold;">
                2026 SCRAP GOAL &le; 5%
            </td>
        <tr>
            <td style="background-color: #ddecf0;">
                Jan
            </td>
            <td style="background-color: #ddecf0;">
                5%
            </td>
        </tr>
        <tr>
            <td>
                Feb
            </td>
            <td>
                0%
            </td>
        </tr>
        <tr>
            <td style="background-color: #ddecf0;">
                Mar
            </td>
            <td style="background-color: #ddecf0;">
                0%
            </td>
        </tr>
        <tr>
            <td>
                Apr
            </td>
            <td>
                0%
            </td>
        </tr>
        <tr>
            <td style="background-color: #ddecf0; font-weight: bold;">
                2026 AVG
            </td>
            <td style="background-color: #ddecf0; font-weight: bold;">
                5%
            </td>
        </tr>
</td>
</tr>
</table>


</td>
<td width=30px>
<!-- Center column  -->
</td>
<td> <!-- 3rd column OTD Goals -->
        <table>
	    <tr style="background-color: lightgrey; font-weight: bold;">
            	<td colspan=5 style="text-align: center; font-weight: bold;">
                	Production OTD KPI
            	</td>
	    </tr>
            <tr>
                <td colspan=2 style="text-align: center; font-weight: bold; background-color: lightgrey;">
                        2025 OTD GOAL &ge; 95%
                </td>
            <tr>
            </tr>
                <td style="background-color: #ddecf0;">
                        1st Qtr
                </td>
                <td style="background-color: #ddecf0;">
                        98%
                </td>
            </tr>
            <tr>
            </tr>
                <td>
                        2nd Qtr
                </td>
                <td>
                        100%
                </td>
            </tr>
            <tr>
            </tr>
                <td style="background-color: #ddecf0;">
                        3rd Qtr
                </td>
                <td style="background-color: #ddecf0;">
                        99%
                </td>
            </tr>
            <tr>
            </tr>
                <td>
                        4th Qtr
                </td>
                <td>
                        95%
                </td>
            </tr>
            <tr>
            </tr>
                <td style="font-weight: bold; background-color: #ddecf0;">
                        2025 AVG
                </td>
                <td style="font-weight: bold; background-color: #ddecf0;">
                        98%
                </td>
            </tr>
            <tr>
                <td colspan=2>
                </td>
            </tr>
            <tr>
                <td colspan=2 style="text-align: center; font-weight: bold; background-color: lightgrey;">
                        2026 OTD GOAL &le; 95%
                </td>
            </tr>
            <tr>
                <td style="background-color: #ddecf0;">
                        Jan
                </td>
                <td style="background-color: #ddecf0;"`>
                        98%
                </td>
            </tr>
            <tr>
                <td>
                        Feb
                </td>
                <td>
                        0%
                </td>
            </tr>
            <tr>
                <td style="background-color: #ddecf0;">
                        Mar
                <td style="background-color: #ddecf0;">
                        0%
                </td>
            </tr>
            <tr>
                <td>
                        Apr
                </td>
                <td>
                        0%
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold; background-color: #ddecf0;">
                        2026 AVG
                </td>
                <td style="font-weight: bold; background-color: #ddecf0;">
                        98%
                </td>
            </tr>

</td>
</tr>
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
