<?php
/*
Paul Ohashi
oee24hourFunctions.php - Functions for queries to collect 24 hours of data. e.g 24 hours of work center output (0800am - 0759am)
*/

function twentyFourHourWorkCenterOutput($conn) {
    // Determine which day's 1st shift to display
$now = new DateTime();

// TEST: Change '08:00' and  $shift1Start to something in the near future and keep an eye
//       on the dashboard (1st shift column) to watch the footage zero out when the new
//       time occurs.
if ($now->format('H:i') < '08:00') {

    // Before 11:30 AM, show yesterday's 1st shift
    $shift1Start = date('Y-m-d 08:00:00', strtotime('-1 day'));
    $shift1End   = date('Y-m-d 16:00:00', strtotime('-1 day'));

} else {

    // 11:30 AM or later, show today's 1st shift
    $shift1Start = date('Y-m-d 08:00:00');
    $shift1End   = date('Y-m-d 16:00:00');
}


    // Define queries for each shift
    $queries = [
        'shift1' => "SELECT received_work_center, CAST(ROUND(SUM(transaction_quantity)) AS UNSIGNED) AS qty
                     FROM v_lrb_transactions
                     WHERE transaction_type = 'Manufacturing Receipt'
                       AND created_date_time >= (CURDATE() - INTERVAL 1 DAY) + INTERVAL 8 HOUR
                       AND created_date_time <  (CURDATE() - INTERVAL 1 DAY) + INTERVAL 16 HOUR
                     GROUP BY received_work_center",

        'shift2' => "SELECT received_work_center, CAST(ROUND(SUM(transaction_quantity)) AS UNSIGNED) AS qty
                     FROM v_lrb_transactions
                     WHERE transaction_type = 'Manufacturing Receipt'
                       AND created_date_time >= (CURDATE() - INTERVAL 1 DAY) + INTERVAL 16 HOUR
                       AND created_date_time <  CURDATE()
                     GROUP BY received_work_center",

        'shift3' => "SELECT received_work_center, CAST(ROUND(SUM(transaction_quantity)) AS UNSIGNED) AS qty
                     FROM v_lrb_transactions
                     WHERE transaction_type = 'Manufacturing Receipt'
                       AND created_date_time >= CURDATE()
                       AND created_date_time <  CURDATE() + INTERVAL 8 HOUR
                     GROUP BY received_work_center",
    ];

    // Store all results in a combined array
    $data = [];
    $percent = 0;
    foreach ($queries as $shift => $sql) {
        $result = $conn->query($sql);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $wc = $row['received_work_center'];
                $qty = (float)$row['qty'];
                if (!isset($data[$wc])) {
                    $data[$wc] = ['shift1' => 0, 'shift2' => 0, 'shift3' => 0];
                }
                $data[$wc][$shift] = $qty;
            }
        }
    }

    if (empty($data)) {
        return "<tr><td colspan='4'>4 No data found.</td></tr>";
    }

    // Sort alphabetically by work center
    ksort($data);

    // Build table rows
    $rows = '';
    $bgcount = 0;

    foreach ($data as $workCenter => $shifts) {
    $backgroundColor = ($bgcount++ % 2 == 0) ? 'background-color: #ddecf0;' : '';

    // Determine max capacity based on Jimmy's numbers
    switch (true) {
        case (strpos($workCenter, 'CABLER 2') === 0):
            $maxCapacity = 160000; break;
        case (strpos($workCenter, 'CABLER 3') === 0):
            $maxCapacity = 31000; break;
        case (strpos($workCenter, 'CABLER 4') === 0):
            $maxCapacity = 30000; break;
        case (strpos($workCenter, 'COILER') === 0):
            $maxCapacity = 105000; break;
        case (strpos($workCenter, 'EXTRUDER-01') === 0):
            $maxCapacity = 630000; break;
        case (strpos($workCenter, 'EXTRUDER-02') === 0):
            $maxCapacity = 170000; break;
        case (strpos($workCenter, 'EXTRUDER-03') === 0):
            $maxCapacity = 367500; break;
        case (strpos($workCenter, 'EXTRUDER-04') === 0):
            $maxCapacity = 105000; break;
        case (strpos($workCenter, 'EXTRUDER-05') === 0):
            $maxCapacity = 'Unknown'; break;
        case (strpos($workCenter, 'EXTRUDER-06') === 0):
            $maxCapacity = 20000; break;
        case (strpos($workCenter, 'EXTRUDER-07') === 0):
            $maxCapacity = 20000; break;
        case (strpos($workCenter, 'FIBER CUTDO') === 0):
            $maxCapacity = 'Unknown'; break;
        case preg_match('/^SPOOLER/', $workCenter):
            $maxCapacity = 105000; break;
        case preg_match('/^TWINNER-(0[1-9]|1[0-9])/', $workCenter):
            $maxCapacity = 43200; break;
        default:
            $maxCapacity = 'Unknown'; break;
    }

    // Work center cell with max capacity
    $rows .= "<tr style='{$backgroundColor}'>";
    $rows .= "<td width='40%'>"
           . htmlspecialchars(substr($workCenter, 0, 11))
           . " (" . (is_numeric($maxCapacity) ? number_format($maxCapacity) : $maxCapacity) . ")</td>";

    foreach (['shift1', 'shift2', 'shift3'] as $shift) {
        $qty = $shifts[$shift];
        $percent = 0;
        $style = '';

        if (is_numeric($maxCapacity) && $maxCapacity > 0) {
            $percent = ($qty / $maxCapacity) * 100;
            $percent = round($percent);
            if ($percent >= 100) {
                $style = "style='color: green; font-weight: ;'";
            } elseif ($percent < 100 && $percent >= 75) {
                $style = "style='color: #d19c47; font-weight: ;'";
            } elseif ($percent < 75) {
                $style = "style='color: red; font-weight: ;'";
            }
        }

        $rows .= "<td width='20%' {$style}>" . number_format($qty) . " (" . round($percent) . "%)</td>";
    }

    $rows .= "</tr>";
}


    return $rows;
} // END OF - workCenterOutput($conn)



