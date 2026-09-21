<?php
# PHP script: oee.php
# Paul Ohashi
# Trans Cable International
# Started: July 2025
#
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
	<link rel="stylesheet" href="./css/style.css">
	<!-- Real time updates on dashboard -->
	<meta http-equiv="refresh" content="60">
</head>
<body>
    <h2 style="text-align: center;">Test page</h2>
    <?php echo displayClock();?>
    <table style="width: 100%; border: 1px;">
        <tr>
            <!-- Left Column: Data Table -->
            <td style="width: 10%; vertical-align: top;">
                <table style="border: 1px;">
				     <tbody>
                    </tbody>
                    <thead>
                        <tr>
                            <th>
                    <a href="http://tci-bt-linux01/oee/oee.php" style="text-decoration: none; color: #36A2EB;">Main</a>
                    <br><hr color=lightblue>
                    <a href="http://tci-bt-linux01/oee/quantByEmployee.php" style="text-decoration: none; color: #36A2EB;">Quantity</a>
                    <br><hr color=lightblue>
                    <a href="http://tci-bt-linux01/oee/operations.php" style="text-decoration: none; color: #36A2EB;">Operations</a>
                    <hr color=lightblue>
							</th>

                            <td>
<canvas id="barChart" width="400" height="300"></canvas>
<?php
$conn =  connectRubiconTci();
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
            <td style="width: 60%;">
                <canvas id="barChart" width="600" height="400"></canvas>
				<br><br>
            </td>
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
                            </td>
                        </tr>
                    </thead>
                </table>
