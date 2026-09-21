<?php
require_once './functions/operationsFunctions.php';
require_once './functions/oeeFunctions.php';

$conn = connectRubiconTci();

echo authenticationLogTable($conn);
?>