function firstShiftYesterdayTotalManufacturingReceiptQuantity($conn) {
    $firstShiftNumber = "
        SELECT SUM(transaction_quantity) AS Quantity
        FROM item_lrb_transactions
        WHERE type = 'Manufacturing Receipt'
          AND created_date_time >= (CURDATE() - INTERVAL 1 DAY) + INTERVAL 8 HOUR
          AND created_date_time <  (CURDATE() - INTERVAL 1 DAY) + INTERVAL 16 HOUR
    ";

    $result = $conn->query($firstShiftNumber);

    if ($result && $row = $result->fetch_assoc()) {
        $total = $row['Quantity'];

        if (is_numeric($total)) {
            echo number_format($total, 2);
        } else {
            echo "0.00";
        }
    }
}

// Month-to-Date functions
function firstShiftMonthToDateTotalManufacturingReceiptQuantity($conn) {
    $sql = "
        SELECT SUM(transaction_quantity) AS Quantity
        FROM item_lrb_transactions
        WHERE type = 'Manufacturing Receipt'
          AND created_date_time >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
          AND created_date_time < CURDATE() + INTERVAL 1 DAY
          AND TIME(created_date_time) >= '08:00:00'
          AND TIME(created_date_time) < '16:00:00'
    ";

    $result = $conn->query($sql);

    if ($result && $row = $result->fetch_assoc()) {
        $total = $row['Quantity'];

        if (is_numeric($total)) {
            echo number_format($total, 2);
        } else {
            echo "0.00";
        }
    }
}

function secondShiftMonthToDateTotalManufacturingReceiptQuantity($conn) {
    $sql = "
        SELECT SUM(transaction_quantity) AS Quantity
        FROM item_lrb_transactions
        WHERE type = 'Manufacturing Receipt'
          AND created_date_time >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
          AND created_date_time < CURDATE() + INTERVAL 1 DAY
          AND TIME(created_date_time) >= '16:00:00'
          AND TIME(created_date_time) <= '23:59:00'
    ";

    $result = $conn->query($sql);

    if ($result && $row = $result->fetch_assoc()) {
        $total = $row['Quantity'];

        if (is_numeric($total)) {
            echo number_format($total, 2);
        } else {
            echo "0.00";
        }
    }
}

