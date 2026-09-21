#!/usr/bin/php

<?php

require_once __DIR__ . '/../functions/oeeFunctions.php';
require_once __DIR__ . '/../functions/oeeEmailFunctions.php';
require_once __DIR__ . '/../functions/scrapFunction.php';

$conn = connectRubiconTci();

ob_start();
displayMonthlyTransactionTotalsEmail($conn);
$monthlyTable = ob_get_clean();

ob_start();
secondShiftTotalManufacturingReceiptQuantity($conn)
$secondShiftTotal = ob_get_clean();

# Comma delimited list of addresses
$to = "paul.ohashi@transcableusa.com, paul.ohashi@ohashisan.com";
# Email subject
$subject = "TCI Operational Efficiency Reports";

# Email body/message
$message = "
<html>
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
            max-width: 100px;
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
            font-size: 13px;
            padding: 6px 8px;
            border: 1px solid #cccccc;
        }

    </style>
<head>
    <title>Monthly Operator KPI</title>
</head>
<body>

$table

<br><br>
<h2>Operator KPI</h2>
$secondShiftTotal


</body>
</html>
";

$headers  = "MIME-Version: 1.0\r\n";
$headers .= "Content-type:text/html;charset=UTF-8\r\n";
$headers .= "From: tci-helpdesk@transcableusa.com\r\n";

mail($to, $subject, $message, $headers);
?>
