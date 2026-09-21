#!/usr/bin/php

<?php

require_once __DIR__ . '/../functions/oeeFunctions.php';
require_once __DIR__ . '/../functions/scrapFunction.php';

$conn = connectRubiconTci();

ob_start();
displayMonthlyTransactionTotals($conn);
$table = ob_get_clean();

$to = "paul.ohashi@transcableusa.com, paul.ohashi@transcableusa";
$subject = "TCI Manufacturing Receipts Report";

$message = "
<html>
<head>
    <title>Manufacturing Receipts</title>
</head>
<body>

<h2>Manufacturing Receipts Report</h2>

$table

</body>
</html>
";

$headers  = "MIME-Version: 1.0\r\n";
$headers .= "Content-type:text/html;charset=UTF-8\r\n";
$headers .= "From: tci-helpdesk@transcableusa.com\r\n";

mail($to, $subject, $message, $headers);
?>