function thirdShiftMonthToDateTotalManufacturingReceiptQuantity($conn) {
    $sql = "
        SELECT SUM(transaction_quantity) AS Quantity
        FROM item_lrb_transactions
        WHERE type = 'Manufacturing Receipt'
          AND created_date_time >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
          AND created_date_time < CURDATE() + INTERVAL 1 DAY
          AND TIME(created_date_time) >= '00:00:00'
          AND TIME(created_date_time) <= '07:59:00'
    ";

    $result = $conn->query($sql);

    if ($result && $row = $result->fetch_assoc()) {
        $total = $row['Quantity'];

        if (is_numeric($total)) {
            echo number_format($total, 2);
        } else {
            echo "0.00";
        }
    }
}

function monthToDateTotalManufacturingReceiptQuantity($conn) {
    $sql = "
        SELECT SUM(transaction_quantity) AS Quantity
        FROM item_lrb_transactions
        WHERE type = 'Manufacturing Receipt'
          AND created_date_time >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
          AND created_date_time < CURDATE() + INTERVAL 1 DAY
    ";

    $result = $conn->query($sql);

    if ($result && $row = $result->fetch_assoc()) {
        $total = $row['Quantity'];

        if (is_numeric($total)) {
            echo number_format($total, 2);
        } else {
            echo '0.00';
        }
    }
}

function firstShiftYesterdayManufacturingReceiptsTable($conn) {
    $sql = "
        SELECT 
            user_name,
            lrb_number,
            item_description,
            created_date_time,
            received_work_center,
            transaction_quantity
        FROM v_lrb_transactions
        WHERE transaction_type = 'Manufacturing Receipt'
          AND created_date_time >= (CURDATE() - INTERVAL 1 DAY) + INTERVAL 8 HOUR
          AND created_date_time <  (CURDATE() - INTERVAL 1 DAY) + INTERVAL 16 HOUR
        ORDER BY user_name
    ";

    $result = mysqli_query($conn, $sql);

    if (!$result) {
        return "<p>Database error: " . htmlspecialchars(mysqli_error($conn)) . "</p>";
    }

    $yesterday = date('F j, Y', strtotime('-1 day'));

    $html = '
    <table class="manufacturing-receipts">
    <tr style="background-color: lightgrey;">
        <td colspan="6" style="font-size: 2.25rem; text-align: center; font-weight: bold;">
		1st Shift - ' . $yesterday . '
        </td>
    </tr>
    <tr style="text-align: center; font-weight: bold; background-color: lightgrey;">
	<td>Operator</td><td>LRB</td><td>Item Description</td><td>Time</td><td>Work Center</td><td>Footage</td>
    </tr>
        <tbody>
    ';

    $rowNumber = 0;
    $totalFootage = 0;

    while ($row = mysqli_fetch_assoc($result)) {
	// Sum up the transaction_quantity
	$totalFootage += (float)$row['transaction_quantity'];
        // Alternate white/lightblue rows
        $rowClass = ($rowNumber % 2 == 0) ? 'row-white' : 'row-lightblue';

	// Display only the time
	//$createdTime = date('g:i A', strtotime($row['created_date_time']));
	$createdTime = date('m/d/Y g:i A', strtotime($row['created_date_time']));

	// Shorten the username to first initial/last name "A Smith"
	$userName = trim($row['user_name']);
	$nameParts = explode(' ', $userName, 2);
	$formattedUserName = strtoupper(substr($nameParts[0], 0, 1));

	if (isset($nameParts[1])) {
    		$formattedUserName .= ' ' . $nameParts[1];
	}

	$workCenter = mb_strlen($row['received_work_center']) > 14
    	? mb_substr($row['received_work_center'], 0, 11) . '...'
    	: $row['received_work_center'];

        $html .= '
            <tr class="' . $rowClass . '">
		<td>' . htmlspecialchars($formattedUserName) . '</td>
                <td>' . htmlspecialchars($row['lrb_number']) . '</td>
		<!-- Truncate the item_description to XX number of characters and add ... -->
		<td>' . htmlspecialchars(
    			mb_strlen($row['item_description']) > 40
        		? mb_substr($row['item_description'], 0, 37) . '...'
        		: $row['item_description']
		) . '</td>
		<!--td>' . htmlspecialchars(date('g:i A', strtotime($row['created_date_time']))) . '</td-->
		<td>' . htmlspecialchars(date('m/d/Y g:i A', strtotime($row['created_date_time']))) . '</td>
		<td>' . htmlspecialchars($workCenter) . '</td>
                <td>' . htmlspecialchars($row['transaction_quantity']) . '</td>
            </tr>
        ';

        $rowNumber++;
    }

