#!/usr/bin/php

<?php

require_once __DIR__ . '/mailfunctions/oeeFunctions.php';
require_once __DIR__ . '/mailfunctions/oee24hourFunctions.php';
#require_once __DIR__ . '/../functions/scrapFunction.php';

$conn = connectRubiconTci();

ob_start();
displayMonthlyTransactionTotals($conn);
$table1 = ob_get_clean();

ob_start();
firstShiftYesterdayTotalManufacturingReceiptQuantity($conn);
$table2 = ob_get_clean();

ob_start();
displayMonthlyTransactionTotals($conn);
$table3 = ob_get_clean();

$to = "paul.ohashi@transcableusa.com, paul.ohashi@transcableusa";
$subject = "24 Hour Production Daily Report";

$message = "
<html>
<head>

    <style type='text/css'>

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
            color: #333333;
        }

        h2 {
            font-size: 22px;
            margin-bottom: 15px;
        }

        table {
            width: auto;
            max-width: 150px;
            border-collapse: collapse;
            font-size: 13px;
        }

        th {
            font-size: 10px;
            font-weight: bold;
            text-align: left;
            padding: 8px;
            border: 1px solid #cccccc;
        }

        td {
	    width: 250;
            font-size: 10px;
            padding: 6px 8px;
            border: 1px solid #cccccc;
        }

    </style>
    <title>24 Hour Production Daily Report</title>
</head>
<body>

<h2>24 Hour Production Daily Report</h2>

<table>
	<tr>
		<td style='width: 40px;'>
			$table1
		</td>
		<td>
			BLAH<br>

			$table2
		</td>
		<td>
			$table3
		</td>
	</tr>
</table>

</body>
</html>
";

$headers  = "MIME-Version: 1.0\r\n";
$headers .= "Content-type:text/html;charset=UTF-8\r\n";
$headers .= "From: tci-helpdesk@transcableusa.com\r\n";

mail($to, $subject, $message, $headers);
?>

