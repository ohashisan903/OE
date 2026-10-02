<?php

function generateEmailPwoLabor24HourReport($conn)
{
    ob_start();

    echo firstShiftPwoLaborHistoryYesterday($conn);
    echo '<hr>';
    echo secondShiftPwoLaborHistoryYesterday($conn);
    echo '<hr>';
    echo thirdShiftPwoLaborHistoryYesterday($conn);

    return ob_get_clean();
}


?>