$html .= '
    <tr style="font-weight: bold; background-color: #d9d9d9;">
        <td colspan="5" style="text-align: right;">TOTAL FOOTAGE</td>
        <td>' . number_format($totalFootage, 0) . '</td>
    </tr>
';

$html .= '
    </tbody>
</table>
';

    mysqli_free_result($result);

    return $html;
}


function secondShiftYesterdayManufacturingReceiptsTable($conn) {
    $sql = "
        SELECT 
            user_name,
            lrb_number,
            item_description,
            created_date_time,
            received_work_center,
            transaction_quantity
        FROM v_lrb_transactions
        WHERE transaction_type = 'Manufacturing Receipt'
          AND created_date_time >= (CURDATE() - INTERVAL 1 DAY) + INTERVAL 16 HOUR
          AND created_date_time <  CURDATE()
        ORDER BY user_name
    ";

    $result = mysqli_query($conn, $sql);

    if (!$result) {
        return "<p>Database error: " . htmlspecialchars(mysqli_error($conn)) . "</p>";
    }

    $yesterday = date('F j, Y', strtotime('-1 day'));

    $html = '
    <table class="manufacturing-receipts">
    <tr style="background-color: lightgrey;">
        <td colspan="6" style="font-size: 2.25rem; text-align: center; font-weight: bold;">
            2nd shift - ' . $yesterday . '
        </td>
    </tr>
    <tr style="font-weight: bold; background-color: lightgrey;">
	<td>Operator</td><td>LRB</td><td>Item Description</td><td>Time</td><td>Work Center</td><td>Footage</td>
    </tr>
        <tbody>
    ';

    $rowNumber = 0;
    $totalFootage = 0;

    while ($row = mysqli_fetch_assoc($result)) {
        // Sum up the transaction_quantity
        $totalFootage += (float)$row['transaction_quantity'];
        // Alternate white/lightblue rows
        $rowClass = ($rowNumber % 2 == 0) ? 'row-white' : 'row-lightblue';

	// Display only the time
	$createdTime = date('g:i A', strtotime($row['created_date_time']));

	// Shorten the username to first initial/last name "A Smith"
	$userName = trim($row['user_name']);
	$nameParts = explode(' ', $userName, 2);

	$formattedUserName = strtoupper(substr($nameParts[0], 0, 1));

	if (isset($nameParts[1])) {
    		$formattedUserName .= ' ' . $nameParts[1];
	}

        $html .= '
            <tr class="' . $rowClass . '">
		<td>' . htmlspecialchars($formattedUserName) . '</td>
                <td>' . htmlspecialchars($row['lrb_number']) . '</td>
		<!-- Truncate the item_description to XX number of characters and add ... -->
		<td>' . htmlspecialchars(
    			mb_strlen($row['item_description']) > 40
        		? mb_substr($row['item_description'], 0, 37) . '...'
        		: $row['item_description']
		) . '</td>
		<!--td>' . htmlspecialchars(date('g:i A', strtotime($row['created_date_time']))) . '</td-->
		<td>' . htmlspecialchars(date('m/d/Y g:i A', strtotime($row['created_date_time']))) . '</td>
                <td>' . htmlspecialchars($row['received_work_center']) . '</td>
                <td>' . htmlspecialchars($row['transaction_quantity']) . '</td>
            </tr>
        ';

        $rowNumber++;
    }

