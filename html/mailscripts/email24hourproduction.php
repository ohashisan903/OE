<?php

/*
 * send24hourproduction.php
 *
 * Generates the 24 Hour Production Output report
 * and emails it through the local SMTP/mail server.
 */

// --------------------------------------------------
// Configuration
// --------------------------------------------------

$to = 'paul.ohashi@transcableusa.com';
$subject = 'TCI - 24 Hour Production Output - ' .
           date('m/d/Y', strtotime('yesterday'));

$from = 'TCI-OE-Reports@transcableusa.com';


// --------------------------------------------------
// Capture the output from the existing dashboard
// --------------------------------------------------

ob_start();

include '/var/www/html/newoee/24hourproductionoutput.php';

$html = ob_get_clean();


// --------------------------------------------------
// Remove browser-specific elements
// --------------------------------------------------

// Remove JavaScript
$html = preg_replace(
    '#<script\b[^>]*>.*?</script>#is',
    '',
    $html
);

// Remove auto-refresh
$html = preg_replace(
    '#<meta[^>]+http-equiv=["\']?refresh["\']?[^>]*>#i',
    '',
    $html
);


// --------------------------------------------------
// Create email-friendly CSS
// --------------------------------------------------

$emailCss = '
<style>

body {
    font-family: Arial, Helvetica, sans-serif;
    background-color: #ffffff;
    color: #000000;
}

table {
    border-collapse: collapse;
}

th {
    font-weight: bold;
}

td,
th {
    padding: 5px;
}

a {
    color: #000000;
    text-decoration: none;
}

</style>
';


// Insert CSS before </head>

$html = str_replace(
    '</head>',
    $emailCss . '</head>',
    $html
);


// --------------------------------------------------
// Email headers
// --------------------------------------------------

$headers = [];

$headers[] = 'MIME-Version: 1.0';

$headers[] = 'Content-type: text/html; charset=UTF-8';

$headers[] = 'From: TCI OE Dashboard <' . $from . '>';


// --------------------------------------------------
// Send the email
// --------------------------------------------------

$result = mail(
    $to,
    $subject,
    $html,
    implode("\r\n", $headers)
);


// --------------------------------------------------
// Logging
// --------------------------------------------------

$logFile = '/var/log/oee-email.log';

$timestamp = date('Y-m-d H:i:s');

if ($result) {

    $message =
        $timestamp .
        ' - SUCCESS - Email sent to ' .
        $to .
        PHP_EOL;

    file_put_contents(
        $logFile,
        $message,
        FILE_APPEND
    );

    echo 'Email sent successfully.' . PHP_EOL;

} else {

    $message =
        $timestamp .
        ' - ERROR - Email FAILED to send to ' .
        $to .
        PHP_EOL;

    file_put_contents(
        $logFile,
        $message,
        FILE_APPEND
    );

    echo 'Email FAILED to send.' . PHP_EOL;

    exit(1);
}

?>

