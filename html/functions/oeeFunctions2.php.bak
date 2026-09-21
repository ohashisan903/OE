<?php

function displayPwoLaborTable($conn)
{
    // Found some time clock issues between UTC, PHP time and MariaDB time, so
    // this function was re-written to make PHP the authority of time.
    // TCI is in Central Time
    date_default_timezone_set('America/Chicago');

    /*
     * Get current Central Time
     */
    $now = new DateTime('now', new DateTimeZone('America/Chicago'));

    /*
     * Determine current shift
     *
     * 1st Shift: 08:05 - 16:05
     * 2nd Shift: 16:06 - 00:05
     * 3rd Shift: 00:06 - 08:04
     */

    $currentTime = $now->format('H:i:s');

    if ($currentTime >= '08:05:00' && $currentTime < '16:06:00') {

        // 1st Shift
        $shiftName = '1st Shift';

        $shiftStart = new DateTime(
            $now->format('Y-m-d') . ' 08:05:00',
            new DateTimeZone('America/Chicago')
        );

    } elseif ($currentTime >= '16:06:00') {

        // 2nd Shift
        $shiftName = '2nd Shift';

        $shiftStart = new DateTime(
            $now->format('Y-m-d') . ' 16:06:00',
            new DateTimeZone('America/Chicago')
        );

    } elseif ($currentTime >= '00:06:00') {

        // 3rd Shift
        $shiftName = '3rd Shift';

        $shiftStart = new DateTime(
            $now->format('Y-m-d') . ' 00:06:00',
            new DateTimeZone('America/Chicago')
        );

    } else {

        /*
         * 00:00 - 00:05
         *
         * This is still considered part of the previous
         * day's 2nd Shift.
         */
        $shiftName = '2nd Shift';

        $shiftStart = new DateTime(
            $now->format('Y-m-d') . ' 16:06:00',
            new DateTimeZone('America/Chicago')
        );

        $shiftStart->modify('-1 day');
    }

    /*
     * Convert the PHP DateTime values into SQL format
     */
    $startDateTime = $shiftStart->format('Y-m-d H:i:s');
    $endDateTime   = $now->format('Y-m-d H:i:s');


    /*
     * SQL
     *
     * We now use explicit start and end times instead of
     * NOW(), CURDATE(), and TIME(NOW()).
     */

    $sql = "
        SELECT employee_name,
               pwo_number,

	       IF(CHAR_LENGTH(item_description) > 30,
   CONCAT(LEFT(item_description, 30), '...'),
   item_description) AS item_description,

               DATE_FORMAT(start_date, '%c/%e/%y %l:%i %p') AS start_date,

               DATE_FORMAT(stop_date, '%c/%e/%y %l:%i %p') AS stop_date,

               total_hours,

               TRUNCATE(completed_quantity, 0) AS completed_quantity

        FROM v_pwo_labor

        WHERE stop_date >= '$startDateTime'
          AND stop_date <= '$endDateTime'

        ORDER BY employee_name ASC
    ";


    /*
     * Execute query
     */

    $result = $conn->query($sql);

    if ($result === false) {

        echo '<p style="color:red;">
                SQL Error: '
                . htmlspecialchars($conn->error)
                . '
              </p>';

        return;
    }


    /*
     * Create the table FIRST.
     *
     * This means the PWO Labor History header will still
     * appear even if there are currently no records.
     */

    echo '<table style="
        width: 100%;
        border-collapse: collapse;
        font-family: Arial, sans-serif;
        font-size: 14px;
    ">';


    /*
     * Table title
     */

    echo '<tr style="background-color: lightgrey;">

            <th colspan="7" style="
                border: 0px solid #999;
                padding: 8px;
            ">

                <div style="
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                ">

                    <span style="
                        flex: 1;
                        text-align: center;
                        font-weight: bold;
                    ">
                        PWO Labor History - ' . $shiftName . '
                    </span>

                    <span style="
                        font-weight: normal;
                    ">
                        ' . $now->format('n/j/y') . '
                    </span>

                </div>

            </th>

          </tr>';


    /*
     * Column headers
     */

    echo '<thead>';

    echo '<tr style="background-color: lightgrey;">';

    echo '<th style="
        border: 0px solid #999;
        padding: 8px;
        text-align: left;
    ">Employee</th>';

    echo '<th style="
        border: 0px solid #999;
        padding: 8px;
        text-align: left;
    ">PWO Number</th>';

    echo '<th style="
        border: 0px solid #999;
        padding: 8px;
        text-align: left;
    ">Item Description</th>';

    echo '<th style="
        border: 0px solid #999;
        padding: 8px;
        text-align: left;
    ">Start Date</th>';

    echo '<th style="
        border: 0px solid #999;
        padding: 8px;
        text-align: left;
    ">Stop Date</th>';

    echo '<th style="
        border: 0px solid #999;
        padding: 8px;
        text-align: right;
    ">Total Hours</th>';

    echo '<th style="
        border: 0px solid #999;
        padding: 8px;
        text-align: right;
    ">Completed Quantity</th>';

    echo '</tr>';

    echo '</thead>';

    echo '<tbody>';


    /*
     * Display records
     */

    if ($result->num_rows > 0) {

        $rowNumber = 0;

        while ($row = $result->fetch_assoc()) {

            $backgroundColor = ($rowNumber % 2 === 0)
                ? '#ADD8E6'
                : '#FFFFFF';

            echo '<tr style="
                background-color: ' . $backgroundColor . ';
            ">';

            echo '<td style="
                border: 0px solid #999;
                padding: 8px;
            ">'
                . htmlspecialchars($row['employee_name'])
                . '</td>';

            echo '<td style="
                border: 0px solid #999;
                padding: 8px;
            ">'
                . htmlspecialchars($row['pwo_number'])
                . '</td>';

            echo '<td style="
                border: 0px solid #999;
                padding: 8px;
            ">'
                . htmlspecialchars($row['item_description'])
                . '</td>';

            echo '<td style="
                border: 0px solid #999;
                padding: 8px;
            ">'
                . htmlspecialchars($row['start_date'])
                . '</td>';

            echo '<td style="
                border: 0px solid #999;
                padding: 8px;
            ">'
                . htmlspecialchars($row['stop_date'])
                . '</td>';

            echo '<td style="
                border: 0px solid #999;
                padding: 8px;
                text-align: right;
            ">'
                . htmlspecialchars($row['total_hours'])
                . '</td>';

            echo '<td style="
                border: 0px solid #999;
                padding: 8px;
                text-align: right;
            ">'
                . htmlspecialchars($row['completed_quantity'])
                . '</td>';

            echo '</tr>';

            $rowNumber++;
        }

    } else {

        /*
         * No records yet for this shift.
         */

        echo '<tr>';

        echo '<td colspan="7" style="
            border: 0px solid #999;
            padding: 12px;
            text-align: center;
            font-style: italic;
        ">
            No labor records yet for this shift.
        </td>';

        echo '</tr>';
    }

    echo '</tbody>';

    echo '</table>';
}

?>