$html .= '
    <tr style="font-weight: bold; background-color: #d9d9d9;">
        <td colspan="5" style="text-align: right;">TOTAL FOOTAGE</td>
        <td>' . number_format($totalFootage, 0) . '</td>
    </tr>
';

$html .= '
    </tbody>
</table>
';

    mysqli_free_result($result);

    return $html;
}

function thirdShiftYesterdayManufacturingReceiptsTable($conn) {
    $sql = "
        SELECT
            user_name,
            lrb_number,
            item_description,
            created_date_time,
            received_work_center,
            transaction_quantity
        FROM v_lrb_transactions
	WHERE transaction_type = 'Manufacturing Receipt'
	   AND created_date_time BETWEEN CURDATE()
	   AND CURDATE() + INTERVAL 8 HOUR - INTERVAL 1 SECOND
	ORDER BY user_name
	";


    $result = mysqli_query($conn, $sql);

    if (!$result) {
        return "<p>Database error: " . htmlspecialchars(mysqli_error($conn)) . "</p>";
    }

    $today = date('F j, Y');

    $html = '
    <table class="manufacturing-receipts">
    <tr style="background-color: lightgrey;">
        <td colspan="6" style="font-size: 2.25rem; text-align: center; font-weight: bold;">
            3rd shift - ' .  $today . '
        </td>
    </tr>
    <tr style="font-weight: bold; background-color: lightgrey;">
	<td>Operator</td><td>LRB</td><td>Item Description</td><td>Time</td><td>Work Center</td><td>Footage</td>
    </tr>
        <tbody>
    ';

    $totalFootage = 0;
    $rowNumber = 0;

    while ($row = mysqli_fetch_assoc($result)) {
        // Sum up the transaction_quantity
        $totalFootage += (float)$row['transaction_quantity'];
        // Alternate white/lightblue rows
        $rowClass = ($rowNumber % 2 == 0) ? 'row-white' : 'row-lightblue';

	// Display only the time
	$createdTime = date('g:i A', strtotime($row['created_date_time']));

	// Shorten the username to first initial/last name "A Smith"
	$userName = trim($row['user_name']);
	$nameParts = explode(' ', $userName, 2);

	$formattedUserName = strtoupper(substr($nameParts[0], 0, 1));

	if (isset($nameParts[1])) {
    		$formattedUserName .= ' ' . $nameParts[1];
	}

        $html .= '
            <tr class="' . $rowClass . '">
		<td>' . htmlspecialchars($formattedUserName) . '</td>
                <td>' . htmlspecialchars($row['lrb_number']) . '</td>
		<!-- Truncate the item_description to XX number of characters and add ... -->
		<td>' . htmlspecialchars(
    			mb_strlen($row['item_description']) > 40
        		? mb_substr($row['item_description'], 0, 37) . '...'
        		: $row['item_description']
		) . '</td>
		<!--td>' . htmlspecialchars(date('g:i A', strtotime($row['created_date_time']))) . '</td-->
		<td>' . htmlspecialchars(date('m/d/Y g:i A', strtotime($row['created_date_time']))) . '</td>
                <td>' . htmlspecialchars($row['received_work_center']) . '</td>
                <td>' . htmlspecialchars($row['transaction_quantity']) . '</td>
            </tr>
        ';

        $rowNumber++;
    }

    $html .= '
        <tr style="font-weight: bold; background-color: #d9d9d9;">
            <td colspan="5" style="text-align: right;">TOTAL FOOTAGE</td>
            <td>' . number_format($totalFootage, 0) . '</td>
        </tr>
    ';

    $html .= '
        </tbody>
    </table>
    ';

    mysqli_free_result($result);

    return $html;
}

?>
