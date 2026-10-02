#!/usr/bin/php
<?php

require_once '../functions/oeeFunctions.php';
require_once '../functions/oee24hourFunctions.php';
require_once './mailfunctions/oeMailFunctions.php';

$conn = connectRubiconTci();

/*
 * Generate the 24-hour PWO labor report.
 */
$reportHtml = generateEmailPwoLabor24HourReport($conn);

/*
 * Wrap the report in HTML/CSS for the email.
 */
$emailBody = '
<html>
<head>
<style>
body {
    font-family: Arial, sans-serif;
    background-color: #ffffff;
    color: #333333;
    margin: 0;
    padding: 0;
}

table {
    border-collapse: collapse;
    font-size: 9px;
}

th, td {
    padding: 1px 3px;
    line-height: 1.1;
}

h2 {
    font-size: 18px;
    margin: 5px 0 10px 0;
}
</style>
</head>
<body>

<h2>TCI 24 Hour PWO Labor History</h2>

' . $reportHtml . '

</body>
</html>
';

/*
 * Email settings.
 */
$to =  'paul.ohashi@transcableusa.com,
	sonel.lazare@transcableusa.com,
	billy.brown@transcableusa.com,';
$subject = 'TCI 24 Hour PWO Labor History';

$headers  = "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
$headers .= "From: OE-Dashboard@transcableusa.com\r\n";

/*
 * Send the email.
 */
if (mail($to, $subject, $emailBody, $headers)) {
    echo date('D M j H:i:s') . " [" . basename(__FILE__) . "] - Email sent successfully." . PHP_EOL;
} else {
    echo date('D M j H:i:s') . " [" . basename(__FILE__) . "] - Email FAILED." . PHP_EOL;
}
?>
