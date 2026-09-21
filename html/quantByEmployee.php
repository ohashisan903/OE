<?php
# PHP script: quantByEmployee.php
# Paul Ohashi
# Trans Cable International
#
# 
# Need some error reporting...yes, of course, because this tangled web of tech is madness...
#ini_set('display_errors', 1);
#ini_set('display_startup_errors', 1);
#error_reporting(E_ALL);

require_once 'oeeFunctions.php';

$conn =  connectRubiconTci();
# Fail if we fail...Failure is not an option!!!
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
# Get employee name and total quantity then sort the output by name
$sql = "
    SELECT employee_name, SUM(completed_quantity) AS total_quantity
    FROM v_pwo_labor
    WHERE TRIM(employee_name) NOT IN ('Allen, James', 'Brown, Billy', 'Brown, Aaron', 'Davis, Jimmy', 'Glass, Michael',
    'Koss, Jacob', 'Limmer, Harley', 'McCarty, Tandy', 'May, Chadwick', 'McNear, Steve', 'Medina, Carlos', 'Medina,
    Erika', 'Mireles, Samuel', 'Ohashi, Paul', 'Ottmo, Alana', 'Rubicon Test', 'Test', 'Whitlow, Katie',
    'Oliver, Stuart', 'Crowson, Richard', 'Castro, Milton', 'Eades, Jim', 'Flanigan. Leslie', 'Joss, Aaron',
    'Reilly, Patrick', 'Rangel, Christian', 'Fuller, Magdalen', 'Troutz, Samuel', 'Nash Micah', 'Thomas, Clifton',
    'Richard, Kaytlin', 'Smyers, Jerrod', 'Parrish, Robert', 'Allen, Elizabeth', 'McInnis, Maxim',
    'Christian, Jake', 'VanBuren, Emile', 'Davis, Dillon', 'Davis Tracy', 'Longoria, Rafhael', 'Vargas, Gabriel',
    'Fortune, Matthew', 'Butner, Ashton', 'Longoria, Said', 'Puckett, Logan', 'Wheatley, Phillip', 'Miller, Layne',
    'North, Brittney', 'Sandstrom, Drheaven')
    GROUP BY employee_name
    ORDER BY total_quantity DESC
";
# hopefully we have some output...
$result = $conn->query($sql);

$labels = [];
$data = [];
$table_rows = '';

# If data is not null from the query, throw it in a table with user and total quantity.
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $labels[] = $row['employee_name'];
        $data[] = $row['total_quantity'];

        // Build table rows
        $table_rows .= "<tr><td>{$row['employee_name']}</td><td>{$row['total_quantity']}</td></tr>";
    }
} else {
    die("Query failed: " . $conn->error);
}
?>

<html>
<head>
    <title>Quantity by Operator</title>
	<!-- Load the chart.js library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<!-- A little CSS to make it pretty. Need more CSS for a professional look -->
	<link rel="stylesheet" href="./css/newstyle.css">
	<!-- Real time updates on dashboard -->
	<meta http-equiv="refresh" content="60">
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
        TCI Operational Efficiency
    </h2>
</div>
    <table style="width: 100%; border: 1px;">
            <!-- Left Column: Data Table -->
            <td style="width: 10%; vertical-align: top;">
                <table style="
                    border-collapse: collapse;
                    border: 1px solid #ccc;
                    box-shadow:
                    -8px 0 10px -5px rgba(0, 123, 255, 0.6),  /* Left glow */
                    0 8px 10px -5px rgba(0, 123, 255, 0.6);   /* Bottom glow */">
				     <tbody>
                    </tbody>
                    <thead>
                        <tr>
                            <th>
                    <?php echo displayClock();?>
                    <a href="http://tci-bt-linux01/oee/oee.php" style="text-decoration: none; color: #36A2EB;">Main</a>
                    <br><hr color=lightblue>
                    <a href="http://tci-bt-linux01/oee/quantByEmployee.php" style="text-decoration: none; color: #36A2EB;">Quantity</a>
                    <br><hr color=lightblue>
                    <a href="http://tci-bt-linux01/oee/operations.php" style="text-decoration: none; color: #36A2EB;">Operations</a>
                    <br><hr color=lightblue>
							</th>
                        </tr>
                    </thead>
                </table>
            </td>
            <td style="width: 20%; vertical-align: top;">
                <table>
                    <thead>
                        <tr>
                            <th>Operator</th>
                            <th>Total Quantity</th>
                        </tr>
                    </thead>
                    <tbody style="text-align: left;">
                        <?php echo $table_rows; ?>
                    </tbody>
                </table>
            </td>

            <!-- Right Column: Bar Chart -->
            <td style="width: 60%;">
                <canvas id="barChart" width="600" height="400"></canvas>
				<br>
				<canvas id="barChart2" width="600" height="400"></canvas>
				<br><br>
            </td>
        </tr>
        <?php echo poweredBy();?>
    </table>

	<!-- Create the bar graph -->
	<div align: center;>
    <script>
        const ctx = document.getElementById('barChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
				/* Dynamically insert the labels and data arrays */
                labels: <?php echo json_encode($labels); ?>,
                datasets: [{
                    label: 'Completed Quantity by Operator',
                    data: <?php echo json_encode($data); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 10000
                        }
                    }
                }
            }
        });
    </script>
	</div>
</body>
</html>
