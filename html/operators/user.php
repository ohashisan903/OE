<?php
# PHP script: user.php
# Paul Ohashi
# Trans Cable International
# Started: July 2025
#
# The user.php file is a generic framework for user (Operator) information. Whenever a username on the oee.php
# page is clicked, this file is called and dynamically updated with information on that user.
#
# Operator information for the last 24 hours and last 7 days is currently displayed on the report.
#
# Need some error reporting...yes, of course, because this tangled web of tech is madness...
#ini_set('display_errors', 1);
#ini_set('display_startup_errors', 1);
#error_reporting(E_ALL);

require_once '../functions/oeeFunctions.php';
?>

<html>
<head>
    <title>OE</title>
	<!-- Load the chart.js library -->
    	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<!-- Near real-time updates on dashboard -->
	<meta http-equiv="refresh" content="300">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- A pointer to CSS that makes it pretty. -->
	<link rel="stylesheet" href="../css/oedashboard.css">


	<style>
	.button-container {
	display: flex;
	gap: 20px;
	}

	.button {
		width: 100px;                 /* fixed width for consistency */
		height: 15px;                 /* fixed height */
		font-size: 18px;
		text-align: center;
		padding: 20px;
		background-color: #36A2EB;   /* match first button */
		color: white;
		border: none;
		border-radius: 20px;          /* rounded shape */
		text-decoration: none;       /* remove underline on <a> */
		display: inline-flex;
		align-items: center;
		justify-content: center;
		cursor: pointer;
		transition: background-color 1.0s;
	}
	.button:hover {
		/* background-color: #0056b3;    hover effect */
		background-color: #36A2EB;
	}
</style>
</head>
<body>

<?php
    $currentMonth = date('n');
    $whichQuarter = ceil($currentMonth / 3);
    switch ($whichQuarter) {
        case '1':
            $ordinalSuffix = 'st';
            break;
        case '2':
            $ordinalSuffix = 'nd';
            break;
        case '3':
            $ordinalSuffix = 'rd';
            break;
        case '4':
            $ordinalSuffix = 'th';
            break;
        default:
            $ordinalSuffix = '??';
            break;
    }
    if (isset($_GET['user'])) {
        $username = htmlspecialchars($_GET['user']); // Sanitize input
        $dbUser = htmlspecialchars($_GET['dbUser']); // Same thing here...
        echo "<h2 style=\"
        text-align: center;
        font-family: 'Poppins', sans-serif;
        font-size: 2.0em;
        background: linear-gradient(to right, #0077ff, #00c3ff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.4);
        letter-spacing: 1px;
        \">{$whichQuarter}{$ordinalSuffix} Quarter LRB Labor statistics - " . $username . "</h2>";
    echo displayClock();
    echo "
    <a href=\"../oee.php\" style=\"text-decoration: none; color: #36A2EB; font-weight: bold;\">Main</a>
    ";
} else {
    echo "No user specified.";
}
echo "
    <!-- PO: with below controls the width of the outside table -->
    <div style=\"margin: 0 auto; width: 50%; text-align: center;\">
    <table width=100%>
    <tr><td>
            <a href=\"\">
                <button class=\"myButton\">All</button></a>

            <br><br>
";

$conn = connectRubiconTci();

// Get the returned array from the function
$data = getUserLaborHistoryThisQuarter($conn, $username);

// Extract values
$totalQuantity = number_format($data['totalQuantity'], 2);
$table_rows = $data['table_rows'];

echo "<table width=100% style=\"border: 0px solid #ccc; border-collapse: collapse;\">"
    . "<style>td { padding: 8px; border: 0px solid #000; text-align: center;}</style>"
    . "<tr><td colspan=8 style=\"background-color: lightgrey; padding: 10px; text-align: center; font-weight: bold;\">{$whichQuarter}{$ordinalSuffix} Quarter Report - $username</td></tr>"
    . "<tr style=\"background-color: lightgrey; font-weight: bold\"><td>LRB #</td><td>Operator</td>"
    . "<td width=375px>Work Center</td><td width=90px>Created Date/Time</td><td width=90px style=\"color: green;\">Quantity<br>" . $totalQuantity . "</td></tr>"
    . $table_rows
    . "</table><hr color=lightblue>";

    ?>
    </td></tr>
    </table>
</div>

</body>
</html>
