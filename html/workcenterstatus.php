<?php
# PHP script: oee.php
# Paul Ohashi
# Trans Cable International
# Started: July 2025
#
# This script is one part of an overall full-stack package of technologies including:
#
# * MySQL database (oee) to store the data - This may be obsolete as I'm now pulling data directly
#       from rubicon.transcableusa.com - Maria DB.
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
<table style="width: 30%; max-width: 100%; border: 0px solid #000;">
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
</body>
</html>
